@extends('layouts.app')

@section('content')
<script>requireAuth();(async()=>{ if(!(await isAdmin())) location.href='/' })();</script>
<div class="card">
    <h2>Kelola Unit Pesawat</h2>
    <div id="msg" class="error" style="display:none"></div>
    <button class="btn" onclick="showForm()" style="margin-top:8px">+ Tambah Unit Baru</button>
</div>

<!-- Form Tambah/Edit Unit -->
<div class="card" id="unitFormCard" style="display:none;margin-top:12px">
    <h3 id="formTitle">Tambah Unit Baru</h3>
    <form id="unitForm">
        <input type="hidden" id="editId" name="id">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-top:8px">
            <div>
                <label style="display:block;margin-bottom:4px;font-weight:bold">Kode Unit *</label>
                <input class="input" name="unit_code" id="unit_code" placeholder="Contoh: F16-BLOCK50" required>
            </div>
            <div>
                <label style="display:block;margin-bottom:4px;font-weight:bold">Nama Unit *</label>
                <input class="input" name="name" id="name" placeholder="Contoh: F-16 Fighting Falcon" required>
            </div>
            <div>
                <label style="display:block;margin-bottom:4px;font-weight:bold">Harga per Hari *</label>
                <input class="input" name="price_per_day" id="price_per_day" type="number" min="0" step="1000" placeholder="Contoh: 4500000" required>
            </div>
            <div>
                <label style="display:block;margin-bottom:4px;font-weight:bold">Stok *</label>
                <input class="input" name="stock" id="stock" type="number" min="0" placeholder="Contoh: 5" required>
            </div>
            <div>
                <label style="display:block;margin-bottom:4px;font-weight:bold">Status *</label>
                <select class="input" name="status" id="status" required>
                    <option value="available">Tersedia</option>
                    <option value="rented">Disewa</option>
                </select>
            </div>
        </div>
        <div style="margin-top:12px">
            <label style="display:block;margin-bottom:4px;font-weight:bold">Deskripsi</label>
            <textarea class="input" name="description" id="description" rows="3" placeholder="Masukkan deskripsi unit pesawat tempur"></textarea>
        </div>
        <div style="margin-top:12px">
            <label style="display:block;margin-bottom:4px;font-weight:bold">Kategori *</label>
            <div id="categoriesContainer" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px"></div>
            <div class="muted" style="margin-top:4px;font-size:12px">Pilih minimal satu kategori</div>
        </div>
        <div style="display:flex;gap:8px;margin-top:12px">
            <button class="btn" type="submit" id="submitBtn">Simpan</button>
            <button class="btn secondary" type="button" onclick="hideForm()">Batal</button>
        </div>
    </form>
</div>

<table class="table" id="tbl" style="margin-top:12px">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Harga/Hari</th>
            <th>Stok</th>
            <th>Status</th>
            <th>Kategori</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
let categories = [];
let editingId = null;

// Load categories
async function loadCategories(){
    categories = await api('/admin/categories');
    const container = document.getElementById('categoriesContainer');
    container.innerHTML = categories.map(cat=>`
        <label style="display:flex;align-items:center;gap:4px;cursor:pointer">
            <input type="checkbox" name="category_ids" value="${cat.id}" class="category-checkbox">
            <span>${cat.name}</span>
        </label>
    `).join('');
}

