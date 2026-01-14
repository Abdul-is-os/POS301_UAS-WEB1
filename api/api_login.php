<?php
// api_login.php
session_start();
header("Content-Type: application/json");

// BAGIAN PENTING: Ini menghubungkan ke database kamu
include '../config.php'; 

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // 1. Ambil data JSON yang dikirim dari JS
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(["status" => "error", "message" => "Isi username dan password!"]);
        exit;
    }

    $user = $conn->real_escape_string($data['username']);
    $pass = $data['password'];

    // 2. Cek ke Database: "Apakah ada user dengan nama ini?"
    $sql = "SELECT * FROM users WHERE username = '$user' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // 3. Cek Password: "Apakah passwordnya cocok dengan yang di database?"
        // Kita pakai password_verify karena password di DB terenkripsi (hash)
        if (password_verify($pass, $row['password'])) {
            
            // Login Sukses! Simpan tiket masuk (Session)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $row['username'];

            echo json_encode([
                "status" => "success", 
                "message" => "Login Berhasil!",
                "data" => ["role" => $row['role']]
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password Salah!"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username tidak ditemukan!"]);
    }
}
?>