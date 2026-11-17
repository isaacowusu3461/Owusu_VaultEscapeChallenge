
<?php
// Login page for Vault Escape Challenge.
// The password is checked against the stored hash.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/user_functions.php';

startGameSession();

// A logged-in player should go straight to the dashboard.
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$username = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!validCsrfToken($token)) {
        $error = 'The form has expired. Please try again.';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter your username and password.';
    } else {
        try {
            $users = loadUsers();
            $matchedUser = null;

            // Find the registered account.
            foreach ($users as $user) {
                if (
                    isset($user['username'], $user['password'])
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
                // Create a new session ID after successful login.
                session_regenerate_id(true);

                $_SESSION['username'] = $matchedUser['username'];
                $_SESSION['login_time'] = time();

                // Start each new login with a fresh game.
                $_SESSION['room'] = 1;
                $_SESSION['score'] = 1000;
                $_SESSION['wrong_attempts'] = [];
                $_SESSION['hints_used'] = [];
                $_SESSION['game_completed'] = false;

                header('Location: dashboard.php');
                exit;
            }

            // Use one message for both unknown users and wrong passwords.
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
