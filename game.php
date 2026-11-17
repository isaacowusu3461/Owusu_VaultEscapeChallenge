
<?php
// Main game page.
// Players must solve each room before moving forward.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/game_functions.php';

requireLogin();
initializeGame();

$username = $_SESSION['username'];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');
    $answer = trim((string) ($_POST['answer'] ?? ''));

    if (!validCsrfToken($token)) {
        $message = 'The form has expired. Please try again.';
        $messageType = 'error';
    } elseif ($_SESSION['game_completed']) {
        $message = 'You have already completed the game.';
        $messageType = 'success';
    } elseif ($answer === '') {
        $message = 'Please enter an answer before submitting.';
        $messageType = 'error';
    } elseif (strlen($answer) > 100) {
        $message = 'Your answer is too long.';
        $messageType = 'error';
    } else {
        $currentRoom = (int) $_SESSION['room'];
        $roomData = getRoom($currentRoom);

        if ($roomData !== null) {
            if (isCorrectAnswer($answer, $roomData['answer'])) {
                completeRoom($currentRoom);

                if ($_SESSION['game_completed']) {
                    header('Location: game.php?finished=1');
                    exit;
                }

                header('Location: game.php?unlocked=1');
                exit;
            }

            recordWrongAnswer($currentRoom);
            $message = 'That answer is incorrect. You lost 50 points. Try again.';
            $messageType = 'error';
        }
    }
}

$completed = (bool) $_SESSION['game_completed'];
$currentRoom = (int) $_SESSION['room'];
$score = (int) $_SESSION['score'];
$roomData = $completed ? null : getRoom($currentRoom);

if (!$completed && $roomData === null) {
    http_response_code(500);
    exit('The current room could not be loaded.');
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['unlocked']) && !$completed) {
        $message = 'Correct answer! The door unlocked. Welcome to the next room.';
        $messageType = 'success';
    }

    if (isset($_GET['finished']) && $completed) {
        $message = 'Congratulations! You escaped the vault.';
        $messageType = 'success';
    }
}

$wrongAttempts = $_SESSION['wrong_attempts'][$currentRoom] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game - Vault Escape Challenge</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header>
        <h1>Vault Escape Challenge</h1>
        <p>Escape Room Adventure</p>
    </header>

    <main>
        <section class="game-container">

            <div class="game-top">
                <p><strong>Player:</strong> <?= escapeHtml($username) ?></p>
                <p><strong>Score:</strong> <?= $score ?></p>
            </div>

            <?php if ($message !== ''): ?>
                <div
                    class="<?= $messageType === 'success' ? 'success-message' : 'error-message' ?>"
                    role="status"
                >
                    <?= escapeHtml($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($completed): ?>

                <div class="completion-box">
                    <h2>Vault Escaped!</h2>

                    <p>
                        Congratulations, <?= escapeHtml($username) ?>!
                        You successfully completed all three rooms.
                    </p>

                    <p>
                        <strong>Final Score:</strong>
                        <?= $score ?> points
                    </p>

                    <p>
                        Your escape has been recorded for this session.
                    </p>

                    <a href="dashboard.php" class="action-button">
                        Back to Dashboard
                    </a>
                </div>

            <?php else: ?>

                <p class="room-progress">
                    Room <?= $currentRoom ?> of 3
                </p>

                <div class="vault-door">
                    <span class="door-icon" aria-hidden="true">🔒</span>
                    <p>Door <?= $currentRoom ?> Locked</p>
                </div>

                <h2><?= escapeHtml($roomData['title']) ?></h2>

                <p><?= escapeHtml($roomData['story']) ?></p>

                <div class="puzzle-box">
                    <h3>Puzzle <?= $currentRoom ?></h3>

                    <p><?= escapeHtml($roomData['question']) ?></p>
                </div>

                <form method="post" action="game.php" class="puzzle-form">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= escapeHtml(getCsrfToken()) ?>"
                    >

                    <label for="answer">Your Answer</label>

                    <input
                        type="text"
                        id="answer"
                        name="answer"
                        placeholder="<?= escapeHtml($roomData['placeholder']) ?>"
                        maxlength="100"
                        autocomplete="off"
                        required
                    >

                    <button type="submit">Unlock Door</button>
                </form>

                <p class="attempt-count">
                    Wrong attempts in this room:
                    <?= (int) $wrongAttempts ?>
                </p>

                <p class="game-note">
                    Each wrong answer costs 50 points.
                </p>

            <?php endif; ?>

            <div class="game-navigation">
                <a href="dashboard.php">Back to Dashboard</a>
            </div>

        </section>
    </main>

    <footer>
        <p>CSC 6370 - Web Programming | Isaac Owusu</p>
    </footer>

</body>
</html>
