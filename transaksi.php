<?php
session_start();
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
    <title>Kasir - POS System</title>
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* --- TEMA HIJAU (Sama dengan Dashboard) --- */
        :root {
            --hijau-tercerah: #96c1ba;
            --hijau-radacerah: #98c2bbff;
            --hijau-radagelap: #539c85ff;
            --hijau-tergelap: #397c67;    
            --text-primary: #ffff;
            --text-secondary: #fefefeff;
            --accent-blue: #539c85ff;
            --sidebar-color: #98c2bbff;
            --grey-white: #f0f2f5;
            --merah: #ff4c4c;
            --sidebar-width: 210px;
        }

        body {
            background-color: var(--hijau-tercerah);
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
            color: #fefefe; padding: 12px 15px; border-radius: 10px;
            margin-bottom: 5px; transition: all 0.3s; display: flex; align-items: center; gap: 10px; text-decoration: none;
        }
        .nav-link:hover, .nav-link.active { background-color: var(--hijau-radagelap); color: white; }
        .nav-section-title { font-size: 12px; text-transform: uppercase; color: #ffff; margin: 20px 0 10px 10px; }

        /* --- MAIN CONTENT --- */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
        }

        /* --- CUSTOM POS STYLES (Mirip Referensi Gambar) --- */
        .card-custom {
            border: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .card-header-custom {
            background-color: var(--accent-blue); /* Menggunakan warna tema kita */
            color: white;
            font-weight: bold;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .search-result-box {
            z-index: 999;
            position: absolute;
            max-height: 200px;
            border: 1px solid #ddd;
            width: 95%;
            background: white;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border-radius: 0 0 8px 8px;
        }

        .search-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .search-item:hover { background-color: #f8f9fa; }

        .table-pos thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }

        .total-section input {
            background-color: white;
            font-size: 1.2rem;
            font-weight: bold;
        }
        
        .form-control:read-only {
            background-color: #e9ecef;
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
            <a href="index.php" class="nav-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="transaksi.php" class="nav-link active" >
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
        
        <div class="row">
            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <i class="bi bi-search"></i> Cari Barang
                    </div>
                    <div class="card-body position-relative">
                        <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Masukan : Kode / Nama Barang [ENTER]" autocomplete="off">
                        <div id="searchResultBox" class="search-result-box mt-1 rounded"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card card-custom">
                    <div class="card-header-custom">
                        <i class="bi bi-list-ul"></i> Hasil Pencarian
                    </div>
                    <div class="card-body d-flex align-items-center justify-content-center" style="height: 85px;">
                        <span class="text-muted fst-italic" id="statusSearch">Belum ada pencarian...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header-custom justify-content-between">
                <span><i class="bi bi-cart-fill"></i> KASIR</span>
                <button class="btn btn-danger btn-sm" onclick="clearCart()">RESET KERANJANG</button>
            </div>
            <div class="card-body">
                
                <div class="row mb-3">
                    <div class="col-md-2 d-flex align-items-center fw-bold">Tanggal</div>
                    <div class="col-md-10">
                        <input type="text" class="form-control" value="<?php echo date('d F Y, H:i'); ?>" readonly>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-pos align-middle">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="35%">Nama Barang</th>
                                <th width="15%">Jumlah</th>
                                <th width="20%">Total</th>
                                <th width="15%">Kasir</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Keranjang Kosong</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="col-md-6">
                        
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Metode Bayar</label>
                            <div class="col-sm-8">
                                <select id="paymentMethod" class="form-select fw-bold text-primary" onchange="handlePaymentMethodChange()">
                                    <option value="cash">💵 Cash (Tunai)</option>
                                    <option value="qris">📱 QRIS</option>
                                    <option value="transfer">🏦 Transfer Bank</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Total Semua</label>
                            <div class="col-sm-8">
                                <input type="text" id="grandTotalDisplay" class="form-control" value="0" readonly>
                                <input type="hidden" id="grandTotalValue" value="0">
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Kembali</label>
                            <div class="col-sm-8">
                                <input type="text" id="changeDisplay" class="form-control" value="0" readonly>
                            </div>
                        </div>
                    </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Total Semua</label>
                            <div class="col-sm-8">
                                <input type="text" id="grandTotalDisplay" class="form-control" value="0" readonly>
                                <input type="hidden" id="grandTotalValue" value="0">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Kembali</label>
                            <div class="col-sm-8">
                                <input type="text" id="changeDisplay" class="form-control" value="0" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3 row">
                            <label class="col-sm-4 col-form-label fw-bold">Bayar</label>
                            <div class="col-sm-8">
                                <input type="number" id="payInput" class="form-control" placeholder="0" oninput="calculateChange()">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8 offset-sm-4">
                                <button class="btn btn-success w-100 py-2 fw-bold" onclick="processPayment()">
                                    <i class="bi bi-cart-check"></i> Bayar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const GLOBAL_USER = {
            id: <?php echo $_SESSION['user_id']; ?>,
            username: "<?php echo $_SESSION['username']; ?>"
        };
    </script>

    <script src="js/transaksi.js"></script> 
</body>
</html>