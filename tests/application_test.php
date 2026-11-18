<?php
// Automated application tests for Vault Escape Challenge.
// Run with: php tests/application_test.php

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/user_functions.php';
require_once __DIR__ . '/../includes/game_functions.php';
require_once __DIR__ . '/../includes/statistics_functions.php';

// Start the session before printing anything.
startGameSession();

$passed = 0;
$failed = 0;

// Print a simple result for each test.
function checkTest(string $name, bool $condition): void
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

// Keep registration tests away from real account data.
$testDirectory = sys_get_temp_dir()
    . '/vault_test_' . bin2hex(random_bytes(8));

if (!mkdir($testDirectory, 0700)) {
    exit("Could not create test directory.\n");
}

putenv('VAULT_DATA_DIR=' . $testDirectory);

// Test 1: Valid registration information.
$errors = validateRegistration(
    'testplayer',
    'testplayer@example.com',
    'Password123',
    'Password123'
);

checkTest(
    'Valid registration details are accepted',
    count($errors) === 0
);

// Test 2: Invalid username.
$errors = validateRegistration(
    'ab',
    'testplayer@example.com',
    'Password123',
    'Password123'
);

checkTest(
    'Short usernames are rejected',
    count($errors) > 0
);

// Test 3: Invalid email.
$errors = validateRegistration(
    'testplayer',
    'invalid-email',
    'Password123',
    'Password123'
);

checkTest(
    'Invalid email addresses are rejected',
    count($errors) > 0
);

// Test 4: Short password.
$errors = validateRegistration(
    'testplayer',
    'testplayer@example.com',
    'short',
    'short'
);

checkTest(
    'Short passwords are rejected',
    count($errors) > 0
);

// Test 5: Mismatched passwords.
$errors = validateRegistration(
    'testplayer',
    'testplayer@example.com',
    'Password123',
    'Different123'
);

checkTest(
    'Mismatched passwords are rejected',
    count($errors) > 0
);

// Test 6: Account registration.
$result = registerUser(
    'testplayer',
    'testplayer@example.com',
    'Password123'
);

checkTest(
    'Valid account can be registered',
    $result['success'] === true
);

// Test 7: Password is stored as a hash.
$users = loadUsers();

checkTest(
    'Password is hashed before storage',
    count($users) === 1
        && $users[0]['password'] !== 'Password123'
        && password_verify('Password123', $users[0]['password'])
);

// Test 8: Duplicate username.
$result = registerUser(
    'testplayer',
    'another@example.com',
    'Password123'
);

checkTest(
    'Duplicate usernames are rejected',
    $result['success'] === false
);

// Test 9: Duplicate email.
$result = registerUser(
    'anotherplayer',
    'testplayer@example.com',
    'Password123'
);

checkTest(
    'Duplicate email addresses are rejected',
    $result['success'] === false
);

// Test 10: Correct puzzle answer.
checkTest(
    'Correct answers are accepted',
    isCorrectAnswer(' 33 ', '33')
);

// Test 11: Case-insensitive puzzle answer.
checkTest(
    'Word answers ignore letter case',
    isCorrectAnswer('vault', 'VAULT')
);

// Test 12: Incorrect puzzle answer.
checkTest(
    'Incorrect answers are rejected',
    !isCorrectAnswer('wrong', 'VAULT')
);

// Start a clean game for scoring tests.
$_SESSION = [];
$_SESSION['username'] = 'testplayer';

initializeGame();

// Test 13: Initial game state.
checkTest(
    'New game starts in Room 1 with 1000 points',
    $_SESSION['room'] === 1
        && $_SESSION['score'] === 1000
        && $_SESSION['game_completed'] === false
);

// Test 14: Wrong-answer penalty.
recordWrongAnswer(1);

checkTest(
    'Wrong answer deducts 50 points',
    $_SESSION['score'] === 950
        && $_SESSION['wrong_attempts'][1] === 1
);

// Test 15: Hint penalty.
$hintUnlocked = requestHint(1);

checkTest(
    'Hint deducts 100 points',
    $hintUnlocked
        && $_SESSION['score'] === 850
        && hasUsedHint(1)
);

// Test 16: Hint cannot charge twice.
$secondHint = requestHint(1);

checkTest(
    'Repeated hint does not deduct points',
    !$secondHint
        && $_SESSION['score'] === 850
);

// Test 17: Fast completion bonus.
checkTest(
    'Fast completion earns 300 points',
    calculateTimeBonus(60) === 300
);

// Test 18: Medium completion bonus.
checkTest(
    'Medium completion earns 200 points',
    calculateTimeBonus(180) === 200
);

