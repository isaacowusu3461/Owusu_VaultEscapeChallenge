
<?php
// Registration page for Vault Escape Challenge.
// All validation is done in PHP before an account is saved.

session_start();

require_once __DIR__ . '/includes/user_functions.php';

$username = '';
$email = '';
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

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
            } else {
                $errors[] = $result['message'];
            }
        } catch (RuntimeException $exception) {
            error_log($exception->getMessage());
            $errors[] = 'Registration is temporarily unavailable. Please try again.';
        }
    }
}

// Escape text before displaying it in HTML.
function escapeText(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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
                            <li><?= escapeText($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($successMessage !== ''): ?>
                <div class="success-message" role="status">
                    <p><?= escapeText($successMessage) ?></p>
                    <p>
                        <a href="login.php">Continue to Login</a>
                    </p>
                </div>
            <?php else: ?>

                <form method="post" action="register.php">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= escapeText($username) ?>"
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
                        value="<?= escapeText($email) ?>"
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
