@extends('layouts.app')

@section('content')
<script>requireAuth();</script>
<div class="card">
    <h2>Pinjaman Saya</h2>
    <div class="muted">Menampilkan semua status: dalam peminjaman, terlambat, selesai</div>
</div>

<div id="list"></div>

<script>
async function loadMyRentals(){
    const container = document.getElementById('list');
    container.innerHTML = '<p class="muted">Loading...</p>';
    const items = await api('/my-rentals');
    if(!items.length){ container.innerHTML = '<p class="muted">Belum ada pinjaman.</p>'; return }
    container.innerHTML = items.map(r=>{
        const unit = r.unit || {};
        const rent = r.rent_date ? new Date(r.rent_date).toLocaleString('id-ID') : '-';
        const due = r.due_date ? new Date(r.due_date).toLocaleString('id-ID') : '-';
        const ret = r.return_date ? new Date(r.return_date).toLocaleString('id-ID') : null;
        const today = new Date();
        let badgeCls = 'muted', badge='Selesai';
        let showFine = false;
        
        if(r.status==='rented'){
            const dueDate=new Date(r.due_date);
            if(today.toDateString()===dueDate.toDateString()) { badgeCls=''; badge='Hari Pengembalian'; }
            else if(today<dueDate){ badgeCls='success'; badge='Dalam Peminjaman'; }
            else { 
                badgeCls='danger'; 
                badge='Terlambat';
                showFine = true;
            }
        } else if(r.status==='overdue'){ 
            badgeCls='danger'; 
            badge='Terlambat';
            showFine = true;
        }
        else if(r.status==='returned'){ badgeCls=''; badge='Selesai'; }
        
        const fineAmount = 7500000; // Denda tetap Rp 7.500.000
        const fineDisplay = showFine ? `
            <div style="margin-top:8px;padding:8px;background:#fef2f2;border-left:3px solid #dc2626;border-radius:4px">
                <div style="color:#dc2626;font-weight:bold">⚠️ Denda yang Harus Dibayar</div>
                <div style="color:#dc2626;margin-top:4px">Rp ${Number(fineAmount).toLocaleString('id-ID')}</div>
                <form class="fineForm" data-id="${r.id}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;margin-top:8px">
                    <input class="input" value="${unit.name||'-'} (${unit.unit_code||''})" readonly>
                    <input class="input" value="${Number(fineAmount).toLocaleString('id-ID')}" readonly>
                    <select class="input" name="payment_method" required>
                        <option value="transfer">Transfer</option>
                        <option value="ewallet">eWallet</option>
                        <option value="cash">Cash</option>
                    </select>
                    <button class="btn" type="submit">Konfirmasi Pembayaran</button>
                </form>
            </div>
        ` : '';
        
        const returnedInfo = r.status==='returned' ? `
            <div class="muted">Dikembalikan: ${ret||'-'}</div>
            <div>Denda Dibayar: Rp ${Number(r.fine_amount||0).toLocaleString('id-ID')}</div>
        ` : '';

        const returnButton = (r.status==='rented' || r.status==='overdue') ? `
            <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap">
                <button class="btn" onclick="returnUnit(${r.id})">Kembalikan Unit</button>
                <button class="btn secondary" onclick="openChat(${r.id})">💬 Chat Admin</button>
            </div>
        ` : '';

        return `
            <div class="card">
                <h3>${unit.name||'-'} <span class="muted">(${unit.unit_code||''})</span></h3>
                <div class="muted">Sewa: ${rent} | Jatuh tempo: ${due}</div>
                ${returnedInfo}
                <div>Kode: ${r.booking_code||'-'}</div>
                <div>Pemohon: ${r.borrower_name||'-'} (${r.borrower_identity_number||'-'})</div>
                <div>Metode Bayar: ${r.payment_method||'-'} | Total: Rp ${Number(r.total_amount||0).toLocaleString()}</div>
                <div style="margin-top:6px"><span class="badge ${badgeCls}">${badge}</span></div>
                ${fineDisplay}
                ${returnButton}
            </div>
        `
    }).join('');

    // Bind submit untuk semua form denda
    document.querySelectorAll('.fineForm').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const rentalId = form.getAttribute('data-id');
            const method = form.payment_method.value;
            try{
                await api(`/my-rentals/${rentalId}/pay-fine`, { method:'POST', body: JSON.stringify({ amount: 7500000, payment_method: method })});
                alert('Pembayaran denda dikonfirmasi. Terima kasih.');
                loadMyRentals();
            }catch(err){
                alert(err.message||'Gagal konfirmasi pembayaran denda');
            }
        });
    });
}

