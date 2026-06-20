<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Panel - ITPLN-RepairHub</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <style>
    /* Tambahan Styling Spesifik Admin */
    body { background: #f3f4f6; padding-top: 0; }
    .admin-container { display: flex; min-height: 100vh; }
    .admin-sidebar { 
      width: 250px; background: #000; color: #fff; padding: 24px; flex-shrink: 0;
      border-right: 4px solid #000;
    }
    .admin-sidebar h2 { font-size: 20px; text-transform: uppercase; margin-bottom: 32px; letter-spacing: -1px; }
    .admin-menu { list-style: none; padding: 0; }
    .admin-menu li { margin-bottom: 12px; }
    .admin-menu a { 
      display: block; padding: 12px; color: #fff; text-decoration: none; font-weight: 900; 
      text-transform: uppercase; font-size: 14px; border: 2px solid transparent; transition: 0.2s;
    }
    .admin-menu a:hover, .admin-menu a.active { background: var(--yellow); color: #000; border-color: #000; }
    
    .admin-content { flex-grow: 1; padding: 32px; max-width: calc(100% - 250px); overflow-x: auto; }
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 4px solid #000; }
    .admin-header h1 { font-size: 28px; text-transform: uppercase; letter-spacing: -1px; }
    
    .data-table { width: 100%; border-collapse: collapse; background: #fff; border: 4px solid #000; box-shadow: var(--shadow-md); margin-bottom: 24px; }
    .data-table th, .data-table td { padding: 12px; border: 2px solid #000; text-align: left; font-size: 12px; }
    .data-table th { background: var(--yellow); font-weight: 900; text-transform: uppercase; }
    
    .btn-admin { padding: 6px 12px; font-size: 11px; font-weight: 900; text-transform: uppercase; background: #fff; border: 2px solid #000; cursor: pointer; box-shadow: 2px 2px 0 0 #000; }
    .btn-admin:hover { transform: translate(-2px, -2px); box-shadow: 4px 4px 0 0 #000; }
    .btn-admin-primary { background: var(--blue); color: #fff; }
    .btn-admin-danger { background: #ef4444; color: #fff; }

    /* Modal Admin */
    .admin-modal-content { max-width: 600px; }
  </style>
</head>
<body>
  
  <div class="admin-container">
    <aside class="admin-sidebar">
      <h2>⚙️ ADMIN PANEL</h2>
      <ul class="admin-menu">
        <li><a href="#" class="active" onclick="switchTab('bookings', this)">📋 Pesanan (Bookings)</a></li>
        <li><a href="#" onclick="switchTab('technicians', this)">👨‍🔧 Teknisi</a></li>
        <li><a href="#" onclick="switchTab('services', this)">🛠️ Layanan Jasa</a></li>
        <li style="margin-top: 32px;"><a href="index.php" style="background:#fff; color:#000; border-color:#000;">← Kembali ke Web</a></li>
      </ul>
    </aside>

    <main class="admin-content">
      
      <!-- TAB BOOKINGS -->
      <div id="tab-bookings" class="tab-pane">
        <div class="admin-header">
          <h1>Manajemen Pesanan</h1>
          <button class="btn-admin" onclick="loadBookings()">Refresh Data</button>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>ID Tiket</th>
              <th>Customer</th>
              <th>Layanan</th>
              <th>Waktu</th>
              <th>Status</th>
              <th>Teknisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="admin-tbody-bookings"></tbody>
        </table>
      </div>

      <!-- TAB TECHNICIANS -->
      <div id="tab-technicians" class="tab-pane hidden">
        <div class="admin-header">
          <h1>Manajemen Teknisi</h1>
          <button class="btn-admin btn-admin-primary" onclick="openTechModal()">+ Tambah Teknisi</button>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Keahlian</th>
              <th>Kontak</th>
              <th>Status</th>
              <th>Rating</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="admin-tbody-technicians"></tbody>
        </table>
      </div>

      <!-- TAB SERVICES -->
      <div id="tab-services" class="tab-pane hidden">
        <div class="admin-header">
          <h1>Manajemen Layanan</h1>
          <button class="btn-admin btn-admin-primary" onclick="openServiceModal()">+ Tambah Layanan</button>
        </div>
        <table class="data-table">
          <thead>
            <tr>
              <th>Nama Layanan</th>
              <th>Kategori</th>
              <th>Durasi</th>
              <th>Harga (Rp)</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="admin-tbody-services"></tbody>
        </table>
      </div>

    </main>
  </div>

  <!-- TOAST NOTIFICATION -->
  <div id="toast" class=""><span id="toast-msg"></span></div>

  <!-- MODAL: UBAH STATUS BOOKING -->
  <div id="modal-edit-booking" class="modal-overlay hidden">
    <div class="modal-content admin-modal-content">
      <h3 style="margin-bottom:16px;">Ubah Status Pesanan</h3>
      <input type="hidden" id="edit-booking-id" />
      <div class="form-group">
        <label>Status Saat Ini</label>
        <select id="edit-booking-status" class="form-input">
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="on_progress">On Progress</option>
          <option value="fixed">Fixed</option>
          <option value="completed">Completed</option>
          <option value="cancelled">Cancelled</option>
        </select>
      </div>
      <div class="form-group">
        <label>Tugaskan Teknisi</label>
        <select id="edit-booking-tech" class="form-input"></select>
      </div>
      <div class="flex-between" style="margin-top:24px;">
        <button class="btn-link" onclick="closeModal('modal-edit-booking')" style="color:#ef4444;">Batal</button>
        <button class="btn-submit" onclick="saveBookingStatus()" style="width:auto; padding:12px 24px;">Simpan Perubahan</button>
      </div>
    </div>
  </div>

  <!-- MODAL: TAMBAH/EDIT TEKNISI -->
  <div id="modal-tech" class="modal-overlay hidden">
    <div class="modal-content admin-modal-content">
      <h3 id="modal-tech-title" style="margin-bottom:16px;">Tambah Teknisi</h3>
      <input type="hidden" id="tech-id" />
      <div class="form-group">
        <label>Nama Teknisi</label>
        <input type="text" id="tech-name" class="form-input" />
      </div>
      <div class="form-group">
        <label>Spesialisasi</label>
        <input type="text" id="tech-specialty" class="form-input" />
      </div>
      <div class="form-group">
        <label>Kontak (Telepon/WA)</label>
        <input type="text" id="tech-phone" class="form-input" />
      </div>
      <div class="form-group">
        <label>Status</label>
        <select id="tech-status" class="form-input">
          <option value="available">Tersedia (Available)</option>
          <option value="busy">Sibuk (Busy)</option>
        </select>
      </div>
      <div class="flex-between" style="margin-top:24px;">
        <button class="btn-link" onclick="closeModal('modal-tech')" style="color:#ef4444;">Batal</button>
        <button class="btn-submit" onclick="saveTechnician()" style="width:auto; padding:12px 24px;">Simpan Teknisi</button>
      </div>
    </div>
  </div>

  <!-- MODAL: TAMBAH/EDIT LAYANAN -->
  <div id="modal-service" class="modal-overlay hidden">
    <div class="modal-content admin-modal-content">
      <h3 id="modal-service-title" style="margin-bottom:16px;">Tambah Layanan</h3>
      <input type="hidden" id="service-id" />
      <div class="form-group">
        <label>Nama Layanan</label>
        <input type="text" id="service-name" class="form-input" />
      </div>
      <div class="form-group">
        <label>Kategori</label>
        <select id="service-category" class="form-input">
          <option value="HARDWARE">HARDWARE</option>
          <option value="SOFTWARE">SOFTWARE</option>
          <option value="MAINTENANCE">MAINTENANCE</option>
          <option value="DATA">DATA</option>
        </select>
      </div>
      <div class="form-group">
        <label>Durasi Pengerjaan</label>
        <input type="text" id="service-duration" class="form-input" placeholder="Contoh: 1 Jam" />
      </div>
      <div class="form-group">
        <label>Harga Dasar (Rp)</label>
        <input type="number" id="service-price" class="form-input" />
      </div>
      <div class="form-group">
        <label>Deskripsi Layanan</label>
        <textarea id="service-description" class="form-input" rows="3"></textarea>
      </div>
      <div class="flex-between" style="margin-top:24px;">
        <button class="btn-link" onclick="closeModal('modal-service')" style="color:#ef4444;">Batal</button>
        <button class="btn-submit" onclick="saveService()" style="width:auto; padding:12px 24px;">Simpan Layanan</button>
      </div>
    </div>
  </div>

  <!-- Load JS Admin -->
  <script src="assets/js/admin.js"></script>
</body>
</html>
