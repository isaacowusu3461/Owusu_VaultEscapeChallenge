
<?php
// Graduate AI tests for Vault Escape Challenge.
// Run with: php tests/graduate_ai_test.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/game_functions.php';

startGameSession();

$passed = 0;
$failed = 0;

// Print the result of each test.
function checkAiTest(string $name, bool $condition): void
{
    global $passed, $failed;

    if ($condition) {
        echo "PASS: $name\n";
        $passed++;
    } else {
        echo "FAIL: $name\n";
        $failed++;
    }
}

// Set up a room with specific performance values.
function setAiTestState(
    int $wrong,
    bool $hintUsed,
    int $seconds,
    string $difficulty = 'normal'
): void {
    $_SESSION = [];

    $_SESSION['room'] = 1;
    $_SESSION['score'] = 1000;
    $_SESSION['wrong_attempts'] = [1 => $wrong];
    $_SESSION['hints_used'] = [1 => $hintUsed];
    $_SESSION['room_durations'] = [1 => $seconds];
    $_SESSION['room_started_at'] = [1 => time() - $seconds];
    $_SESSION['room_difficulty'] = [1 => $difficulty];
    $_SESSION['difficulty_reasons'] = [
        1 => 'All players begin with the standard puzzle.'
    ];
    $_SESSION['game_completed'] = false;
}

// AI-1: Fast and accurate performance should increase difficulty.
setAiTestState(0, false, 60);

$decision = calculateNextDifficulty(1);

checkAiTest(
    'AI-1: Fast and accurate player receives Hard difficulty',
    $decision['level'] === 'hard'
);

// AI-2: Several mistakes should reduce difficulty.
setAiTestState(3, true, 180);

$decision = calculateNextDifficulty(1);

checkAiTest(
    'AI-2: Struggling player receives Easy difficulty',
    $decision['level'] === 'easy'
);

// AI-3: Average performance should keep Normal difficulty.
setAiTestState(1, false, 150);

$decision = calculateNextDifficulty(1);

checkAiTest(
    'AI-3: Average player receives Normal difficulty',
    $decision['level'] === 'normal'
);

// AI-4: Exactly 120 seconds should not qualify for Hard.
setAiTestState(0, false, 120);

$decision = calculateNextDifficulty(1);

checkAiTest(
    'AI-4: Exactly 120 seconds selects Normal difficulty',
    $decision['level'] === 'normal'
);

// AI-5: Exactly 300 seconds should qualify for Easy.
setAiTestState(0, false, 300);

$decision = calculateNextDifficulty(1);

checkAiTest(
    'AI-5: Exactly 300 seconds selects Easy difficulty',
    $decision['level'] === 'easy'
);

// Check the additional rule for two mistakes with a hint.
setAiTestState(2, true, 60);

$decision = calculateNextDifficulty(1);

checkAiTest(
    'Two mistakes with a hint select Easy difficulty',
    $decision['level'] === 'easy'
);

// Check that zero mistakes give general instructions.
setAiTestState(0, false, 10);

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'Zero mistakes display general instructions',
    str_contains($feedback, 'Read the puzzle carefully')
);

// Check feedback after the first wrong answer.
setAiTestState(1, false, 10);

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'One mistake displays encouraging feedback',
    str_contains($feedback, 'try again')
);

// Check that two mistakes give a smaller puzzle clue.
setAiTestState(2, false, 10, 'normal');

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'Two mistakes display a puzzle-specific clue',
    str_contains($feedback, 'order of operations')
);

// Check that three mistakes give a more specific clue.
setAiTestState(3, false, 10, 'normal');

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'Three mistakes display a more detailed clue',
    str_contains($feedback, 'subtract 5')
);

// Check that Easy feedback matches the Easy puzzle.
setAiTestState(3, false, 10, 'easy');

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'Easy puzzle receives matching feedback',
    str_contains($feedback, 'Multiply 5 by 4')
);

// Check that Hard feedback matches the Hard puzzle.
setAiTestState(3, false, 10, 'hard');

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'Hard puzzle receives matching feedback',
    str_contains($feedback, 'divide by 4')
);

// Check the time-based hint suggestion.
setAiTestState(0, false, 120);

$feedback = getAdaptiveFeedback(1);

checkAiTest(
    'Long room time suggests using a hint',
    str_contains($feedback, 'Consider using the hint')
);

// Check that the next room receives the selected difficulty.
setAiTestState(0, false, 60);

$_SESSION['username'] = 'testplayer';
$_SESSION['game_started_at'] = time() - 60;
$_SESSION['leaderboard_recorded'] = false;

completeRoom(1);

checkAiTest(
    'Completing a room saves the next difficulty',
    $_SESSION['room'] === 2
        && $_SESSION['room_difficulty'][2] === 'hard'
);

// Check that the reason is saved for the final results.
checkAiTest(
    'Difficulty adjustment explanation is saved',
    !empty($_SESSION['difficulty_reasons'][2])
        && str_contains(
            $_SESSION['difficulty_reasons'][2],
            'quickly'
        )
);

// Check that the actual next puzzle uses Hard difficulty.
$nextRoom = getCurrentRoom();

checkAiTest(
    'Next room loads the selected Hard puzzle',
    $nextRoom !== null
        && $nextRoom['difficulty'] === 'hard'
        && $nextRoom['answer'] === 'SECRET'
);

// Check that a player profile can be generated.
setAiTestState(0, false, 60);

$profile = getPlayerProfile();

checkAiTest(
    'Independent player receives a profile',
    $profile['name'] === 'Independent Solver'
        && !empty($profile['reason'])
);

// Clear test data.
$_SESSION = [];

echo "\n";
echo "Graduate AI Test Summary\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

exit($failed === 0 ? 0 : 1);
