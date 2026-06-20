// ═══════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════
let services    = [];
let technicians = [];
let bookings    = []; // list of BookingDetail objects
let selectedProblems = [];
let uploadedBase64   = '';
let customerEmailSession = 'rezarenaldi122@gmail.com';
let focusedTicket = null;

const PROBLEM_OPTIONS = [
  { id: 'prob_power',       label: 'Mati Total / Board Konslet',    extraPrice: 150000, description: 'Lampu indikator pengisian daya dan power mati sepenuhnya.' },
  { id: 'prob_liquid',      label: 'Tumpahan Air / Cairan',         extraPrice: 180000, description: 'Cairan masuk ke board sirkuit. Butuh pembersihan ultrasonik.' },
  { id: 'prob_overheat',    label: 'Panas Berlebih / Overheat',     extraPrice: 50000,  description: 'Suhu ekstrem, kipas bising, & sering mati mendadak.' },
  { id: 'prob_screen',      label: 'Layar Retak / Berkedip',        extraPrice: 120000, description: 'Kerusakan komponen LCD atau kelonggaran fleksibel ribbon.' },
  { id: 'prob_corrosion',   label: 'Karat / Korosi Internal',       extraPrice: 90000,  description: 'Karat sirkuit board akibat lingkungan lembab/asam.' },
  { id: 'prob_bsod',        label: 'Sering Blue Screen (BSOD)',     extraPrice: 40000,  description: 'Kegagalan boot driver atau registry OS crash mendadak.' },
  { id: 'prob_keyboard',    label: 'Keyboard Typo / Eror',          extraPrice: 60000,  description: 'Tombol keyboard mendem atau mengetik sendiri secara liar.' },
  { id: 'prob_slow',        label: 'Lemot Parah / OS Corrupt',      extraPrice: 30000,  description: 'Banyak file sampah, malware, atau storage hampir penuh.' },
];

// ═══════════════════════════════════════════════════════
// TOAST
// ═══════════════════════════════════════════════════════
let toastTimer;
function showToast(type, msg) {
  const el  = document.getElementById('toast');
  const txt = document.getElementById('toast-msg');
  el.className = `show ${type}`;
  txt.textContent = msg;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(hideToast, 5000);
}
function hideToast() {
  document.getElementById('toast').className = '';
}

// ═══════════════════════════════════════════════════════
// API HELPERS
// ═══════════════════════════════════════════════════════
const BASE = '';  // same origin

async function apiFetch(url, options = {}) {
  const res = await fetch(BASE + url, options);
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.error || `HTTP ${res.status}`);
  }
  return res.json();
}

// ═══════════════════════════════════════════════════════
// INITIAL LOAD
// ═══════════════════════════════════════════════════════
async function fetchAllData() {
  try {
    const [srv, tech, bkng, health] = await Promise.all([
      apiFetch('api/services.php'),
      apiFetch('api/technicians.php'),
      apiFetch('api/booking_detail.php'),
      apiFetch('api/db_health.php').catch(() => ({ mysqlConnected: false })),
    ]);

    services    = srv;
    technicians = tech;
    bookings    = bkng;

    // Update DB badge
    const badge = document.getElementById('db-mode-badge');
    if (health.mysqlConnected) {
      badge.textContent = 'DATABASE: MYSQL ✓';
      badge.className   = 'badge badge-green';
    } else {
      badge.textContent = 'DATABASE: ERROR';
      badge.className   = 'badge';
      badge.style.cssText = 'background:#f59e0b;color:#000;border-color:#000;';
    }

    renderServicesGrid();
    renderServiceSelects();
    renderTechSelect();
    renderProblemsGrid();
    renderTracking();
  } catch (err) {
    showToast('error', 'Gagal memuat data: ' + err.message);
  }
}

