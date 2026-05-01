# 💳 Sistem Informasi Keuangan Sekolah (SI Keuangan)

Sistem ini dirancang untuk memudahkan administrasi keuangan sekolah dengan fokus pada transparansi data dan profesionalitas pelaporan.

## 🌟 Fitur Unggulan Terkini:

### 1. Laporan Transaksi Profesional (Ready-to-Print)
*   **Filter Cerdas:** Memungkinkan bendahara memfilter transaksi berdasarkan rentang tanggal dan kategori spesifik (contoh: Gaji Guru, Administrasi Siswa, Biaya Operasional).
*   **Standar Administrasi Resmi:** Cetakan laporan otomatis menyertakan **KOP Surat Resmi** sekolah (Logo, Nama, Alamat, Motto) yang diambil dari pengaturan sistem.
*   **Format Lansekap:** Layout otomatis melebar untuk memastikan tabel data terlihat rapi dan tidak terpotong.
*   **Lembar Pengesahan:** Dilengkapi kolom tanda tangan Kepala Sekolah dan Bendahara yang terstruktur di bagian bawah laporan.

### 2. Panel Kendali (Dashboard) & UI Premium
*   **Desain "Modern Green Emerald":** Menggunakan estetika terkini dengan nuansa hijau (green/emerald) yang memberikan kesan profesional, segar, dan dapat dipercaya untuk sistem finansial.
*   **Efek Glassmorphism:** Navigasi menggunakan efek transparansi kaca yang modern dan bersih.
*   **Struktur Menu Terkonsolidasi:** Menu "Data Master" dan "Akademik" telah disederhanakan untuk navigasi yang lebih efisien dan tidak membingungkan pengguna.

### 3. Sinkronisasi Data Otomatis:
*   Setiap transaksi pembayaran siswa maupun penggajian guru otomatis masuk ke dalam **Jurnal Kas**, sehingga bendahara tidak perlu mencatat dua kali.

---

## 🛠️ Spesifikasi Teknis:
*   **Framework Backend:** Laravel 11 (Versi terbaru & stabil).
*   **Framework Frontend:** Vue.js 3 dengan Inertia.js.
*   **Styling:** Tailwind CSS.
*   **Database:** MySQL.
*   **Runtime:** PHP 8.3.

---

## 🚀 Panduan Instalasi (Installation Guide)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi ini di komputer lain dengan mudah. Database akan ter-setup secara otomatis!

### Prasyarat (Prerequisites)
Pastikan komputer Anda sudah terinstal:
- PHP (Versi 8.2 atau 8.3)
- Composer
- Node.js & npm
- MySQL Server (misalnya XAMPP atau Laragon)

### Langkah-langkah Instalasi
1. **Clone Repositori**
   ```bash
   git clone https://github.com/Aenunakhkam/keuangan.git
   cd keuangan
   ```

2. **Instal Dependensi PHP (Vendor)**
   ```bash
   composer install
   ```

3. **Instal Dependensi Frontend (Node Modules)**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment**
   Duplikat file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Buka file `.env` dan atur koneksi database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=keuangan
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Pastikan Anda sudah membuat database kosong bernama `keuangan` di phpMyAdmin / MySQL)*

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi dan Seed Database (Otomatis)**
   Jalankan perintah ini untuk membuat semua tabel dan mengisi data awal secara otomatis (termasuk akun Admin default):
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Kompilasi Aset Frontend (Build)**
   ```bash
   npm run build
   ```
   *(Jika Anda ingin mengembangkan aplikasi, gunakan `npm run dev`)*

8. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Akses aplikasi di browser melalui URL: `http://localhost:8000`

---

## 🔐 Kredensial Login Default

Setelah Anda melakukan instalasi dan `migrate:fresh --seed`, Anda dapat login menggunakan akun berikut:

- **Email:** `admin@admin.com`
- **Password:** `password`

*(Mohon segera ganti password ini setelah Anda berhasil login demi keamanan!)*

---

## 📁 Struktur Proyek (Ringkasan):
*   `app/Http/Controllers`: Logika bisnis dan pengolahan data laporan.
*   `resources/js/Pages`: Tampilan antarmuka yang interaktif (Inertia/Vue).
*   `resources/js/Layouts`: Struktur desain utama aplikasi.
*   `routes/web.php`: Pengaturan rute akses halaman.
*   `database/seeders`: Konfigurasi pengisian awal database otomatis.
