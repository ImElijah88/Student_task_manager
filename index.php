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
                <input type="password" id="login-password" placeholder="••••••••" autocomplete="current-password">
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
                <input type="password" id="reg-password" placeholder="••••••••" autocomplete="new-password">
            </div>
            <button class="btn" onclick="register()">Create Account</button>
        </div>
    </div>
</div>

<script>
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

</body>
</html>
