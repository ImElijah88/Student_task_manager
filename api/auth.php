<?php
/**
 * auth.php — REST API Authentication Endpoints
 * =============================================
 * POST /api/auth.php?action=register  — create new account
 * POST /api/auth.php?action=login     — authenticate user
 * POST /api/auth.php?action=logout    — destroy session
 *
 * Security:
 *   - password_hash(PASSWORD_DEFAULT) for hashing
 *   - password_verify() for checking
 *   - PDO prepared statements for SQL injection prevention
 *   - session_regenerate_id() on login to prevent session fixation
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

$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

// ── REGISTER ──────────────────────────────────────────────
if ($action === 'register') {
    $username = trim($data['username'] ?? '');
    $email    = trim($data['email']    ?? '');
    $password = $data['password']      ?? '';

    if (!$username || !$email || strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['error' => 'All fields required. Password min 6 characters.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email address.']);
        exit;
    }

    // PASSWORD_DEFAULT — uses PHP best available algorithm (currently bcrypt)
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, email, passkey) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, $hashed]);
        echo json_encode(['success' => 'Account created. Please log in.']);
    } catch (PDOException $e) {
        http_response_code(409);
        echo json_encode(['error' => 'Username or email already taken.']);
    }
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────
if ($action === 'login') {
    $username = trim($data['username'] ?? '');
    $password = $data['password']      ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required.']);
        exit;
    }

    $stmt = $pdo->prepare('SELECT user_id, username, passkey FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['passkey'])) {
        session_regenerate_id(true); // prevent session fixation
        $_SESSION['user_id']  = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['success' => 'Logged in.', 'username' => $user['username']]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password.']);
    }
    exit;
}

// ── LOGOUT ────────────────────────────────────────────────
if ($action === 'logout') {
    session_unset();
    session_destroy();
    echo json_encode(['success' => 'Logged out.']);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action.']);
