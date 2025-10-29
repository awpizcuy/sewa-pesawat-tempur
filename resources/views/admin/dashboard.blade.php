@extends('layouts.app')

@section('content')
<script>requireAuth();(async()=>{ if(!(await isAdmin())) location.href='/' })();</script>
<div class="card">
    <h2>Rekapan Data Peminjaman</h2>
    <div class="muted" style="margin-top:8px">Kelola semua data peminjaman pesawat tempur</div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px">
        <a class="btn" href="/admin/units">Kelola Unit</a>
        <a class="btn" href="/admin/categories">Kelola Kategori</a>
        <a class="btn" href="/admin/users">Kelola User</a>
        <a class="btn" href="/admin/rentals">Kelola Peminjaman</a>
    </div>
</div>

<table class="table" id="tbl" style="margin-top:12px">
    <thead>
        <tr>
            <th>User</th>
            <th>Unit</th>
            <th>Sewa</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
async function load(){
    const items = await api('/admin/rentals');
    const tb = document.querySelector('#tbl tbody');
    
    if(items.length === 0){
        tb.innerHTML = '<tr><td colspan="6" style="text-align:center" class="muted">Tidak ada data peminjaman</td></tr>';
        return;
    }
    
    tb.innerHTML = items.map(r=>{
        const user = r.user||{}; 
        const unit = r.unit||{};
        const rentDate = r.rent_date ? new Date(r.rent_date).toLocaleString('id-ID') : '-';
        const dueDate = r.due_date ? new Date(r.due_date).toLocaleString('id-ID') : '-';
        
        let statusBadge = '';
        if(r.status === 'rented'){
            statusBadge = '<span style="color:#2563eb">Dalam peminjaman</span>';
        } else if(r.status === 'overdue'){
            statusBadge = '<span style="color:#dc2626">Terlambat</span>';
        } else if(r.status === 'returned'){
            statusBadge = '<span style="color:#16a34a">Selesai</span>';
        }
        
        return `
            <tr>
                <td>${user.name||'-'}<br><span class="muted" style="font-size:12px">${user.email||''}</span></td>
                <td>${unit.name||'-'}<br><span class="muted" style="font-size:12px">${unit.unit_code||''}</span></td>
                <td>${rentDate}</td>
                <td>${dueDate}</td>
                <td>${statusBadge}</td>
                <td>
                    <select data-status="${r.id}" class="input" style="max-width:160px;margin-bottom:4px">
                        <option value="rented" ${r.status==='rented'?'selected':''}>Dalam peminjaman</option>
                        <option value="overdue" ${r.status==='overdue'?'selected':''}>Terlambat</option>
                        <option value="returned" ${r.status==='returned'?'selected':''}>Selesai</option>
                    </select>
                    <button class="btn" data-save="${r.id}" style="margin-right:4px">Simpan Status</button>
                    ${r.status==='rented'||r.status==='overdue' ? `<button class="btn secondary" data-return="${r.id}" style="margin-right:4px">Kembalikan</button>` : ''}
                    <button class="btn" data-chat="${r.id}" style="background:#10b981">💬 Chat</button>
                </td>
            </tr>
        `
    }).join('');
    
    // Event listener untuk simpan status
    tb.querySelectorAll('[data-save]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-save');
        const status = tb.querySelector(`[data-status="${id}"]`).value;
        try {
            await api(`/admin/rentals/${id}/status`, { method:'PATCH', body: JSON.stringify({ status })});
            load();
        } catch(err) {
            alert(err.message || 'Gagal mengupdate status');
        }
    }));
    
    // Event listener untuk kembalikan
    tb.querySelectorAll('[data-return]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-return');
        if(!confirm('Proses pengembalian unit ini?')) return;
        try {
            await api(`/admin/rentals/${id}/return`, { method:'POST' });
            load();
        } catch(err) {
            alert(err.message || 'Gagal memproses pengembalian');
        }
    }));

    // Event listener untuk chat
    tb.querySelectorAll('[data-chat]').forEach(b=> b.addEventListener('click', ()=>{
        const id = b.getAttribute('data-chat');
        openAdminChat(id);
    }));
}

