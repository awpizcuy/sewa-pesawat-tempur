@extends('layouts.app')

@section('content')
<script>requireAuth();</script>
<div class="card">
    <h2>Units</h2>
    <div style="display:flex;gap:8px;align-items:center;margin-top:8px">
        <input class="input" id="searchInput" placeholder="Cari nama unit..." style="max-width:320px">
        <button class="btn" id="searchBtn">Cari</button>
    </div>
    <div id="msg" class="error" style="display:none"></div>
</div>

<div id="unitsGrid" class="grid"></div>

<script>
async function loadUnits(q){
    try{
        const msg = document.getElementById('msg');
        msg.style.display = 'none';
        const container = document.getElementById('unitsGrid');
        container.innerHTML = '<p class="muted">Loading...</p>';
        const path = q ? `/units/search?name=${encodeURIComponent(q)}` : '/units';
        const units = await api(path);
        if(!units.length){ container.innerHTML = '<p class="muted">Tidak ada unit.</p>'; return }
        container.innerHTML = units.map(u=>{
            const cats = (u.categories||[]).map(c=>c.name).join(', ');
            const price = Number(u.price_per_day||0);
            return `
                <div class="card">
                    <h3>${u.name} <span class="muted">(${u.unit_code})</span></h3>
                    <div class="muted">${cats}</div>
                    <p style="margin:8px 0">${u.description||''}</p>
                    <div class="muted">Harga/hari: ${price>0?`Rp ${price.toLocaleString()}`:'-'}</div>
                    <div class="muted">Stock: ${u.stock} | Status: ${u.status}</div>
                    <button class="btn" data-rent="${u.id}" ${u.stock<=0?'disabled':''}>Sewa</button>
                </div>
            `
        }).join('');
        container.querySelectorAll('[data-rent]').forEach(btn=>{
            btn.addEventListener('click', ()=> openRentForm(btn.getAttribute('data-rent')));
        })
    }catch(err){
        const msg = document.getElementById('msg');
        msg.textContent = err.message; msg.style.display = '';
    }
}

function openRentForm(unitId){
    location.href = `/rentals/new?unit_id=${unitId}`;
}

document.getElementById('searchBtn').addEventListener('click', ()=>{
    const q = document.getElementById('searchInput').value.trim();
    loadUnits(q);
});
document.getElementById('searchInput').addEventListener('keydown', (e)=>{
    if(e.key==='Enter') document.getElementById('searchBtn').click();
});
loadUnits();
</script>
@endsection

