<?php
/**
 * ITPLN-RepairHub — index.php
 * Halaman utama: HTML + Vanilla JS (konversi dari React App.tsx)
 * Backend: PHP + MySQL via PDO
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ITPLN-RepairHub | Servis Laptop & Komputer Panggilan Onsite Terpercaya</title>
  <meta name="description" content="Platform booking servis komputer dan laptop panggilan onsite terpercaya ITPLN. Teknisi berpengalaman, harga transparan, garansi 3 bulan." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&family=JetBrains+Mono:wght@700;800&display=swap" rel="stylesheet" />
  <!-- Lucide Icons CDN -->
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<div id="app-root">

  <!-- 1. UTILITY BAR -->
  <div class="util-bar">
    <div class="inner">
      <div class="contact">
        <span>📞 +62 821-1234-ITPLN</span>
        <span>✉️ repair-hub@itpln.ac.id</span>
        <span class="hidden" id="schedule-span">🕐 SENIN - MINGGU (08:00 - 22:00 WIB)</span>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <span class="badge badge-green" id="db-mode-badge">DATABASE: MYSQL</span>
        <span class="badge badge-blue">SERVIS INSTAN PANGGILAN</span>
      </div>
    </div>
  </div>

  <!-- 2. HEADER -->
  <header>
    <div>
      <div class="brand-name">ITPLN-REPAIR<span>HUB</span></div>
      <p class="brand-sub">PLATFORM BOOKING SERVIS KOMPUTER &amp; LAPTOP TERPERCAYA</p>
    </div>
    <div class="session-box">
      <span class="session-label">🔍 Lacak Tiket:</span>
      <input type="email" id="session-email-input" class="session-input" placeholder="Masukkan email Anda..." value="rezarenaldi122@gmail.com" />
      <button class="session-btn" onclick="applySession()">Lacak Sesi</button>
    </div>
  </header>

  <!-- TOAST NOTIFICATION -->
  <div id="toast">
    <span class="toast-msg" id="toast-msg"></span>
    <button class="toast-close" onclick="hideToast()">✕</button>
  </div>

  <!-- 3. HERO -->
  <section class="hero">
    <div>
      <span class="hero-badge">✦ TEKNISI AHLI KUNJUNGAN ONSITE LANGSUNG KE RUMAH &amp; KOS</span>
      <h2>FIX YOUR<br>HARDWARE</h2>
      <p class="hero-desc">Layar laptop garis-garis? Mati total? Windows lemot? Jangan cemas! ITPLN-RepairHub menyediakan mekanik handal terverifikasi untuk reparasi di tempat Anda, amanah, cepat, 100% transparan biaya.</p>
      <div class="hero-trust">
        <div class="trust-card">
          <div class="trust-icon" style="background:#2563eb;color:#fff;">AS</div>
          <div>
            <div style="font-size:11px;font-weight:900;text-transform:uppercase;">Teknisi Tersertifikasi</div>
            <div style="font-size:10px;color:#6b7280;font-weight:700;text-transform:uppercase;">Paham Skema Motherboard</div>
          </div>
        </div>
        <div class="trust-card">
          <div class="trust-icon" style="background:#10b981;color:#fff;">P</div>
          <div>
            <div style="font-size:11px;font-weight:900;text-transform:uppercase;">Garansi Suku Cadang</div>
            <div style="font-size:10px;color:#6b7280;font-weight:700;text-transform:uppercase;">Garansi 3 Bulan Resmi</div>
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="hero-widget">
        <div>
          <div class="widget-title">📅 Booking Instan</div>
          <div class="widget-sub">Isi Nama &amp; Atur Tanggal untuk Reservasi Cepat</div>
        </div>
        <div>
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" class="form-input" id="w-name" placeholder="Masukkan nama Anda..." value="Reza Reynaldi" />
          </div>
          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">Pilihan Layanan</label>
              <select class="form-select form-input" id="w-service"></select>
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal Rencana</label>
              <input type="date" class="form-input" id="w-date" />
            </div>
          </div>
          <a href="#booking-form-section" class="btn-primary">Lengkapi Form &amp; Foto Kendala →</a>
        </div>
      </div>
    </div>
  </section>

  <!-- 4. PROMO BOXES -->
  <div class="promo-boxes">
    <div class="promo-box">
      <div class="promo-num" style="background:#000;color:#fff;">01</div>
      <h4>Diagnosis Masalah Transparan</h4>
      <p>Setiap kendala dianalisis secara terbuka dan transparan oleh teknisi ahli kami guna mencocokkan tindakan perbaikan terbaik sebelum kunjungan tiba.</p>
    </div>
    <div class="promo-box" style="background:#000;color:#fff;">
      <div class="promo-num" style="background:#fff;color:#000;border-color:#fff;">02</div>
      <h4>Mekanik ITPLN Berpengalaman</h4>
      <p style="color:#9ca3af;">Setiap perbaikan ditangani oleh staf berkeahlian mumpuni dalam merawat sistem operasi komputer, ganti LCD, &amp; perbaikan rangkaian kelistrikan motherboard.</p>
    </div>
    <div class="promo-box">
      <div class="promo-num" style="background:#2563eb;color:#fff;border-color:#000;">03</div>
      <h4>Keamanan &amp; Kejujuran 100%</h4>
      <p>Data pribadi Anda terjaga rahasianya. Semua komponen laptop lama yang diganti wajib dikembalikan kepada Anda tanpa rekayasa.</p>
    </div>
  </div>

  <!-- 5. SERVICES CATALOG -->
  <section class="section" id="services-section">
    <div class="text-center" style="max-width:640px;margin:0 auto;">
      <span class="section-tag">KATALOG HARGA STANDARD</span>
      <h3 class="section-title">LET'S CHECK OUR BEST SERVICES</h3>
      <p class="section-sub">Pilihlah standard perbaikan berikut untuk memudahkan pemesanan panggilan onsite atau penanganan garansi 3 bulan.</p>
    </div>
    <div class="services-grid" id="services-grid">
      <div class="loading-box" style="grid-column:1/-1;"><div class="spinner"></div><p style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.05em;">Memuat katalog layanan...</p></div>
    </div>
  </section>

  <!-- 6. BOOKING FORM -->
  <section id="booking-form-section">
    <div class="booking-grid">
      <!-- FORM UTAMA -->
      <div class="form-box">
        <div style="border-bottom:2px dashed #000;padding-bottom:16px;">
          <span class="section-tag">Formulir Verifikasi Reservasi</span>
          <h3 style="font-size:22px;font-weight:900;text-transform:uppercase;letter-spacing:-.02em;margin-top:12px;">Form Pemesanan Servis Panggilan Onsite</h3>
          <p style="font-size:11px;color:#4b5563;font-weight:700;text-transform:uppercase;margin-top:4px;">Silakan isi data diri, jenis komputer Anda, deskripsi kendala teknis, serta sertakan foto bukti layar error / bagian fisik yang rusak.</p>
        </div>

        <form id="booking-form" onsubmit="handleCreateBooking(event)">
          <!-- PART 1: Customer -->
          <div>
            <div class="form-section-title"><span class="step-num">1</span> Data Pelanggan (Tabel: customers)</div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" class="form-input" id="f-name" placeholder="Contoh: Reza Reynaldi" value="Reza Reynaldi" required />
              </div>
              <div class="form-group">
                <label class="form-label">Email Aktif *</label>
                <input type="email" class="form-input" id="f-email" placeholder="Contoh: customer@email.com" value="rezarenaldi122@gmail.com" required />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Nomor HP/WhatsApp *</label>
              <input type="text" class="form-input" id="f-phone" placeholder="Contoh: +62 812-9988-7766" value="+62 812-9988-7766" required />
            </div>
            <div class="form-group">
              <label class="form-label">Alamat Lengkap Servis Panggilan *</label>
              <textarea class="form-input form-textarea" id="f-address" rows="2" placeholder="Tuliskan alamat detail penjemputan/servis onsite" required>Kampus ITPLN, Duri Kosambi, Jakarta Barat</textarea>
            </div>
          </div>

          <!-- PART 2: Layanan & Masalah -->
          <div class="form-part">
            <div class="form-section-title"><span class="step-num">2</span> Layanan &amp; Detail Masalah</div>
            <div class="form-group">
              <label class="form-label">Pilih Jenis Standar Layanan *</label>
              <select class="form-select form-input" id="f-service" required></select>
              <p style="font-size:10px;color:#9ca3af;font-weight:700;font-family:'JetBrains Mono',monospace;margin-top:4px;text-transform:uppercase;">HARGA JASA UTAMA SEBELUM PENYESUAIAN RINCIAN MASALAH DETAIL</p>
            </div>

            <div class="form-group">
              <label class="form-label" style="margin-bottom:8px;">Pilih Gejala / Masalah Tambahan (Multiselect)</label>
              <div class="problems-grid" id="problems-grid"></div>
              <p style="font-size:10px;color:#9ca3af;font-weight:700;font-family:'JetBrains Mono',monospace;margin-top:6px;text-transform:uppercase;">*Estimasi biaya akhir dihitung dari akumulasi masalah yang Anda pilih.</p>
            </div>

            <div class="form-group">
              <label class="form-label">Pilih Teknisi (Opsional)</label>
              <select class="form-select form-input" id="f-tech"></select>
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Tanggal Kedatangan *</label>
                <input type="date" class="form-input" id="f-date" required />
              </div>
              <div class="form-group">
                <label class="form-label">Jam Kedatangan *</label>
                <select class="form-select form-input" id="f-time" required>
                  <option value="09:00">09:00 WIB (Pagi)</option>
                  <option value="10:30">10:30 WIB (Pagi)</option>
                  <option value="13:00" selected>13:00 WIB (Siang)</option>
                  <option value="14:30">14:30 WIB (Siang)</option>
                  <option value="16:00">16:00 WIB (Sore)</option>
                  <option value="19:00">19:00 WIB (Malam - Urgent)</option>
                </select>
              </div>
            </div>
          </div>

          <!-- PART 3: Detail Perangkat -->
          <div class="form-part">
            <div class="form-section-title"><span class="step-num">3</span> Detail Perangkat &amp; Unggah Bukti (Tabel: device_issues)</div>
            <div class="grid-2">
              <div class="form-group">
                <label class="form-label">Merek &amp; Model Perangkat *</label>
                <input type="text" class="form-input" id="f-device" placeholder="Contoh: Acer Swift 3, Lenovo ThinkPad" required />
              </div>
              <div class="form-group">
                <label class="form-label">Kategori Kendala *</label>
                <input type="text" class="form-input" id="f-issuetype" placeholder="Contoh: Overheat / Pecah / Bootloop" required />
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Deskripsi Detail Keluhan *</label>
              <textarea class="form-input form-textarea" id="f-issuedesc" rows="3" placeholder="Masukkan gejala rinci yang dirasakan..." required></textarea>
            </div>

            <!-- Upload zone -->
            <div class="form-group">
              <label class="form-label">Upload Foto Layar Error / Bukti Kerusakan (Maks 8 MB)</label>
              <div class="upload-zone" id="upload-zone" onclick="document.getElementById('file-input').click()"
                   ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                <div id="upload-placeholder">
                  <div style="font-size:32px;margin-bottom:8px;">☁</div>
                  <span style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.05em;">Drag &amp; Drop atau <span style="color:var(--blue);text-decoration:underline;">Pilih File Foto</span></span>
                  <div style="font-size:9px;color:#9ca3af;font-weight:700;text-transform:uppercase;margin-top:4px;">Mendukung PNG, JPG, JPEG (disimpan sebagai Base64)</div>
                </div>
                <div id="upload-preview" class="hidden" style="text-align:center;">
                  <img id="preview-img" src="" alt="Preview" style="max-height:140px;border:2px solid #000;box-shadow:var(--shadow-sm);" />
                  <div style="margin-top:8px;display:flex;align-items:center;justify-content:center;gap:8px;">
                    <span style="font-size:10px;color:#059669;font-weight:900;text-transform:uppercase;">✓ Foto Terlampir</span>
                    <button type="button" onclick="removePhoto(event)" style="font-size:10px;color:#e11d48;font-weight:900;text-transform:uppercase;border:none;background:none;cursor:pointer;">Hapus Foto</button>
                  </div>
                </div>
              </div>
              <input type="file" id="file-input" accept="image/*" onchange="handleFileChange(event)" />
            </div>

            <div class="form-group">
              <label class="form-label">Catatan Tambahan untuk Teknisi (Opsional)</label>
              <input type="text" class="form-input" id="f-notes" placeholder="Contoh: Datang setelah jam makan siang saja ya mas" />
            </div>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-reset" onclick="resetForm()">Reset Form</button>
            <button type="submit" class="btn-submit" id="submit-btn">CONFIRM BOOKING NOW →</button>
          </div>
        </form>
      </div>

      <!-- SIDEBAR -->
      <div>
        <!-- Pricing Summary -->
        <div class="sidebar-box">
          <div class="sidebar-title">
            <span>Rincian Estimasi Biaya</span>
            <span>💲</span>
          </div>
          <div id="pricing-summary">
            <p style="font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;text-align:center;">Silakan pilih layanan perbaikan pada formulir terlebih dahulu.</p>
          </div>
        </div>
        <!-- SOP -->
        <div class="sop-box">
          <div class="sop-title">📖 SOP KERJA TEKNISI ITPLN</div>
          <ol class="sop-list">
            <li>Staf mengkonfirmasi pesanan maksimal 30 menit setelah disubmit.</li>
            <li>Teknisi membawa perkakas komplit (Solder, thermal paste, kit) ke kos.</li>
            <li>Konsultasi mengenai kondisi komponen sebelum penggantian hardware.</li>
            <li>Semua penggantian suku cadang dikonfirmasikan di awal. 100% transparan.</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- 7. TRACKING SECTION -->
  <section id="tracking-section">
    <div class="tracking-header">
      <div>
        <span class="section-tag" style="background:#67e8f9;">Portal Dashboard Customer</span>
        <h3 style="font-size:clamp(22px,3vw,32px);font-weight:900;text-transform:uppercase;letter-spacing:-.02em;margin-top:12px;display:flex;align-items:center;gap:8px;">📋 Tiket Servis Anda (Booking Tracker)</h3>
        <p style="font-size:11px;font-weight:700;text-transform:uppercase;margin-top:4px;">
          Menampilkan relasi data: <span style="font-weight:900;color:var(--blue);text-decoration:underline;">bookings</span>,
          <span style="font-weight:900;color:var(--blue);text-decoration:underline;">services</span>, dan
          <span style="font-weight:900;color:var(--blue);text-decoration:underline;">technicians</span>.
        </p>
      </div>
      <div class="tracking-header-actions">
        <span style="font-size:10px;font-weight:900;text-transform:uppercase;color:#374151;">Tampilkan email:</span>
        <span class="email-badge" id="active-session-display">rezarenaldi122@gmail.com</span>
        <button class="refresh-btn" onclick="fetchAllData()" title="Refresh tiket">↻</button>
      </div>
    </div>

    <div id="tracking-content">
      <div class="loading-box"><div class="spinner"></div><p style="font-size:11px;font-weight:900;text-transform:uppercase;letter-spacing:.05em;">Sinkronisasi status tiket...</p></div>
    </div>
  </section>

  <!-- 8. FAQ -->
  <section class="section" style="border-bottom:0;">
    <div class="text-center" style="max-width:640px;margin:0 auto 0;">
      <span class="section-tag">FAQ</span>
      <h3 class="section-title">Tindakan Tanya Jawab (FAQ)</h3>
      <p class="section-sub">Beberapa hal pokok mengenai operasional servis panggilan ITPLN-RepairHub.</p>
    </div>
    <div class="faq-grid">
      <div class="faq-card"><p class="faq-q"><span class="faq-dot"></span>Apakah saya perlu membayar jika membatalkan booking?</p><p class="faq-a">Tidak ada biaya pembatalan. Jika rencana berubah, silakan tekan tombol "Batalkan Booking" melalui halaman ini maksimal 1 jam sebelum jadwal teknisi berkunjung demi menghormati waktu teknisi kami.</p></div>
      <div class="faq-card"><p class="faq-q"><span class="faq-dot"></span>Bagaimana kelanjutan biaya jika memerlukan penggantian hardware?</p><p class="faq-a">Teknisi akan menginfokan jenis kerusakan fisik/komponen terlebih dahulu. Biaya suku cadang diinfokan transparan terpisah dari biaya jasa pendaftaran pemesanan standard.</p></div>
      <div class="faq-card"><p class="faq-q"><span class="faq-dot"></span>Kapan pengerjaan perbaikan dilakukan di toko fisik?</p><p class="faq-a">Apabila kerusakan terlampau kompleks (misalnya re-balling CPU atau jalur motherboard), teknisi akan membawa unit Anda secara aman dengan surat serah terima fisik ke lab ITPLN RepairHub.</p></div>
      <div class="faq-card"><p class="faq-q"><span class="faq-dot"></span>Bagaimana jaminan keamanan data pribadi saya?</p><p class="faq-a">Kami memegang teguh etika kerahasiaan. Semua data customer aman di dalam cloud lokal atau media penyimpanan orisinal Anda. Kami menyarankan mem-backup data rahasia sebelum pengerjaan jika masih memungkinkan.</p></div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <div class="footer-grid">
      <div>
        <div class="footer-brand">
          <div class="footer-logo">🔧</div>
          <h1 class="footer-name">ITPLN <span>RepairHub</span></h1>
        </div>
        <p class="footer-desc">Platform layanan service laptop, computer, &amp; perangkat keras kelistrikan panggilan onsite terpadu untuk wilayah operasional kampus ITPLN &amp; sekitarnya.</p>
        <p style="font-size:9px;color:#6b7280;font-family:'JetBrains Mono',monospace;text-transform:uppercase;margin-top:8px;">Sistem Informasi 5-Tabel Relasi Database CRUD — PHP + MySQL</p>
      </div>
      <div class="footer-col">
        <h5>Tautan Cepat</h5>
        <ul class="footer-links">
          <li><a href="#booking-form-section">Form Booking</a></li>
          <li><a href="#tracking-section">Lacak Jadwal Booking</a></li>
          <li><a href="#services-section">Katalog Layanan</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Lokasi Workshop</h5>
        <p style="font-size:10px;line-height:1.8;font-weight:700;text-transform:uppercase;">Gedung Utama Lt. 1 Kampus ITPLN,<br>Jl. Lingkar Luar Barat,<br>Duri Kosambi, Cengkareng,<br>Jakarta Barat, DKI Jakarta 11750</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> ITPLN-RepairHub. All rights reserved.</p>
      <div style="display:flex;gap:16px;"><span>Kebijakan Privasi</span><span>•</span><span>Syarat &amp; Ketentuan Layanan</span></div>
    </div>
  </footer>

</div><!-- #app-root -->

<!-- RESCHEDULE MODAL -->
<div class="modal-overlay hidden" id="reschedule-modal">
  <div class="modal-box">
    <div class="modal-header">
      <span class="modal-title">Reschedule Servis: <span id="modal-booking-id"></span></span>
      <button class="modal-close" onclick="closeRescheduleModal()">✕</button>
    </div>
    <div>
      <div class="form-group">
        <label class="form-label">Ubah Tanggal Kedatangan *</label>
        <input type="date" class="form-input" id="modal-date" />
      </div>
      <div class="form-group">
        <label class="form-label">Ubah Jam Kedatangan *</label>
        <select class="form-select form-input" id="modal-time">
          <option value="09:00">09:00 WIB (Pagi)</option>
          <option value="10:30">10:30 WIB (Pagi)</option>
          <option value="13:00">13:00 WIB (Siang)</option>
          <option value="14:30">14:30 WIB (Siang)</option>
          <option value="16:00">16:00 WIB (Sore)</option>
          <option value="19:00">19:00 WIB (Malam - Urgent)</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Perbarui Catatan Masalah Tambahan</label>
        <textarea class="form-input form-textarea" id="modal-notes" rows="3" placeholder="Tambahkan keluhan baru..."></textarea>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn-reset" onclick="closeRescheduleModal()">Batal</button>
      <button class="btn-submit" id="modal-save-btn" onclick="handleRescheduleSubmit()">Simpan Jadwal Baru</button>
    </div>
  </div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
