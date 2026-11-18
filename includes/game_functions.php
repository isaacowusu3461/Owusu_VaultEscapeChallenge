
<?php
// Game functions for Vault Escape Challenge.
// Sessions store progress, scoring, timing, and difficulty.

require_once __DIR__ . '/leaderboard_functions.php';
require_once __DIR__ . '/adaptive_functions.php';

// Return a puzzle for the requested room and difficulty.
function getRoom(int $roomNumber, string $difficulty = 'normal'): ?array
{
    $rooms = [
        1 => [
            'title' => 'The Math Cipher',
            'story' => 'The first door is protected by a numerical lock. Solve the equation to unlock it.',
            'easy' => [
                'question' => 'What is (5 x 4) + 3?',
                'answer' => '23',
                'hint' => 'Multiply 5 by 4, then add 3.'
            ],
            'normal' => [
                'question' => 'A vault code follows this rule: (8 x 4) + (18 / 3) - 5. What is the code?',
                'answer' => '33',
                'hint' => 'Follow the order of operations. Multiply and divide first, then add and subtract.'
            ],
            'hard' => [
                'question' => 'Solve: ((12 x 7) - (36 / 3)) / 4 + 9.',
                'answer' => '27',
                'hint' => 'Work inside the parentheses first, then divide by 4 and add 9.'
            ],
            'placeholder' => 'Enter the number'
        ],
        2 => [
            'title' => 'The Symbol Decoder',
            'story' => 'You have entered a room filled with strange symbols. Decode the message to open the next door.',
            'easy' => [
                'question' => 'Using A = 1, B = 2, C = 3, decode 3 - 1 - 20.',
                'answer' => 'CAT',
                'hint' => 'The first letter is C. Match the other numbers to their letters.'
            ],
            'normal' => [
                'question' => 'Using A = 1, B = 2, C = 3, and so on, decode 22 - 1 - 21 - 12 - 20.',
                'answer' => 'VAULT',
                'hint' => 'Replace each number with its matching letter in the alphabet. For example, 22 represents V.'
            ],
            'hard' => [
                'question' => 'Using A = 1 through Z = 26, decode 19 - 5 - 3 - 18 - 5 - 20.',
                'answer' => 'SECRET',
                'hint' => 'The first number, 19, represents S. Decode the remaining letters.'
            ],
            'placeholder' => 'Enter the decoded word'
        ],
        3 => [
            'title' => 'The Pattern Lock',
            'story' => 'This is the final door. The security system displays a number pattern.',
            'easy' => [
                'question' => 'Find the next number: 5, 10, 15, 20, ?',
                'answer' => '25',
                'hint' => 'The pattern increases by the same amount each time.'
            ],
            'normal' => [
                'question' => 'Find the next number in the sequence: 3, 6, 12, 24, 48, ?',
                'answer' => '96',
                'hint' => 'Look at how each number changes. Every number is multiplied by the same value.'
            ],
            'hard' => [
                'question' => 'Find the next number: 2, 6, 12, 20, 30, ?',
                'answer' => '42',
                'hint' => 'Look at the differences: 4, 6, 8, 10. What difference comes next?'
            ],
            'placeholder' => 'Enter the next number'
        ]
    ];

    if (!isset($rooms[$roomNumber])) {
        return null;
    }

    if (!in_array($difficulty, ['easy', 'normal', 'hard'], true)) {
        return null;
    }

    $room = $rooms[$roomNumber];
    $puzzle = $room[$difficulty];

    return [
        'title' => $room['title'],
        'story' => $room['story'],
        'question' => $puzzle['question'],
        'answer' => $puzzle['answer'],
        'hint' => $puzzle['hint'],
        'placeholder' => $room['placeholder'],
        'difficulty' => $difficulty
    ];
}

// Return the difficulty saved for the current room.
function getCurrentRoomDifficulty(): string
{
    $roomNumber = (int) ($_SESSION['room'] ?? 1);

    return (string) (
        $_SESSION['room_difficulty'][$roomNumber] ?? 'normal'
    );
}

// Return the current puzzle.
function getCurrentRoom(): ?array
{
    $roomNumber = (int) ($_SESSION['room'] ?? 1);

    return getRoom($roomNumber, getCurrentRoomDifficulty());
}

