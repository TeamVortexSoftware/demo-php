<?php
// Vortex PHP SDK Demo
// Simple demonstration of the Vortex PHP SDK

require_once __DIR__ . '/../../../packages/vortex-php-sdk/src/VortexClient.php';

use TeamVortexSoftware\VortexSDK\VortexClient;

// Start session
session_start();

// Demo users (simplified structure)
$demoUsers = [
    [
        'id' => 'admin-user-123',
        'email' => 'admin@example.com',
        'password' => 'password123',
        'name' => 'Alice Johnson',
        'is_autojoin_admin' => true
    ],
    [
        'id' => 'user-user-456',
        'email' => 'user@example.com',
        'password' => 'userpass',
        'name' => 'Bob Smith',
        'is_autojoin_admin' => false
    ]
];

// Initialize Vortex client
$apiKey = getenv('VORTEX_API_KEY') ?: 'demo-api-key';
$vortex = new VortexClient($apiKey);

// Simple router
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Set JSON header for API routes
if (strpos($path, '/api/') === 0) {
    header('Content-Type: application/json');
}

// Routes
switch ($path) {
    case '/api/auth/login':
        handleLogin($demoUsers);
        break;
    case '/api/auth/logout':
        handleLogout();
        break;
    case '/api/auth/me':
        handleGetMe();
        break;
    case '/api/auth/users':
        handleGetUsers($demoUsers);
        break;
    case '/api/vortex/jwt':
        handleGenerateJWT($vortex, $demoUsers);
        break;
    case '/api/vortex/invitations':
        handleGetInvitations($vortex);
        break;
    default:
        if (preg_match('#^/api/vortex/invitations/([^/]+)$#', $path, $matches)) {
            handleInvitationById($vortex, $matches[1]);
        } else {
            serveHTML();
        }
        break;
}

function handleLogin($users) {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    foreach ($users as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            $_SESSION['user'] = $user;
            echo json_encode(['success' => true, 'user' => $user]);
            return;
        }
    }

    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}

function handleLogout() {
    session_destroy();
    echo json_encode(['success' => true]);
}

function handleGetMe() {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }

    echo json_encode($_SESSION['user']);
}

function handleGetUsers($users) {
    $usersInfo = array_map(function($u) {
        return ['email' => $u['email'], 'password' => 'password123'];
    }, $users);
    echo json_encode($usersInfo);
}

function handleGenerateJWT($vortex, $users) {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }

    $sessionUser = $_SESSION['user'];

    try {
        // Build admin scopes
        $adminScopes = [];
        if ($sessionUser['is_autojoin_admin']) {
            $adminScopes[] = 'autojoin';
        }

        // Create user array
        $user = [
            'id' => $sessionUser['id'],
            'email' => $sessionUser['email'],
            'adminScopes' => $adminScopes
        ];

        $jwt = $vortex->generateJwt($user);

        echo json_encode(['jwt' => $jwt]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function handleGetInvitations($vortex) {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }

    $targetType = $_GET['targetType'] ?? 'email';
    $targetValue = $_GET['targetValue'] ?? '';

    try {
        $invitations = $vortex->getInvitationsByTarget($targetType, $targetValue);
        echo json_encode(['invitations' => $invitations]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function handleInvitationById($vortex, $id) {
    if (!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        try {
            $invitation = $vortex->getInvitation($id);
            echo json_encode($invitation);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        try {
            $vortex->revokeInvitation($id);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}

function serveHTML() {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vortex PHP SDK Demo</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <h1>🚀 Vortex PHP SDK Demo</h1>
        <p>Interactive demo for testing Vortex PHP SDK integration</p>
    </div>

    <div id="login-section" class="section">
        <h2>🔐 Login</h2>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" id="login-email" value="admin@example.com">
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" id="login-password" value="password123">
        </div>
        <button onclick="login()">Login</button>
        <button onclick="getDemoUsers()">Show Demo Users</button>
        <div id="login-result" class="result"></div>
        <div class="demo-users-info">
            <h4>Demo Users:</h4>
            <p><strong>admin@example.com</strong> / password123 (autojoin admin)</p>
            <p><strong>user@example.com</strong> / userpass (regular user)</p>
        </div>
    </div>

    <div id="jwt-section" class="section" style="display:none;">
        <h2>🎯 Generate JWT</h2>
        <button onclick="generateJWT()">Generate JWT</button>
        <div id="jwt-result" class="result"></div>
    </div>

    <div id="invitations-section" class="section" style="display:none;">
        <h2>📧 Get Invitations</h2>
        <div class="form-group">
            <label>Target Type:</label>
            <select id="target-type">
                <option value="email">Email</option>
                <option value="sms">SMS</option>
            </select>
        </div>
        <div class="form-group">
            <label>Target Value:</label>
            <input type="text" id="target-value">
        </div>
        <button onclick="getInvitations()">Get Invitations</button>
        <div id="invitations-result" class="result"></div>
    </div>

    <script src="app.js"></script>
</body>
</html>
    <?php
}