// ═══════════════════════════════════════════════════════
// RENDER: SERVICES GRID
// ═══════════════════════════════════════════════════════
function renderServicesGrid() {
  const grid = document.getElementById('services-grid');
  if (!services.length) {
    grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;"><p>Belum ada layanan tersedia.</p></div>';
    return;
  }
  grid.innerHTML = services.map(s => `
    <div class="service-card" onclick="selectServiceFromCard('${s.id}')">
      <div>
        <div class="service-card-head">
          <span class="service-cat">${escHtml(s.category)}</span>
          <div style="text-align:right;">
            <span class="service-price">Rp ${Number(s.price).toLocaleString('id-ID')}</span>
            <span class="service-price-label">Biaya Jasa</span>
          </div>
        </div>
        <h4 class="service-name">${escHtml(s.name)}</h4>
        <p class="service-desc">${escHtml(s.description)}</p>
      </div>
      <div class="service-footer">
        <span class="service-duration">⏱ ${escHtml(s.duration)}</span>
        <button class="btn-link">PILIH JASA →</button>
      </div>
    </div>
  `).join('');
}

function selectServiceFromCard(id) {
  document.getElementById('f-service').value = id;
  document.getElementById('w-service').value = id;
  document.getElementById('booking-form-section').scrollIntoView({ behavior: 'smooth' });
  const srv = services.find(s => s.id === id);
  if (srv) showToast('success', `Layanan "${srv.name}" terpilih! Silakan isi formulir di bawah.`);
  updatePricingSummary();
}

// ═══════════════════════════════════════════════════════
// RENDER: SELECT ELEMENTS
// ═══════════════════════════════════════════════════════
function renderServiceSelects() {
  const opts = services.map(s =>
    `<option value="${s.id}">` +
    escHtml(`${s.name} - (Mulai Rp ${Number(s.price).toLocaleString('id-ID')})`) +
    `</option>`
  ).join('');
  document.getElementById('f-service').innerHTML = opts;
  document.getElementById('w-service').innerHTML = services.map(s =>
    `<option value="${s.id}">${escHtml(s.name.substring(0, 22))}...</option>`
  ).join('');
  if (services.length) {
    document.getElementById('f-service').value = services[0].id;
    document.getElementById('w-service').value = services[0].id;
  }
  document.getElementById('f-service').addEventListener('change', updatePricingSummary);
  updatePricingSummary();
}

function renderTechSelect() {
  const opts = technicians.map(t =>
    `<option value="${t.id}">${escHtml(t.name)} (${escHtml(t.specialty)}) [Rating ${t.rating}] - ${t.status === 'available' ? 'Tersedia' : 'Sibuk'}</option>`
  ).join('');
  document.getElementById('f-tech').innerHTML = opts;
  if (technicians.length) document.getElementById('f-tech').value = technicians[0].id;
}

// ═══════════════════════════════════════════════════════
// RENDER: PROBLEMS GRID
// ═══════════════════════════════════════════════════════
function renderProblemsGrid() {
  const grid = document.getElementById('problems-grid');
  grid.innerHTML = PROBLEM_OPTIONS.map(opt => `
    <div class="problem-card ${selectedProblems.includes(opt.id) ? 'selected' : ''}"
         id="prob-${opt.id}" onclick="toggleProblem('${opt.id}')">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
        <div>
          <span class="problem-label">${escHtml(opt.label)}</span>
          <p class="problem-desc">${escHtml(opt.description)}</p>
        </div>
        <input type="checkbox" ${selectedProblems.includes(opt.id) ? 'checked' : ''}
               style="width:16px;height:16px;flex-shrink:0;margin-top:2px;accent-color:var(--yellow);" readonly />
      </div>
      <div style="border-top:1px dashed #d1d5db;padding-top:6px;margin-top:8px;display:flex;justify-content:space-between;">
        <span style="font-size:8px;font-family:'JetBrains Mono',monospace;color:#9ca3af;text-transform:uppercase;font-weight:900;">Tambahan:</span>
        <span class="problem-price">+Rp ${opt.extraPrice.toLocaleString('id-ID')}</span>
      </div>
    </div>
  `).join('');
}

function toggleProblem(id) {
  if (selectedProblems.includes(id)) {
    selectedProblems = selectedProblems.filter(p => p !== id);
  } else {
    selectedProblems.push(id);
  }
  renderProblemsGrid();
  updatePricingSummary();
}