// Test 19: Slower completion bonus.
checkTest(
    'Slower completion earns 100 points',
    calculateTimeBonus(400) === 100
);

// Test 20: No bonus after ten minutes.
checkTest(
    'Long completion earns no time bonus',
    calculateTimeBonus(650) === 0
);

// Test 21: Strong performance selects hard difficulty.
$_SESSION['wrong_attempts'][1] = 0;
$_SESSION['hints_used'][1] = false;
$_SESSION['room_durations'][1] = 30;

$decision = calculateNextDifficulty(1);

checkTest(
    'Strong performance selects hard difficulty',
    $decision['level'] === 'hard'
);

// Test 22: Struggling performance selects easy difficulty.
$_SESSION['wrong_attempts'][1] = 3;
$_SESSION['hints_used'][1] = true;
$_SESSION['room_durations'][1] = 200;

$decision = calculateNextDifficulty(1);

checkTest(
    'Struggling performance selects easy difficulty',
    $decision['level'] === 'easy'
);

// Test 23: Average performance selects normal difficulty.
$_SESSION['wrong_attempts'][1] = 1;
$_SESSION['hints_used'][1] = false;
$_SESSION['room_durations'][1] = 150;

$decision = calculateNextDifficulty(1);

checkTest(
    'Average performance selects normal difficulty',
    $decision['level'] === 'normal'
);

// Test 24: Easy puzzle selection.
$easyRoom = getRoom(2, 'easy');

checkTest(
    'Easy difficulty loads the correct puzzle',
    $easyRoom !== null
        && $easyRoom['answer'] === 'CAT'
);

// Test 25: Hard puzzle selection.
$hardRoom = getRoom(3, 'hard');

checkTest(
    'Hard difficulty loads the correct puzzle',
    $hardRoom !== null
        && $hardRoom['answer'] === '42'
);

// Test 26: Invalid difficulty.
checkTest(
    'Invalid difficulty is rejected',
    getRoom(1, 'impossible') === null
);

// Test 27: Game reset.
$_SESSION['score'] = 500;
$_SESSION['room'] = 3;

resetGame();

checkTest(
    'Restart resets room and score',
    $_SESSION['room'] === 1
        && $_SESSION['score'] === 1000
);

// Test 28: Login survives reset.
checkTest(
    'Restart keeps the logged-in player',
    $_SESSION['username'] === 'testplayer'
);

// Test 29: Empty statistics.
$statistics = getPlayerStatistics([]);

checkTest(
    'Empty statistics return zero completed games',
    $statistics['completed_games'] === 0
);

// Test 30: Completed-game statistics.
$sampleResults = [
    [
        'score' => 1300,
        'duration' => 60,
        'wrong_attempts' => 0,
        'hints_used' => 0,
        'completed_at' => 100
    ],
    [
        'score' => 1100,
        'duration' => 120,
        'wrong_attempts' => 2,
        'hints_used' => 1,
        'completed_at' => 200
    ]
];

$statistics = getPlayerStatistics($sampleResults);

$expectedStatistics = [
    'completed_games' => 2,
    'best_score' => 1300,
    'average_score' => 1200.0,
    'fastest_escape' => 60,
    'average_time' => 90,
    'total_wrong_answers' => 2,
    'total_hints_used' => 1
];

// Compare the values without hiding type differences.
// Print details only if a value does not match.
$statisticsCorrect = true;

foreach ($expectedStatistics as $key => $expected) {
    $actual = $statistics[$key] ?? null;

    if ($actual !== $expected) {
        $statisticsCorrect = false;

        echo "Statistics mismatch for $key:\n";
        echo "Expected: " . var_export($expected, true) . "\n";
        echo "Actual: " . var_export($actual, true) . "\n";
    }
}

checkTest(
    'Completed-game statistics are calculated correctly',
    $statisticsCorrect
);

// Test 31: Recent games.
$recentGames = getRecentGames($sampleResults);

checkTest(
    'Recent games show newest results first',
    count($recentGames) === 2
        && $recentGames[0]['score'] === 1100
);

// Test 32: HTML escaping.
checkTest(
    'HTML escaping prevents raw script output',
    escapeHtml('<script>') === '&lt;script&gt;'
);

// Remove the temporary registration data.
$testFile = $testDirectory . '/users.json';

if (is_file($testFile)) {
    unlink($testFile);
}

rmdir($testDirectory);
putenv('VAULT_DATA_DIR');

// Clear the test session before exiting.
$_SESSION = [];

echo "\n";
echo "Application Test Summary\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

exit($failed === 0 ? 0 : 1);
