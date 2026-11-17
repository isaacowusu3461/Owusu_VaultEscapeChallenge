
<?php
// Game functions for Vault Escape Challenge.
// The game uses PHP sessions to track rooms, scores, hints, and time.

// Return the puzzle details for a room.
function getRoom(int $roomNumber): ?array
{
    $rooms = [
        1 => [
            'title' => 'The Math Cipher',
            'story' => 'The first door is protected by a numerical lock. Solve the equation to unlock it.',
            'question' => 'A vault code follows this rule: (8 x 4) + (18 / 3) - 5. What is the code?',
            'answer' => '33',
            'placeholder' => 'Enter the number',
            'hint' => 'Follow the order of operations. Multiply and divide first, then add and subtract.'
        ],
        2 => [
            'title' => 'The Symbol Decoder',
            'story' => 'You have entered a room filled with strange symbols. Decode the message to open the next door.',
            'question' => 'Using A = 1, B = 2, C = 3, and so on, decode 22 - 1 - 21 - 12 - 20.',
            'answer' => 'VAULT',
            'placeholder' => 'Enter the decoded word',
            'hint' => 'Replace each number with its matching letter in the alphabet. For example, 22 represents V.'
        ],
        3 => [
            'title' => 'The Pattern Lock',
            'story' => 'This is the final door. The security system displays a number pattern.',
            'question' => 'Find the next number in the sequence: 3, 6, 12, 24, 48, ?',
            'answer' => '96',
            'placeholder' => 'Enter the next number',
            'hint' => 'Look at how each number changes. Every number is multiplied by the same value.'
        ]
    ];

    return $rooms[$roomNumber] ?? null;
}

// Set up game values when they are missing.
function initializeGame(): void
{
    if (!isset($_SESSION['room'])) {
        $_SESSION['room'] = 1;
    }

    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 1000;
    }

    if (!isset($_SESSION['wrong_attempts'])) {
        $_SESSION['wrong_attempts'] = [];
    }

    if (!isset($_SESSION['hints_used'])) {
        $_SESSION['hints_used'] = [];
    }

    if (!isset($_SESSION['game_completed'])) {
        $_SESSION['game_completed'] = false;
    }

    // The timer starts when the player first enters the game.
    if (!isset($_SESSION['game_started_at'])) {
        $_SESSION['game_started_at'] = time();
    }

    if (!isset($_SESSION['game_finished_at'])) {
        $_SESSION['game_finished_at'] = null;
    }

    if (!isset($_SESSION['time_bonus'])) {
        $_SESSION['time_bonus'] = 0;
    }
}

// Check an answer without worrying about capitalization or extra spaces.
function isCorrectAnswer(string $submitted, string $expected): bool
{
    return strcasecmp(trim($submitted), $expected) === 0;
}

// Record a wrong answer and deduct 50 points.
function recordWrongAnswer(int $roomNumber): void
{
    if (!isset($_SESSION['wrong_attempts'][$roomNumber])) {
        $_SESSION['wrong_attempts'][$roomNumber] = 0;
    }

    $_SESSION['wrong_attempts'][$roomNumber]++;

    // The score cannot become negative from penalties.
    $_SESSION['score'] = max(0, (int) $_SESSION['score'] - 50);
}

// Check whether the hint was already unlocked.
function hasUsedHint(int $roomNumber): bool
{
    return !empty($_SESSION['hints_used'][$roomNumber]);
}

// Charge for a hint only the first time it is requested.
function requestHint(int $roomNumber): bool
{
    if (getRoom($roomNumber) === null || hasUsedHint($roomNumber)) {
        return false;
    }

    $_SESSION['hints_used'][$roomNumber] = true;

    $_SESSION['score'] = max(0, (int) $_SESSION['score'] - 100);

    return true;
}

// Count all wrong answers across the three rooms.
function getTotalWrongAttempts(): int
{
    return array_sum($_SESSION['wrong_attempts'] ?? []);
}

// Count how many hints were unlocked.
function getTotalHintsUsed(): int
{
    return count(array_filter($_SESSION['hints_used'] ?? []));
}

// Calculate how long the player has been playing.
function getElapsedSeconds(): int
{
    if (!isset($_SESSION['game_started_at'])) {
        return 0;
    }

    $start = (int) $_SESSION['game_started_at'];

    // Once the game ends, the timer stops permanently.
    $end = $_SESSION['game_finished_at'] ?? time();

    return max(0, (int) $end - $start);
}

// Convert seconds into a readable time.
function formatGameTime(int $seconds): string
{
    $minutes = intdiv($seconds, 60);
    $remainingSeconds = $seconds % 60;

    return sprintf('%02d:%02d', $minutes, $remainingSeconds);
}

// Award a bonus based on how quickly the player escapes.
function calculateTimeBonus(int $elapsedSeconds): int
{
    if ($elapsedSeconds < 120) {
        return 300;
    }

    if ($elapsedSeconds < 300) {
        return 200;
    }

    if ($elapsedSeconds < 600) {
        return 100;
    }

    return 0;
}

// Move the player to the next room after a correct answer.
function completeRoom(int $roomNumber): void
{
    // Only the current room can be completed.
    if (
        !empty($_SESSION['game_completed'])
        || $roomNumber !== (int) ($_SESSION['room'] ?? 0)
    ) {
        return;
    }

    if ($roomNumber === 3) {
        $_SESSION['game_completed'] = true;
        $_SESSION['room'] = 4;

        // Save the finish time before calculating the bonus.
        $_SESSION['game_finished_at'] = time();

        $elapsedSeconds = getElapsedSeconds();
        $bonus = calculateTimeBonus($elapsedSeconds);

        $_SESSION['time_bonus'] = $bonus;

        // This code runs only when Room 3 is first completed.
        $_SESSION['score'] = (int) $_SESSION['score'] + $bonus;
    } else {
        $_SESSION['room'] = $roomNumber + 1;
    }
}
