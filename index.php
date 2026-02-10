<?php
include 'config.php';
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html"); // Pastikan ini mengarah ke file login yang benar (php/html)
    exit();
}

// --- LOGIKA HITUNG DASHBOARD ---

// 1. Menghitung Jumlah Transaksi
$query_transaksi = mysqli_query($conn, "SELECT COUNT(*) as jumlah_struk FROM sales");
$data_transaksi = mysqli_fetch_assoc($query_transaksi);
$total_transaksi = $data_transaksi['jumlah_struk'] ?: 0;

// 2. Menghitung Total Pendapatan (Semua Waktu)
$query_pendapatan = mysqli_query($conn, "SELECT SUM(total_amount) as total_uang FROM sales");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total_uang'] ?: 0; // Perbaikan nama variabel array

// 3. Menghitung Pendapatan HARI INI
$tanggal_hari_ini = date('Y-m-d');
$query_today = mysqli_query($conn, "SELECT SUM(total_amount) as duit_hari_ini FROM sales WHERE DATE(created_at) = '$tanggal_hari_ini'");
$data_today = mysqli_fetch_assoc($query_today);
$pendapatan_hari_ini = $data_today['duit_hari_ini'] ?: 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD KASIR</title>
    
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --bg-dark: #02021e;      
            --bg-light: #96c1ba;      
            --bg-card: #397c67; 
            --hijau-tercerah: #96c1ba;
            --hijau-radacerah: #98c2bbff;
            --hijau-radagelap: #539c85ff;
            --hijau-tergelap: #397c67;    
            --text-primary: #ffff;
            --text-secondary: #fefefeff;
            --accent-blue: #539c85ff;
            --sidebar-width: 210px;
        }

        /* RESET DEFAULT MARGIN/PADDING */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            background-color: var(--hijau-tercerah);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--hijau-tergelap);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            padding: 20px;
            border-right: 1px solid var(--hijau-radagelap);
            z-index: 1000;
        }
        
        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: px;
        }

        .brand-logo img {
            max-width: 120px;
            max-height: 60px;
            object-fit: cover;
        }

        .nav-link {
            color: var(--text-secondary);
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--hijau-radagelap);
            color: white;
        }

        .nav-section-title {
            font-size: 12px;
            text-transform: uppercase;
            color: #ffff;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-left: 10px;
            display: block;
        }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            background-color: var(--hijau-tercerah);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* --- HEADER / TOP BAR --- */
        .top-bar {
            background-color: var(--hijau-radagelap); 
            color: white; 
            padding: 15px 30px; /* Padding sedikit dikecilkan agar proporsional */
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            /* Pastikan menempel ke atas */
            margin-top: 0;
            width: 100%;
        }

        .content-wrapper {
            padding: 30px;
            flex: 1; /* Agar footer terdorong ke bawah */
        }

        .profile-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* --- CARDS STYLING --- */
        .card-custom {
            background-color: var(--hijau-radagelap); /* Menggunakan warna tema */
            border-radius: 15px;
            padding: 25px;
            height: 100%;
            transition: transform 0.2s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
        }
        
        .card-title-text {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand-logo">
            <img src="assets/images/logo.png" height="60" width="160" alt="Logo">
        </div>

        <nav class="nav flex-column">
            <span class="nav-section-title">Menu Utama</span>
            <a href="index.php" class="nav-link active">
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
            <a href="users.php" class="nav-link" onclick="loadPage('users')">
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
        <div class="top-bar">
            <h4 class="m-0 fw-bold">Dashboard Overview</h4>
            
            <div class="d-flex align-items-center gap-3">
                <div class="profile-area">
                    <div class="text-end d-none d-md-block">
                        <small class="d-block text-white-50">
                            <?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'User'; ?>
                        </small>
                        <span class="fw-bold"><?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?></span>
                    </div>
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                        <?php echo isset($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 2)) : 'GU'; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="row g-4">
                
                <div class="col-md-4">
                    <div class="card-custom text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title-text text-white-50">Total Transaksi</div>
                                <div class="card-value"><?php echo number_format($total_transaksi); ?></div>
                                <small>Transaksi Berhasil</small>
                            </div>
                            <i class="bi bi-receipt fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title-text text-white-50">Total Pendapatan</div>
                                <div class="card-value">Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></div>
                                <small>Semua Waktu</small>
                            </div>
                            <i class="bi bi-cash-coin fs-1 text-white-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card-custom text-white">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="card-title-text text-warning">Pendapatan Hari Ini</div>
                                <div class="card-value">Rp <?php echo number_format($pendapatan_hari_ini, 0, ',', '.'); ?></div>
                                <small class="text-white-50"><?php echo date('d M Y'); ?></small>
                            </div>
                            <i class="bi bi-calendar-check fs-1 text-warning"></i>
                        </div>
                    </div>
                </div>

            </div> </div>

        <?php include 'footer.php'; ?>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
