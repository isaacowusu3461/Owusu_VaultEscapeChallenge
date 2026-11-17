
<?php
// Player dashboard.
// Only authenticated players can open this page.

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$username = $_SESSION['username'];
$score = $_SESSION['score'] ?? 1000;
$room = $_SESSION['room'] ?? 1;
$completed = $_SESSION['game_completed'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Player Dashboard</p>
    </header>

    <main>
        <section class="dashboard-container">
            <h2>Welcome, <?= escapeHtml($username) ?>!</h2>

            <p>
                You are now logged in and ready to enter the vault.
                Solve three different puzzles to complete the challenge.
            </p>

            <div class="player-stats">
                <div class="stat-box">
                    <h3>Current Score</h3>
                    <p><?= (int) $score ?> points</p>
                </div>

                <div class="stat-box">
                    <h3>Current Room</h3>
                    <p>
                        <?= $completed ? 'Completed' : 'Room ' . (int) $room ?>
                    </p>
                </div>

                <div class="stat-box">
                    <h3>Game Status</h3>
                    <p><?= $completed ? 'Escaped' : 'Not Completed' ?></p>
                </div>
            </div>

            <h3>Game Rules</h3>

            <ul>
                <li>Complete all three puzzle rooms.</li>
                <li>Each wrong answer costs 50 points.</li>
                <li>Each hint costs 100 points.</li>
                <li>Try to finish with the highest possible score.</li>
            </ul>

            <div class="dashboard-actions">
                <a href="game.php" class="action-button">
                    Start Game
                </a>

                <a href="leaderboard.php" class="action-button secondary-button">
                    Leaderboard
                </a>

                <form method="post" action="logout.php">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapeHtml(getCsrfToken()) ?>"
                    >

                    <button type="submit" class="logout-button">
                        Logout
                    </button>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
