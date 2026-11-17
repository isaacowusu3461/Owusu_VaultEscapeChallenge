
<?php
// Logout handler.
// Clear the current session and return to the login page.

require_once __DIR__ . '/includes/auth.php';

startGameSession();

// Logout should only happen through a valid POST request.
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST'
    || !validCsrfToken((string) ($_POST['csrf_token'] ?? ''))
) {
    http_response_code(403);
    exit('Invalid logout request.');
}

// Remove all stored session values.
$_SESSION = [];

// Delete the session cookie from the browser.
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

// Destroy the server-side session.
session_destroy();

header('Location: login.php');
exit;
