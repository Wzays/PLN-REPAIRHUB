// ═══════════════════════════════════════════════════════
// ADMIN STATE & HELPERS
// ═══════════════════════════════════════════════════════
let adminData = {
  bookings: [],
  technicians: [],
  services: []
};

let toastTimer;
function showToast(type, msg) {
  const el  = document.getElementById('toast');
  const txt = document.getElementById('toast-msg');
  el.className = `show ${type}`;
  txt.textContent = msg;
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.className = '', 5000);
}

function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

async function adminFetch(url, options = {}) {
  const res = await fetch(url, options);
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.error || `HTTP ${res.status}`);
  }
  return res.json();
}

// ═══════════════════════════════════════════════════════
// TABS & INIT
// ═══════════════════════════════════════════════════════
function switchTab(tabId, el) {
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.add('hidden'));
  document.getElementById(`tab-${tabId}`).classList.remove('hidden');
  document.querySelectorAll('.admin-menu a').forEach(a => a.classList.remove('active'));
  if (el) el.classList.add('active');
  
  if (tabId === 'bookings') loadBookings();
  if (tabId === 'technicians') loadTechnicians();
  if (tabId === 'services') loadServices();
}

document.addEventListener('DOMContentLoaded', () => {
  loadBookings();
  loadTechnicians(true); // Load tech list for booking assignments
});

// ═══════════════════════════════════════════════════════
// MODAL HELPERS
// ═══════════════════════════════════════════════════════
function closeModal(id) {
  document.getElementById(id).classList.add('hidden');
}

// ═══════════════════════════════════════════════════════
// SERVICES CRUD
// ═══════════════════════════════════════════════════════
async function loadServices() {
  try {
    const data = await adminFetch('api/admin_services.php');
    adminData.services = data;
    const tbody = document.getElementById('admin-tbody-services');
    tbody.innerHTML = data.map(s => `
      <tr>
        <td><strong>${escHtml(s.name)}</strong><br><small style="color:#666">${escHtml(s.description)}</small></td>
        <td>${escHtml(s.category)}</td>
        <td>${escHtml(s.duration)}</td>
        <td class="text-mono">Rp ${Number(s.price).toLocaleString('id-ID')}</td>
        <td>
          <button class="btn-admin" onclick="editService('${s.id}')">Edit</button>
          <button class="btn-admin btn-admin-danger" onclick="deleteService('${s.id}')">Hapus</button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    showToast('error', 'Gagal memuat layanan: ' + err.message);
  }
}

function openServiceModal(id = null) {
  const modal = document.getElementById('modal-service');
  if (id) {
    const s = adminData.services.find(x => x.id === id);
    document.getElementById('modal-service-title').textContent = 'Edit Layanan';
    document.getElementById('service-id').value = s.id;
    document.getElementById('service-name').value = s.name;
    document.getElementById('service-category').value = s.category;
    document.getElementById('service-duration').value = s.duration;
    document.getElementById('service-price').value = s.price;
    document.getElementById('service-description').value = s.description;
  } else {
    document.getElementById('modal-service-title').textContent = 'Tambah Layanan Baru';
    document.getElementById('service-id').value = '';
    document.getElementById('service-name').value = '';
    document.getElementById('service-category').value = 'HARDWARE';
    document.getElementById('service-duration').value = '';
    document.getElementById('service-price').value = '';
    document.getElementById('service-description').value = '';
  }
  modal.classList.remove('hidden');
}

function editService(id) { openServiceModal(id); }

async function saveService() {
  const id = document.getElementById('service-id').value;
  const payload = {
    name: document.getElementById('service-name').value,
    category: document.getElementById('service-category').value,
    duration: document.getElementById('service-duration').value,
    price: document.getElementById('service-price').value,
    description: document.getElementById('service-description').value,
  };
  
  try {
    if (id) {
      await adminFetch(`api/admin_services.php?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
      showToast('success', 'Layanan berhasil diperbarui.');
    } else {
      await adminFetch(`api/admin_services.php`, { method: 'POST', body: JSON.stringify(payload) });
      showToast('success', 'Layanan baru berhasil ditambahkan.');
    }
    closeModal('modal-service');
    loadServices();
  } catch(e) { showToast('error', e.message); }
}

async function deleteService(id) {
  if (!confirm('Hapus layanan ini permanen?')) return;
  try {
    await adminFetch(`api/admin_services.php?id=${id}`, { method: 'DELETE' });
    showToast('success', 'Layanan dihapus.');
    loadServices();
  } catch(e) { showToast('error', e.message); }
}

