<?php
// Player dashboard for Vault Escape Challenge.
// This page shows the current game and completed-game statistics.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/game_functions.php';
require_once __DIR__ . '/includes/statistics_functions.php';

requireLogin();
initializeGame();

$username = $_SESSION['username'];
$score = (int) $_SESSION['score'];
$currentRoom = (int) $_SESSION['room'];
$completed = (bool) $_SESSION['game_completed'];

// Use the session leaderboard to calculate player statistics.
$results = getLeaderboardResults();
$statistics = getPlayerStatistics($results);
$recentGames = getRecentGames($results);

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
                    <p><?= $statistics['completed_games'] ?></p>
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

            <section class="performance-section">
                <h2>My Performance Summary</h2>

                <p>
                    These statistics are calculated from the games
                    you have completed during this login session.
                </p>

                <?php if ($statistics['completed_games'] === 0): ?>

                    <div class="empty-performance">
                        <p>
                            No completed games yet.
                            Finish all three rooms to see your statistics.
                        </p>
                    </div>

                <?php else: ?>

                    <div class="performance-grid">
                        <div class="performance-card">
                            <h3>Best Score</h3>
                            <p><?= $statistics['best_score'] ?></p>
                        </div>

                        <div class="performance-card">
                            <h3>Average Score</h3>
                            <p><?= $statistics['average_score'] ?></p>
                        </div>

                        <div class="performance-card">
                            <h3>Fastest Escape</h3>
                            <p>
                                <?= escapeHtml(
                                    formatStatisticsTime(
                                        $statistics['fastest_escape']
                                    )
                                ) ?>
                            </p>
                        </div>

                        <div class="performance-card">
                            <h3>Average Completion Time</h3>
                            <p>
                                <?= escapeHtml(
                                    formatStatisticsTime(
                                        $statistics['average_time']
                                    )
                                ) ?>
                            </p>
                        </div>

                        <div class="performance-card">
                            <h3>Total Wrong Answers</h3>
                            <p><?= $statistics['total_wrong_answers'] ?></p>
                        </div>

                        <div class="performance-card">
                            <h3>Total Hints Used</h3>
                            <p><?= $statistics['total_hints_used'] ?></p>
                        </div>
                    </div>

                    <h3>Recent Completed Games</h3>

                    <div class="recent-games-wrapper">
                        <table class="recent-games-table">
                            <thead>
                                <tr>
                                    <th scope="col">Game</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Time</th>
                                    <th scope="col">Wrong</th>
                                    <th scope="col">Hints</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($recentGames as $index => $game): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>

                                        <td>
                                            <?= (int) $game['score'] ?>
                                        </td>

                                        <td>
                                            <?= escapeHtml(
                                                formatStatisticsTime(
                                                    (int) $game['duration']
                                                )
                                            ) ?>
                                        </td>

                                        <td>
                                            <?= (int) $game['wrong_attempts'] ?>
                                        </td>

                                        <td>
                                            <?= (int) $game['hints_used'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </section>

            <p class="game-note">
                Your completed-game statistics are stored in the
                current PHP session. Logging out clears this history.
            </p>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
