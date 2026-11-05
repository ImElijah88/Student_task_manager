<?php
/**
 * tasks.php — REST API Task Endpoints
 * =====================================
 * GET    /api/tasks.php          — fetch all tasks for logged-in user
 * POST   /api/tasks.php          — create a new task
 * PUT    /api/tasks.php?id={id}  — update a task (title, status, priority, deadline)
 * DELETE /api/tasks.php?id={id}  — delete a task
 *
 * Security:
 *   - Session check on every request (authentication gate)
 *   - user_id always taken from session, never from client (IDOR prevention)
 *   - PDO prepared statements (SQL injection prevention)
 */

header('Content-Type: application/json');

require_once '../db/database.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// ── AUTH GATE — all task endpoints require a valid session ──
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised. Please log in.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$taskId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

// ── GET — fetch all tasks for the logged-in user ───────────
if ($method === 'GET') {
    $status   = $_GET['status']   ?? '';
    $priority = $_GET['priority'] ?? '';

    $sql    = 'SELECT * FROM tasks WHERE user_id = ?';
    $params = [$userId];

    // Optional filters
    if ($status) {
        $sql      .= ' AND status = ?';
        $params[]  = $status;
    }
    if ($priority) {
        $sql      .= ' AND priority = ?';
        $params[]  = $priority;
    }

    $sql .= ' ORDER BY deadline ASC, priority DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll());
    exit;
}

// ── POST — create a new task ───────────────────────────────
if ($method === 'POST') {
    $title       = trim($data['title']       ?? '');
    $description = trim($data['description'] ?? '');
    $priority    = $data['priority']          ?? 'Medium';
    $deadline    = $data['deadline']          ?? null;

    if (!$title) {
        http_response_code(400);
        echo json_encode(['error' => 'Task title is required.']);
        exit;
    }

    $allowed_priority = ['Low', 'Medium', 'High'];
    if (!in_array($priority, $allowed_priority)) $priority = 'Medium';

    $stmt = $pdo->prepare(
        'INSERT INTO tasks (user_id, title, description, priority, deadline) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $description, $priority, $deadline ?: null]);

    http_response_code(201);
    echo json_encode(['success' => 'Task created.', 'task_id' => $pdo->lastInsertId()]);
    exit;
}

// ── PUT — update a task ────────────────────────────────────
if ($method === 'PUT') {
    if (!$taskId) {
        http_response_code(400);
        echo json_encode(['error' => 'Task ID required.']);
        exit;
    }

    // Verify the task belongs to the logged-in user (IDOR prevention)
    $check = $pdo->prepare('SELECT task_id FROM tasks WHERE task_id = ? AND user_id = ?');
    $check->execute([$taskId, $userId]);
    if (!$check->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied.']);
        exit;
    }

    $title       = trim($data['title']       ?? '');
    $description = trim($data['description'] ?? '');
    $priority    = $data['priority']          ?? 'Medium';
    $status      = $data['status']            ?? 'Pending';
    $deadline    = $data['deadline']          ?? null;

    $allowed_priority = ['Low', 'Medium', 'High'];
    $allowed_status   = ['Pending', 'In Progress', 'Complete'];
    if (!in_array($priority, $allowed_priority)) $priority = 'Medium';
    if (!in_array($status,   $allowed_status))   $status   = 'Pending';

    $stmt = $pdo->prepare(
        'UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, deadline = ? WHERE task_id = ? AND user_id = ?'
    );
    $stmt->execute([$title, $description, $priority, $status, $deadline ?: null, $taskId, $userId]);

    echo json_encode(['success' => 'Task updated.']);
    exit;
}

// ── DELETE — delete a task ─────────────────────────────────
if ($method === 'DELETE') {
    if (!$taskId) {
        http_response_code(400);
        echo json_encode(['error' => 'Task ID required.']);
        exit;
    }

    // Verify ownership before deleting (IDOR prevention)
    $stmt = $pdo->prepare('DELETE FROM tasks WHERE task_id = ? AND user_id = ?');
    $stmt->execute([$taskId, $userId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'Task not found or access denied.']);
        exit;
    }

    echo json_encode(['success' => 'Task deleted.']);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed.']);
