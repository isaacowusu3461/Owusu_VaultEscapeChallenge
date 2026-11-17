
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

// Give feedback based on the player's attempts and time.
function getAdaptiveFeedback(int $roomNumber): string
{
    $wrong = (int) ($_SESSION['wrong_attempts'][$roomNumber] ?? 0);
    $seconds = getRoomElapsedSeconds($roomNumber);
    $usedHint = !empty($_SESSION['hints_used'][$roomNumber]);

    if ($wrong >= 3) {
        $messages = [
            1 => 'Try solving the multiplication and division first. Then combine the results and subtract 5.',
            2 => 'Convert each number into its alphabet letter, one at a time.',
            3 => 'Compare each number with the one before it to find the rule.'
        ];

        return $messages[$roomNumber] ?? 'Review the puzzle carefully.';
    }

    if ($wrong >= 2) {
        $messages = [
            1 => 'Check the order of operations before calculating.',
            2 => 'Write down the alphabet and match the numbers to letters.',
            3 => 'Look for a repeated mathematical operation.'
        ];

        return $messages[$roomNumber] ?? 'Try a different approach.';
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
