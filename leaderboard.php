
<?php
// Session-based leaderboard for Vault Escape Challenge.
// Players must be logged in to view the results.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/leaderboard_functions.php';

requireLogin();

initializeLeaderboard();

$results = getLeaderboardResults();
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/leaderboard.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Player Leaderboard</p>
    </header>

    <main>
        <section class="leaderboard-container">
            <h2>Leaderboard</h2>

            <p>
                Welcome, <?= escapeHtml($username) ?>.
                Here are the completed games recorded in this session.
            </p>

            <?php if (empty($results)): ?>

                <div class="empty-leaderboard">
                    <h3>No Completed Games Yet</h3>

                    <p>
                        Complete all three puzzle rooms to earn a place
                        on the leaderboard.
                    </p>

                    <a href="game.php" class="action-button">
                        Play Game
                    </a>
                </div>

            <?php else: ?>

                <div class="leaderboard-table-wrapper">
                    <table class="leaderboard-table">
                        <thead>
                            <tr>
                                <th scope="col">Rank</th>
                                <th scope="col">Player</th>
                                <th scope="col">Score</th>
                                <th scope="col">Time</th>
                                <th scope="col">Wrong</th>
                                <th scope="col">Hints</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($results as $index => $result): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>

                                    <td>
                                        <?= escapeHtml($result['username']) ?>
                                    </td>

                                    <td>
                                        <?= (int) $result['score'] ?>
                                    </td>

                                    <td>
                                        <?= escapeHtml(
                                            formatLeaderboardTime(
                                                (int) $result['duration']
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= (int) $result['wrong_attempts'] ?>
                                    </td>

                                    <td>
                                        <?= (int) $result['hints_used'] ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <p class="leaderboard-note">
                    Results are ranked by highest score.
                    If scores are equal, the faster completion ranks first.
                </p>

            <?php endif; ?>

            <p class="leaderboard-note">
                Note: This leaderboard uses PHP sessions.
                Results are not shared between different browser sessions
                and are cleared when the session ends.
            </p>

            <div class="leaderboard-navigation">
                <a href="dashboard.php" class="action-button">
                    Back to Dashboard
                </a>
            </div>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
