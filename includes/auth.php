<?php
// Shared authentication and security functions.

function startGameSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

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

// Send browser security headers before page output.
function sendSecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Cache-Control: no-store, private');

    // The progress bar currently uses an inline width style.
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

function isLoggedIn(): bool
{
    return isset($_SESSION['username'])
        && is_string($_SESSION['username'])
        && $_SESSION['username'] !== '';
}

function requireLogin(): void
{
    startGameSession();
    sendSecurityHeaders();

    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Create a token for forms that change data.
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

function escapeHtml(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}

// Read a form value without changing its contents.
// This is important for passwords, where spaces may be intentional.
function getPostRawString(string $key, int $maxLength = 200): ?string
{
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
        return null;
    }

    if (strlen($_POST[$key]) > $maxLength) {
        return null;
    }

    return $_POST[$key];
}

// Trim ordinary form values such as usernames and answers.
function getPostString(string $key, int $maxLength = 200): ?string
{
    $value = getPostRawString($key, $maxLength);

    return $value === null ? null : trim($value);
}

function isPostRequest(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
