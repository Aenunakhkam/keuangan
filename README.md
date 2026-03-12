# 💳 Sistem Informasi Keuangan Sekolah (SI Keuangan)

Sistem ini dirancang untuk memudahkan administrasi keuangan sekolah dengan fokus pada transparansi data dan profesionalitas pelaporan.

## 🌟 Fitur Unggulan Terkini:

### 1. Laporan Transaksi Profesional (Ready-to-Print)
*   **Filter Cerdas:** Memungkinkan bendahara memfilter transaksi berdasarkan rentang tanggal dan kategori spesifik (contoh: Gaji Guru, Administrasi Siswa, Biaya Operasional).
*   **Standar Administrasi Resmi:** Cetakan laporan otomatis menyertakan **KOP Surat Resmi** sekolah (Logo, Nama, Alamat, Motto) yang diambil dari pengaturan sistem.
*   **Format Lansekap:** Layout otomatis melebar untuk memastikan tabel data terlihat rapi dan tidak terpotong.
*   **Lembar Pengesahan:** Dilengkapi kolom tanda tangan Kepala Sekolah dan Bendahara yang terstruktur di bagian bawah laporan.

### 2. Panel Kendali (Dashboard) & UI Premium
*   **Desain "Modern Mesh Aura":** Menggunakan estetika terkini dengan gradasi mesh biru tua yang memberikan kesan mewah, tenang, dan profesional.
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

## 📁 Struktur Proyek (Ringkasan):
*   `app/Http/Controllers`: Logika bisnis dan pengolahan data laporan.
*   `resources/js/Pages`: Tampilan antarmuka yang interaktif (Inertia/Vue).
*   `resources/js/Layouts`: Struktur desain utama aplikasi (Mesh Aura Design).
*   `routes/web.php`: Pengaturan rute akses halaman.
