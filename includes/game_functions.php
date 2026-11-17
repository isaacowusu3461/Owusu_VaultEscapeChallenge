
<?php
// Game functions for Vault Escape Challenge.
// Each room has its own puzzle and expected answer.

// Return the details for a specific room.
function getRoom(int $roomNumber): ?array
{
    $rooms = [
        1 => [
            'title' => 'The Math Cipher',
            'story' => 'The first door is protected by a numerical lock. Solve the equation to unlock it.',
            'question' => 'A vault code follows this rule: (8 x 4) + (18 / 3) - 5. What is the code?',
            'answer' => '33',
            'placeholder' => 'Enter the number'
        ],
        2 => [
            'title' => 'The Symbol Decoder',
            'story' => 'You have entered a room filled with strange symbols. Decode the message to open the next door.',
            'question' => 'Using A = 1, B = 2, C = 3, and so on, decode 22 - 1 - 21 - 12 - 20.',
            'answer' => 'VAULT',
            'placeholder' => 'Enter the decoded word'
        ],
        3 => [
            'title' => 'The Pattern Lock',
            'story' => 'This is the final door. The security system displays a number pattern.',
            'question' => 'Find the next number in the sequence: 3, 6, 12, 24, 48, ?',
            'answer' => '96',
            'placeholder' => 'Enter the next number'
        ]
    ];

    return $rooms[$roomNumber] ?? null;
}

// Prepare the game session if the player has not started.
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
}

// Compare the submitted answer with the correct answer.
function isCorrectAnswer(string $submitted, string $expected): bool
{
    return strcasecmp(trim($submitted), $expected) === 0;
}

// Apply the penalty for an incorrect answer.
function recordWrongAnswer(int $roomNumber): void
{
    if (!isset($_SESSION['wrong_attempts'][$roomNumber])) {
        $_SESSION['wrong_attempts'][$roomNumber] = 0;
    }

    $_SESSION['wrong_attempts'][$roomNumber]++;

    // Scores cannot become negative.
    $_SESSION['score'] = max(0, (int) $_SESSION['score'] - 50);
}

// Move to the next room after a correct answer.
function completeRoom(int $roomNumber): void
{
    if ($roomNumber === 3) {
        $_SESSION['game_completed'] = true;
        $_SESSION['room'] = 4;
    } else {
        $_SESSION['room'] = $roomNumber + 1;
    }
}
