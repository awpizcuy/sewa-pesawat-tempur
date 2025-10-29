<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa Pesawat Tempur</title>
    <style>
        .container{max-width:960px;margin:24px auto;padding:0 16px}
        .nav{display:flex;gap:12px;flex-wrap:wrap;padding:12px 0;border-bottom:1px solid #eee}
        .nav a{color:#2563eb;text-decoration:none}
        .nav .brand{color:#111827;text-decoration:none;font-weight:700;font-size:18px}
        .nav .brand:hover{color:#111827}
        .card{border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:12px 0}
        .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:16px}
        .input, select{border:1px solid #cbd5e1;border-radius:6px;padding:8px 10px;width:100%}
        .btn{background:#2563eb;color:#fff;border:none;border-radius:6px;padding:8px 12px;cursor:pointer;text-decoration:none;display:inline-block;font-size:14px;transition:all 0.2s;box-shadow:0 2px 4px rgba(37,99,235,0.3)}
        .btn:hover{background:#1d4ed8;transform:translateY(-1px);box-shadow:0 4px 8px rgba(37,99,235,0.4)}
        .btn.secondary{background:#334155;box-shadow:0 2px 4px rgba(51,65,85,0.3)}
        .btn.secondary:hover{background:#1e293b;box-shadow:0 4px 8px rgba(51,65,85,0.4)}
        .btn.danger{background:#dc2626;box-shadow:0 2px 4px rgba(220,38,38,0.3)}
        .btn.danger:hover{background:#b91c1c;box-shadow:0 4px 8px rgba(220,38,38,0.4)}
        .btn-outline{background:transparent;color:#2563eb;border:2px solid #2563eb;box-shadow:none}
        .btn-outline:hover{background:#2563eb;color:#fff;box-shadow:0 4px 8px rgba(37,99,235,0.4)}
        .muted{color:#64748b}
        .error{color:#dc2626;margin:8px 0}
        .success{color:#16a34a;margin:8px 0}
        .table{width:100%;border-collapse:collapse}
        .table th,.table td{border:1px solid #e5e7eb;padding:8px;text-align:left}
    </style>
    <script>
        window.API_BASE = '/api';
        function getToken(){return localStorage.getItem('token')||''}
        function setToken(t){localStorage.setItem('token',t)}
        function clearToken(){localStorage.removeItem('token')}
        async function api(path, options={}){
            const headers = options.headers || {};
            if(!(options.body instanceof FormData)){
                headers['Content-Type'] = headers['Content-Type'] || 'application/json';
            }
            const token = getToken();
            if(token){ headers['Authorization'] = `Bearer ${token}` }
            const res = await fetch(`${window.API_BASE}${path}`, {
                credentials: 'same-origin',
                ...options,
                headers
            });
            if(res.status === 401){
                clearToken();
                if(!location.pathname.startsWith('/login')) location.href = '/login';
                throw new Error('Unauthorized');
            }
            const contentType = res.headers.get('content-type')||'';
            const data = contentType.includes('application/json') ? await res.json() : await res.text();
            if(!res.ok){
                let msg = 'Request failed';
                if (data && typeof data === 'object'){
                    if (data.message) msg = data.message;
                    const e = data.errors || data.error || null;
                    if (e){
                        if (typeof e === 'string') msg = `${msg}: ${e}`;
                        else if (typeof e === 'object'){
                            const first = Object.values(e).flat()[0];
                            if (first) msg = `${msg}: ${first}`;
                        }
                    }
                }
                throw new Error(msg);
            }
            return data;
        }
        async function requireAuth(){ if(!getToken()){ location.href = '/login' } }
        async function isAdmin(){
            try{ const me = await api('/profile'); return me.role === 'admin' }catch{ return false }
        }
        function navInit(){
            const token = getToken();
            const authOnly = document.querySelectorAll('[data-auth]');
            const guestOnly = document.querySelectorAll('[data-guest]');
            authOnly.forEach(el=> el.style.display = token ? '' : 'none');
            guestOnly.forEach(el=> el.style.display = token ? 'none' : '');
        }
        document.addEventListener('DOMContentLoaded', navInit);
    </script>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="/" id="homeLink" class="brand">JET505</a>
            <a href="/units" data-auth data-user-only>Units</a>
            <a href="/my-rentals" data-auth data-user-only>My Rentals</a>
            <a href="/profile" data-auth data-user-only>Profile</a>
            <a href="/admin/dashboard" id="adminLink" style="display:none">Admin</a>
            <span class="muted" style="margin-left:auto" data-guest></span>
            <a href="/login" data-guest>Login</a>
            <a href="/register" data-guest>Register</a>
            <button class="btn secondary" id="logoutBtn" data-auth style="display:none">Logout</button>
        </nav>
        <main>
            @yield('content')
        </main>
    </div>
    <script>
        (async function(){
            if(getToken()){
                document.getElementById('logoutBtn').style.display = '';
                try{
                    const me = await api('/profile');
                    if(me.role === 'admin'){
                        document.getElementById('adminLink').style.display = '';
                        // Sembunyikan menu user-only untuk admin
                        document.querySelectorAll('[data-user-only]').forEach(el => el.style.display = 'none');
                        // Update home link untuk admin
                        document.getElementById('homeLink').href = '/admin/dashboard';
                    } else {
                        // Tampilkan menu user-only untuk user biasa
                        document.querySelectorAll('[data-user-only]').forEach(el => el.style.display = '');
                    }
                }catch{}
            }
            const btn = document.getElementById('logoutBtn');
            if(btn){ btn.addEventListener('click', async ()=>{
                try{ await api('/logout', { method: 'POST' }); }catch{}
                clearToken(); location.href = '/login';
            })}
        })();
    </script>
</body>
</html>

