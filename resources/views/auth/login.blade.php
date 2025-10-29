@extends('layouts.app')

@section('content')
<div class="card" style="max-width:480px;margin:24px auto">
    <h2>Login</h2>
    <div id="msg" class="error" style="display:none"></div>
    <form id="loginForm">
        <label>Email</label>
        <input class="input" type="email" name="email" required>
        <label style="margin-top:8px">Password</label>
        <input class="input" type="password" name="password" required>
        <button class="btn" type="submit" style="margin-top:12px">Login</button>
    </form>
    <p class="muted" style="margin-top:8px">Belum punya akun? <a href="/register">Register</a></p>
</div>
<script>
document.getElementById('loginForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const msg = document.getElementById('msg');
    msg.style.display = 'none';
    const form = e.target;
    const payload = {
        email: form.email.value,
        password: form.password.value
    };
    try{
        const res = await api('/login', { method:'POST', body: JSON.stringify(payload) });
        setToken(res.token);
        location.href = '/units';
    }catch(err){
        msg.textContent = err.message;
        msg.style.display = '';
    }
});
</script>
@endsection

