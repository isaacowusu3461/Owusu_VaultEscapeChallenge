
<?php
// Adaptive gameplay functions.
// These rules use mistakes, hints, and time to adjust the next room.

// Count how long the player has spent in the current room.
function getRoomElapsedSeconds(int $roomNumber): int
{
    $startedAt = $_SESSION['room_started_at'][$roomNumber] ?? time();

    return max(0, time() - (int) $startedAt);
}

// Choose the difficulty for the next room.
function calculateNextDifficulty(int $completedRoom): array
{
    $wrong = (int) ($_SESSION['wrong_attempts'][$completedRoom] ?? 0);
    $hintUsed = !empty($_SESSION['hints_used'][$completedRoom]);
    $seconds = (int) (
        $_SESSION['room_durations'][$completedRoom]
        ?? getRoomElapsedSeconds($completedRoom)
    );

    // Players who need more help get an easier next puzzle.
    if ($wrong >= 3 || ($wrong >= 2 && $hintUsed) || $seconds >= 300) {
        return [
            'level' => 'easy',
            'reason' => 'The previous room took several attempts or extra time, so the next puzzle has been simplified.'
        ];
    }

    // Fast, accurate solutions lead to a harder puzzle.
    if ($wrong === 0 && !$hintUsed && $seconds < 120) {
        return [
            'level' => 'hard',
            'reason' => 'You solved the previous room quickly without mistakes or hints, so the next puzzle is more challenging.'
        ];
    }

    return [
        'level' => 'normal',
        'reason' => 'Your previous performance suggests that the standard puzzle is a suitable challenge.'
    ];
}

// Give feedback based on the player's attempts and current puzzle.
function getAdaptiveFeedback(int $roomNumber): string
{
    $wrong = (int) ($_SESSION['wrong_attempts'][$roomNumber] ?? 0);
    $seconds = getRoomElapsedSeconds($roomNumber);
    $usedHint = !empty($_SESSION['hints_used'][$roomNumber]);

    $difficulty = (string) (
        $_SESSION['room_difficulty'][$roomNumber] ?? 'normal'
    );

    // These messages match the puzzle difficulty being played.
    $feedback = [
        1 => [
            'easy' => [
                'Check the multiplication before adding.',
                'Multiply 5 by 4 first, then add 3.'
            ],
            'normal' => [
                'Remember the order of operations.',
                'Multiply and divide first, then combine the results and subtract 5.'
            ],
            'hard' => [
                'Start with the calculations inside the parentheses.',
                'Calculate both parentheses, subtract, divide by 4, and finally add 9.'
            ]
        ],
        2 => [
            'easy' => [
                'Match each number to its alphabet position.',
                'The first letter is C. Decode the remaining two numbers.'
            ],
            'normal' => [
                'Write down the alphabet and match each number.',
                'The first number represents V. Continue decoding the word.'
            ],
            'hard' => [
                'Convert each number into a letter using A = 1.',
                'The first number represents S. Decode the remaining five letters.'
            ]
        ],
        3 => [
            'easy' => [
                'Check the difference between consecutive numbers.',
                'The numbers increase by 5 each time.'
            ],
            'normal' => [
                'Look for an operation repeated at every step.',
                'Each number is multiplied by 2.'
            ],
            'hard' => [
                'Look at how the differences change.',
                'The differences are 4, 6, 8, and 10. Continue that pattern.'
            ]
        ]
    ];

    $roomFeedback = $feedback[$roomNumber][$difficulty] ?? null;

    // Give a more specific clue after three wrong answers.
    if ($wrong >= 3) {
        return $roomFeedback[1] ?? 'Review the puzzle carefully.';
    }

    // Give a smaller clue after two wrong answers.
    if ($wrong >= 2) {
        return $roomFeedback[0] ?? 'Try a different approach.';
    }

    if ($seconds >= 120 && !$usedHint) {
        return 'You have spent more than two minutes in this room. Consider using the hint if you need help.';
    }

    if ($usedHint) {
        return 'You have unlocked the hint for this room. Use it to guide your next attempt.';
    }

    if ($wrong === 1) {
        return 'That was not the correct answer, but you can try again. Look carefully at the clues.';
    }

    return 'Read the puzzle carefully and enter your answer when you are ready.';
}

// Classify the player's overall problem-solving approach.
function getPlayerProfile(): array
{
    $wrong = array_sum($_SESSION['wrong_attempts'] ?? []);
    $hints = count(array_filter($_SESSION['hints_used'] ?? []));

    if ($hints >= 2) {
        return [
            'name' => 'Hint-Guided Solver',
            'reason' => 'You used multiple hints to work through the puzzles.'
        ];
    }

    if ($wrong >= 4) {
        return [
            'name' => 'Persistent Solver',
            'reason' => 'You continued trying different answers even when the puzzles were challenging.'
        ];
    }

    if ($wrong <= 1 && $hints === 0) {
        return [
            'name' => 'Independent Solver',
            'reason' => 'You solved the puzzles with very few mistakes and without using hints.'
        ];
    }

    return [
        'name' => 'Careful Explorer',
        'reason' => 'You worked through the puzzles with some trial and error.'
    ];
}

// Explain what the adaptive feedback is measuring.
function getAdaptationExplanation(int $roomNumber): string
{
    $wrong = (int) ($_SESSION['wrong_attempts'][$roomNumber] ?? 0);
    $seconds = getRoomElapsedSeconds($roomNumber);
    $hintUsed = !empty($_SESSION['hints_used'][$roomNumber]);

    $minutes = intdiv($seconds, 60);
    $remainingSeconds = $seconds % 60;

    $timeText = sprintf('%02d:%02d', $minutes, $remainingSeconds);

    return 'Feedback is based on '
        . $wrong . ' wrong answer(s), '
        . $timeText . ' spent in this room, and '
        . ($hintUsed ? 'a hint already used.' : 'no hint used yet.');
}
