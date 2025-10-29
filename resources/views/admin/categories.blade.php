@extends('layouts.app')

@section('content')
<script>requireAuth();(async()=>{ if(!(await isAdmin())) location.href='/' })();</script>
<div class="card">
    <h2>Categories</h2>
    <form id="catForm" style="display:flex;gap:8px;margin-top:8px">
        <input class="input" name="name" placeholder="Nama kategori" required>
        <button class="btn" type="submit">Tambah</button>
    </form>
</div>

<table class="table" id="tbl" style="margin-top:12px">
    <thead><tr><th>Nama</th><th>Slug</th><th>Aksi</th></tr></thead>
    <tbody></tbody>
  </table>

<script>
async function load(){
    const cats = await api('/admin/categories');
    const tb = document.querySelector('#tbl tbody');
    tb.innerHTML = cats.map(c=>`
        <tr>
            <td><input class="input" data-name="${c.id}" value="${c.name}"></td>
            <td>${c.slug}</td>
            <td>
                <button class="btn" data-save="${c.id}">Simpan</button>
                <button class="btn danger" data-del="${c.id}">Hapus</button>
            </td>
        </tr>
    `).join('');
    tb.querySelectorAll('[data-save]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-save');
        const name = tb.querySelector(`[data-name="${id}"]`).value;
        await api(`/admin/categories/${id}`, { method:'PUT', body: JSON.stringify({ name })});
        alert('Tersimpan');
        load();
    }));
    tb.querySelectorAll('[data-del]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-del');
        if(!confirm('Hapus kategori?')) return;
        await api(`/admin/categories/${id}`, { method:'DELETE' });
        load();
    }));
}
document.getElementById('catForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const f = e.target; const payload = { name:f.name.value };
    try{ await api('/admin/categories', { method:'POST', body: JSON.stringify(payload)}); f.reset(); load(); }catch(err){ alert(err.message) }
});
load();
</script>
@endsection

