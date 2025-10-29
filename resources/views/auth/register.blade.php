@extends('layouts.app')

@section('content')
<div class="card" style="max-width:480px;margin:24px auto">
    <h2>Register</h2>
    <div id="msg" class="error" style="display:none"></div>
    <form id="registerForm">
        <label>Nama</label>
        <input class="input" type="text" name="name" required>
        <label style="margin-top:8px">Email</label>
        <input class="input" type="email" name="email" required>
        <label style="margin-top:8px">Nomer Identitas Anggota</label>
        <input class="input" type="text" name="member_identity_number" required placeholder="Contoh: AU0598">
        <label style="margin-top:8px">Password</label>
        <input class="input" type="password" name="password" required>
        <label style="margin-top:8px">Konfirmasi Password</label>
        <input class="input" type="password" name="password_confirmation" required>
        <button class="btn" type="submit" style="margin-top:12px">Register</button>
    </form>
    <p class="muted" style="margin-top:8px">Sudah punya akun? <a href="/login">Login</a></p>
    <p class="muted" style="margin-top:8px">Default role: anggota</p>
    </div>
<script>
document.getElementById('registerForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const msg = document.getElementById('msg');
    msg.style.display = 'none';
    const form = e.target;
    const payload = {
        name: form.name.value,
        email: form.email.value,
        member_identity_number: form.member_identity_number.value,
        password: form.password.value,
        password_confirmation: form.password_confirmation.value
    };
    try{
        const response = await api('/register', { method:'POST', body: JSON.stringify(payload) });
        // Tampilkan pesan sukses
        msg.className = 'success';
        msg.textContent = response.message || 'Berhasil register';
        msg.style.display = '';
        // Auto login after register
        const loginRes = await api('/login', { method:'POST', body: JSON.stringify({ email: payload.email, password: payload.password })});
        setToken(loginRes.token);
        setTimeout(() => {
            location.href = '/units';
        }, 1500);
    }catch(err){
        msg.className = 'error';
        msg.textContent = err.message;
        msg.style.display = '';
    }
});
</script>
@endsection

