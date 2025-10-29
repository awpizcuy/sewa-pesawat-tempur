@extends('layouts.app')

@section('content')
<script>requireAuth();(async()=>{ if(!(await isAdmin())) location.href='/' })();</script>
<div class="card">
    <h2>Daftar Peminjaman</h2>
    <div class="muted">Proses pengembalian hanya oleh admin</div>
</div>

<table class="table" id="tbl" style="margin-top:12px">
    <thead><tr><th>User</th><th>Unit</th><th>Sewa</th><th>Jatuh Tempo</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody></tbody>
  </table>

<script>
async function load(){
    const items = await api('/admin/rentals');
    const tb = document.querySelector('#tbl tbody');
    tb.innerHTML = items.map(r=>{
        const user = r.user||{}; const unit=r.unit||{};
        return `
            <tr>
                <td>${user.name||'-'}</td>
                <td>${unit.name||'-'} <span class="muted">(${unit.unit_code||''})</span></td>
                <td>${new Date(r.rent_date).toLocaleString()}</td>
                <td>${new Date(r.due_date).toLocaleString()}</td>
                <td>
                    <select data-status="${r.id}" class="input" style="max-width:160px">
                        <option value="rented" ${r.status==='rented'?'selected':''}>Dalam peminjaman</option>
                        <option value="overdue" ${r.status==='overdue'?'selected':''}>Terlambat</option>
                        <option value="returned" ${r.status==='returned'?'selected':''}>Selesai</option>
                    </select>
                </td>
                <td>
                    <button class="btn" data-save="${r.id}">Simpan Status</button>
                    ${r.status==='rented'||r.status==='overdue' ? `<button class="btn" data-return="${r.id}">Kembalikan</button>` : ''}
                </td>
            </tr>
        `
    }).join('');
    tb.querySelectorAll('[data-save]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-save');
        const status = tb.querySelector(`[data-status="${id}"]`).value;
        await api(`/admin/rentals/${id}/status`, { method:'PATCH', body: JSON.stringify({ status })});
        load();
    }));
    tb.querySelectorAll('[data-return]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-return');
        if(!confirm('Proses pengembalian?')) return;
        await api(`/admin/rentals/${id}/return`, { method:'POST' });
        load();
    }));
}
load();
</script>
@endsection

