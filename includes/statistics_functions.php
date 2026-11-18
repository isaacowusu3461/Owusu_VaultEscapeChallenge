<?php
// Statistics helpers for Vault Escape Challenge.
// These functions use completed games stored in the session.

// Calculate a summary of completed games.
function getPlayerStatistics(array $results): array
{
    $completedGames = count($results);

    // Return safe defaults when there are no completed games.
    if ($completedGames === 0) {
        return [
            'completed_games' => 0,
            'best_score' => 0,
            'average_score' => 0,
            'fastest_escape' => 0,
            'average_time' => 0,
            'total_wrong_answers' => 0,
            'total_hints_used' => 0
        ];
    }

    $totalScore = 0;
    $totalTime = 0;
    $totalWrong = 0;
    $totalHints = 0;

    $bestScore = 0;
    $fastestEscape = PHP_INT_MAX;

    foreach ($results as $result) {
        $score = (int) ($result['score'] ?? 0);
        $duration = (int) ($result['duration'] ?? 0);
        $wrong = (int) ($result['wrong_attempts'] ?? 0);
        $hints = (int) ($result['hints_used'] ?? 0);

        $totalScore += $score;
        $totalTime += $duration;
        $totalWrong += $wrong;
        $totalHints += $hints;

        if ($score > $bestScore) {
            $bestScore = $score;
        }

        if ($duration < $fastestEscape) {
            $fastestEscape = $duration;
        }
    }

    return [
        'completed_games' => $completedGames,
        'best_score' => $bestScore,
        'average_score' => round($totalScore / $completedGames),
        'fastest_escape' => $fastestEscape,
        'average_time' => (int) round($totalTime / $completedGames),
        'total_wrong_answers' => $totalWrong,
        'total_hints_used' => $totalHints
    ];
}

// Return the most recently completed games.
// The leaderboard is sorted by score, so we sort a copy by finish time.
function getRecentGames(array $results, int $limit = 5): array
{
    usort($results, function (array $first, array $second): int {
        return (int) ($second['completed_at'] ?? 0)
            <=> (int) ($first['completed_at'] ?? 0);
    });

    return array_slice($results, 0, $limit);
}

// Format a game duration for the statistics page.
function formatStatisticsTime(int $seconds): string
{
    return sprintf(
        '%02d:%02d',
        intdiv($seconds, 60),
        $seconds % 60
    );
}
