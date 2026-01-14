<?php
$host = "localhost"; 
$username = "root";
$pass = "";
$db = "nama database";

$conn = new mysqli($host, $username, $pass, $db);
if ($conn->connect_error) {
    header('content-type: application/json');
    echo json_encode(["status" => "error", "message" => "KONEKSI DATABASE GAGAL: " . $conn->connect_error]);
    exit();
}
?>
