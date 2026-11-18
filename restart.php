
<?php
// Restart a game without logging out.
// Completed leaderboard results are kept in the session.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/game_functions.php';

requireLogin();
initializeGame();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');

    if (!validCsrfToken($token)) {
        http_response_code(403);
        $error = 'The restart request could not be verified.';
    } else {
        resetGame();

        // Redirect so refreshing the page does not restart again.
        header('Location: game.php');
        exit;
    }
}

$completed = !empty($_SESSION['game_completed']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restart Game - Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Start a New Game</p>
    </header>

    <main>
        <section class="game-container">
            <h2>Restart Game</h2>

            <?php if ($error !== ''): ?>
                <div class="error-message" role="alert">
                    <?= escapeHtml($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($completed): ?>
                <p>
                    You have completed the vault. You can start a new
                    game and try to improve your score.
                </p>
            <?php else: ?>
                <p>
                    You still have a game in progress.
                    Restarting will erase your current room progress,
                    score, hints, and timer.
                </p>
            <?php endif; ?>

            <p>
                Your completed leaderboard results will be kept
                during this login session.
            </p>

            <form method="post" action="restart.php">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= escapeHtml(getCsrfToken()) ?>"
                >

                <button type="submit" class="action-button">
                    Confirm Restart
                </button>
            </form>

            <div class="game-navigation">
                <a href="dashboard.php">Cancel and Return to Dashboard</a>
            </div>
        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