// Fungsi untuk return unit
async function returnUnit(rentalId) {
    if(!confirm('Apakah Anda yakin ingin mengembalikan unit ini?')) return;
    try {
        await api(`/my-rentals/${rentalId}/return`, { method: 'POST' });
        alert('Unit berhasil dikembalikan!');
        loadMyRentals();
    } catch(err) {
        alert(err.message || 'Gagal mengembalikan unit');
    }
}

// Fungsi untuk buka chat
function openChat(rentalId) {
    const chatModal = document.getElementById('chatModal');
    if(!chatModal) {
        createChatModal();
    }
    document.getElementById('chatModal').style.display = 'block';
    document.getElementById('currentRentalId').value = rentalId;
    loadChatMessages(rentalId);
}

// Fungsi untuk buat modal chat
function createChatModal() {
    const modal = document.createElement('div');
    modal.id = 'chatModal';
    modal.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
        background: rgba(0,0,0,0.5); z-index: 1000; display: none;
    `;
    modal.innerHTML = `
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                    background: white; border-radius: 8px; width: 400px; max-width: 90vw; height: 500px; 
                    display: flex; flex-direction: column;">
            <div style="padding: 16px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <h3>💬 Chat Admin</h3>
                <button onclick="closeChat()" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
            </div>
            <div id="chatMessages" style="flex: 1; padding: 16px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                <!-- Messages will be loaded here -->
            </div>
            <div style="padding: 16px; border-top: 1px solid #eee; display: flex; gap: 8px;">
                <input type="hidden" id="currentRentalId">
                <input type="text" id="messageInput" placeholder="Ketik pesan..." 
                       style="flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <button onclick="sendMessage()" class="btn">Kirim</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Fungsi untuk load chat messages
async function loadChatMessages(rentalId) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '<p class="muted">Loading messages...</p>';
    try {
        const messages = await api(`/chat/${rentalId}/messages`);
        container.innerHTML = messages.map(msg => `
            <div style="display: flex; ${msg.sender === 'user' ? 'justify-content: flex-end' : 'justify-content: flex-start'}">
                <div style="max-width: 70%; padding: 8px 12px; border-radius: 12px; 
                            background: ${msg.sender === 'user' ? '#2563eb' : '#f1f5f9'}; 
                            color: ${msg.sender === 'user' ? 'white' : 'black'};">
                    <div style="font-size: 12px; opacity: 0.7; margin-bottom: 4px;">
                        ${msg.sender === 'user' ? 'Anda' : 'Admin'}
                    </div>
                    <div>${msg.message}</div>
                    <div style="font-size: 10px; opacity: 0.7; margin-top: 4px;">
                        ${new Date(msg.created_at).toLocaleString('id-ID')}
                    </div>
                </div>
            </div>
        `).join('');
        container.scrollTop = container.scrollHeight;
    } catch(err) {
        container.innerHTML = '<p class="error">Gagal memuat pesan</p>';
    }
}

// Fungsi untuk kirim pesan
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const rentalId = document.getElementById('currentRentalId').value;
    const message = input.value.trim();
    if(!message) return;
    
    try {
        await api(`/chat/${rentalId}/send`, {
            method: 'POST',
            body: JSON.stringify({ message })
        });
        input.value = '';
        loadChatMessages(rentalId);
    } catch(err) {
        alert(err.message || 'Gagal mengirim pesan');
    }
}

// Fungsi untuk tutup chat
function closeChat() {
    document.getElementById('chatModal').style.display = 'none';
}

// Event listener untuk Enter key
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keypress', function(e) {
        if(e.key === 'Enter' && e.target.id === 'messageInput') {
            sendMessage();
        }
    });
});

loadMyRentals();
</script>
<style>
.badge{display:inline-block;padding:2px 8px;border-radius:999px;background:#94a3b8;color:#fff;font-size:12px}
.badge.success{background:#16a34a}
.badge.danger{background:#dc2626}
</style>
@endsection