// ═══════════════════════════════════════════════════════
// TECHNICIANS CRUD
// ═══════════════════════════════════════════════════════
async function loadTechnicians(silent = false) {
  try {
    const data = await adminFetch('api/admin_technicians.php');
    adminData.technicians = data;
    
    // Update select options for Booking edit
    document.getElementById('edit-booking-tech').innerHTML = `
      <option value="">-- Belum Ditugaskan --</option>
      ${data.map(t => `<option value="${t.id}">${escHtml(t.name)}</option>`).join('')}
    `;

    if(silent) return;

    const tbody = document.getElementById('admin-tbody-technicians');
    tbody.innerHTML = data.map(t => `
      <tr>
        <td><strong>${escHtml(t.name)}</strong></td>
        <td>${escHtml(t.specialty)}</td>
        <td class="text-mono">${escHtml(t.phone)}</td>
        <td>
          <span style="padding:2px 6px; font-size:10px; text-transform:uppercase; border:1px solid #000; background: ${t.status === 'available' ? '#dcfce7' : '#fee2e2'}">
            ${t.status}
          </span>
        </td>
        <td>${t.rating}</td>
        <td>
          <button class="btn-admin" onclick="editTechnician('${t.id}')">Edit</button>
          <button class="btn-admin btn-admin-danger" onclick="deleteTechnician('${t.id}')">Hapus</button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    if(!silent) showToast('error', 'Gagal memuat teknisi: ' + err.message);
  }
}

function openTechModal(id = null) {
  const modal = document.getElementById('modal-tech');
  if (id) {
    const t = adminData.technicians.find(x => x.id === id);
    document.getElementById('modal-tech-title').textContent = 'Edit Teknisi';
    document.getElementById('tech-id').value = t.id;
    document.getElementById('tech-name').value = t.name;
    document.getElementById('tech-specialty').value = t.specialty;
    document.getElementById('tech-phone').value = t.phone;
    document.getElementById('tech-status').value = t.status;
  } else {
    document.getElementById('modal-tech-title').textContent = 'Tambah Teknisi Baru';
    document.getElementById('tech-id').value = '';
    document.getElementById('tech-name').value = '';
    document.getElementById('tech-specialty').value = '';
    document.getElementById('tech-phone').value = '';
    document.getElementById('tech-status').value = 'available';
  }
  modal.classList.remove('hidden');
}

function editTechnician(id) { openTechModal(id); }

async function saveTechnician() {
  const id = document.getElementById('tech-id').value;
  const payload = {
    name: document.getElementById('tech-name').value,
    specialty: document.getElementById('tech-specialty').value,
    phone: document.getElementById('tech-phone').value,
    status: document.getElementById('tech-status').value,
    rating: 5.0,
    avatar: 'https://ui-avatars.com/api/?name=' + encodeURIComponent(document.getElementById('tech-name').value)
  };
  
  try {
    if (id) {
      await adminFetch(`api/admin_technicians.php?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
      showToast('success', 'Teknisi diperbarui.');
    } else {
      await adminFetch(`api/admin_technicians.php`, { method: 'POST', body: JSON.stringify(payload) });
      showToast('success', 'Teknisi baru ditambahkan.');
    }
    closeModal('modal-tech');
    loadTechnicians();
  } catch(e) { showToast('error', e.message); }
}

async function deleteTechnician(id) {
  if (!confirm('Hapus teknisi ini permanen?')) return;
  try {
    await adminFetch(`api/admin_technicians.php?id=${id}`, { method: 'DELETE' });
    showToast('success', 'Teknisi dihapus.');
    loadTechnicians();
  } catch(e) { showToast('error', e.message); }
}

// ═══════════════════════════════════════════════════════
// BOOKINGS CRUD
// ═══════════════════════════════════════════════════════
async function loadBookings() {
  try {
    const data = await adminFetch('api/admin_bookings.php');
    adminData.bookings = data;
    const tbody = document.getElementById('admin-tbody-bookings');
    
    tbody.innerHTML = data.map(b => `
      <tr>
        <td class="text-mono" style="font-size:10px;">${b.booking.id}</td>
        <td><strong>${escHtml(b.customer?.name)}</strong><br>${escHtml(b.customer?.phone)}</td>
        <td>${escHtml(b.service?.name)}<br><small>Rp ${b.booking.finalPrice.toLocaleString('id-ID')}</small></td>
        <td>${b.booking.bookingDate} <br> ${b.booking.bookingTime}</td>
        <td>
          <span style="padding:2px 6px; font-size:9px; text-transform:uppercase; border:1px solid #000; font-weight:900;">
            ${b.booking.status}
          </span>
        </td>
        <td>${b.technician ? escHtml(b.technician.name) : '<i style="color:#ef4444">Belum ada</i>'}</td>
        <td>
          <button class="btn-admin" onclick="editBookingStatus('${b.booking.id}')">Atur</button>
          <button class="btn-admin btn-admin-danger" onclick="deleteBooking('${b.booking.id}')">Hapus</button>
        </td>
      </tr>
    `).join('');
  } catch (err) {
    showToast('error', 'Gagal memuat pesanan: ' + err.message);
  }
}

function editBookingStatus(id) {
  const b = adminData.bookings.find(x => x.booking.id === id);
  if(!b) return;
  document.getElementById('edit-booking-id').value = b.booking.id;
  document.getElementById('edit-booking-status').value = b.booking.status;
  document.getElementById('edit-booking-tech').value = b.technician ? b.technician.id : '';
  document.getElementById('modal-edit-booking').classList.remove('hidden');
}

async function saveBookingStatus() {
  const id = document.getElementById('edit-booking-id').value;
  const payload = {
    status: document.getElementById('edit-booking-status').value,
    technicianId: document.getElementById('edit-booking-tech').value || null
  };
  
  try {
    await adminFetch(`api/admin_bookings.php?id=${id}`, { method: 'PUT', body: JSON.stringify(payload) });
    showToast('success', 'Status pesanan berhasil diupdate.');
    closeModal('modal-edit-booking');
    loadBookings();
  } catch(e) { showToast('error', e.message); }
}

async function deleteBooking(id) {
  if (!confirm('Hapus tiket pesanan ini secara permanen dari database?')) return;
  try {
    await adminFetch(`api/admin_bookings.php?id=${id}`, { method: 'DELETE' });
    showToast('success', 'Pesanan dihapus.');
    loadBookings();
  } catch(e) { showToast('error', e.message); }
}