// Load units
async function load(){
    const units = await api('/admin/units');
    const tb = document.querySelector('#tbl tbody');
    
    if(units.length === 0){
        tb.innerHTML = '<tr><td colspan="8" style="text-align:center" class="muted">Belum ada unit pesawat</td></tr>';
        return;
    }
    
    tb.innerHTML = units.map(u=>{
        const categoriesList = (u.categories||[]).map(c=>c.name).join(', ') || '-';
        const statusBadge = u.status === 'available' 
            ? '<span style="color:#16a34a;font-weight:bold">Tersedia</span>' 
            : '<span style="color:#dc2626;font-weight:bold">Disewa</span>';
        
        return `
            <tr>
                <td>${u.unit_code||'-'}</td>
                <td>${u.name||'-'}</td>
                <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${u.description||''}">${u.description||'-'}</td>
                <td>Rp ${Number(u.price_per_day||0).toLocaleString('id-ID')}</td>
                <td>${u.stock||0}</td>
                <td>${statusBadge}</td>
                <td style="font-size:12px">${categoriesList}</td>
                <td>
                    <button class="btn" data-edit="${u.id}" style="margin-right:4px">Edit</button>
                    <button class="btn danger" data-del="${u.id}">Hapus</button>
                </td>
            </tr>
        `
    }).join('');
    
    // Event listeners untuk edit
    tb.querySelectorAll('[data-edit]').forEach(b=> b.addEventListener('click', ()=>{
        const id = b.getAttribute('data-edit');
        editUnit(id, units.find(u=>u.id==id));
    }));
    
    // Event listeners untuk hapus
    tb.querySelectorAll('[data-del]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-del');
        if(!confirm('Hapus unit ini? Tindakan ini tidak dapat dibatalkan.')) return;
        try {
            await api(`/admin/units/${id}`, { method:'DELETE' });
            showMsg('Unit berhasil dihapus', 'success');
            load();
        } catch(err) {
            showMsg(err.message || 'Gagal menghapus unit', 'error');
        }
    }));
}

// Show form
function showForm(){
    document.getElementById('unitFormCard').style.display = '';
    document.getElementById('formTitle').textContent = 'Tambah Unit Baru';
    document.getElementById('editId').value = '';
    editingId = null;
    document.getElementById('unitForm').reset();
    document.querySelectorAll('.category-checkbox').forEach(cb => cb.checked = false);
}

// Hide form
function hideForm(){
    document.getElementById('unitFormCard').style.display = 'none';
    document.getElementById('unitForm').reset();
    editingId = null;
}

// Edit unit
function editUnit(id, unit){
    editingId = id;
    document.getElementById('unitFormCard').style.display = '';
    document.getElementById('formTitle').textContent = 'Edit Unit';
    document.getElementById('editId').value = id;
    document.getElementById('unit_code').value = unit.unit_code||'';
    document.getElementById('name').value = unit.name||'';
    document.getElementById('description').value = unit.description||'';
    document.getElementById('price_per_day').value = unit.price_per_day||0;
    document.getElementById('stock').value = unit.stock||0;
    document.getElementById('status').value = unit.status||'available';
    
    // Set categories
    document.querySelectorAll('.category-checkbox').forEach(cb => {
        cb.checked = (unit.categories||[]).some(c => c.id == cb.value);
    });
    
    // Scroll to form
    document.getElementById('unitFormCard').scrollIntoView({ behavior: 'smooth' });
}

// Show message
function showMsg(msg, type){
    const msgEl = document.getElementById('msg');
    msgEl.textContent = msg;
    msgEl.className = type;
    msgEl.style.display = '';
    setTimeout(()=>{ msgEl.style.display = 'none'; }, 3000);
}

// Form submit
document.getElementById('unitForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const f = e.target;
    const msgEl = document.getElementById('msg');
    msgEl.style.display = 'none';
    
    // Get selected categories
    const selectedCategories = Array.from(f.querySelectorAll('.category-checkbox:checked')).map(cb => Number(cb.value));
    
    if(selectedCategories.length === 0){
        showMsg('Pilih minimal satu kategori', 'error');
        return;
    }
    
    const payload = {
        unit_code: f.unit_code.value,
        name: f.name.value,
        description: f.description.value || null,
        stock: Number(f.stock.value),
        price_per_day: Number(f.price_per_day.value),
        status: f.status.value,
        categories: selectedCategories
    };
    
    try {
        if(editingId){
            // Update
            await api(`/admin/units/${editingId}`, { 
                method: 'PUT', 
                body: JSON.stringify(payload)
            });
            showMsg('Unit berhasil diperbarui', 'success');
        } else {
            // Create
            await api('/admin/units', { 
                method: 'POST', 
                body: JSON.stringify(payload)
            });
            showMsg('Unit berhasil ditambahkan', 'success');
        }
        hideForm();
        load();
    } catch(err) {
        showMsg(err.message || 'Terjadi kesalahan', 'error');
    }
});

// Initialize
(async ()=>{
    await loadCategories();
    load();
})();
</script>
@endsection