<?php
// api_products.php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow requests from anywhere (useful for testing)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($conn);
        break;
    case 'POST':
        handlePost($conn);
        break;
    case 'PUT':
        handlePut($conn);
        break;
    case 'DELETE':
        handleDelete($conn);
        break;
    default:
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}

// --- FUNCTIONS ---

// 1. READ (Get all products or single product by ID)
function handleGet($conn) {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sql = "SELECT * FROM products WHERE id = $id";
    } else {
        $sql = "SELECT * FROM products ORDER BY id DESC";
    }

    $result = $conn->query($sql);
    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $products]);
}

// 2. CREATE (Add new product)
function handlePost($conn) {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name']) || !isset($data['selling_price'])) {
        echo json_encode(["status" => "error", "message" => "Name and Selling Price are required"]);
        return;
    }

    $name = $conn->real_escape_string($data['name']);
    $cost = isset($data['cost_price']) ? floatval($data['cost_price']) : 0;
    $price = floatval($data['selling_price']);
    $stock = isset($data['stock']) ? intval($data['stock']) : 0;
    $category = isset($data['category']) ? $conn->real_escape_string($data['category']) : '';
    $brand = isset($data['brand']) ? $conn->real_escape_string($data['brand']) : '';
    $unit = isset($data['unit']) ? $conn->real_escape_string($data['unit']) : 'pcs';

    $sql = "INSERT INTO products (name, cost_price, selling_price, stock, category, brand, unit) 
            VALUES ('$name', $cost, $price, $stock, '$category', '$brand', '$unit')";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Product created", "id" => $conn->insert_id]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

// 3. UPDATE (Edit existing product)
function handlePut($conn) {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["status" => "error", "message" => "Product ID is required for update"]);
        return;
    }

    $id = intval($data['id']);
    $name = $conn->real_escape_string($data['name']);
    $cost = floatval($data['cost_price']);
    $price = floatval($data['selling_price']);
    $stock = intval($data['stock']);
    $category = $conn->real_escape_string($data['category']);
    $brand = $conn->real_escape_string($data['brand']);
    $unit = $conn->real_escape_string($data['unit']);

    $sql = "UPDATE products SET 
            name='$name', cost_price=$cost, selling_price=$price, 
            stock=$stock, category='$category', brand='$brand', unit='$unit' 
            WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Product updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}

// 4. DELETE (Remove product)
function handleDelete($conn) {
    // For DELETE, we often pass ID in URL (e.g., ?id=1) or JSON body. 
    // Let's support JSON body for consistency.
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        // Fallback: check URL parameter
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
        } else {
            echo json_encode(["status" => "error", "message" => "ID is required"]);
            return;
        }
    } else {
        $id = intval($data['id']);
    }

    $sql = "DELETE FROM products WHERE id=$id";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Product deleted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error: " . $conn->error]);
    }
}
?>