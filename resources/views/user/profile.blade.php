@extends('layouts.app')

@section('content')
<script>requireAuth();</script>
<div class="card" style="max-width:640px;margin:0 auto">
    <h2>Profil</h2>
    <div id="msg" class="error" style="display:none"></div>
    <div id="ok" class="success" style="display:none">Profil tersimpan</div>
    <form id="profileForm" style="margin-top:8px">
        <label>Nama</label>
        <input class="input" type="text" name="name" required>
        <label style="margin-top:8px">Email</label>
        <input class="input" type="email" name="email" required disabled>
        <label style="margin-top:8px">Nomor Telepon</label>
        <input class="input" type="text" name="phone_number">
        <label style="margin-top:8px">Alamat</label>
        <input class="input" type="text" name="address">
        <button class="btn" type="submit" style="margin-top:12px">Simpan</button>
    </form>
</div>
<script>
async function loadProfile(){
    const me = await api('/profile');
    const f = document.getElementById('profileForm');
    f.name.value = me.name||'';
    f.email.value = me.email||'';
    f.phone_number.value = me.phone_number||'';
    f.address.value = me.address||'';
}
document.getElementById('profileForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const msg = document.getElementById('msg');
    const ok = document.getElementById('ok');
    msg.style.display = 'none'; ok.style.display = 'none';
    const f = e.target;
    const payload = {
        name: f.name.value,
        phone_number: f.phone_number.value,
        address: f.address.value
    };
    try{
        await api('/profile', { method:'PUT', body: JSON.stringify(payload) });
        ok.style.display = '';
    }catch(err){ msg.textContent = err.message; msg.style.display = '' }
});
loadProfile();
</script>
@endsection

