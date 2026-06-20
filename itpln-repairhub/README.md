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

<img width="806" height="422" alt="image" src="https://github.com/user-attachments/assets/171c030e-e8ec-499c-94cd-dddda9d7e114" />

<img width="787" height="481" alt="image" src="https://github.com/user-attachments/assets/1401978a-4bdb-4e47-9ee8-e5b71cc188fb" />

<img width="461" height="436" alt="image" src="https://github.com/user-attachments/assets/23a2ebb7-ae71-4e53-b953-fce024b14098" />


2. Katalog Layanan
   - Menampilkan daftar layanan standar, rincian biaya dasar, dan estimasi waktu.
   - Menyediakan tampilan *Grid* dinamis dengan desain bergaya brutalist.

<img width="803" height="488" alt="image" src="https://github.com/user-attachments/assets/ff95f923-3fdb-4bda-9073-ff3a7a415b38" />


3. Portal Pelacakan Pelanggan (Booking Tracker)
   - Panel interaktif bagi pelanggan untuk melacak, mengelola, mengubah jadwal, atau membatalkan pesanan.
   - Menampilkan detail teknisi yang bertugas dan rincian biaya akhir.
<img width="461" height="450" alt="image" src="https://github.com/user-attachments/assets/bb4f5286-0723-4bc6-93ce-cb6a7919f196" />

4. Sistem Administrator (Admin Panel)

   - Dasbor antarmuka CRUD (*Create, Read, Update, Delete*) khusus untuk admin.

   - Memudahkan manajemen pemesanan, modifikasi profil teknisi, dan penambahan paket layanan baru.
<img width="781" height="467" alt="image" src="https://github.com/user-attachments/assets/4078471b-5bd1-4e54-922a-70b9cf9336d7" />
<img width="704" height="458" alt="image" src="https://github.com/user-attachments/assets/54d542ec-583d-47cc-bbff-67116cfd3047" />
<img width="731" height="478" alt="image" src="https://github.com/user-attachments/assets/3eef18ef-6e83-49cf-8524-7fc6b0406f23" />


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
