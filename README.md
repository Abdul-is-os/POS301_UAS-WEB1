# 🛒 Sistem Point of Sales (POS) Berbasis Web

![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-00758F?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap%205-7952B3?style=for-the-badge&logo=bootstrap)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

Sistem Kasir (POS) sederhana namun handal yang dibangun menggunakan **PHP Native** dan **MySQL**. Proyek ini dikembangkan untuk memenuhi tugas Ujian Akhir Semester (UAS) mata kuliah Pemrograman Web dan Keamanan Informasi.

---

## 📖 Mengenai Sistem

Sistem ini dirancang untuk mempermudah operasional transaksi penjualan ritel skala kecil hingga menengah. Dibangun dengan arsitektur *Monolithic* sederhana, sistem ini mengutamakan kecepatan akses, kemudahan penggunaan, dan keamanan data dasar.

### Fitur Utama:
* **Role-Based Access Control (RBAC):** Pemisahan hak akses antara **Admin** dan **Kasir**.
* **Dashboard Real-time:** Menampilkan ringkasan total penjualan harian dan stok barang.
* **Manajemen Produk:** Tambah, edit, dan hapus data barang beserta stoknya.
* **Transaksi Kasir:** Antarmuka kasir yang responsif dengan perhitungan otomatis.
* **Laporan Penjualan:** Rekapitulasi riwayat transaksi yang tersimpan di database.
* **Keamanan (ISMS):** Password hashing (Bcrypt), proteksi SQL Injection, dan manajemen sesi yang aman.

---

## ⚙️ Tata Cara Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal (Localhost) menggunakan XAMPP/Laragon.

### 1. Persiapan Lingkungan
Pastikan komputer Anda sudah terinstall:
* Web Server (Apache/Nginx)
* PHP Versi 8.0 atau lebih baru
* MySQL / MariaDB

### 2. Clone Repositori
Buka terminal atau Git Bash, lalu jalankan perintah:
```bash
git clone [https://github.com/Abdul-is-os/POS301_UAS-WEB1.git](https://github.com/Abdul-is-os/POS301_UAS-WEB1.git)