// ═══════════════════════════════════════════════════════
// PRICING SUMMARY
// ═══════════════════════════════════════════════════════
function updatePricingSummary() {
  const srvId = document.getElementById('f-service')?.value;
  const srv   = services.find(s => s.id === srvId);
  const box   = document.getElementById('pricing-summary');
  if (!srv) { box.innerHTML = '<p style="font-size:11px;color:#6b7280;font-weight:700;text-transform:uppercase;text-align:center;">Silakan pilih layanan terlebih dahulu.</p>'; return; }

  const base  = Number(srv.price);
  const added = PROBLEM_OPTIONS.filter(p => selectedProblems.includes(p.id));
  const extra = added.reduce((sum, p) => sum + p.extraPrice, 0);
  const total = base + extra;

  const addedRows = added.length ? added.map(p => `
    <div class="flex-between" style="font-size:10px;color:#374151;font-weight:800;text-transform:uppercase;padding-left:8px;border-left:2px solid var(--yellow);">
      <span style="max-width:160px;overflow:hidden;text-overflow:ellipsis;">${escHtml(p.label)}:</span>
      <span class="text-mono" style="color:#059669;flex-shrink:0;">+Rp ${p.extraPrice.toLocaleString('id-ID')}</span>
    </div>`).join('') : '';

  box.innerHTML = `
    <div>
      <span style="font-size:9px;color:#6b7280;font-weight:900;text-transform:uppercase;font-family:'JetBrains Mono',monospace;">Layanan Terpilih:</span>
      <div style="font-size:13px;font-weight:900;text-transform:uppercase;">${escHtml(srv.name)}</div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;border-top:2px solid #000;border-bottom:2px solid #000;padding:12px 0;margin:12px 0;">
      <div><span style="font-size:9px;color:#6b7280;font-weight:900;text-transform:uppercase;font-family:'JetBrains Mono',monospace;display:block;">Durasi:</span><span style="font-size:12px;font-weight:900;text-transform:uppercase;">${escHtml(srv.duration)}</span></div>
      <div><span style="font-size:9px;color:#6b7280;font-weight:900;text-transform:uppercase;font-family:'JetBrains Mono',monospace;display:block;">Kategori:</span><span style="font-size:12px;font-weight:900;text-transform:uppercase;">${escHtml(srv.category)}</span></div>
    </div>
    <div style="margin-bottom:8px;">
      <div class="flex-between"><span style="font-size:10px;color:#6b7280;font-family:'JetBrains Mono',monospace;text-transform:uppercase;">Biaya Dasar Jasa:</span><span class="text-mono">Rp ${base.toLocaleString('id-ID')}</span></div>
      ${addedRows}
    </div>
    <div class="price-total">
      <span class="price-total-label">Total Estimasi Jasa:</span>
      <span class="price-total-val">Rp ${total.toLocaleString('id-ID')}</span>
    </div>
    <p style="font-size:9px;color:#6b7280;text-align:center;font-family:'JetBrains Mono',monospace;text-transform:uppercase;margin-top:8px;line-height:1.4;">*Biaya di atas adalah jasa teknisi panggilan. Belum termasuk harga suku cadang.</p>
  `;
}

// ═══════════════════════════════════════════════════════
// FILE UPLOAD
// ═══════════════════════════════════════════════════════
function handleFileChange(e) {
  const file = e.target.files?.[0];
  if (file) processFile(file);
}
function handleDragOver(e) { e.preventDefault(); document.getElementById('upload-zone').classList.add('dragging'); }
function handleDragLeave()  { document.getElementById('upload-zone').classList.remove('dragging'); }
function handleDrop(e) {
  e.preventDefault();
  document.getElementById('upload-zone').classList.remove('dragging');
  const file = e.dataTransfer.files?.[0];
  if (file) processFile(file);
}
function processFile(file) {
  if (file.size > 8 * 1024 * 1024) { showToast('error', 'Ukuran foto maksimal 8 MB.'); return; }
  const reader = new FileReader();
  reader.onloadend = () => {
    uploadedBase64 = reader.result;
    document.getElementById('upload-placeholder').classList.add('hidden');
    document.getElementById('upload-preview').classList.remove('hidden');
    document.getElementById('preview-img').src = uploadedBase64;
    showToast('success', 'Foto bukti kendala berhasil diunggah!');
  };
  reader.readAsDataURL(file);
}
function removePhoto(e) {
  e.stopPropagation();
  uploadedBase64 = '';
  document.getElementById('upload-placeholder').classList.remove('hidden');
  document.getElementById('upload-preview').classList.add('hidden');
  document.getElementById('preview-img').src = '';
  document.getElementById('file-input').value = '';
}

