
<?php
// Logout handler for Vault Escape Challenge.
// A valid POST request is required to end the session.

require_once __DIR__ . '/includes/auth.php';

startGameSession();
sendSecurityHeaders();

if (!isPostRequest()) {
    http_response_code(405);
    header('Allow: POST');
    exit('Request method not allowed.');
}

$token = getPostString('csrf_token', 64);

if ($token === null || !validCsrfToken($token)) {
    http_response_code(403);
    exit('Invalid logout request.');
}

// Remove all game and authentication data.
$_SESSION = [];

// Expire the browser's session cookie.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();

    setcookie(session_name(), '', [
        'expires' => time() - 42000,
        'path' => $params['path'],
        'domain' => $params['domain'],
        'secure' => $params['secure'],
        'httponly' => $params['httponly'],
        'samesite' => $params['samesite'] ?? 'Lax'
    ]);
}

session_destroy();

header('Location: login.php');
exit;
