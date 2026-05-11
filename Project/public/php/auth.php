<?php
/**
 * auth.php — Authentication & Session Management
 * SpecialistHub · PHP Stub (ready to connect to your database)
 *
 * Endpoints (call via fetch/AJAX with JSON body):
 *   POST /php/auth.php?action=login
 *   POST /php/auth.php?action=register
 *   POST /php/auth.php?action=logout
 *   GET  /php/auth.php?action=session
 *   POST /php/auth.php?action=reset_password
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

// ── TODO: Replace with your DB credentials ──────────────────
// $pdo = new PDO('mysql:host=localhost;dbname=specialisthub', 'user', 'pass');

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

session_start();

switch ($action) {

    /**
     * LOGIN
     * Body: { email, password, remember_me? }
     * Returns: { success, user: { id, name, role, avatar_initials }, token }
     */
    case 'login':
        $email    = filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $body['password'] ?? '';

        if (!$email || !$password) {
            echo json_encode(['success' => false, 'error' => 'Email and password are required.']);
            exit;
        }

        // TODO: Query DB
        // $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        // $stmt->execute([$email]);
        // $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // if (!$user || !password_verify($password, $user['password_hash'])) { ... }

        // STUB RESPONSE
        $_SESSION['user_id']   = 1;
        $_SESSION['user_role'] = 'client';

        echo json_encode([
            'success' => true,
            'user'    => [
                'id'              => 1,
                'name'            => 'Acme Corp',
                'email'           => $email,
                'role'            => 'client',       // client | specialist | admin
                'avatar_initials' => 'AC',
                'verified'        => true,
            ],
            'token'   => bin2hex(random_bytes(32)), // TODO: Use JWT
        ]);
        break;

    /**
     * REGISTER
     * Body: { name, email, password, role, niche? }
     * Returns: { success, user_id, message }
     */
    case 'register':
        $name     = trim($body['name'] ?? '');
        $email    = filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $body['password'] ?? '';
        $role     = in_array($body['role'] ?? '', ['client', 'specialist']) ? $body['role'] : 'client';

        if (!$name || !$email || strlen($password) < 8) {
            echo json_encode(['success' => false, 'error' => 'Invalid registration data. Password must be 8+ chars.']);
            exit;
        }

        // TODO:
        // $hash = password_hash($password, PASSWORD_BCRYPT);
        // $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
        // $stmt->execute([$name, $email, $hash, $role]);
        // $user_id = $pdo->lastInsertId();
        // Send verification email...

        echo json_encode([
            'success'  => true,
            'user_id'  => 999,          // STUB
            'message'  => 'Account created. Please verify your email.',
            'kyc_required' => $role === 'specialist',
        ]);
        break;

    /**
     * LOGOUT
     * Returns: { success }
     */
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    /**
     * SESSION CHECK
     * Returns: { authenticated, user? }
     */
    case 'session':
        if (!empty($_SESSION['user_id'])) {
            // TODO: Fetch fresh user data from DB
            echo json_encode([
                'authenticated' => true,
                'user' => [
                    'id'   => $_SESSION['user_id'],
                    'role' => $_SESSION['user_role'],
                ],
            ]);
        } else {
            echo json_encode(['authenticated' => false]);
        }
        break;

    /**
     * RESET PASSWORD
     * Body: { email }
     * Returns: { success, message }
     */
    case 'reset_password':
        $email = filter_var($body['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            echo json_encode(['success' => false, 'error' => 'Valid email required.']);
            exit;
        }
        // TODO: Generate reset token, store in DB, send email
        echo json_encode(['success' => true, 'message' => 'If that email exists, a reset link has been sent.']);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action.']);
}
