# 🛒 Sistem Point of Sales (POS) Berbasis Web

![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-MariaDB-00758F?style=for-the-badge&logo=mysql)
![Bootstrap](https://img.shields.io/badge/Frontend-Bootstrap%205-7952B3?style=for-the-badge&logo=bootstrap)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

Sistem Kasir (POS) sederhana namun handal yang dibangun menggunakan **PHP Native** dan **MySQL**. Proyek ini dikembangkan untuk memenuhi tugas Ujian Akhir Semester (UAS) mata kuliah Pemrograman Web dan Keamanan Informasi.

**Halaman Web** https://aspel.cyou/ABDUL_UAS/
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
git clone https://github.com/Abdul-is-os/POS301_UAS-WEB1
```
### 3. Konfigurasi Database
1.  Buka **phpMyAdmin** di browser (biasanya di `http://localhost/phpmyadmin`).
2.  Buat database baru dengan nama **`db_pos_301`**.
3.  Klik tab **Import**, lalu pilih file database berakhiran `.sql` yang terdapat di dalam folder `database/` pada project ini.
4.  Klik tombol **Go** atau **Kirim** untuk mengimpor tabel.

### 4. Konfigurasi Koneksi (PENTING)
Karena file konfigurasi tidak di-upload ke GitHub demi keamanan, Anda harus membuatnya secara manual.
1.  Buat file baru bernama **`config.php`** di folder utama proyek.
2.  Salin dan tempel kode berikut ke dalam file tersebut:

```php
<?php
$host = "localhost";
$user = "root";      // Default user XAMPP
$pass = "";          // Default password XAMPP (biarkan kosong)
$db   = "db_pos_301"; // Pastikan sama dengan nama database di langkah 3

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi Gagal: " . $conn->connect_error);
}
?>
```
### 5. Jalankan Aplikasi
Setelah database dan konfigurasi siap, buka browser Anda dan akses URL berikut: http://localhost/POS301_UAS-WEB1

### 6. Login & Penggunaan (Akun Demo)
Setelah aplikasi terbuka, gunakan akun default berikut untuk masuk ke dalam sistem:

| Role | Username | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin` | `password` | Full Akses (Dashboard, Kelola Produk, Laporan, User). |
| **Kasir** | `` | `` | Terbatas (Hanya akses menu Transaksi Kasir). |

> **Catatan:** Demi keamanan, sangat disarankan untuk segera mengganti password default ini melalui menu *USER* setelah berhasil login.

> Secara default belum ada akun kasir

**Alur Pengujian:**
1. Login sebagai **Admin** untuk mengisi stok barang di menu *Produk*.
2. Logout, lalu login sebagai **Kasir** untuk mencoba fitur transaksi.
3. Login kembali sebagai **Admin** untuk melihat data yang masuk di menu *Laporan*.

### 7. Demonstrasi
**Gambar UI**

A. Halaman Dashboard
<img width="1892" height="548" alt="image" src="https://github.com/user-attachments/assets/865cb89a-1fde-4d68-9df6-0efff190830b" />

> Menu User hanya tersedia untuk admin

B. Halaman Kasir
<img width="1908" height="781" alt="image" src="https://github.com/user-attachments/assets/0e75805c-f1af-4ed5-b140-8e005f073475" />

C. Halaman Data Produk
<img width="1892" height="543" alt="image" src="https://github.com/user-attachments/assets/3231b147-820d-41db-9baf-5240d50bff68" />

D. Halaman Laporan Penjualan
<img width="1913" height="536" alt="image" src="https://github.com/user-attachments/assets/cb093820-8845-423a-9eb3-9579eb73fd46" />

E. Halaman Manajemen User **(ADMIN ONLY)**
<img width="1897" height="526" alt="image" src="https://github.com/user-attachments/assets/6d58978d-42c9-4e68-96cf-a74b073f72da" />

F. Video Demonstrasi

![video DEMO pos 301](https://github.com/user-attachments/assets/7a8334e2-f94e-4122-965f-73f3af154f6c)

### 8. Kontribusi dan Kredit
Projek ini dibuat oleh (**generative AI was used in this project**) Abdul Rafi (menggunakan berbagai laptop karena punya aku matot, aku tidak tahu cara logout git jadi saya jadikan collaborator)





