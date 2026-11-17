
<?php
// Vault Escape Challenge homepage.
// CSC 6370 - Web Programming
// Isaac Owusu

require_once __DIR__ . '/includes/auth.php';

startGameSession();

$loggedIn = isLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Can you solve the puzzles and escape the vault?</p>
    </header>

    <main>
        <section class="welcome">
            <h2>Welcome to the Vault</h2>

            <p>
                You have been locked inside a mysterious vault.
                To escape, you must solve three different puzzles.
                Each correct answer brings you closer to freedom.
            </p>

            <h3>How the Game Works</h3>

            <ul>
                <li>Start with 1,000 points.</li>
                <li>Solve three puzzle rooms.</li>
                <li>Lose 50 points for each wrong answer.</li>
                <li>Lose 100 points when you request a hint.</li>
                <li>Finish the game and check your score.</li>
            </ul>

            <div class="actions">
                <?php if ($loggedIn): ?>
                    <a href="dashboard.php">Go to Dashboard</a>
                <?php else: ?>
                    <a href="register.php">Create Account</a>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