// ═══════════════════════════════════════════════════════
// CREATE BOOKING
// ═══════════════════════════════════════════════════════
async function handleCreateBooking(e) {
  e.preventDefault();
  const btn = document.getElementById('submit-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan...';

  const srvId   = document.getElementById('f-service').value;
  const srv     = services.find(s => s.id === srvId);
  const base    = srv ? Number(srv.price) : 150000;
  const extra   = selectedProblems.reduce((sum, id) => {
    const opt = PROBLEM_OPTIONS.find(p => p.id === id);
    return sum + (opt ? opt.extraPrice : 0);
  }, 0);
  const total   = base + extra;
  const diff    = selectedProblems.length > 0
    ? selectedProblems.map(id => PROBLEM_OPTIONS.find(p => p.id === id)?.label || id).join(', ')
    : 'Standar';

  const payload = {
    customer: {
      name:    document.getElementById('f-name').value,
      email:   document.getElementById('f-email').value,
      phone:   document.getElementById('f-phone').value,
      address: document.getElementById('f-address').value,
    },
    serviceId:    srvId,
    technicianId: document.getElementById('f-tech').value,
    bookingDate:  document.getElementById('f-date').value || new Date().toISOString().split('T')[0],
    bookingTime:  document.getElementById('f-time').value,
    address:      document.getElementById('f-address').value,
    notes:        document.getElementById('f-notes').value,
    difficulty:   diff,
    finalPrice:   total,
    deviceIssue: {
      deviceName:       document.getElementById('f-device').value || 'Laptop / PC Umum',
      issueType:        document.getElementById('f-issuetype').value || 'Standard',
      issueDescription: document.getElementById('f-issuedesc').value || 'Kendala tidak dispesifikasikan',
      photoUrl:         uploadedBase64 || 'https://images.unsplash.com/photo-1597872200319-382a4d3393a6?auto=format&fit=crop&q=80&w=300',
    },
  };

  try {
    const data = await apiFetch('api/bookings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    customerEmailSession = payload.customer.email;
    document.getElementById('session-email-input').value = customerEmailSession;
    document.getElementById('active-session-display').textContent = customerEmailSession;

    // Reset issue fields only
    document.getElementById('f-device').value = '';
    document.getElementById('f-issuetype').value = '';
    document.getElementById('f-issuedesc').value = '';
    document.getElementById('f-notes').value = '';
    selectedProblems = [];
    uploadedBase64   = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('upload-preview').classList.add('hidden');
    document.getElementById('file-input').value = '';

    showToast('success', `Booking berhasil! ID Tiket: ${data.booking.id}`);
    await fetchAllData();
    document.getElementById('tracking-section').scrollIntoView({ behavior: 'smooth' });
  } catch (err) {
    showToast('error', err.message || 'Gagal terhubung ke server.');
  } finally {
    btn.disabled = false; btn.textContent = 'CONFIRM BOOKING NOW →';
  }
}

function resetForm() {
  if (!confirm('Reset isi seluruh formulir reservasi?')) return;
  document.getElementById('f-device').value = '';
  document.getElementById('f-issuetype').value = '';
  document.getElementById('f-issuedesc').value = '';
  document.getElementById('f-notes').value = '';
  selectedProblems = []; uploadedBase64 = '';
  document.getElementById('upload-placeholder').classList.remove('hidden');
  document.getElementById('upload-preview').classList.add('hidden');
  renderProblemsGrid(); updatePricingSummary();
}

// ═══════════════════════════════════════════════════════
// SESSION
// ═══════════════════════════════════════════════════════
function applySession() {
  const val = document.getElementById('session-email-input').value.trim();
  customerEmailSession = val;
  document.getElementById('active-session-display').textContent = val || '(Belum diset)';
  showToast('success', `Pelacakan diubah ke email: ${val}`);
  renderTracking();
}

// sync form email → session email
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('f-email')?.addEventListener('input', e => {
    document.getElementById('session-email-input').value = e.target.value;
  });
});

