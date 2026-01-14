<?php
session_start();
// Cek login (biarkan saja, tidak kita ubah logikanya)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DASHBOARD KASIR </title>
    
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            --sidebar-color: #98c2bbff;
            --merah: #ff4c4c;
            --sidebar-width: 210px;
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
            top: 0;
            left: 0;
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
        }

        /* --- MAIN CONTENT --- */
       .main-content {
            margin-left: var(--sidebar-width);
            padding: 0; 
            background-color: var(--hijau-tercerah);
            min-height: 100vh;
        }

        /* --- HEADER / TOP BAR --- */
        .top-bar {
            background-color: var(--hijau-radagelap); 
            color: white; 
            padding: 20px 30px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .content-wrapper {
            padding: 0 30px 30px 30px;
        }

    

        .profile-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* --- CARDS --- */
        .stat-card {
            background-color: var(--accent-blue);
            border-radius: 15px;
            padding: 20px;
            border: none;
            height: 100%;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        /* Warna-warni Icon */
        .bg-icon-purple { background: rgba(201, 200, 204, 0.2); color: #ffff; }
        .bg-icon-green  { background: rgba(201, 200, 204, 0.2); color: #ffff; }
        .bg-icon-blue   { background: rgba(201, 200, 204, 0.2); color: #ffff; }
        .bg-icon-orange { background: rgba(201, 200, 204, 0.2); color: #ffff; }

        .stat-title { color: var(--text-secondary); font-size: 14px; }
        .stat-value { font-size: 28px; font-weight: bold; margin: 5px 0; }
        .stat-change { font-size: 12px; }
        .text-up { color: #ffff; }
        .text-down { color: #ffff; }

        /* --- CHARTS AREA --- */
        .chart-container {
            background-color: var(--hijau-radagelap);
            border-radius: 15px;
            padding: 20px;
            margin-top: 25px;
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
            <a href="index.php" class="nav-link active" onclick="loadPage('dashboard')">
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
            <a href="#" class="nav-link" onclick="loadPage('users')">
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
            <h4 class="m-0">Dashboard Overview</h4>
            
            <div class="d-flex align-items-center gap-3">
                <div class="profile-area">
                    <div class="text-end d-none d-md-block">
                        <small class="d-block text-white-50">Admin</small>
                        <span class="fw-bold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo $_SESSION['username']; ?>&background=random" class="rounded-circle" width="40">
                </div>
            </div>
        </div>

        <div class="content-wrapper">

        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon-box bg-icon-purple">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <div class="stat-title">Total Penjualan</div>
                    <div class="stat-value">Rp 12.5jt</div>
                    <div class="stat-change text-up">
                        <i class="bi bi-arrow-up-short"></i> +2.00% (30 hari)
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon-box bg-icon-green">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div class="stat-title">Total Pendapatan</div>
                    <div class="stat-value">Rp 8.2jt</div>
                    <div class="stat-change text-up">
                        <i class="bi bi-arrow-up-short"></i> +5.45%
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon-box bg-icon-orange">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-title">Total Pengeluaran</div>
                    <div class="stat-value">Rp 4.3jt</div>
                    <div class="stat-change text-down">
                        <i class="bi bi-arrow-down-short"></i> -2.00%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function loadPage(page) {
            console.log("Navigasi ke: " + page);
            // Nanti kita buat logika ganti halaman disini
        }
    </script>
</body>
</html>