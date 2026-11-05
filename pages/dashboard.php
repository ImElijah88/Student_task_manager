<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — MyFlow</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: Inter, Arial, system-ui, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
        }

        /* ── Header ── */
        .header {
            background: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 0 24px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 1rem; }
        .header-logo span { font-size: 20px; }
        .header-right { display: flex; align-items: center; gap: 12px; }
        .username-badge {
            padding: 5px 12px;
            border-radius: 999px;
            background: #1e1b4b;
            color: #a5b4fc;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid #3730a3;
        }
        .btn-logout {
            padding: 7px 14px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: transparent;
            color: #94a3b8;
            font: inherit;
            font-size: .8rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s;
        }
        .btn-logout:hover { background: #7f1d1d; color: #fca5a5; border-color: #7f1d1d; }

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
            background: #1e293b;
            border-radius: 14px;
            border: 1px solid #334155;
            padding: 24px;
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
            border: 1px solid #334155;
            background: #0f172a;
            color: #e2e8f0;
            font: inherit;
            font-size: .85rem;
            outline: none;
            transition: border-color .2s;
        }
        input:focus, textarea:focus, select:focus { border-color: #6366f1; }
        textarea { resize: vertical; min-height: 72px; }
        select option { background: #1e293b; }

        .priority-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        .btn-add {
            width: 100%;
            padding: 11px;
            border-radius: 9px;
            border: none;
            background: #6366f1;
            color: #fff;
            font: inherit;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
            margin-top: 4px;
        }
        .btn-add:hover { background: #4f46e5; }

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
            border: 1px solid #334155;
            background: transparent;
            color: #64748b;
            font: inherit;
            font-size: .78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
        }
        .filter-btn.active { background: #6366f1; color: #fff; border-color: #6366f1; }
        .filter-btn:hover:not(.active) { border-color: #6366f1; color: #a5b4fc; }

        /* ── Stats row ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 10px;
            padding: 14px 16px;
            text-align: center;
        }
        .stat-num { font-size: 1.6rem; font-weight: 700; color: #6366f1; }
        .stat-label { font-size: .75rem; color: #64748b; font-weight: 600; margin-top: 2px; }

        /* ── Task list ── */
        .task-list { display: flex; flex-direction: column; gap: 10px; }
        .task-item {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: border-color .2s;
        }
        .task-item:hover { border-color: #6366f1; }
        .task-item.complete { opacity: .55; }

        .task-check {
            width: 20px; height: 20px;
            border-radius: 6px;
            border: 2px solid #334155;
            background: transparent;
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 2px;
            display: flex; align-items: center; justify-content: center;
            transition: background .2s, border-color .2s;
        }
        .task-check.checked { background: #059669; border-color: #059669; }
        .task-check.checked::after { content: '✓'; color: #fff; font-size: 11px; font-weight: 700; }

        .task-body { flex: 1; min-width: 0; }
        .task-title {
            font-size: .92rem;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 4px;
        }
        .task-item.complete .task-title { text-decoration: line-through; color: #475569; }
        .task-desc { font-size: .8rem; color: #64748b; margin-bottom: 8px; line-height: 1.5; }
        .task-meta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        .badge {
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }
        .badge.High     { background: rgba(239,68,68,.15);  color: #f87171; }
        .badge.Medium   { background: rgba(245,158,11,.15); color: #fbbf24; }
        .badge.Low      { background: rgba(34,197,94,.15);  color: #4ade80; }
        .badge.Pending      { background: rgba(100,116,139,.15); color: #94a3b8; }
        .badge.In-Progress  { background: rgba(99,102,241,.15);  color: #a5b4fc; }
        .badge.Complete     { background: rgba(34,197,94,.15);   color: #4ade80; }

        .deadline-text { font-size: .75rem; color: #64748b; }
        .deadline-text.overdue { color: #f87171; }

        .task-actions { display: flex; gap: 6px; flex-shrink: 0; }
        .btn-icon {
            width: 30px; height: 30px;
            border-radius: 7px;
            border: 1px solid #334155;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            transition: all .2s;
        }
        .btn-icon:hover { background: #7f1d1d; color: #fca5a5; border-color: #7f1d1d; }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #475569;
        }
        .empty-state .icon { font-size: 40px; margin-bottom: 12px; opacity: .4; }
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
    </style>
</head>
<body>

<!-- Header -->
<header class="header">
    <div class="header-logo">
        <span>📋</span> MyFlow
    </div>
    <div class="header-right">
        <span class="username-badge" id="username-badge">...</span>
        <button class="btn-logout" onclick="logout()">Log Out</button>
    </div>
</header>

<div class="layout">

    <!-- Add Task Panel -->
    <aside>
        <div class="card">
            <div class="card-title">➕ New Task</div>
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
                <div class="form-group">
                    <label>Deadline</label>
                    <input type="date" id="task-deadline">
                </div>
            </div>
            <button class="btn-add" onclick="createTask()">Add Task</button>
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
            <button class="filter-btn" onclick="setFilter('High', this, 'priority')">🔴 High</button>
            <button class="filter-btn" onclick="setFilter('Medium', this, 'priority')">🟡 Medium</button>
            <button class="filter-btn" onclick="setFilter('Low', this, 'priority')">🟢 Low</button>
        </div>

        <!-- Task List -->
        <div class="task-list" id="task-list">
            <div class="empty-state"><div class="icon">📭</div><p>No tasks yet. Add one!</p></div>
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
            document.getElementById('username-badge').textContent = '👤 ' + data.username;
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
            list.innerHTML = '<div class="empty-state"><div class="icon">📭</div><p>No tasks here.</p></div>';
            return;
        }

        list.innerHTML = '';
        tasks.forEach(task => {
            const isComplete = task.status === 'Complete';
            const overdue    = task.deadline && new Date(task.deadline) < new Date() && !isComplete;
            const statusClass = task.status.replace(' ', '-');

            const item = document.createElement('div');
            item.className = 'task-item' + (isComplete ? ' complete' : '');
            item.innerHTML = `
                <div class="task-check ${isComplete ? 'checked' : ''}"
                     onclick="toggleComplete(${task.task_id}, '${task.status}', ${JSON.stringify(task)})"></div>
                <div class="task-body">
                    <div class="task-title"></div>
                    ${task.description ? '<div class="task-desc"></div>' : ''}
                    <div class="task-meta">
                        <span class="badge ${task.priority}">${task.priority}</span>
                        <span class="badge ${statusClass}">${task.status}</span>
                        ${task.deadline ? `<span class="deadline-text ${overdue ? 'overdue' : ''}">
                            ${overdue ? '⚠ Overdue · ' : '📅 '}${task.deadline}
                        </span>` : ''}
                    </div>
                </div>
                <div class="task-actions">
                    <button class="btn-icon" onclick="deleteTask(${task.task_id})" title="Delete">🗑</button>
                </div>`;

            // textContent prevents XSS from task data
            item.querySelector('.task-title').textContent = task.title;
            if (task.description) item.querySelector('.task-desc').textContent = task.description;

            list.appendChild(item);
        });
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
            showFormNotice('Task added!', 'success');
            loadTasks();
        } else {
            showFormNotice(data.error, 'error');
        }
    }

    // ── Toggle complete ────────────────────────────────────
    async function toggleComplete(id, currentStatus, task) {
        const newStatus = currentStatus === 'Complete' ? 'Pending' : 'Complete';
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

    // Kick off — load username then tasks
    loadUsername();
    loadTasks();
</script>

</body>
</html>