// ═══════════════════════════════════════════════════════
// STATUS HELPERS
// ═══════════════════════════════════════════════════════
function getStatusClass(s) {
  const map = { pending:'status-pending', confirmed:'status-confirmed', on_progress:'status-on_progress', fixed:'status-fixed', completed:'status-completed', cancelled:'status-cancelled' };
  return map[s] || 'status-pending';
}
function getStatusLabel(s) {
  const map = { pending:'Menunggu Konfirmasi', confirmed:'Telah Dikonfirmasi', on_progress:'Sedang Diperbaiki', fixed:'Selesai Perbaikan (Uji Coba)', completed:'Selesai & Pembayaran', cancelled:'Dibatalkan' };
  return map[s] || s;
}

// ═══════════════════════════════════════════════════════
// RENDER: TRACKING SECTION
// ═══════════════════════════════════════════════════════
function renderTracking() {
  const filtered = bookings.filter(b => b.customer?.email?.toLowerCase() === customerEmailSession.toLowerCase());
  const container = document.getElementById('tracking-content');

  if (!filtered.length) {
    container.innerHTML = `
      <div class="empty-state">
        <p>📋 Tidak ada tiket booking aktif didaftarkan.</p>
        <p class="sub">Tidak ada tiket terhubung dengan email <strong>${escHtml(customerEmailSession)}</strong>.</p>
        <a href="#booking-form-section" class="empty-cta">Buat Jadwal Booking Servis Baru →</a>
      </div>`;
    return;
  }

  const ticketListHtml = filtered.map(d => {
    const isFocused = focusedTicket?.booking?.id === d.booking.id;
    return `
      <div class="ticket-card ${isFocused ? 'active' : ''}" onclick="setFocusedTicket('${d.booking.id}')">
        <div class="flex-between" style="margin-bottom:8px;">
          <div>
            <div class="ticket-id">ID: ${escHtml(d.booking.id)}</div>
            <div class="ticket-device">${escHtml(d.deviceIssue?.deviceName || 'Laptop/Komputer')}</div>
          </div>
          <span class="badge-status ${getStatusClass(d.booking.status)}">${getStatusLabel(d.booking.status)}</span>
        </div>
        <div class="ticket-divider">
          <div class="flex-between"><span class="text-gray">Jasa Servis:</span><span style="font-weight:900;">${escHtml((d.service?.name || '').substring(0,28))}...</span></div>
          <div style="margin:6px 0 4px;"><span style="font-size:9px;color:#6b7280;text-transform:uppercase;">Gejala:</span><span class="difficulty-badge" style="display:block;margin-top:2px;">${escHtml(d.booking.difficulty || 'Standar')}</span></div>
          <div class="flex-between"><span class="text-gray">Estimasi:</span><span class="text-mono text-blue">Rp ${Number(d.booking.finalPrice || d.service?.price || 0).toLocaleString('id-ID')}</span></div>
          <div class="flex-between"><span class="text-gray">Jadwal:</span><span style="font-weight:900;">${escHtml(d.booking.bookingDate)} Pukul ${escHtml(d.booking.bookingTime)} WIB</span></div>
        </div>
        <div class="flex-between" style="font-size:10px;font-family:'JetBrains Mono',monospace;">
          <span style="font-weight:900;text-transform:uppercase;">👤 ${escHtml(d.technician?.name || 'BELUM DITUGASKAN')}</span>
          <span class="text-blue" style="font-weight:900;text-transform:uppercase;text-decoration:underline;">TINJAU →</span>
        </div>
      </div>`;
  }).join('');

  const detailHtml = focusedTicket ? renderTicketDetail(focusedTicket) : `
    <div class="empty-state" style="border-style:dashed;height:100%;justify-content:center;">
      <p>👁 Detail Laporan Pemesanan</p>
      <p class="sub">Ketuk salah satu tiket untuk melihat data relasi lengkap dari 5 tabel database.</p>
    </div>`;

  container.innerHTML = `
    <div class="tickets-grid">
      <div>
        <span style="font-size:10px;font-weight:900;text-transform:uppercase;font-family:'JetBrains Mono',monospace;display:block;margin-bottom:12px;">PILIH TIKET UNTUK DETAIL LENGKAP (${filtered.length})</span>
        ${ticketListHtml}
      </div>
      <div>${detailHtml}</div>
    </div>`;
}

function setFocusedTicket(id) {
  focusedTicket = bookings.find(b => b.booking.id === id) || null;
  renderTracking();
}