// Fungsi untuk buka chat admin
function openAdminChat(rentalId) {
    const chatModal = document.getElementById('adminChatModal');
    if(!chatModal) {
        createAdminChatModal();
    }
    document.getElementById('adminChatModal').style.display = 'block';
    document.getElementById('adminCurrentRentalId').value = rentalId;
    loadAdminChatMessages(rentalId);
}

// Fungsi untuk buat modal chat admin
function createAdminChatModal() {
    const modal = document.createElement('div');
    modal.id = 'adminChatModal';
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.5); z-index: 1000; display: none;
    `;
    modal.innerHTML = `
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                    background: white; border-radius: 8px; width: 500px; max-width: 90vw; height: 600px; 
                    display: flex; flex-direction: column;">
            <div style="padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3>💬 Chat dengan User</h3>
                <button onclick="closeAdminChat()" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
            </div>
            <div id="adminChatMessages" style="flex: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                <!-- Messages will be loaded here -->
            </div>
            <div style="padding: 16px; border-top: 1px solid #eee; display: flex; gap: 8px;">
                <input type="hidden" id="adminCurrentRentalId">
                <input type="text" id="adminMessageInput" placeholder="Ketik pesan untuk user..." 
                       style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <button onclick="sendAdminMessage()" class="btn">Kirim</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Fungsi untuk load chat messages admin
async function loadAdminChatMessages(rentalId) {
    const container = document.getElementById('adminChatMessages');
    container.innerHTML = '<p class="muted">Loading messages...</p>';
    try {
        console.log('Loading chat for rental ID:', rentalId);
        console.log('Current token:', getToken());
        const response = await api(`/admin/chat/${rentalId}/messages`);
        console.log('Chat response:', response);
        
        const { rental, messages } = response;
        
        // Tampilkan info rental
        container.innerHTML = `
            <div style="background: #f8f9fa; padding: 12px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #2563eb;">
                <div style="font-weight: bold; color: #2563eb;">Chat dengan: ${rental.user.name}</div>
                <div style="font-size: 12px; color: #666; margin-top: 4px;">
                    Unit: ${rental.unit.name} | Status: ${rental.status}
                </div>
            </div>
        `;
        
        // Tampilkan messages
        if (messages && messages.length > 0) {
            container.innerHTML += messages.map(msg => `
                <div style="display: flex; ${msg.sender === 'admin' ? 'justify-content: flex-end' : 'justify-content: flex-start'}">
                    <div style="max-width: 70%; padding: 8px 12px; border-radius: 12px; 
                                background: ${msg.sender === 'admin' ? '#2563eb' : '#f1f5f9'}; 
                                color: ${msg.sender === 'admin' ? 'white' : 'black'};">
                        <div style="font-size: 12px; opacity: 0.7; margin-bottom: 4px;">
                            ${msg.sender === 'admin' ? 'Admin' : 'User'}
                        </div>
                        <div>${msg.message}</div>
                        <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">
                            ${new Date(msg.created_at).toLocaleString('id-ID')}
                        </div>
                    </div>
                </div>
            `).join('');
        } else {
            container.innerHTML += '<p class="muted" style="text-align: center; margin-top: 20px;">Belum ada pesan</p>';
        }
        
        container.scrollTop = container.scrollHeight;
    } catch(err) {
        console.error('Chat error:', err);
        container.innerHTML = `<p class="error">Gagal memuat pesan: ${err.message || 'Unknown error'}</p>`;
    }
}

// Fungsi untuk kirim pesan admin
async function sendAdminMessage() {
    const input = document.getElementById('adminMessageInput');
    const rentalId = document.getElementById('adminCurrentRentalId').value;
    const message = input.value.trim();
    if(!message) return;
    
    try {
        await api(`/admin/chat/${rentalId}/send`, {
            method: 'POST',
            body: JSON.stringify({ message })
        });
        input.value = '';
        loadAdminChatMessages(rentalId);
    } catch(err) {
        alert(err.message || 'Gagal mengirim pesan');
    }
}

// Fungsi untuk tutup chat admin
function closeAdminChat() {
    document.getElementById('adminChatModal').style.display = 'none';
}

// Event listener untuk Enter key admin
document.addEventListener('keypress', function(e) {
    if(e.key === 'Enter' && e.target.id === 'adminMessageInput') {
        sendAdminMessage();
    }
});

load();
</script>
@endsection

