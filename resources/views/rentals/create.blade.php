@extends('layouts.app')

@section('content')
<script>requireAuth();</script>
<div class="card" style="max-width:740px;margin:0 auto">
    <h2>Form Penyewaan</h2>
    <div id="msg" class="error" style="display:none"></div>
    <div id="ok" class="success" style="display:none"></div>
    <form id="rentForm" style="margin-top:8px">
        <input type="hidden" name="unit_id">
        <label>Identitas Pemohon</label>
        <input class="input" name="borrower_name" placeholder="Nama lengkap" required>
        <input class="input" name="borrower_identity_number" placeholder="No. identitas" required style="margin-top:8px">

        <label style="margin-top:8px">Unit</label>
        <input class="input" name="unit_display" disabled>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;margin-top:8px">
            <div>
                <label>Tanggal Pinjam</label>
                <input class="input" type="date" name="rent_date" required>
            </div>
            <div>
                <label>Tanggal Pengembalian</label>
                <input class="input" type="date" name="due_date" required>
            </div>
            <div>
                <label>Lama Sewa (hari)</label>
                <input class="input" type="number" name="days" min="1" value="1" readonly>
            </div>
        </div>

        <label style="margin-top:8px">Metode Pembayaran</label>
        <select class="input" name="payment_method" required>
            <option value="ewallet">E-Wallet</option>
            <option value="transfer">Transfer</option>
            <option value="va">Virtual Account</option>
        </select>

        <label style="margin-top:8px">Total Bayar (Rp)</label>
        <input class="input" type="number" name="total_amount" min="0" step="1000" required readonly>

        <button class="btn" type="submit" style="margin-top:12px">Bayar & Sewa</button>
    </form>
</div>

<script>
function parsePriceFromText(text){
    if(!text) return 0;
    // Ambil angka terbesar dari string, mengabaikan titik/koma pemisah
    const nums = (text.match(/[0-9][0-9\.\, ]+/g) || [])
        .map(s=> Number(s.replace(/[^0-9]/g,'')||'0'))
        .filter(n=> n>0);
    if(!nums.length) return 0;
    return Math.max(...nums);
}

let currentPricePerDay = 0;

function calcDays(){
    const rs = document.querySelector('[name="rent_date"]').value;
    const ds = document.querySelector('[name="due_date"]').value;
    if(!rs || !ds) return 1;
    const r = new Date(rs), d = new Date(ds);
    const ms = d - r; // difference in ms
    const days = Math.max(1, Math.round(ms/(1000*60*60*24)));
    document.querySelector('[name="days"]').value = days;
    document.querySelector('[name="total_amount"]').value = days * currentPricePerDay;
    return days;
}

async function init(){
    const params = new URLSearchParams(location.search);
    const unitId = params.get('unit_id');
    if(!unitId){ location.href = '/units'; return }
    document.querySelector('[name="unit_id"]').value = unitId;
    try{
        const units = await api(`/units`);
        const u = units.find(x=> String(x.id)===String(unitId));
        if(u){ 
            document.querySelector('[name="unit_display"]').value = `${u.name} (${u.unit_code})`;
            const price = Number(u.price_per_day||0);
            currentPricePerDay = price > 0 ? price : 0;
            calcDays();
        }
    }catch{}
    const today = new Date().toISOString().slice(0,10);
    document.querySelector('[name="rent_date"]').value = today;
    const due = new Date(); due.setDate(due.getDate()+1);
    document.querySelector('[name="due_date"]').value = due.toISOString().slice(0,10);
    calcDays();

    document.querySelector('[name="rent_date"]').addEventListener('change', calcDays);
    document.querySelector('[name="due_date"]').addEventListener('change', calcDays);
}

document.getElementById('rentForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const f = e.target; const msg = document.getElementById('msg'); const ok = document.getElementById('ok');
    msg.style.display = 'none'; ok.style.display = 'none';
    const payload = {
        unit_id: Number(f.unit_id.value),
        borrower_name: f.borrower_name.value,
        borrower_identity_number: f.borrower_identity_number.value,
        rent_date: f.rent_date.value,
        due_date: f.due_date.value,
        payment_method: f.payment_method.value,
        total_amount: Number(f.total_amount.value)
    };
    try{
        const res = await api('/rentals', { method:'POST', body: JSON.stringify(payload)});
        ok.textContent = `Pembayaran berhasil. Kode peminjaman: ${res.rental.booking_code}`;
        ok.style.display = '';
        setTimeout(()=> location.href = '/my-rentals', 1200);
    }catch(err){ msg.textContent = err.message; msg.style.display = '' }
});

init();
</script>
@endsection