function renderTicketDetail(d) {
  const tech = d.technician;
  const issue = d.deviceIssue;

  const canCancel = !['cancelled','completed'].includes(d.booking.status);

  return `
    <div class="detail-panel">
      <div class="flex-between" style="flex-wrap:wrap;border-bottom:2px solid #000;padding-bottom:16px;margin-bottom:16px;gap:8px;">
        <div>
          <span style="font-size:9px;color:var(--blue);font-weight:900;text-transform:uppercase;background:#eff6ff;border:1px solid #000;padding:2px 8px;box-shadow:1px 1px 0 0 #000;">Tiket Servis Terbit</span>
          <h4 style="font-size:16px;font-weight:900;text-transform:uppercase;margin-top:6px;">No Tiket: ${escHtml(d.booking.id)}</h4>
        </div>
        <div class="detail-actions">
          <button class="btn-reschedule" onclick="openRescheduleModal()">Ubah Jadwal</button>
          ${canCancel ? `<button class="btn-cancel" onclick="handleCancelBooking('${escHtml(d.booking.id)}')">Batalkan</button>` : ''}
        </div>
      </div>

      <div class="detail-grid">
        <div class="detail-box">
          <h5>👤 Data Customer &amp; Alat</h5>
          <div class="detail-row"><span class="lbl">Nama Pemilik:</span><span class="val">${escHtml(d.customer?.name || '')}</span></div>
          <div class="detail-row"><span class="lbl">Kontak WA &amp; Email:</span><span class="val">${escHtml(d.customer?.phone || '')} / ${escHtml(d.customer?.email || '')}</span></div>
          <div class="detail-row"><span class="lbl">Model Laptop:</span><span class="val val-blue">${escHtml(issue?.deviceName || 'ITPLN PC')}</span></div>
          <div class="detail-row"><span class="lbl">Alamat Panggilan:</span><span class="val">${escHtml(d.booking.address || '')}</span></div>
        </div>
        <div class="detail-box">
          <h5>🔧 Pilihan Layanan Servis</h5>
          <div class="detail-row"><span class="lbl">Paket Standard:</span><span class="val">${escHtml(d.service?.name || '')}</span></div>
          <div class="detail-row"><span class="lbl">Durasi Kerja:</span><span class="val">${escHtml(d.service?.duration || '')}</span></div>
          <div class="detail-row"><span class="lbl">Gejala / Masalah:</span><span class="difficulty-badge">${escHtml(d.booking.difficulty || 'Standar')}</span></div>
          <div class="detail-row"><span class="lbl">Estimasi Ongkos:</span><span class="val val-blue">Rp ${Number(d.booking.finalPrice || d.service?.price || 0).toLocaleString('id-ID')}</span></div>
          <div class="detail-row"><span class="lbl">Status Reservasi:</span><span class="badge-status ${getStatusClass(d.booking.status)}">${getStatusLabel(d.booking.status)}</span></div>
        </div>
      </div>

      ${issue ? `
      <div class="detail-box" style="margin-bottom:16px;">
        <h5>📱 Kendala Perangkat &amp; Bukti Foto (device_issues)</h5>
        <span style="font-size:9px;color:#6b7280;font-family:'JetBrains Mono',monospace;text-transform:uppercase;font-weight:700;">Deskripsi:</span>
        <p style="background:#fff;border:2px solid #000;padding:12px;font-size:11px;font-weight:700;text-transform:uppercase;font-style:italic;line-height:1.6;margin:4px 0 12px;">
          "${escHtml(issue.issueDescription || d.booking.notes || '-')}"
        </p>
        ${issue.photoUrl ? `
        <span style="font-size:10px;font-weight:900;text-transform:uppercase;font-family:'JetBrains Mono',monospace;display:block;margin-bottom:6px;">Foto Layar Error / Bukti:</span>
        <div class="issue-photo-wrap">
          <img src="${escHtml(issue.photoUrl)}" alt="Bukti kerusakan" />
          <div class="issue-photo-overlay"><a href="${escHtml(issue.photoUrl)}" target="_blank" class="btn-view-photo">👁 Buka Foto Penuh</a></div>
        </div>` : ''}
      </div>` : ''}

      <div class="tech-card" style="margin-bottom:16px;">
        <h5 style="font-size:10px;font-weight:900;text-transform:uppercase;border-bottom:1px solid #000;padding-bottom:6px;margin-bottom:12px;display:flex;align-items:center;gap:6px;">✅ Profil Teknisi Penanggung Jawab</h5>
        ${tech ? `
        <div class="tech-card-inner">
          <div style="display:flex;align-items:center;gap:12px;">
            <img src="${escHtml(tech.avatar || '')}" alt="${escHtml(tech.name)}" class="tech-avatar" />
            <div>
              <div class="tech-name">${escHtml(tech.name)}</div>
              <div class="tech-spec">${escHtml(tech.specialty)}</div>
              <div class="tech-rating">⭐ ${tech.rating} Rating Standard</div>
            </div>
          </div>
          <div style="text-align:right;">
            <span style="font-size:8px;font-family:'JetBrains Mono',monospace;font-weight:900;text-transform:uppercase;display:block;">Kontak Teknisi:</span>
            <a href="tel:${escHtml(tech.phone)}" class="btn-call">📞 ${escHtml(tech.phone)}</a>
          </div>
        </div>` : '<p style="font-size:11px;font-weight:700;text-transform:uppercase;">Teknisi sedang dijadwalkan oleh admin kampus ITPLN. Silakan tunggu.</p>'}
      </div>

      <div style="border-top:2px dashed #000;padding-top:12px;display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px;font-size:9px;color:#6b7280;font-family:'JetBrains Mono',monospace;text-transform:uppercase;font-weight:700;">
        <span>Metode Pembayaran: Onsite Tunai / Transfer setelah selesai servis</span>
        <span>Dibuat pada: ${new Date(d.booking.createdAt).toLocaleString('id-ID')}</span>
      </div>
    </div>`;
}

