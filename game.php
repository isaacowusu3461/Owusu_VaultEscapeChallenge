
<?php
// Main game page for Vault Escape Challenge.
// This page handles answers, hints, and the game timer.

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/game_functions.php';

requireLogin();
initializeGame();

$username = $_SESSION['username'];
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = (string) ($_POST['csrf_token'] ?? '');
    $action = (string) ($_POST['action'] ?? '');

    if (!validCsrfToken($token)) {
        $message = 'The form has expired. Please try again.';
        $messageType = 'error';
    } elseif ($_SESSION['game_completed']) {
        $message = 'You have already completed the game.';
        $messageType = 'success';
    } else {
        $currentRoom = (int) $_SESSION['room'];
        $roomData = getRoom($currentRoom);

        if ($roomData === null) {
            http_response_code(500);
            exit('The current room could not be loaded.');
        }

        if ($action === 'hint') {
            $hintUnlocked = requestHint($currentRoom);

            if ($hintUnlocked) {
                header('Location: game.php?hint=unlocked');
            } else {
                header('Location: game.php?hint=already');
            }

            exit;
        } elseif ($action === 'answer') {
            $answer = trim((string) ($_POST['answer'] ?? ''));

            if ($answer === '') {
                $message = 'Please enter an answer before submitting.';
                $messageType = 'error';
            } elseif (strlen($answer) > 100) {
                $message = 'Your answer is too long.';
                $messageType = 'error';
            } elseif (isCorrectAnswer($answer, $roomData['answer'])) {
                completeRoom($currentRoom);

                if ($_SESSION['game_completed']) {
                    header('Location: game.php?finished=1');
                } else {
                    header('Location: game.php?unlocked=1');
                }

                exit;
            } else {
                recordWrongAnswer($currentRoom);

                $message = 'That answer is incorrect. You lost 50 points. Try again.';
                $messageType = 'error';
            }
        } else {
            $message = 'Invalid game action.';
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

// Show feedback after a successful form submission.
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['unlocked']) && !$completed) {
        $message = 'Correct answer! The door unlocked. Welcome to the next room.';
        $messageType = 'success';
    } elseif (isset($_GET['finished']) && $completed) {
        $message = 'Congratulations! You escaped the vault.';
        $messageType = 'success';
    } elseif (isset($_GET['hint']) && !$completed) {
        if ($_GET['hint'] === 'unlocked') {
            $message = 'Hint unlocked. 100 points were deducted.';
        } else {
            $message = 'You have already unlocked this hint. No additional points were deducted.';
        }

        $messageType = 'success';
    }
}

$wrongAttempts = $_SESSION['wrong_attempts'][$currentRoom] ?? 0;
$hintUsed = !$completed && hasUsedHint($currentRoom);
$totalWrongAttempts = getTotalWrongAttempts();
$totalHintsUsed = getTotalHintsUsed();

$elapsedSeconds = getElapsedSeconds();
$formattedTime = formatGameTime($elapsedSeconds);
$timeBonus = (int) ($_SESSION['time_bonus'] ?? 0);
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
                <p><strong>Time:</strong> <?= escapeHtml($formattedTime) ?></p>
            </div>

            <?php if ($message !== ''): ?>
                <div
                    class="<?= $messageType === 'success' ? 'success-message' : 'error-message' ?>"
                    role="<?= $messageType === 'error' ? 'alert' : 'status' ?>"
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

                    <h3>Final Results</h3>

                    <div class="results-summary">
                        <p><strong>Starting Score:</strong> 1000</p>

                        <p>
                            <strong>Wrong Answers:</strong>
                            <?= $totalWrongAttempts ?>
                            (-<?= $totalWrongAttempts * 50 ?> points)
                        </p>

                        <p>
                            <strong>Hints Used:</strong>
                            <?= $totalHintsUsed ?>
                            (-<?= $totalHintsUsed * 100 ?> points)
                        </p>

                        <p>
                            <strong>Completion Time:</strong>
                            <?= escapeHtml($formattedTime) ?>
                        </p>

                        <p>
                            <strong>Time Bonus:</strong>
                            +<?= $timeBonus ?> points
                        </p>

                        <p class="final-score">
                            <strong>Final Score:</strong>
                            <?= $score ?> points
                        </p>
                    </div>

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

                    <input type="hidden" name="action" value="answer">

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

                <div class="hint-section">
                    <h3>Need Help?</h3>

                    <?php if ($hintUsed): ?>

                        <div class="hint-box">
                            <strong>Your Hint:</strong>
                            <p><?= escapeHtml($roomData['hint']) ?></p>
                        </div>

                        <p class="game-note">
                            This hint has already been unlocked.
                            You will not be charged again.
                        </p>

                    <?php else: ?>

                        <p>
                            You can unlock a hint for this room,
                            but it will cost 100 points.
                        </p>

                        <form method="post" action="game.php">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= escapeHtml(getCsrfToken()) ?>"
                            >

                            <input type="hidden" name="action" value="hint">

                            <button type="submit" class="hint-button">
                                Get Hint (-100 Points)
                            </button>
                        </form>

                    <?php endif; ?>
                </div>

                <div class="game-stats">
                    <p>
                        <strong>Wrong Attempts in This Room:</strong>
                        <?= (int) $wrongAttempts ?>
                    </p>

                    <p>
                        <strong>Total Wrong Answers:</strong>
                        <?= $totalWrongAttempts ?>
                    </p>

                    <p>
                        <strong>Total Hints Used:</strong>
                        <?= $totalHintsUsed ?>
                    </p>

                    <p>
                        <strong>Elapsed Time:</strong>
                        <?= escapeHtml($formattedTime) ?>
                    </p>
                </div>

                <p class="game-note">
                    Each wrong answer costs 50 points.
                    Each room hint costs 100 points.
                    A time bonus is added after completing the final room.
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
