
<?php
// Adaptive gameplay functions.
// These rules adjust feedback based on the player's performance.

// Count how long the player has spent in the current room.
function getRoomElapsedSeconds(int $roomNumber): int
{
    $startedAt = $_SESSION['room_started_at'][$roomNumber] ?? time();

    return max(0, time() - (int) $startedAt);
}

// Return guidance for the current room.
function getAdaptiveFeedback(int $roomNumber): string
{
    $wrong = (int) ($_SESSION['wrong_attempts'][$roomNumber] ?? 0);
    $seconds = getRoomElapsedSeconds($roomNumber);
    $usedHint = !empty($_SESSION['hints_used'][$roomNumber]);

    if ($wrong >= 3) {
        $messages = [
            1 => 'Try solving the multiplication and division first. Then combine the results and subtract 5.',
            2 => 'Use the alphabet positions one at a time. The first number, 22, gives you V.',
            3 => 'Compare consecutive numbers. The sequence doubles each time.'
        ];

        return $messages[$roomNumber] ?? 'Review the puzzle carefully.';
    }

    if ($wrong >= 2) {
        $messages = [
            1 => 'Check the order of operations before calculating the final number.',
            2 => 'Write down the alphabet and match each number to its letter.',
            3 => 'Look for a multiplication pattern between the numbers.'
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

// Classify the player's problem-solving approach.
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

// Explain which factors are being considered.
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
