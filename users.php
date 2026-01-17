<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - POS System</title>
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --hijau-tercerah: #96c1ba;
            --hijau-tergelap: #397c67;
            --sidebar-width: 210px;
        }

        body {
            background-color: var(--hijau-tercerah);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--hijau-tergelap);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            padding: 20px;
            z-index: 1000;
        }
        
        .brand-logo img { max-width: 120px; max-height: 60px; object-fit: cover; }
        .nav-link { color: #fefefe; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background-color: #539c85; color: white; }
        .nav-section-title { font-size: 12px; text-transform: uppercase; color: #ffff; margin: 20px 0 10px 10px; }

        .main-content { margin-left: var(--sidebar-width); padding: 30px; }

        .card-table {
            border: none;
            border-radius: 10px;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .badge-role-admin { background-color: #6f42c1; color: white; }
        .badge-role-cashier { background-color: #198754; color: white; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand-logo">
            <img src="assets/images/logo.png" height="60" width="160" alt="Logo">
        </div>

        <nav class="nav flex-column">
            <span class="nav-section-title">Menu Utama</span>
            <a href="index.php" class="nav-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="transaksi.php" class="nav-link" >
                <i class="bi bi-cart3"></i> Kasir   
            </a>
            
            <span class="nav-section-title">Manajemen</span>
            <a href="produk.php" class="nav-link" onclick="loadPage('produk')">
                <i class="bi bi-box-seam"></i> Produk
            </a>
            <a href="laporan.php" class="nav-link" onclick="loadPage('laporan')">
                <i class="bi bi-bar-chart-line"></i> Laporan
            </a>
            
            <?php if($_SESSION['role'] === 'admin'): ?>
            <a href="users.php" class="nav-link active" onclick="loadPage('users')">
                <i class="bi bi-people"></i> Users
            </a>
            <?php endif; ?>

            <span class="nav-section-title">Lainnya</span>
            <a href="logout.php" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <h3 class="fw-bold mb-4 text-white">Manajemen Pengguna</h3>

        <div class="card-table">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" onclick="openModal('add')">
                    <i class="bi bi-person-plus-fill"></i> Tambah User
                </button>
                <button class="btn btn-outline-secondary" onclick="loadUsers()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Role / Jabatan</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        <tr><td colspan="5" class="text-center">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Tambah User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        <input type="hidden" id="userId"> <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="uName" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Role / Jabatan</label>
                            <select class="form-select" id="uRole">
                                <option value="cashier">Cashier (Kasir)</option>
                                <option value="admin">Admin (Full Akses)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="uPass" placeholder="Masukkan password...">
                            <small class="text-muted d-none" id="editPassHint">
                                *Kosongkan jika tidak ingin mengubah password
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveUser()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const CURRENT_LOGIN_ID = <?php echo $_SESSION['user_id']; ?>;
    </script>
    
    <script src="js/user.js"></script>
</body>
</html>