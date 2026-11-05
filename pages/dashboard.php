<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — MyFlow</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Inter, Arial, system-ui, sans-serif;
            background: #080b14;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,60,220,.3) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 90% 90%, rgba(139,92,246,.12) 0%, transparent 60%);
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* ── Header ── */
        .header {
            background: rgba(8,11,20,.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(124,58,237,.2);
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-logo { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.05rem; letter-spacing: -.02em; }
        .header-logo img { width: 32px; height: 32px; border-radius: 8px; box-shadow: 0 0 12px rgba(124,58,237,.5); }
        .header-logo span { background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .header-right { display: flex; align-items: center; gap: 12px; }
        .username-badge {
            padding: 5px 14px;
            border-radius: 999px;
            background: rgba(124,58,237,.15);
            color: #a78bfa;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid rgba(124,58,237,.3);
        }
        .btn-logout {
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid rgba(124,58,237,.2);
            background: transparent;
            color: #6d7fa8;
            font: inherit;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .btn-logout:hover { background: rgba(127,29,29,.4); color: #fca5a5; border-color: rgba(127,29,29,.6); }

        /* ── Layout ── */
        .layout {
            max-width: 1100px;
            margin: 32px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            align-items: start;
        }

        /* ── Card ── */
        .card {
            background: rgba(15,20,40,.7);
            backdrop-filter: blur(16px);
            border-radius: 14px;
            border: 1px solid rgba(124,58,237,.2);
            padding: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,.3);
        }
        .card-title {
            font-size: .95rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Add Task Form ── */
        .form-group { margin-bottom: 14px; }
        label { display: block; font-size: .78rem; font-weight: 600; color: #64748b; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .05em; }
        input, textarea, select {
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid rgba(124,58,237,.2);
            background: rgba(0,0,0,.3);
            color: #e2e8f0;
            font: inherit;
            font-size: .85rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        input:focus, textarea:focus, select:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.15); }
        textarea { resize: vertical; min-height: 72px; }
        select option { background: #0f172a; }

        .priority-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        /* ── Calendar picker ── */
        .cal-trigger {
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            border: 1px solid rgba(124,58,237,.2);
            background: rgba(0,0,0,.3);
            color: #e2e8f0;
            font: inherit;
            font-size: .85rem;
            cursor: pointer;
            text-align: left;
            transition: border-color .2s, box-shadow .2s;
        }
        .cal-trigger:focus, .cal-trigger:hover { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.15); }
        body.light .cal-trigger { background: #f8fafc; color: #1e293b; border-color: rgba(124,58,237,.2); }

        .cal-popup {
            display: none;
            position: absolute;
            z-index: 200;
            background: #0f1428;
            border: 1px solid rgba(124,58,237,.35);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 16px 48px rgba(0,0,0,.6);
            width: 272px;
            margin-top: 4px;
        }
        .cal-popup.open { display: block; }
        body.light .cal-popup { background: #fff; border-color: rgba(124,58,237,.25); box-shadow: 0 16px 48px rgba(0,0,0,.15); }

        .cal-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .cal-nav button {
            width: 28px; height: 28px;
            border-radius: 7px;
            border: 1px solid rgba(124,58,237,.25);
            background: transparent;
            color: #a78bfa;
            cursor: pointer;
            font-size: 14px;
            display: flex; align-items: center; justify-content: center;
            padding: 0;
            transition: background .15s;
        }
        .cal-nav button:hover { background: rgba(124,58,237,.2); }
        .cal-month-label { font-size: .88rem; font-weight: 700; color: #f1f5f9; }
        body.light .cal-month-label { color: #1e1b4b; }

        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
        }
        .cal-dow {
            text-align: center;
            font-size: .68rem;
            font-weight: 700;
            color: #475569;
            padding: 4px 0;
            text-transform: uppercase;
        }
        .cal-day {
            text-align: center;
            padding: 6px 2px;
            border-radius: 7px;
            font-size: .82rem;
            cursor: pointer;
            color: #94a3b8;
            transition: background .15s, color .15s;
            border: 1px solid transparent;
        }
        .cal-day:hover:not(.empty):not(.selected) { background: rgba(124,58,237,.2); color: #e2e8f0; }
        .cal-day.today { border-color: rgba(124,58,237,.4); color: #a78bfa; font-weight: 700; }
        .cal-day.selected { background: linear-gradient(135deg,#7c3aed,#6366f1); color: #fff; font-weight: 700; border-color: transparent; }
        .cal-day.empty { cursor: default; }
        body.light .cal-day { color: #475569; }
        body.light .cal-day:hover:not(.empty):not(.selected) { background: rgba(124,58,237,.1); color: #1e1b4b; }
        body.light .cal-day.today { color: #7c3aed; }

        .cal-time {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(124,58,237,.15);
        }
        .cal-time label { font-size: .75rem; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin: 0; white-space: nowrap; }
        .cal-time select {
            flex: 1;
            padding: 6px 8px;
            font-size: .82rem;
        }
        .cal-actions {
            display: flex;
            gap: 8px;
            margin-top: 12px;
        }
        .cal-actions button {
            flex: 1;
            padding: 8px;
            border-radius: 8px;
            font: inherit;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid rgba(124,58,237,.25);
            transition: all .15s;
        }
        .cal-btn-confirm { background: linear-gradient(135deg,#7c3aed,#6366f1); color: #fff; border-color: transparent !important; }
        .cal-btn-clear   { background: transparent; color: #64748b; }
        .cal-btn-clear:hover { background: rgba(127,29,29,.3); color: #fca5a5; border-color: rgba(127,29,29,.4); }

        .btn-add {
            width: 100%;
            padding: 11px;
            border-radius: 9px;
            border: none;
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            color: #fff;
            font: inherit;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .2s, transform .15s;
            margin-top: 4px;
            box-shadow: 0 4px 16px rgba(124,58,237,.4);
        }
        .btn-add:hover { opacity: .88; transform: translateY(-1px); }

        /* ── Filters ── */
        .filters {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(124,58,237,.2);
            background: transparent;
            color: #6d7fa8;
            font: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
        }
        .filter-btn.active { background: linear-gradient(135deg, #7c3aed, #6366f1); color: #fff; border-color: transparent; box-shadow: 0 4px 12px rgba(124,58,237,.35); }
        .filter-btn:hover:not(.active) { border-color: #7c3aed; color: #a78bfa; }

        /* ── Stats row ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: rgba(15,20,40,.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(124,58,237,.2);
            border-radius: 10px;
            padding: 14px 16px;
            text-align: center;
        }
        .stat-num { font-size: 1.6rem; font-weight: 700; background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .stat-label { font-size: .75rem; color: #64748b; font-weight: 600; margin-top: 2px; }

        /* ── Task list ── */
        .task-list { display: flex; flex-direction: column; gap: 10px; }
        .task-item {
            background: rgba(15,20,40,.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(124,58,237,.15);
            border-radius: 12px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: border-color .2s, box-shadow .2s;
        }
        .task-item:hover { border-color: rgba(124,58,237,.4); box-shadow: 0 4px 20px rgba(124,58,237,.12); }
        .task-item.status-complete { opacity: .5; }
        .task-item.status-in-progress { border-left: 3px solid #6366f1; }

        .task-body { flex: 1; min-width: 0; }
        .task-title {
            font-size: .9rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .task-item.status-complete .task-title { text-decoration: line-through; color: #475569; }
        .task-desc { font-size: .78rem; color: #64748b; margin-bottom: 6px; line-height: 1.5; }
        .task-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }

        .badge {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .badge.High        { background: rgba(239,68,68,.12);   color: #f87171; }
        .badge.Medium      { background: rgba(245,158,11,.12);  color: #fbbf24; }
        .badge.Low         { background: rgba(34,197,94,.12);   color: #4ade80; }
        .badge.Pending     { background: rgba(100,116,139,.12); color: #94a3b8; }
        .badge.In-Progress { background: rgba(99,102,241,.12);  color: #a5b4fc; }
        .badge.Complete    { background: rgba(34,197,94,.12);   color: #4ade80; }

        .deadline-text { font-size: .72rem; color: #64748b; display: flex; align-items: center; gap: 4px; }
        .deadline-text.overdue { color: #f87171; }
        .deadline-text svg { flex-shrink: 0; }

        /* ── Status dropdown ── */
        .status-wrap { position: relative; display: inline-flex; }
        .status-badge {
            padding: 2px 8px 2px 6px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            cursor: pointer;
            display: inline-flex; align-items: center; gap: 4px;
            border: 1px solid transparent;
            transition: opacity .15s;
            user-select: none;
        }
        .status-badge:hover { opacity: .8; }
        .status-badge.Pending     { background: rgba(100,116,139,.12); color: #94a3b8; }
        .status-badge.In-Progress { background: rgba(99,102,241,.12);  color: #a5b4fc; }
        .status-badge.Complete    { background: rgba(34,197,94,.12);   color: #4ade80; }
        .status-badge svg { pointer-events: none; }

        .status-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            z-index: 400;
            background: #0f1428;
            border: 1px solid rgba(124,58,237,.3);
            border-radius: 10px;
            padding: 4px;
            box-shadow: 0 12px 32px rgba(0,0,0,.5);
            min-width: 148px;
        }
        .status-menu.open { display: block; }
        body.light .status-menu { background: #fff; border-color: rgba(124,58,237,.2); box-shadow: 0 12px 32px rgba(0,0,0,.12); }

        .status-option {
            display: flex; align-items: center; gap: 8px;
            padding: 7px 10px;
            border-radius: 7px;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            color: #94a3b8;
            transition: background .15s, color .15s;
        }
        .status-option:hover { background: rgba(124,58,237,.12); color: #e2e8f0; }
        .status-option.active { color: #a78bfa; }
        .status-option svg { flex-shrink: 0; }
        body.light .status-option { color: #475569; }
        body.light .status-option:hover { background: rgba(124,58,237,.08); color: #1e1b4b; }

        /* ── Action buttons ── */
        .task-actions { display: flex; gap: 5px; flex-shrink: 0; align-items: center; }
        .btn-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            border: 1px solid rgba(124,58,237,.18);
            background: transparent;
            color: #6d7fa8;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all .18s;
            position: relative;
        }
        .btn-icon svg { pointer-events: none; }
        .btn-icon.delete:hover { background: rgba(239,68,68,.12); color: #f87171; border-color: rgba(239,68,68,.4); }

        /* tooltip */
        .btn-icon::after {
            content: attr(data-tip);
            position: absolute;
            bottom: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: #e2e8f0;
            font-size: .7rem;
            font-weight: 600;
            white-space: nowrap;
            padding: 4px 8px;
            border-radius: 5px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .15s;
            z-index: 300;
        }
        .btn-icon:hover::after { opacity: 1; }
        body.light .btn-icon::after { background: #1e1b4b; color: #f1f5f9; }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #475569;
        }
        .empty-state svg { opacity: .25; margin-bottom: 12px; }
        .empty-state p { font-size: .88rem; }

        .notice {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            margin-bottom: 14px;
            display: none;
        }
        .notice.error   { background: #450a0a; color: #fca5a5; border: 1px solid #7f1d1d; display: block; }
        .notice.success { background: #052e16; color: #86efac; border: 1px solid #14532d; display: block; }

        @media (max-width: 800px) {
            .layout { grid-template-columns: 1fr; }
            .stats  { grid-template-columns: repeat(3, 1fr); }
        }

        /* ── Light theme ── */
        body.light {
            background: #f1f5f9;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(124,58,237,.1) 0%, transparent 65%),
                radial-gradient(ellipse 40% 40% at 90% 90%, rgba(99,102,241,.06) 0%, transparent 60%);
            color: #1e293b;
        }
        body.light .header {
            background: rgba(255,255,255,.9);
            border-bottom-color: rgba(124,58,237,.15);
        }
        body.light .card {
            background: rgba(255,255,255,.85);
            border-color: rgba(124,58,237,.15);
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
        }
        body.light .stat-card {
            background: rgba(255,255,255,.85);
            border-color: rgba(124,58,237,.15);
        }
        body.light .stat-label { color: #94a3b8; }
        body.light .card-title { color: #1e1b4b; }
        body.light label { color: #64748b; }
        body.light input, body.light textarea, body.light select {
            background: #f8fafc;
            border-color: rgba(124,58,237,.2);
            color: #1e293b;
        }
        body.light select option { background: #fff; color: #1e293b; }
        body.light .filter-btn { color: #64748b; border-color: rgba(124,58,237,.2); }
        body.light .filter-btn:hover:not(.active) { color: #7c3aed; }
        body.light .task-item {
            background: rgba(255,255,255,.8);
            border-color: rgba(124,58,237,.12);
        }
        body.light .task-item:hover { border-color: rgba(124,58,237,.4); box-shadow: 0 4px 20px rgba(124,58,237,.08); }
        body.light .task-title { color: #1e1b4b; }
        body.light .task-desc  { color: #64748b; }
        body.light .btn-logout { color: #64748b; }
        body.light .notice.error   { background: #fef2f2; color: #b91c1c; border-color: #fecaca; }
        body.light .notice.success { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }

        /* ── Header icon buttons (theme / logout) ── */
        .btn-header-icon {
            width: 36px; height: 36px;
            border-radius: 9px;
            border: 1px solid rgba(124,58,237,.22);
            background: transparent;
            color: #6d7fa8;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: background .2s, color .2s, transform .15s;
            position: relative;
        }
        .btn-header-icon::after {
            content: attr(data-tip);
            position: absolute;
            top: calc(100% + 6px);
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: #e2e8f0;
            font-size: .7rem;
            font-weight: 600;
            white-space: nowrap;
            padding: 4px 8px;
            border-radius: 5px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .15s;
            z-index: 300;
        }
        .btn-header-icon:hover::after { opacity: 1; }
        .btn-header-icon:hover { background: rgba(124,58,237,.12); color: #a78bfa; }
        .btn-header-icon.danger:hover { background: rgba(239,68,68,.1); color: #f87171; border-color: rgba(239,68,68,.3); }
        body.light .btn-header-icon::after { background: #1e1b4b; }

        /* ── Add task btn ── */
        .btn-add {
            width: 100%;
            padding: 11px;
            border-radius: 9px;
            border: none;
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            color: #fff;
            font: inherit;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: opacity .2s, transform .15s;
            margin-top: 4px;
            box-shadow: 0 4px 16px rgba(124,58,237,.4);
        }
        .btn-add:hover { opacity: .88; transform: translateY(-1px); }
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="header-logo">
        <img src="/student-task-manager/assets/logo.png" alt="MyFlow">
        My<span>Flow</span>
    </div>
    <div class="header-right">
        <span class="username-badge" id="username-badge">...</span>
        <button class="btn-header-icon" id="theme-btn" onclick="toggleTheme()" data-tip="Toggle theme" aria-label="Toggle theme">
            <i data-lucide="moon" width="16" height="16"></i>
        </button>
        <button class="btn-header-icon danger" onclick="logout()" data-tip="Log out" aria-label="Log out">
            <i data-lucide="log-out" width="16" height="16"></i>
        </button>
    </div>
</header>

<div class="layout">

    <!-- Add Task Panel -->
    <aside>
        <div class="card">
            <div class="card-title"><i data-lucide="circle-plus" width="16" height="16"></i> New Task</div>
            <div id="form-notice" class="notice"></div>
            <div class="form-group">
                <label>Title *</label>
                <input type="text" id="task-title" placeholder="What needs doing?">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea id="task-desc" placeholder="Optional details..."></textarea>
            </div>
            <div class="priority-row">
                <div class="form-group">
                    <label>Priority</label>
                    <select id="task-priority">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>
                <div class="form-group" style="position:relative;">
                    <label>Deadline</label>
                    <button type="button" class="cal-trigger" id="cal-trigger" onclick="toggleCal()">📅 Pick date &amp; time</button>
                    <input type="hidden" id="task-deadline">
                    <div class="cal-popup" id="cal-popup">
                        <div class="cal-nav">
                            <button onclick="calMove(-1)">&#8249;</button>
                            <span class="cal-month-label" id="cal-label"></span>
                            <button onclick="calMove(1)">&#8250;</button>
                        </div>
                        <div class="cal-grid" id="cal-grid"></div>
                        <div class="cal-time">
                            <label>Time</label>
                            <select id="cal-hour"></select>
                            <select id="cal-min"><option value="00">:00</option><option value="15">:15</option><option value="30">:30</option><option value="45">:45</option></select>
                        </div>
                        <div class="cal-actions">
                            <button class="cal-btn-clear" onclick="calClear()">Clear</button>
                            <button class="cal-btn-confirm" onclick="calConfirm()">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn-add" onclick="createTask()">
                <i data-lucide="plus" width="15" height="15"></i> Add Task
            </button>
        </div>
    </aside>

    <!-- Task Dashboard -->
    <main>
        <!-- Stats -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-num" id="stat-total">0</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" id="stat-pending" style="color:#fbbf24;">0</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-num" id="stat-complete" style="color:#4ade80;">0</div>
                <div class="stat-label">Complete</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <button class="filter-btn active" onclick="setFilter('', this)">All</button>
            <button class="filter-btn" onclick="setFilter('Pending', this)">Pending</button>
            <button class="filter-btn" onclick="setFilter('In Progress', this)">In Progress</button>
            <button class="filter-btn" onclick="setFilter('Complete', this)">Complete</button>
            <button class="filter-btn" onclick="setFilter('High', this, 'priority')">High</button>
            <button class="filter-btn" onclick="setFilter('Medium', this, 'priority')">Medium</button>
            <button class="filter-btn" onclick="setFilter('Low', this, 'priority')">Low</button>
        </div>

        <!-- Task List -->
        <div class="task-list" id="task-list">
            <div class="empty-state"><i data-lucide="inbox" width="40" height="40"></i><p>No tasks yet. Add one!</p></div>
        </div>
    </main>
</div>

<script>
    let currentFilter   = '';
    let currentFilterBy = 'status';

    // ── Load username into badge ───────────────────────────
    async function loadUsername() {
        const res = await fetch('/student-task-manager/api/auth.php?action=me');
        if (res.status === 401) {
            window.location.href = '/student-task-manager/index.php';
            return;
        }
        const data = await res.json();
        if (data.username) {
            document.getElementById('username-badge').innerHTML =
                '<i data-lucide="user" width="12" height="12" style="margin-right:5px;vertical-align:middle;"></i>' + data.username;
            lucide.createIcons();
        }
    }

    // ── Load tasks from API ────────────────────────────────
    async function loadTasks() {
        let url = '/student-task-manager/api/tasks.php';
        if (currentFilter) {
            url += currentFilterBy === 'priority'
                ? `?priority=${encodeURIComponent(currentFilter)}`
                : `?status=${encodeURIComponent(currentFilter)}`;
        }

        const res = await fetch(url);
        if (res.status === 401) {
            window.location.href = '/student-task-manager/index.php';
            return;
        }

        const tasks = await res.json();
        renderTasks(tasks);
        updateStats(tasks);
    }

    // ── Render task list ───────────────────────────────────
    function renderTasks(tasks) {
        const list = document.getElementById('task-list');
        if (!tasks.length) {
            list.innerHTML = '<div class="empty-state"><i data-lucide="inbox" width="40" height="40"></i><p>No tasks here.</p></div>';
            lucide.createIcons();
            return;
        }

        list.innerHTML = '';
        tasks.forEach(task => {
            const s         = task.status;
            const isComplete = s === 'Complete';
            const overdue    = task.deadline && new Date(task.deadline.replace(' ','T')) < new Date() && !isComplete;
            const statusCls  = s.replace(' ', '-');

            const deadlineHtml = task.deadline
                ? `<span class="deadline-text ${overdue ? 'overdue' : ''}">
                       <i data-lucide="${overdue ? 'alert-triangle' : 'calendar'}" width="11" height="11"></i>
                       ${fmtDeadline(task.deadline)}
                   </span>`
                : '';

            const taskJson = JSON.stringify(task).replace(/'/g, "&#39;");

            const item = document.createElement('div');
            item.className = `task-item status-${statusCls.toLowerCase()}`;
            item.innerHTML = `
                <div class="task-body">
                    <div class="task-title"></div>
                    ${task.description ? '<div class="task-desc"></div>' : ''}
                    <div class="task-meta">
                        <span class="badge ${task.priority}">${task.priority}</span>
                        <div class="status-wrap">
                            <span class="status-badge ${statusCls}" onclick="toggleStatusMenu(event, this)">
                                <i data-lucide="chevron-down" width="10" height="10"></i>${s}
                            </span>
                            <div class="status-menu">
                                <div class="status-option ${s==='Pending'?'active':''}"     onclick="pickStatus(event,${task.task_id},'Pending','${taskJson}')">
                                    <i data-lucide="circle" width="12" height="12"></i>Pending
                                </div>
                                <div class="status-option ${s==='In Progress'?'active':''}" onclick="pickStatus(event,${task.task_id},'In Progress','${taskJson}')">
                                    <i data-lucide="play-circle" width="12" height="12"></i>In Progress
                                </div>
                                <div class="status-option ${s==='Complete'?'active':''}"    onclick="pickStatus(event,${task.task_id},'Complete','${taskJson}')">
                                    <i data-lucide="check-circle" width="12" height="12"></i>Complete
                                </div>
                            </div>
                        </div>
                        ${deadlineHtml}
                    </div>
                </div>
                <div class="task-actions">
                    <button class="btn-icon delete" data-tip="Delete" onclick="deleteTask(${task.task_id})">
                        <i data-lucide="trash-2" width="14" height="14"></i>
                    </button>
                </div>`;

            item.querySelector('.task-title').textContent = task.title;
            if (task.description) item.querySelector('.task-desc').textContent = task.description;
            list.appendChild(item);
        });

        lucide.createIcons();
    }

    // ── Update stats ───────────────────────────────────────
    function updateStats(tasks) {
        document.getElementById('stat-total').textContent    = tasks.length;
        document.getElementById('stat-pending').textContent  = tasks.filter(t => t.status === 'Pending').length;
        document.getElementById('stat-complete').textContent = tasks.filter(t => t.status === 'Complete').length;
    }

    // ── Create task ────────────────────────────────────────
    async function createTask() {
        const title    = document.getElementById('task-title').value.trim();
        const desc     = document.getElementById('task-desc').value.trim();
        const priority = document.getElementById('task-priority').value;
        const deadline = document.getElementById('task-deadline').value;

        if (!title) return showFormNotice('Task title is required.', 'error');

        const res  = await fetch('/student-task-manager/api/tasks.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ title, description: desc, priority, deadline })
        });
        const data = await res.json();

        if (res.ok) {
            document.getElementById('task-title').value    = '';
            document.getElementById('task-desc').value     = '';
            document.getElementById('task-deadline').value = '';
            document.getElementById('cal-trigger').textContent = '📅 Pick date & time';
            showFormNotice('Task added!', 'success');
            loadTasks();
        } else {
            showFormNotice(data.error, 'error');
        }
    }

    // ── Status dropdown ────────────────────────────────────
    function toggleStatusMenu(e, badge) {
        e.stopPropagation();
        const menu = badge.nextElementSibling;
        const isOpen = menu.classList.contains('open');
        closeAllStatusMenus();
        if (!isOpen) menu.classList.add('open');
    }

    function closeAllStatusMenus() {
        document.querySelectorAll('.status-menu.open').forEach(m => m.classList.remove('open'));
    }

    async function pickStatus(e, id, newStatus, taskJson) {
        e.stopPropagation();
        closeAllStatusMenus();
        const task = JSON.parse(taskJson);
        await fetch(`/student-task-manager/api/tasks.php?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                title:       task.title,
                description: task.description,
                priority:    task.priority,
                deadline:    task.deadline,
                status:      newStatus
            })
        });
        loadTasks();
    }

    // ── Delete task ────────────────────────────────────────
    async function deleteTask(id) {
        if (!confirm('Delete this task?')) return;
        await fetch(`/student-task-manager/api/tasks.php?id=${id}`, { method: 'DELETE' });
        loadTasks();
    }

    // ── Filter ─────────────────────────────────────────────
    function setFilter(value, btn, by = 'status') {
        currentFilter   = value;
        currentFilterBy = by;
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        loadTasks();
    }

    // ── Logout ─────────────────────────────────────────────
    async function logout() {
        await fetch('/student-task-manager/api/auth.php?action=logout', { method: 'POST' });
        window.location.href = '/student-task-manager/index.php';
    }

    function showFormNotice(msg, type) {
        const el = document.getElementById('form-notice');
        el.textContent = msg;
        el.className   = 'notice ' + type;
        setTimeout(() => el.className = 'notice', 3000);
    }

    function fmtDeadline(dt) {
        if (!dt) return '';
        const d = new Date(dt.replace(' ', 'T'));
        const date = d.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
        const time = dt.includes(' ') && !dt.endsWith('00:00') ? ' · ' + d.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit' }) : '';
        return date + time;
    }

    // ── Calendar ───────────────────────────────────────────
    let calYear, calMonth, calSelected = null;

    function initCal() {
        const now = new Date();
        calYear  = now.getFullYear();
        calMonth = now.getMonth();

        // Populate hours
        const hSel = document.getElementById('cal-hour');
        for (let h = 0; h < 24; h++) {
            const o = document.createElement('option');
            o.value = String(h).padStart(2,'0');
            o.textContent = String(h).padStart(2,'0') + ':00';
            if (h === 9) o.selected = true;
            hSel.appendChild(o);
        }
        renderCal();
    }

    function renderCal() {
        const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('cal-label').textContent = months[calMonth] + ' ' + calYear;

        const grid  = document.getElementById('cal-grid');
        grid.innerHTML = '';

        ['Su','Mo','Tu','We','Th','Fr','Sa'].forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-dow';
            el.textContent = d;
            grid.appendChild(el);
        });

        const firstDay = new Date(calYear, calMonth, 1).getDay();
        const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
        const today = new Date();

        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'cal-day empty';
            grid.appendChild(el);
        }

        for (let d = 1; d <= daysInMonth; d++) {
            const el  = document.createElement('div');
            el.className = 'cal-day';
            el.textContent = d;
            const isToday = d === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear();
            const isSel   = calSelected && d === calSelected.d && calMonth === calSelected.m && calYear === calSelected.y;
            if (isToday) el.classList.add('today');
            if (isSel)   el.classList.add('selected');
            el.onclick = () => selectDay(d);
            grid.appendChild(el);
        }
    }

    function selectDay(d) {
        calSelected = { d, m: calMonth, y: calYear };
        renderCal();
    }

    function calMove(dir) {
        calMonth += dir;
        if (calMonth > 11) { calMonth = 0;  calYear++; }
        if (calMonth < 0)  { calMonth = 11; calYear--; }
        renderCal();
    }

    function toggleCal() {
        const popup = document.getElementById('cal-popup');
        popup.classList.toggle('open');
    }

    function calConfirm() {
        if (!calSelected) return;
        const { d, m, y } = calSelected;
        const h   = document.getElementById('cal-hour').value;
        const min = document.getElementById('cal-min').value;
        const dateStr = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')} ${h}:${min}`;
        document.getElementById('task-deadline').value = dateStr;
        const label = new Date(dateStr.replace(' ','T')).toLocaleDateString('en-GB',{day:'2-digit',month:'short',year:'numeric'}) + ' · ' + h + ':' + min;
        document.getElementById('cal-trigger').textContent = '📅 ' + label;
        document.getElementById('cal-popup').classList.remove('open');
    }

    function calClear() {
        calSelected = null;
        document.getElementById('task-deadline').value = '';
        document.getElementById('cal-trigger').textContent = '📅 Pick date & time';
        document.getElementById('cal-popup').classList.remove('open');
        renderCal();
    }

    // Close status menus when clicking outside
    document.addEventListener('click', e => {
        if (!e.target.closest('.status-wrap')) closeAllStatusMenus();

        // Close calendar when clicking outside
        const popup   = document.getElementById('cal-popup');
        const trigger = document.getElementById('cal-trigger');
        if (popup && !popup.contains(e.target) && e.target !== trigger) {
            popup.classList.remove('open');
        }
    });

    initCal();

    // Kick off — load username then tasks
    loadUsername();
    loadTasks();

    // ── Theme ──
    function applyTheme(light) {
        document.body.classList.toggle('light', light);
        const btn = document.getElementById('theme-btn');
        btn.innerHTML = light
            ? '<i data-lucide="sun" width="16" height="16"></i>'
            : '<i data-lucide="moon" width="16" height="16"></i>';
        lucide.createIcons();
    }
    (function() { applyTheme(localStorage.getItem('mf-theme') === 'light'); })();
    function toggleTheme() {
        const isLight = !document.body.classList.contains('light');
        localStorage.setItem('mf-theme', isLight ? 'light' : 'dark');
        applyTheme(isLight);
    }

    // Init Lucide after DOM ready
    lucide.createIcons();
</script>

</body>
</html>
