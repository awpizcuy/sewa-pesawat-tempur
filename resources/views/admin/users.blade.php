@extends('layouts.app')

@section('content')
<script>requireAuth();(async()=>{ if(!(await isAdmin())) location.href='/' })();</script>
<div class="card">
    <h2>Users</h2>
    <div id="msg" class="error" style="display:none"></div>
    <form id="userForm" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:8px;margin-top:8px">
        <input class="input" name="name" placeholder="Nama" required>
        <input class="input" name="email" placeholder="Email" required>
        <input class="input" name="password" placeholder="Password" required>
        <select class="input" name="role">
            <option value="anggota">anggota</option>
            <option value="admin">admin</option>
        </select>
        <button class="btn" type="submit">Tambah</button>
    </form>
</div>

<table class="table" id="tbl" style="margin-top:12px">
    <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr></thead>
    <tbody></tbody>
  </table>

<script>
async function load(){
    const users = await api('/admin/users');
    const tb = document.querySelector('#tbl tbody');
    tb.innerHTML = users.map(u=>`
        <tr>
            <td>${u.name}</td>
            <td>${u.email}</td>
            <td>
                <select data-role="${u.id}" class="input" style="max-width:140px">
                    <option ${u.role==='anggota'?'selected':''}>anggota</option>
                    <option ${u.role==='admin'?'selected':''}>admin</option>
                </select>
            </td>
            <td>
                <button class="btn" data-save="${u.id}">Simpan</button>
                <button class="btn danger" data-del="${u.id}">Hapus</button>
            </td>
        </tr>
    `).join('');
    tb.querySelectorAll('[data-save]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-save');
        const role = tb.querySelector(`[data-role="${id}"]`).value;
        await api(`/admin/users/${id}`, { method:'PUT', body: JSON.stringify({ role })});
        alert('Tersimpan');
    }));
    tb.querySelectorAll('[data-del]').forEach(b=> b.addEventListener('click', async ()=>{
        const id = b.getAttribute('data-del');
        if(!confirm('Hapus user?')) return;
        await api(`/admin/users/${id}`, { method:'DELETE' });
        load();
    }));
}
document.getElementById('userForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const f = e.target; const payload = { name:f.name.value, email:f.email.value, password:f.password.value, role:f.role.value };
    try{ await api('/admin/users', { method:'POST', body: JSON.stringify(payload)}); f.reset(); load(); }catch(err){ alert(err.message) }
});
load();
</script>
@endsection

