
<?php
// Player dashboard for Vault Escape Challenge.
// This page shows the current game and navigation options.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/game_functions.php';

requireLogin();
initializeGame();

$username = $_SESSION['username'];
$score = (int) $_SESSION['score'];
$currentRoom = (int) $_SESSION['room'];
$completed = (bool) $_SESSION['game_completed'];

$results = getLeaderboardResults();
$completedGames = count($results);

if ($completed) {
    $gameButtonText = 'View Final Results';
} elseif ($currentRoom > 1) {
    $gameButtonText = 'Continue Game';
} else {
    $gameButtonText = 'Start Game';
}
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
                Solve three puzzle rooms, collect points, and
                escape the vault as quickly as possible.
            </p>

            <div class="player-stats">
                <div class="stat-box">
                    <h3>Current Score</h3>
                    <p><?= $score ?></p>
                </div>

                <div class="stat-box">
                    <h3>Game Progress</h3>
                    <p>
                        <?= $completed ? 'Completed' : 'Room ' . $currentRoom . ' of 3' ?>
                    </p>
                </div>

                <div class="stat-box">
                    <h3>Completed Games</h3>
                    <p><?= $completedGames ?></p>
                </div>
            </div>

            <h3>Game Rules</h3>

            <ul>
                <li>Start each game with 1,000 points.</li>
                <li>Each incorrect answer costs 50 points.</li>
                <li>Each room hint costs 100 points.</li>
                <li>Complete the vault quickly to earn a time bonus.</li>
                <li>The next room's difficulty depends on your performance.</li>
            </ul>

            <?php if ($completed): ?>
                <div class="success-message" role="status">
                    You escaped the vault! You can view your results
                    or play again.
                </div>
            <?php endif; ?>

            <div class="dashboard-actions">
                <a href="game.php" class="action-button">
                    <?= escapeHtml($gameButtonText) ?>
                </a>

                <a href="leaderboard.php" class="action-button secondary-button">
                    View Leaderboard
                </a>

                <a href="restart.php" class="action-button secondary-button">
                    <?= $completed ? 'Play Again' : 'Restart Game' ?>
                </a>

                <form method="post" action="logout.php">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapeHtml(getCsrfToken()) ?>"
                    >

                    <button type="submit" class="logout-button">
                        Log Out
                    </button>
                </form>
            </div>

            <p class="game-note">
                Completed leaderboard results remain available while
                this login session is active. Logging out clears them.
            </p>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
