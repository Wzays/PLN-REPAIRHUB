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
<img width="933" height="488" alt="Cuplikan layar 2026-06-19 160504" src="https://github.com/user-attachments/assets/9c81eb57-6572-4cbd-9103-4ea8092a518f" />
<img width="796" height="477" alt="Cuplikan layar 2026-06-19 160610" src="https://github.com/user-attachments/assets/2b17cb33-cc6f-4a7d-a383-3f9e7ae7dd8f" />
<img width="446" height="508" alt="Cuplikan layar 2026-06-19 160639" src="https://github.com/user-attachments/assets/52cc1441-9216-4817-855e-f04615d3052a" />



2. Katalog Layanan
   - Menampilkan daftar layanan standar, rincian biaya dasar, dan estimasi waktu.
   - Menyediakan tampilan *Grid* dinamis dengan desain bergaya brutalist.

<img width="814" height="482" alt="Cuplikan layar 2026-06-19 160536" src="https://github.com/user-attachments/assets/b84648fb-32ca-4814-ae4e-772bd68be948" />


3. Portal Pelacakan Pelanggan (Booking Tracker)
   - Panel interaktif bagi pelanggan untuk melacak, mengelola, mengubah jadwal, atau membatalkan pesanan.
   - Menampilkan detail teknisi yang bertugas dan rincian biaya akhir.

<img width="718" height="498" alt="Cuplikan layar 2026-06-19 160709" src="https://github.com/user-attachments/assets/91e9671e-c94e-4ea6-906a-5eb976c94125" />

4. Sistem Administrator (Admin Panel)

   - Dasbor antarmuka CRUD (*Create, Read, Update, Delete*) khusus untuk admin.
   - Memudahkan manajemen pemesanan, modifikasi profil teknisi, dan penambahan paket layanan baru.
<img width="781" height="467" alt="Cuplikan layar 2026-06-20 075816" src="https://github.com/user-attachments/assets/261947ef-2a75-43ff-ae51-2fa3dc3c01c3" />
<img width="704" height="458" alt="Cuplikan layar 2026-06-20 075829" src="https://github.com/user-attachments/assets/6dc3ec9e-7692-4421-909b-84e1f7108096" />
<img width="731" height="478" alt="Cuplikan layar 2026-06-20 075746" src="https://github.com/user-attachments/assets/f90fcb53-f439-4449-a3b4-4e86759c54bf" />

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