// ═══════════════════════════════════════════════════════
// CANCEL BOOKING
// ═══════════════════════════════════════════════════════
async function handleCancelBooking(id) {
  if (!confirm('Apakah Anda yakin ingin membatalkan jadwal booking servis ini?')) return;
  try {
    await apiFetch(`api/booking_action.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
    showToast('success', 'Booking berhasil dibatalkan.');
    focusedTicket = null;
    await fetchAllData();
  } catch (err) {
    showToast('error', err.message || 'Proses pembatalan gagal.');
  }
}

// ═══════════════════════════════════════════════════════
// RESCHEDULE MODAL
// ═══════════════════════════════════════════════════════
function openRescheduleModal() {
  if (!focusedTicket) return;
  document.getElementById('modal-booking-id').textContent = focusedTicket.booking.id;
  document.getElementById('modal-date').value  = focusedTicket.booking.bookingDate || '';
  document.getElementById('modal-time').value  = focusedTicket.booking.bookingTime || '10:00';
  document.getElementById('modal-notes').value = focusedTicket.booking.notes || '';
  document.getElementById('reschedule-modal').classList.remove('hidden');
}
function closeRescheduleModal() {
  document.getElementById('reschedule-modal').classList.add('hidden');
}

async function handleRescheduleSubmit() {
  if (!focusedTicket) return;
  const btn = document.getElementById('modal-save-btn');
  btn.disabled = true; btn.textContent = 'Menyimpan...';
  try {
    await apiFetch(`api/booking_action.php?id=${encodeURIComponent(focusedTicket.booking.id)}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        bookingDate: document.getElementById('modal-date').value,
        bookingTime: document.getElementById('modal-time').value,
        notes:       document.getElementById('modal-notes').value,
      }),
    });
    showToast('success', 'Detail pemesanan berhasil diperbarui!');
    closeRescheduleModal();
    await fetchAllData();
    // Refocus updated ticket
    focusedTicket = bookings.find(b => b.booking.id === focusedTicket.booking.id) || null;
    renderTracking();
  } catch (err) {
    showToast('error', err.message || 'Gagal mengubah jadwal.');
  } finally {
    btn.disabled = false; btn.textContent = 'Simpan Jadwal Baru';
  }
}

// ═══════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ═══════════════════════════════════════════════════════
// BOOT
// ═══════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  // Set default date to today
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('f-date').value = today;
  document.getElementById('w-date').value = today;

  // Render problems grid immediately (no data needed)
  renderProblemsGrid();

  // Fetch all API data
  fetchAllData();
});