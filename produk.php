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
    <title>Data Produk - POS System</title>
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        /* --- TEMA HIJAU KONSISTEN --- */
        :root {
            --hijau-tercerah: #96c1ba;
            --hijau-radacerah: #98c2bbff;
            --hijau-radagelap: #539c85ff;
            --hijau-tergelap: #397c67;    
            --sidebar-width: 210px;
        }

        body {
            background-color: var(--hijau-tercerah);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Sidebar Styling (Copy dari transaksi.php biar sama) */
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
        
        .brand-logo img { max-width: 120px; max-height: 60px; object-fit: cover; }

        .nav-link { color: #fefefe; padding: 12px 15px; border-radius: 10px; margin-bottom: 5px; text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background-color: var(--hijau-radagelap); color: white; }
        .nav-section-title { font-size: 12px; text-transform: uppercase; color: #ffff; margin: 20px 0 10px 10px; }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); padding: 30px; }

        /* Card Custom untuk Tabel */
        .card-table {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            background: white;
            padding: 20px;
        }

        /* Button Custom sesuai Referensi */
        .btn-custom-blue { background-color: #435ebe; color: white; }
        .btn-custom-blue:hover { background-color: #364b98; color: white; }
        
        .btn-custom-yellow { background-color: #ffc107; color: #000; }
        
        .btn-custom-green { background-color: var(--hijau-tergelap); color: white; }
        .btn-custom-green:hover { background-color: var(--hijau-radagelap); color: white; }

        /* Table Header Color */
        .table thead th {
            background-color: #f2f7ff;
            color: #435ebe;
            border-bottom: 2px solid #dee2e6;
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
            <a href="index.html" class="nav-link" onclick="loadPage('dashboard')">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="transaksi.php" class="nav-link" >
                <i class="bi bi-cart3"></i> Kasir   
            </a>
            
            <span class="nav-section-title">Manajemen</span>
            <a href="produk.php" class="nav-link active" onclick="loadPage('produk')">
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
        <h3 class="fw-bold mb-4 text-white">Data Barang</h3>

        <div class="card-table">
            
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div class="d-flex gap-2">
                    <button class="btn btn-custom-blue" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-lg"></i> Insert Data
                    </button>
                    <button class="btn btn-custom-yellow" onclick="loadProducts()">
                        <i class="bi bi-arrow-clockwise"></i> Refresh
                    </button>
                </div>
                
                <div class="d-flex gap-2">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search: Nama Barang...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Harga Beli (Modal)</th>
                            <th>Harga Jual</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <tr><td colspan="8" class="text-center">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addProductForm">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" class="form-control" id="pName" required placeholder="Contoh: Kopi Kapal Api">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" id="pCategory">
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="ATK">ATK</option>
                                    <option value="Sembako">Sembako</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Merk / Brand</label>
                                <input type="text" class="form-control" id="pBrand" placeholder="Contoh: Kapal Api">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-danger">Harga Beli (Modal)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="pCost" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-success">Harga Jual</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="pPrice" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok Awal</label>
                                <input type="number" class="form-control" id="pStock" value="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Satuan</label>
                                <select class="form-select" id="pUnit">
                                    <option value="pcs">Pcs</option>
                                    <option value="pack">Pack</option>
                                    <option value="kg">Kg</option>
                                    <option value="dus">Dus</option>
                                    <option value="lusin">Lusin</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/produk.js"></script>
</body>
</html>