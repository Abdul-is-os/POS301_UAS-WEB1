<?php
// api_sales.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include '../config.php'; // Using your correct filename

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    handleTransaction($conn);
} else {
    echo json_encode(["status" => "error", "message" => "Only POST method allowed for sales"]);
}

function handleTransaction($conn) {
    // 1. Get the Input Data (Cashier ID, Items, Amount Paid)
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['user_id']) || !isset($data['items']) || !isset($data['amount_paid'])) {
        echo json_encode(["status" => "error", "message" => "Incomplete data"]);
        return;
    }

    $userId = intval($data['user_id']);
    $amountPaid = floatval($data['amount_paid']);
    $items = $data['items']; // This should be an array of {product_id, quantity}

    // 2. Start a Database Transaction
    // This ensures that if ANY step fails (like not enough stock), NOTHING is saved.
    $conn->begin_transaction();

    try {
        $totalAmount = 0;
        $saleItemsBuffer = []; // Temporary storage for item details

        // 3. Process each item to calculate Total and Check Stock
        foreach ($items as $item) {
            $prodId = intval($item['product_id']);
            $qty = intval($item['quantity']);

            // Fetch current price and stock
            $sqlProduct = "SELECT selling_price, stock FROM products WHERE id = $prodId FOR UPDATE"; 
            // 'FOR UPDATE' locks this row so no one else can buy it while we process
            $result = $conn->query($sqlProduct);
            
            if ($result->num_rows === 0) {
                throw new Exception("Product ID $prodId not found");
            }

            $product = $result->fetch_assoc();
            
            // Check Stock
            if ($product['stock'] < $qty) {
                throw new Exception("Not enough stock for Product ID $prodId (Available: " . $product['stock'] . ")");
            }

            $price = floatval($product['selling_price']);
            $subtotal = $price * $qty;
            $totalAmount += $subtotal;

            // Add to buffer to save later
            $saleItemsBuffer[] = [
                'product_id' => $prodId,
                'quantity' => $qty,
                'price' => $price,
                'subtotal' => $subtotal
            ];
        }

        // 4. Check if user paid enough
        if ($amountPaid < $totalAmount) {
            throw new Exception("Insufficient payment. Total is $totalAmount");
        }

        $changeAmount = $amountPaid - $totalAmount;

        // 5. Insert into SALES table
        $sqlSale = "INSERT INTO sales (user_id, total_amount, amount_paid, change_amount) 
                    VALUES ($userId, $totalAmount, $amountPaid, $changeAmount)";
        
        if (!$conn->query($sqlSale)) {
            throw new Exception("Failed to save sale header: " . $conn->error);
        }

        $saleId = $conn->insert_id; // Get the ID of the sale we just made

        // 6. Insert into SALE_ITEMS table and UPDATE Stock
        foreach ($saleItemsBuffer as $lineItem) {
            $pId = $lineItem['product_id'];
            $qty = $lineItem['quantity'];
            $price = $lineItem['price'];
            $sub = $lineItem['subtotal'];

            // Insert Item
            $sqlItem = "INSERT INTO sale_items (sale_id, product_id, quantity, price_at_sale, subtotal) 
                        VALUES ($saleId, $pId, $qty, $price, $sub)";
            $conn->query($sqlItem);

            // Decrease Stock
            $sqlUpdateStock = "UPDATE products SET stock = stock - $qty WHERE id = $pId";
            $conn->query($sqlUpdateStock);
        }

        // 7. Commit the Transaction (Save everything)
        $conn->commit();

        echo json_encode([
            "status" => "success", 
            "message" => "Transaction successful",
            "data" => [
                "sale_id" => $saleId,
                "total" => $totalAmount,
                "change" => $changeAmount
            ]
        ]);

    } catch (Exception $e) {
        // If anything went wrong, Rollback (Undo changes)
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>