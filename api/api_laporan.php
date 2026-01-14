<?php
// api/api_laporan.php
header("Content-Type: application/json");
include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Ambil parameter tanggal dari URL (Default: Hari ini)
    $startDate = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01'); // Default awal bulan
    $endDate   = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d');     // Default hari ini

    // Query join Sales + Users
    $sql = "SELECT 
                s.id, 
                s.created_at, 
                s.payment_method, 
                s.total_amount, 
                u.username as kasir_name
            FROM sales s
            JOIN users u ON s.user_id = u.id
            WHERE DATE(s.created_at) BETWEEN '$startDate' AND '$endDate'
            ORDER BY s.created_at DESC";

    $result = $conn->query($sql);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode(["status" => "success", "data" => $data]);

} else {
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>