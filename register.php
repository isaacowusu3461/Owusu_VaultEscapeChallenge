<?php
// Registration page for Vault Escape Challenge.
// Validate form data in PHP before saving an account.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/user_functions.php';

startGameSession();
sendSecurityHeaders();

$username = '';
$email = '';
$errors = [];
$successMessage = '';

if (isPostRequest()) {
    $token = getPostString('csrf_token', 64);
    $submittedUsername = getPostString('username', 20);
    $submittedEmail = getPostString('email', 254);

    // Do not trim passwords because spaces may be intentional.
    $password = getPostRawString('password', 128);
    $confirmPassword = getPostRawString('confirm_password', 128);

    if ($token === null || !validCsrfToken($token)) {
        $errors[] = 'The form has expired. Please try again.';
    } elseif (
        $submittedUsername === null
        || $submittedEmail === null
        || $password === null
        || $confirmPassword === null
    ) {
        $errors[] = 'Invalid form data. Please check your entries.';
    } else {
        $username = $submittedUsername;
        $email = $submittedEmail;

        $errors = validateRegistration(
            $username,
            $email,
            $password,
            $confirmPassword
        );

        if (empty($errors)) {
            try {
                $result = registerUser($username, $email, $password);

                if ($result['success']) {
                    $successMessage = $result['message'];
                    $username = '';
                    $email = '';

                    // Use a fresh token after registration.
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                } else {
                    $errors[] = $result['message'];
                }
            } catch (RuntimeException $exception) {
                error_log($exception->getMessage());

                $errors[] =
                    'Registration is temporarily unavailable. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Create your account to begin playing.</p>
    </header>

    <main>
        <section class="form-container">
            <h2>Create an Account</h2>

            <?php if (!empty($errors)): ?>
                <div class="error-message" role="alert">
                    <strong>Please fix the following:</strong>

                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= escapeHtml($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($successMessage !== ''): ?>
                <div class="success-message" role="status">
                    <p><?= escapeHtml($successMessage) ?></p>
                    <p>
                        <a href="login.php">Continue to Login</a>
                    </p>
                </div>
            <?php else: ?>

                <form method="post" action="register.php">
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
                        minlength="3"
                        maxlength="20"
                        autocomplete="username"
                        required
                    >

                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= escapeHtml($email) ?>"
                        maxlength="254"
                        autocomplete="email"
                        required
                    >

                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        minlength="8"
                        maxlength="128"
                        autocomplete="new-password"
                        required
                    >

                    <label for="confirm_password">Confirm Password</label>
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        minlength="8"
                        maxlength="128"
                        autocomplete="new-password"
                        required
                    >

                    <button type="submit">Register</button>
                </form>

            <?php endif; ?>

            <p class="form-link">
                Already have an account?
                <a href="login.php">Login here</a>
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
