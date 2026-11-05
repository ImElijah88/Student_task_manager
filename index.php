<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFlow — Login</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Inter, Arial, system-ui, sans-serif;
            background: #080b14;
            background-image:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(99,60,220,.35) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 80% 80%, rgba(139,92,246,.15) 0%, transparent 60%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .shell {
            width: 100%;
            max-width: 420px;
        }
        .logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .logo-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px; height: 72px;
            border-radius: 18px;
            margin-bottom: 14px;
            box-shadow: 0 0 32px rgba(124,58,237,.5), 0 8px 24px rgba(0,0,0,.4);
        }
        .logo-badge img { width: 72px; height: 72px; border-radius: 18px; object-fit: cover; }
        .logo h1 { font-size: 1.6rem; font-weight: 800; color: #f1f5f9; letter-spacing: -.03em; }
        .logo h1 span { background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .logo p  { font-size: .85rem; color: #6d7fa8; margin-top: 4px; }

        .card {
            background: rgba(15,20,40,.85);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 24px 64px rgba(0,0,0,.5), inset 0 1px 0 rgba(255,255,255,.06);
            border: 1px solid rgba(124,58,237,.25);
        }
        .tabs {
            display: flex;
            background: rgba(0,0,0,.3);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 24px;
            border: 1px solid rgba(124,58,237,.15);
        }
        .tab {
            flex: 1;
            padding: 9px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #6d7fa8;
            font: inherit;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .tab.active { background: linear-gradient(135deg, #7c3aed, #6366f1); color: #fff; box-shadow: 0 4px 12px rgba(124,58,237,.4); }

        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: .8rem; font-weight: 600; color: #94a3b8; margin-bottom: 6px; }
        input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 9px;
            border: 1px solid rgba(124,58,237,.25);
            background: rgba(0,0,0,.3);
            color: #e2e8f0;
            font: inherit;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.2); }
        input::placeholder { color: #3d4f6e; }

        .pw-wrap { position: relative; }
        .pw-wrap input { padding-right: 42px; }
        .pw-toggle {
            position: absolute;
            right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none;
            cursor: pointer; padding: 0;
            color: #475569;
            display: flex; align-items: center;
            transition: color .2s;
        }
        .pw-toggle:hover { color: #a78bfa; }

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            color: #fff;
            font: inherit;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity .2s, transform .15s;
            box-shadow: 0 4px 20px rgba(124,58,237,.45);
        }
        .btn:hover { opacity: .88; transform: translateY(-1px); }

        .notice {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: .83rem;
            font-weight: 600;
            margin-bottom: 16px;
            display: none;
        }
        .notice.error   { background: #450a0a; color: #fca5a5; border: 1px solid #7f1d1d; display: block; }
        .notice.success { background: #052e16; color: #86efac; border: 1px solid #14532d; display: block; }

        /* ── Theme toggle ── */
        .theme-toggle {
            position: fixed;
            top: 16px; right: 16px;
            width: 38px; height: 38px;
            border-radius: 50%;
            border: 1px solid rgba(124,58,237,.3);
            background: rgba(15,20,40,.8);
            backdrop-filter: blur(10px);
            cursor: pointer;
            font-size: 18px;
            display: flex; align-items: center; justify-content: center;
            transition: background .25s, border-color .25s, transform .15s;
            z-index: 999;
        }
        .theme-toggle:hover { transform: scale(1.1); border-color: #7c3aed; }

        /* ── Light theme ── */
        body.light {
            background: #f1f5f9;
            background-image:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(124,58,237,.12) 0%, transparent 70%),
                radial-gradient(ellipse 40% 40% at 80% 80%, rgba(99,102,241,.08) 0%, transparent 60%);
        }
        body.light .logo h1 { color: #1e1b4b; }
        body.light .logo p  { color: #64748b; }
        body.light .card {
            background: rgba(255,255,255,.9);
            border-color: rgba(124,58,237,.2);
            box-shadow: 0 24px 64px rgba(0,0,0,.1);
        }
        body.light .tabs { background: rgba(0,0,0,.06); border-color: rgba(124,58,237,.15); }
        body.light .tab  { color: #64748b; }
        body.light label { color: #475569; }
        body.light input {
            background: #f8fafc;
            border-color: rgba(124,58,237,.25);
            color: #1e293b;
        }
        body.light input::placeholder { color: #94a3b8; }
        body.light .pw-toggle { color: #94a3b8; }
        body.light .theme-toggle { background: rgba(255,255,255,.9); }
        body.light .notice.error   { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        body.light .notice.success { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
    </style>
</head>
<body>

<div class="shell">
    <div class="logo">
        <div class="logo-badge">
            <img src="/student-task-manager/assets/logo.png" alt="MyFlow logo">
        </div>
        <h1>My<span>Flow</span></h1>
        <p>Student Task Management</p>
    </div>

    <div class="card">
        <div class="tabs">
            <button class="tab active" onclick="switchTab('login')">Log In</button>
            <button class="tab" onclick="switchTab('register')">Register</button>
        </div>

        <div id="notice" class="notice"></div>

        <!-- Login Form -->
        <div id="form-login">
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="login-username" placeholder="Your username" autocomplete="username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="pw-wrap">
                    <input type="password" id="login-password" placeholder="••••••••" autocomplete="current-password">
                    <button type="button" class="pw-toggle" onclick="togglePw('login-password', this)" aria-label="Show password">&#128065;</button>
                </div>
            </div>
            <button class="btn" onclick="login()">Log In</button>
        </div>

        <!-- Register Form -->
        <div id="form-register" style="display:none;">
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="reg-username" placeholder="Choose a username" autocomplete="username">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="reg-email" placeholder="your@email.com" autocomplete="email">
            </div>
            <div class="form-group">
                <label>Password <span style="color:#475569;font-weight:400;">(min. 6 characters)</span></label>
                <div class="pw-wrap">
                    <input type="password" id="reg-password" placeholder="••••••••" autocomplete="new-password">
                    <button type="button" class="pw-toggle" onclick="togglePw('reg-password', this)" aria-label="Show password">&#128065;</button>
                </div>
            </div>
            <button class="btn" onclick="register()">Create Account</button>
        </div>
    </div>
</div>

<script>
    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        btn.innerHTML = show ? '&#128064;' : '&#128065;';
    }

    function switchTab(name) {
        document.querySelectorAll('.tab').forEach((t, i) => {
            t.classList.toggle('active', (i === 0 && name === 'login') || (i === 1 && name === 'register'));
        });
        document.getElementById('form-login').style.display    = name === 'login'    ? 'block' : 'none';
        document.getElementById('form-register').style.display = name === 'register' ? 'block' : 'none';
        hideNotice();
    }

    function showNotice(msg, type) {
        const el = document.getElementById('notice');
        el.textContent  = msg;
        el.className    = 'notice ' + type;
    }
    function hideNotice() {
        const el = document.getElementById('notice');
        el.className = 'notice';
    }

    async function login() {
        const username = document.getElementById('login-username').value.trim();
        const password = document.getElementById('login-password').value;
        if (!username || !password) return showNotice('Please fill in all fields.', 'error');

        const res  = await fetch('/student-task-manager/api/auth.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        const data = await res.json();
        if (res.ok) {
            window.location.href = '/student-task-manager/pages/dashboard.php';
        } else {
            showNotice(data.error, 'error');
        }
    }

    async function register() {
        const username = document.getElementById('reg-username').value.trim();
        const email    = document.getElementById('reg-email').value.trim();
        const password = document.getElementById('reg-password').value;
        if (!username || !email || !password) return showNotice('Please fill in all fields.', 'error');

        const res  = await fetch('/student-task-manager/api/auth.php?action=register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, email, password })
        });
        const data = await res.json();
        if (res.ok) {
            showNotice(data.success, 'success');
            setTimeout(() => switchTab('login'), 1500);
        } else {
            showNotice(data.error, 'error');
        }
    }

    // Allow Enter key to submit
    document.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            const loginVisible = document.getElementById('form-login').style.display !== 'none';
            loginVisible ? login() : register();
        }
    });
</script>

<button class="theme-toggle" id="theme-btn" onclick="toggleTheme()" aria-label="Toggle theme">🌙</button>

<script>
    (function() {
        if (localStorage.getItem('mf-theme') === 'light') {
            document.body.classList.add('light');
            document.getElementById('theme-btn').textContent = '☀️';
        }
    })();
    function toggleTheme() {
        const isLight = document.body.classList.toggle('light');
        document.getElementById('theme-btn').textContent = isLight ? '☀️' : '🌙';
        localStorage.setItem('mf-theme', isLight ? 'light' : 'dark');
    }
</script>

</body>
</html>
