<?php
/**
 * auth.php — REST API Authentication Endpoints
 * =============================================
 * POST /api/auth.php?action=register  — create new account
 * POST /api/auth.php?action=login     — authenticate and start session
 * POST /api/auth.php?action=logout    — destroy session
 * GET  /api/auth.php?action=me        — return logged-in username
 *
 * Security mechanisms:
 *   1. Password hashing    — password_hash(PASSWORD_DEFAULT) on register
 *                            password_verify() on login
 *   2. SQL injection       — PDO prepared statements, no string concatenation
 *   3. Secure sessions     — httponly + samesite cookie params
 *                            session_regenerate_id(true) on login
 *   4. XSS prevention      — filter_var() + htmlspecialchars() on all input
 *   5. Input validation    — server-side checks, never trust client
 *   6. Brute force         — login attempt limiting via session counter
 */

header('Content-Type: application/json');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once '../db/database.php';

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,   // JS cannot read the session cookie
    'samesite' => 'Lax',  // blocks cross-site request forgery via cookies
]);

if (session_status() === PHP_SESSION_NONE) session_start();

$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents('php://input'), true) ?? [];

// ── ME — return session username for dashboard display ─────
if ($action === 'me') {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in.']);
        exit;
    }
    echo json_encode(['username' => htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8')]);
    exit;
}

// ── REGISTER ──────────────────────────────────────────────
if ($action === 'register') {
    // Sanitise inputs — strip special chars to prevent stored XSS
    $username = htmlspecialchars(trim(filter_var($data['username'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8');
    $email    = trim($data['email']    ?? '');
    $password = $data['password']      ?? '';

    // Server-side validation — never rely on client alone
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

    if (strlen($username) > 50) {
        http_response_code(400);
        echo json_encode(['error' => 'Username too long (max 50 characters).']);
        exit;
    }

    // PASSWORD_DEFAULT — uses PHP recommended algorithm (currently bcrypt)
    // Automatically future-proofs if PHP upgrades its default algorithm
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Prepared statement — username and email never concatenated into SQL
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
    // Brute force protection — max 5 attempts per session
    if (($_SESSION['login_attempts'] ?? 0) >= 5) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many login attempts. Please restart your browser.']);
        exit;
    }

    $username = htmlspecialchars(trim(filter_var($data['username'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS)), ENT_QUOTES, 'UTF-8');
    $password = $data['password'] ?? '';

    if (!$username || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'Username and password required.']);
        exit;
    }

    // Prepared statement — username bound as parameter, never in SQL string
    $stmt = $pdo->prepare('SELECT user_id, username, passkey FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['passkey'])) {
        // Reset attempt counter on success
        $_SESSION['login_attempts'] = 0;

        // session_regenerate_id prevents session fixation attacks
        // A new session ID is issued on every successful login
        session_regenerate_id(true);

        $_SESSION['user_id']  = $user['user_id'];
        $_SESSION['username'] = $user['username'];

        echo json_encode([
            'success'  => 'Logged in.',
            'username' => htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8')
        ]);
    } else {
        // Increment attempt counter — generic message prevents user enumeration
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        http_response_code(401);
        echo json_encode(['error' => 'Invalid username or password.']);
    }
    exit;
}

// ── LOGOUT ────────────────────────────────────────────────
if ($action === 'logout') {
    session_unset();
    session_destroy();
    // Expire the session cookie immediately
    setcookie(session_name(), '', time() - 3600, '/');
    echo json_encode(['success' => 'Logged out.']);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action.']);
