
<?php
// Shared authentication and security functions.
// These helpers are used by the login, game, and dashboard pages.

// Start a secure PHP session.
function startGameSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    // Set session options before starting the session.
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    // HTTPS cookies are secure on the deployed site.
    // Local HTTP development can still work.
    $secure = !empty($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Add security headers to pages that use authentication.
function sendSecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store, private');

    // The project uses local CSS and no JavaScript.
    // Inline styles are currently used for the progress bar.
    header(
        "Content-Security-Policy: "
        . "default-src 'self'; "
        . "script-src 'none'; "
        . "style-src 'self' 'unsafe-inline'; "
        . "img-src 'self' data:; "
        . "object-src 'none'; "
        . "base-uri 'self'; "
        . "form-action 'self'; "
        . "frame-ancestors 'none'"
    );
}

// Check whether the current session belongs to a logged-in player.
function isLoggedIn(): bool
{
    return isset($_SESSION['username'])
        && is_string($_SESSION['username'])
        && $_SESSION['username'] !== '';
}

// Protect pages that require login.
function requireLogin(): void
{
    startGameSession();
    sendSecurityHeaders();

    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Generate a CSRF token for forms.
function getCsrfToken(): string
{
    startGameSession();

    if (
        !isset($_SESSION['csrf_token'])
        || !is_string($_SESSION['csrf_token'])
        || strlen($_SESSION['csrf_token']) !== 64
    ) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Check whether a submitted CSRF token is valid.
function validCsrfToken(string $token): bool
{
    startGameSession();

    if (
        strlen($token) !== 64
        || !ctype_xdigit($token)
        || !isset($_SESSION['csrf_token'])
        || !is_string($_SESSION['csrf_token'])
    ) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

// Escape text before displaying it in HTML.
function escapeHtml(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Safely read a string from submitted form data.
// Arrays and other unexpected values are rejected.
function getPostString(string $key, int $maxLength = 200): ?string
{
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
        return null;
    }

    $value = trim($_POST[$key]);

    if (strlen($value) > $maxLength) {
        return null;
    }

    return $value;
}

// Check whether the request uses the expected HTTP method.
function isPostRequest(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
