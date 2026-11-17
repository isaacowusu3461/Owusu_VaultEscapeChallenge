
<?php
// Authentication helpers for Vault Escape Challenge.
// These functions are shared by the login and game pages.

// Start the session only when it has not already started.
function startGameSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_httponly', '1');

        // Use secure cookies when the site is running over HTTPS.
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
}

// Check whether the player is logged in.
function isLoggedIn(): bool
{
    return isset($_SESSION['username'])
        && is_string($_SESSION['username'])
        && $_SESSION['username'] !== '';
}

// Protect pages that should only be accessed after login.
function requireLogin(): void
{
    startGameSession();

    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Create a token for forms that change session state.
function getCsrfToken(): string
{
    startGameSession();

    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Check that a submitted token matches the session token.
function validCsrfToken(string $token): bool
{
    startGameSession();

    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Escape values before displaying them on a page.
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