// Set up a game when its session values are missing.
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

    if (!isset($_SESSION['game_started_at'])) {
        $_SESSION['game_started_at'] = time();
    }

    if (!array_key_exists('game_finished_at', $_SESSION)) {
        $_SESSION['game_finished_at'] = null;
    }

    if (!isset($_SESSION['time_bonus'])) {
        $_SESSION['time_bonus'] = 0;
    }

    if (!isset($_SESSION['room_started_at'])) {
        $_SESSION['room_started_at'] = [];
    }

    if (!isset($_SESSION['room_durations'])) {
        $_SESSION['room_durations'] = [];
    }

    if (!isset($_SESSION['room_difficulty'])) {
        $_SESSION['room_difficulty'] = [];
    }

    if (!isset($_SESSION['difficulty_reasons'])) {
        $_SESSION['difficulty_reasons'] = [];
    }

    // Every new game starts at Normal difficulty.
    if (!isset($_SESSION['room_difficulty'][1])) {
        $_SESSION['room_difficulty'][1] = 'normal';
        $_SESSION['difficulty_reasons'][1] =
            'All players begin with the standard puzzle.';
    }

    $currentRoom = (int) $_SESSION['room'];

    if (
        empty($_SESSION['game_completed'])
        && getRoom($currentRoom) !== null
        && !isset($_SESSION['room_started_at'][$currentRoom])
    ) {
        $_SESSION['room_started_at'][$currentRoom] = time();
    }

    initializeLeaderboard();

    if (!isset($_SESSION['leaderboard_recorded'])) {
        $_SESSION['leaderboard_recorded'] = false;
    }
}

// Reset only the current game, keeping login and leaderboard history.
function resetGame(): void
{
    $gameKeys = [
        'room',
        'score',
        'wrong_attempts',
        'hints_used',
        'game_completed',
        'game_started_at',
        'game_finished_at',
        'time_bonus',
        'room_started_at',
        'room_durations',
        'room_difficulty',
        'difficulty_reasons',
        'leaderboard_recorded'
    ];

    foreach ($gameKeys as $key) {
        unset($_SESSION[$key]);
    }

    initializeGame();
}

// Compare answers without case sensitivity or extra spaces.
function isCorrectAnswer(string $submitted, string $expected): bool
{
    return strcasecmp(trim($submitted), $expected) === 0;
}

// Deduct 50 points for an incorrect answer.
function recordWrongAnswer(int $roomNumber): void
{
    if (!isset($_SESSION['wrong_attempts'][$roomNumber])) {
        $_SESSION['wrong_attempts'][$roomNumber] = 0;
    }

    $_SESSION['wrong_attempts'][$roomNumber]++;
    $_SESSION['score'] = max(0, (int) $_SESSION['score'] - 50);
}

// Check whether a room hint was unlocked.
function hasUsedHint(int $roomNumber): bool
{
    return !empty($_SESSION['hints_used'][$roomNumber]);
}

// Unlock a hint and charge only once.
function requestHint(int $roomNumber): bool
{
    if (getRoom($roomNumber) === null || hasUsedHint($roomNumber)) {
        return false;
    }

    $_SESSION['hints_used'][$roomNumber] = true;
    $_SESSION['score'] = max(0, (int) $_SESSION['score'] - 100);

    return true;
}

// Count wrong answers across all rooms.
function getTotalWrongAttempts(): int
{
    return array_sum($_SESSION['wrong_attempts'] ?? []);
}

// Count unlocked hints.
function getTotalHintsUsed(): int
{
    return count(array_filter($_SESSION['hints_used'] ?? []));
}

// Calculate total game time.
function getElapsedSeconds(): int
{
    if (!isset($_SESSION['game_started_at'])) {
        return 0;
    }

    $start = (int) $_SESSION['game_started_at'];
    $end = $_SESSION['game_finished_at'] ?? time();

    return max(0, (int) $end - $start);
}

// Format time as minutes and seconds.
function formatGameTime(int $seconds): string
{
    return sprintf(
        '%02d:%02d',
        intdiv($seconds, 60),
        $seconds % 60
    );
}

// Calculate the time bonus.
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

// Complete the current room and adjust the next difficulty.
function completeRoom(int $roomNumber): void
{
    if (
        !empty($_SESSION['game_completed'])
        || $roomNumber !== (int) ($_SESSION['room'] ?? 0)
    ) {
        return;
    }

    $_SESSION['room_durations'][$roomNumber] =
        getRoomElapsedSeconds($roomNumber);

    if ($roomNumber === 3) {
        $_SESSION['game_completed'] = true;
        $_SESSION['room'] = 4;
        $_SESSION['game_finished_at'] = time();

        $bonus = calculateTimeBonus(getElapsedSeconds());

        $_SESSION['time_bonus'] = $bonus;
        $_SESSION['score'] = (int) $_SESSION['score'] + $bonus;

        recordLeaderboardResult();
    } else {
        $nextRoom = $roomNumber + 1;
        $decision = calculateNextDifficulty($roomNumber);

        $_SESSION['room_difficulty'][$nextRoom] =
            $decision['level'];

        $_SESSION['difficulty_reasons'][$nextRoom] =
            $decision['reason'];

        $_SESSION['room'] = $nextRoom;
        $_SESSION['room_started_at'][$nextRoom] = time();
    }
}
