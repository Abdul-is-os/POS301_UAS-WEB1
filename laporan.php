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
    <title>Laporan Penjualan - POS System</title>
    <link href="bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>

    <style>
        /* --- TEMA HIJAU --- */
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

        /* Sidebar Styling */
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

        /* Card Custom */
        .card-report {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            background: white;
            padding: 20px;
            margin-bottom: 20px;
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
            <a href="index.php" class="nav-link" onclick="loadPage('dashboard')">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="transaksi.php" class="nav-link" >
                <i class="bi bi-cart3"></i> Kasir   
            </a>
            
            <span class="nav-section-title">Manajemen</span>
            <a href="produk.php" class="nav-link" onclick="loadPage('produk')">
                <i class="bi bi-box-seam"></i> Produk
            </a>
            <a href="laporan.php" class="nav-link active" onclick="loadPage('laporan')">
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
        <h3 class="fw-bold mb-4 text-white">Laporan Penjualan</h3>

        <div class="card-report">
            <h5 class="mb-3"><i class="bi bi-filter"></i> Filter Periode</h5>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100" onclick="loadReport()">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>

        <div class="card-report">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="m-0">Data Transaksi</h5>
                <div>
                    <button onclick="downloadExcel()" class="btn btn-success me-2">
                        <i class="bi bi-file-earmark-excel"></i> Unduh Excel
                    </button>
                    <button onclick="downloadPDF()" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Unduh PDF
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal & Waktu</th>
                            <th>Kasir</th>
                            <th>Metode Bayar</th>
                            <th class="text-end">Total Belanja</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody">
                        <tr><td colspan="5" class="text-center">Silakan pilih tanggal dan klik Tampilkan</td></tr>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end">Grand Total</td>
                            <td class="text-end" id="grandTotalDisplay">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script src="bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let reportData = []; // Variabel global untuk menyimpan data laporan

        // Set default tanggal hari ini
        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date().toISOString().split('T')[0];
            const firstDay = new Date();
            firstDay.setDate(1); // Tanggal 1 bulan ini
            
            document.getElementById('startDate').value = firstDay.toISOString().split('T')[0];
            document.getElementById('endDate').value = today;
            
            loadReport(); // Auto load saat buka halaman
        });

        // 1. FETCH DATA DARI API
        async function loadReport() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const tbody = document.getElementById('reportTableBody');
            const totalEl = document.getElementById('grandTotalDisplay');

            tbody.innerHTML = '<tr><td colspan="5" class="text-center">Memuat data...</td></tr>';

            try {
                const res = await fetch(`api/api_laporan.php?start=${start}&end=${end}`);
                const json = await res.json();

                if (json.status === 'success') {
                    reportData = json.data; // Simpan ke variabel global untuk PDF/Excel
                    renderTable(reportData);
                } else {
                    alert('Gagal mengambil data');
                }
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error koneksi</td></tr>';
            }
        }

        function renderTable(data) {
            const tbody = document.getElementById('reportTableBody');
            const totalEl = document.getElementById('grandTotalDisplay');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada transaksi pada periode ini.</td></tr>';
                totalEl.innerText = 'Rp 0';
                return;
            }

            let grandTotal = 0;

            data.forEach(row => {
                grandTotal += parseFloat(row.total_amount);
                const tr = `
                    <tr>
                        <td>#${row.id}</td>
                        <td>${row.created_at}</td>
                        <td>${row.kasir_name}</td>
                        <td><span class="badge bg-info text-dark">${row.payment_method.toUpperCase()}</span></td>
                        <td class="text-end">Rp ${parseInt(row.total_amount).toLocaleString('id-ID')}</td>
                    </tr>
                `;
                tbody.innerHTML += tr;
            });

            totalEl.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        // 2. FUNGSI DOWNLOAD EXCEL (Sesuai Referensi Slide)
        function downloadExcel() {
            if (reportData.length === 0) {
                alert("Tidak ada data untuk diunduh!");
                return;
            }

            // Format data agar rapi di Excel
            const dataToExport = reportData.map(item => ({
                "ID Transaksi": item.id,
                "Tanggal": item.created_at,
                "Nama Kasir": item.kasir_name,
                "Metode Bayar": item.payment_method,
                "Total (Rp)": parseInt(item.total_amount)
            }));

            // Buat Worksheet
            var ws = XLSX.utils.json_to_sheet(dataToExport);

            // Buat Workbook
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Laporan Penjualan");

            // Simpan File
            XLSX.writeFile(wb, "Laporan_POS_" + document.getElementById('startDate').value + ".xlsx");
        }

        // 3. FUNGSI DOWNLOAD PDF (Sesuai Referensi Slide)
        function downloadPDF() {
            if (reportData.length === 0) {
                alert("Tidak ada data untuk diunduh!");
                return;
            }

            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;

            // Siapkan Header Tabel PDF
            const tableBody = [
                [
                    { text: 'ID', style: 'tableHeader' }, 
                    { text: 'Tanggal', style: 'tableHeader' }, 
                    { text: 'Kasir', style: 'tableHeader' }, 
                    { text: 'Total', style: 'tableHeader' }
                ]
            ];

            // Masukkan data baris per baris
            let grandTotal = 0;
            reportData.forEach(item => {
                grandTotal += parseFloat(item.total_amount);
                tableBody.push([
                    item.id,
                    item.created_at,
                    item.kasir_name,
                    "Rp " + parseInt(item.total_amount).toLocaleString('id-ID')
                ]);
            });

            // Tambahkan baris Grand Total di bawah tabel
            tableBody.push([
                { text: 'GRAND TOTAL', colSpan: 3, bold: true, alignment: 'right' },
                {}, {},
                { text: "Rp " + grandTotal.toLocaleString('id-ID'), bold: true }
            ]);

            // Definisi Dokumen PDF
            var docDefinition = {
                content: [
                    { text: 'Laporan Penjualan POS', style: 'header' },
                    { text: 'Periode: ' + start + ' s/d ' + end, style: 'subheader' },
                    {
                        style: 'tableExample',
                        table: {
                            headerRows: 1,
                            widths: ['auto', '*', 'auto', 'auto'], // Mengatur lebar kolom
                            body: tableBody
                        },
                        layout: 'lightHorizontalLines'
                    }
                ],
                styles: {
                    header: { fontSize: 18, bold: true, margin: [0, 0, 0, 10] },
                    subheader: { fontSize: 12, margin: [0, 0, 0, 20], italics: true },
                    tableHeader: { bold: true, fontSize: 12, color: 'black', fillColor: '#eeeeee' },
                    tableExample: { margin: [0, 5, 0, 15] }
                }
            };

            // Generate PDF
            pdfMake.createPdf(docDefinition).download('Laporan_POS_' + start + '.pdf');
        }
    </script>
</body>
</html>