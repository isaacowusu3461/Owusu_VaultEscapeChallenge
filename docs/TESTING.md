
# Vault Escape Challenge - Testing Report

Course: CSC 6370 - Web Programming  
Student: Isaac Owusu

## Testing Overview

I tested Vault Escape Challenge throughout development to make sure the main features worked correctly.

My testing included browser-based checks, automated application tests, security tests, and GitHub Actions.

## 1. Manual Testing

I tested the following features in the browser during development.

| Test | Expected Result | Result |
|---|---|---|
| User registration | Valid users can create accounts | Passed |
| Invalid registration | Invalid information is rejected | Passed |
| Duplicate registration | Existing accounts cannot be registered again | Passed |
| Login | Registered users can log in | Passed |
| Puzzle progression | Correct answers allow players to advance | Passed |
| Incorrect answers | Wrong answers reduce the score | Passed |
| Hints | Hints appear and deduct points | Passed |
| Repeated hints | The same hint does not deduct points twice | Passed |
| Adaptive difficulty | Puzzle difficulty changes based on performance | Passed |
| Game completion | Finishing all rooms completes the challenge | Passed |
| Game restart | Players can restart without logging in again | Passed |
| Player statistics | Completed game statistics are displayed | Passed |
| Leaderboard | Completed results are available for ranking | Passed |

## 2. Automated Application Tests

File: `tests/application_test.php`

The application test suite contains 32 tests.

It covers registration, password hashing, scoring, hints, adaptive difficulty, game resets, statistics, and puzzle validation.

Command:

```bash
php tests/application_test.php
```

Result:

```text
Application Test Summary
Passed: 32
Failed: 0
```

## 3. Automated Security Tests

File: `tests/security_test.php`

The security test suite contains 11 tests.

It checks CSRF tokens, HTML escaping, POST input validation, and HTTP request method handling.

Command:

```bash
php tests/security_test.php
```

Result:

```text
Passed: 11
Failed: 0
```

## 4. PHP Syntax Validation

I checked the PHP files for syntax errors using the PHP command-line tool.

Example:

```bash
php -l includes/auth.php
```

The GitHub Actions workflow also checks PHP syntax across the project.

## 5. GitHub Actions

I configured GitHub Actions to run automated checks when code is pushed to the repository.

The workflow runs:

1. PHP syntax validation
2. Application tests
3. Security tests

The workflow completed successfully.

Workflow:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge/actions

## 6. Problems Fixed During Development

### PHP Session Warnings

During automated testing, PHP displayed warnings because some files contained whitespace before the opening PHP tag.

This caused output to be sent before the application initialized its session.

I fixed the problem by removing the leading whitespace from the affected PHP files.

After the changes, the session warnings were resolved.

### Statistics Test Comparison

One automated test initially failed when comparing the average score.

The expected value was an integer, but the statistics function returned a floating-point value.

For example:

- Expected: 1200
- Actual: 1200.0

The calculation was correct. I updated the test expectation to match the function's return type.

After the correction, the test passed.

## 7. Final Test Summary

| Category | Passed | Failed |
|---|---:|---:|
| Application tests | 32 | 0 |
| Security tests | 11 | 0 |
| GitHub Actions workflow | 1 | 0 |

All 43 automated test cases passed.

## Conclusion

The testing process helped me identify and fix issues before completing the project.

The automated tests confirmed that the main application functions behaved as expected, while GitHub Actions provided an additional check whenever I pushed code changes.
