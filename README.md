# ITPLN-RepairHub

Sistem ITPLN-RepairHub untuk digitalisasi layanan servis komputer dan laptop yang dibangun menggunakan murni PHP dan Vanilla JavaScript. Aplikasi ini dirancang untuk membantu pelanggan memesan teknisi secara online, melacak status perbaikan perangkat, serta memberikan kontrol penuh kepada admin/pemilik bisnis melalui sistem pengelolaan mandiri.

## Problem Solving

Aplikasi ini dibuat untuk menyelesaikan masalah utama dalam layanan teknisi komputer:

- Mengurangi miskomunikasi biaya melalui katalog jasa berstandar dengan transparansi harga.
- Mempercepat pengelompokan riwayat servis teknisi yang sering tercecer di riwayat chatting WhatsApp.
- Menyediakan antarmuka yang mudah digunakan bagi pelanggan untuk mendeskripsikan *error* dan mengunggah foto kendala.
- Memadukan pemesanan instan dengan portal pelacakan (*Self-Service Dashboard*) agar pelanggan tidak perlu terus bertanya ke admin.

## Fungsi Utama

1. Dashboard Utama & Reservasi
   - Tampilan ringkas untuk akses cepat ke fitur utama reservasi dan form pemesanan instan.
   - Menampilkan status konektivitas database secara *real-time*.
   - Menyediakan formulir detail kendala perangkat beserta fitur unggah bukti kerusakan (Base64 renderer).

("assets/images/screenshots/Cuplikan layar 2026-06-19 160504.png")
<img width="1823" height="895" alt="Detail Perangkat & Unggah Bukti" src="assets/images/screenshots/Cuplikan layar 2026-06-19 160639.png" />

2. Katalog Layanan
   - Menampilkan daftar layanan standar, rincian biaya dasar, dan estimasi waktu.
   - Menyediakan tampilan *Grid* dinamis dengan desain bergaya brutalist.

<img width="1812" height="892" alt="Katalog Layanan" src="assets/images/screenshots/Cuplikan layar 2026-06-19 160536.png" />

3. Portal Pelacakan Pelanggan (Booking Tracker)
   - Panel interaktif bagi pelanggan untuk melacak, mengelola, mengubah jadwal, atau membatalkan pesanan.
   - Menampilkan detail teknisi yang bertugas dan rincian biaya akhir.

<img width="1823" height="895" alt="Portal Pelacakan" src="assets/images/screenshots/Cuplikan layar 2026-06-19 160709.png" />


## Teknologi & Tools

- **PHP 8+ (Native)**
  - Menangani *logic backend*, *routing* sistem API terpisah, serta manipulasi data server.
- **MySQL / MariaDB**
  - Mesin *Relational Database Management System* utama.
- **PDO (PHP Data Objects)**
  - Lapisan penghubung database yang mengamankan eksekusi query dari *SQL Injection*.
- **Vanilla JavaScript (ES6)**
  - Dasar UI interaktif aplikasi, pemanggilan *Fetch API*, dan manipulasi DOM secara *real-time*.
- **Vanilla CSS3**
  - Utility styling untuk membangun tata letak *Flexbox* & *Grid* dengan desain neo-brutalist.
- **Laragon / XAMPP**
  - Solusi server lokal pengembangan yang menangani Apache/Nginx serta MySQL secara bersamaan.

## Struktur Proyek

- `index.php` - Dashboard utama pelanggan dengan form pemesanan dan katalog.
- `admin.php` - Halaman manajemen administrator (CRUD Penuh).
- `assets/css/style.css` - Layout dan desain UI keseluruhan.
- `assets/js/main.js` - Interaktivitas form dan logika pelacakan klien.
- `assets/js/admin.js` - Logika *fetch* AJAX khusus dasbor admin.
- `api/` - Kumpulan *endpoint backend* untuk melayani *request* dari/ke database.
- `config/database.php` - Pusat pengaturan koneksi database PDO.
- `database/setup.php` - Skrip instalator otomatis MySQL.

## Cara Menjalankan

1. Pastikan Anda memiliki *local server* (Laragon / XAMPP) dan letakkan proyek di dalam folder `www` atau `htdocs`.

2. Jalankan skrip setup database otomatis melalui browser:

```bash
http://localhost/itpln-repairhub/database/setup.php
