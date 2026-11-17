
<?php
// Leaderboard helpers for Vault Escape Challenge.
// Results are stored in the current PHP session, not in a database.

// Make sure the leaderboard exists in the session.
function initializeLeaderboard(): void
{
    if (!isset($_SESSION['leaderboard']) || !is_array($_SESSION['leaderboard'])) {
        $_SESSION['leaderboard'] = [];
    }
}

// Record a completed game only once.
function recordLeaderboardResult(): bool
{
    initializeLeaderboard();

    if (empty($_SESSION['game_completed'])) {
        return false;
    }

    // Each completed game receives one result ID.
    // This prevents refreshing the results page from adding duplicates.
    if (!empty($_SESSION['leaderboard_recorded'])) {
        return false;
    }

    $wrongAttempts = array_sum($_SESSION['wrong_attempts'] ?? []);
    $hintsUsed = count(array_filter($_SESSION['hints_used'] ?? []));

    $start = (int) ($_SESSION['game_started_at'] ?? time());
    $finish = (int) ($_SESSION['game_finished_at'] ?? time());
    $duration = max(0, $finish - $start);

    $_SESSION['leaderboard'][] = [
        'username' => (string) ($_SESSION['username'] ?? 'Player'),
        'score' => (int) ($_SESSION['score'] ?? 0),
        'duration' => $duration,
        'wrong_attempts' => $wrongAttempts,
        'hints_used' => $hintsUsed,
        'completed_at' => $finish
    ];

    $_SESSION['leaderboard_recorded'] = true;

    return true;
}

// Sort results by highest score, then fastest completion.
function getLeaderboardResults(): array
{
    initializeLeaderboard();

    $results = $_SESSION['leaderboard'];

    usort($results, function (array $first, array $second): int {
        $scoreComparison = $second['score'] <=> $first['score'];

        if ($scoreComparison !== 0) {
            return $scoreComparison;
        }

        $timeComparison = $first['duration'] <=> $second['duration'];

        if ($timeComparison !== 0) {
            return $timeComparison;
        }

        return $first['completed_at'] <=> $second['completed_at'];
    });

    return $results;
}

// Format a duration as minutes and seconds.
function formatLeaderboardTime(int $seconds): string
{
    $minutes = intdiv($seconds, 60);
    $remainingSeconds = $seconds % 60;

    return sprintf('%02d:%02d', $minutes, $remainingSeconds);
}
