<?php
/**
 * tasks.php — REST API Task Endpoints
 * =====================================
 * GET    /api/tasks.php          — fetch all tasks for logged-in user
 * POST   /api/tasks.php          — create a new task
 * PUT    /api/tasks.php?id={id}  — update a task
 * DELETE /api/tasks.php?id={id}  — delete a task
 *
 * Security mechanisms:
 *   1. Authentication gate  — every request checks $_SESSION['user_id']
 *   2. IDOR prevention      — user_id always from session, never from client
 *                             ownership verified before every PUT/DELETE
 *   3. SQL injection        — PDO prepared statements throughout
 *   4. XSS prevention       — htmlspecialchars() + filter_var() on all input
 *   5. Input validation     — whitelist checks on priority and status values
 *   6. Security headers     — X-Content-Type-Options, X-Frame-Options
 */

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once '../db/database.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (session_status() === PHP_SESSION_NONE) session_start();

// ── AUTHENTICATION GATE ────────────────────────────────────
// All task endpoints require a valid session.
// user_id is ALWAYS read from the session — never from GET/POST params.
// This prevents IDOR (Insecure Direct Object Reference) attacks where
// a user could manipulate a parameter to access another user's tasks.
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorised. Please log in.']);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$taskId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

// Whitelists for server-side validation — reject anything not in these lists
$allowed_priority = ['Low', 'Medium', 'High'];
$allowed_status   = ['Pending', 'In Progress', 'Complete'];

// ── GET — fetch tasks for the logged-in user ───────────────
if ($method === 'GET') {
    $status   = $_GET['status']   ?? '';
    $priority = $_GET['priority'] ?? '';

    // Validate filter values against whitelists to prevent injection
    if ($status   && !in_array($status,   $allowed_status))   $status   = '';
    if ($priority && !in_array($priority, $allowed_priority)) $priority = '';

    $sql    = 'SELECT * FROM tasks WHERE user_id = ?';
    $params = [$userId];

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
    $tasks = $stmt->fetchAll();

    // Sanitise output — htmlspecialchars on text fields before sending to client
    $tasks = array_map(function($t) {
        $t['title']       = htmlspecialchars($t['title'],       ENT_QUOTES, 'UTF-8');
        $t['description'] = htmlspecialchars($t['description'] ?? '', ENT_QUOTES, 'UTF-8');
        return $t;
    }, $tasks);

    echo json_encode($tasks);
    exit;
}

// ── POST — create a new task ───────────────────────────────
if ($method === 'POST') {
    // Sanitise and validate all inputs server-side
    $title       = htmlspecialchars(trim($data['title']       ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $priority    = $data['priority'] ?? 'Medium';
    $deadline    = $data['deadline'] ?? null;

    if (!$title) {
        http_response_code(400);
        echo json_encode(['error' => 'Task title is required.']);
        exit;
    }

    if (strlen($title) > 150) {
        http_response_code(400);
        echo json_encode(['error' => 'Title too long (max 150 characters).']);
        exit;
    }

    // Whitelist check — reject any priority value not in the allowed list
    if (!in_array($priority, $allowed_priority)) $priority = 'Medium';

    // Validate deadline format if provided
    if ($deadline && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) $deadline = null;

    $stmt = $pdo->prepare(
        'INSERT INTO tasks (user_id, title, description, priority, deadline) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $title, $description, $priority, $deadline ?: null]);

    http_response_code(201);
    echo json_encode(['success' => 'Task created.', 'task_id' => (int) $pdo->lastInsertId()]);
    exit;
}

// ── PUT — update a task ────────────────────────────────────
if ($method === 'PUT') {
    if (!$taskId) {
        http_response_code(400);
        echo json_encode(['error' => 'Task ID required.']);
        exit;
    }

    // IDOR check — verify this task belongs to the logged-in user
    // A user must never be able to update another user's task by guessing an ID
    $check = $pdo->prepare('SELECT task_id FROM tasks WHERE task_id = ? AND user_id = ?');
    $check->execute([$taskId, $userId]);
    if (!$check->fetch()) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied.']);
        exit;
    }

    $title       = htmlspecialchars(trim($data['title']       ?? ''), ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
    $priority    = $data['priority'] ?? 'Medium';
    $status      = $data['status']   ?? 'Pending';
    $deadline    = $data['deadline'] ?? null;

    if (!$title) {
        http_response_code(400);
        echo json_encode(['error' => 'Task title is required.']);
        exit;
    }

    // Whitelist validation — prevents arbitrary values being stored
    if (!in_array($priority, $allowed_priority)) $priority = 'Medium';
    if (!in_array($status,   $allowed_status))   $status   = 'Pending';
    if ($deadline && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline)) $deadline = null;

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

    // IDOR check — user_id in WHERE clause ensures users can only delete their own tasks
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
