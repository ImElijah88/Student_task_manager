<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Task Manager — Login</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Inter, Arial, system-ui, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
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
            width: 52px; height: 52px;
            border-radius: 14px;
            background: #6366f1;
            font-size: 24px;
            margin-bottom: 12px;
            box-shadow: 0 8px 24px rgba(99,102,241,.4);
        }
        .logo h1 { font-size: 1.4rem; font-weight: 700; color: #f1f5f9; letter-spacing: -.02em; }
        .logo p  { font-size: .85rem; color: #94a3b8; margin-top: 4px; }

        .card {
            background: #1e293b;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,.4);
            border: 1px solid #334155;
        }
        .tabs {
            display: flex;
            background: #0f172a;
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 24px;
        }
        .tab {
            flex: 1;
            padding: 9px;
            border: none;
            border-radius: 8px;
            background: transparent;
            color: #64748b;
            font: inherit;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .tab.active { background: #6366f1; color: #fff; }

        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: .8rem; font-weight: 600; color: #94a3b8; margin-bottom: 6px; }
        input {
            width: 100%;
            padding: 11px 14px;
            border-radius: 9px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #f1f5f9;
            font: inherit;
            font-size: .9rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
        input::placeholder { color: #475569; }

        .btn {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: #6366f1;
            color: #fff;
            font: inherit;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: background .2s, transform .15s;
            box-shadow: 0 4px 14px rgba(99,102,241,.35);
        }
        .btn:hover { background: #4f46e5; transform: translateY(-1px); }

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
        <div class="logo-badge">📋</div>
        <h1>TaskFlow</h1>
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
