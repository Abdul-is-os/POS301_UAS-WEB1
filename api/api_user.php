<?php
session_start();
header("Content-Type: application/json");
include '../config.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

// 1. GET: Ambil Semua User
if ($method === 'GET') {
    $sql = "SELECT id, username, role, created_at FROM users ORDER BY id ASC";
    $result = $conn->query($sql);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["status" => "success", "data" => $data]);
}

// 2. POST: Tambah User Baru
elseif ($method === 'POST') {
    if (!isset($input['username']) || !isset($input['password']) || !isset($input['role'])) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap!"]);
        exit;
    }

    $username = $conn->real_escape_string($input['username']);
    $role = $conn->real_escape_string($input['role']);
    // PENTING: Hash password
    $password = password_hash($input['password'], PASSWORD_DEFAULT);

    // Cek username kembar
    $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username sudah dipakai!"]);
        exit;
    }

    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "User berhasil ditambahkan"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal: " . $conn->error]);
    }
}

// 3. PUT: Edit User
elseif ($method === 'PUT') {
    if (!isset($input['id']) || !isset($input['username']) || !isset($input['role'])) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap!"]);
        exit;
    }

    $id = $input['id'];
    $username = $conn->real_escape_string($input['username']);
    $role = $input['role'];

    // Logika Password:
    // Jika password diisi, update password. Jika kosong, skip update password.
    if (!empty($input['password'])) {
        $password = password_hash($input['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET username='$username', role='$role', password='$password' WHERE id=$id";
    } else {
        $sql = "UPDATE users SET username='$username', role='$role' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "User berhasil diupdate"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal update: " . $conn->error]);
    }
}

// 4. DELETE: Hapus User
elseif ($method === 'DELETE') {
    $id = $input['id'];

    // PROTEKSI: Jangan hapus diri sendiri
    if ($id == $_SESSION['user_id']) {
        echo json_encode(["status" => "error", "message" => "DILARANG menghapus akun sendiri!"]);
        exit;
    }

    if ($conn->query("DELETE FROM users WHERE id=$id")) {
        echo json_encode(["status" => "success", "message" => "User dihapus"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal hapus"]);
    }
}
?>  