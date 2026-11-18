<?php
// Login page for Vault Escape Challenge.
// Passwords are checked against their stored hashes.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/user_functions.php';
require_once __DIR__ . '/includes/game_functions.php';

startGameSession();
sendSecurityHeaders();

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$username = '';
$error = '';

if (isPostRequest()) {
    $token = getPostString('csrf_token', 64);
    $submittedUsername = getPostString('username', 20);
    $password = getPostRawString('password', 128);

    if ($token === null || !validCsrfToken($token)) {
        $error = 'The form has expired. Please try again.';
    } elseif (
        $submittedUsername === null
        || $password === null
    ) {
        $error = 'Invalid form data. Please try again.';
    } elseif ($submittedUsername === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        $username = $submittedUsername;

        try {
            $users = loadUsers();
            $matchedUser = null;

            foreach ($users as $user) {
                if (
                    is_array($user)
                    && isset($user['username'], $user['password'])
                    && is_string($user['username'])
                    && is_string($user['password'])
                    && strcasecmp($user['username'], $username) === 0
                ) {
                    $matchedUser = $user;
                    break;
                }
            }

            if (
                $matchedUser !== null
                && password_verify($password, $matchedUser['password'])
            ) {
                session_regenerate_id(true);

                // Clear old login data before starting a new game.
                $_SESSION = [];
                $_SESSION['username'] = $matchedUser['username'];
                $_SESSION['login_time'] = time();

                initializeGame();

                header('Location: dashboard.php');
                exit;
            }

            $error = 'Invalid username or password.';
        } catch (RuntimeException $exception) {
            error_log($exception->getMessage());
            $error = 'Login is temporarily unavailable. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Log in to enter the vault.</p>
    </header>

    <main>
        <section class="form-container">
            <h2>Player Login</h2>

            <?php if ($error !== ''): ?>
                <div class="error-message" role="alert">
                    <?= escapeHtml($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= escapeHtml(getCsrfToken()) ?>"
                >

                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= escapeHtml($username) ?>"
                    autocomplete="username"
                    maxlength="20"
                    required
                >

                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    maxlength="128"
                    required
                >

                <button type="submit">Login</button>
            </form>

            <p class="form-link">
                Don't have an account?
                <a href="register.php">Register here</a>
            </p>

            <p class="form-link">
                <a href="index.php">Back to Home</a>
            </p>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
