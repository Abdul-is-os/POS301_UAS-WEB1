<?php
$host = "localhost"; 
$username = "root";
$pass = "1122";
$db = "db_pos_301";

$conn = new mysqli($host, $username, $pass, $db);
if ($conn->connect_error) {
    header('content-type: application/json');
    echo json_encode(["status" => "error", "message" => "KONEKSI DATABASE GAGAL: " . $conn->connect_error]);
    exit();
}
?>