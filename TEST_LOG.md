
# Vault Escape Challenge - Test Log

Course: CSC 6370 - Web Programming  
Student: Isaac Owusu  
Institution: Georgia State University  
Semester: Fall 2026

## 1. Testing Overview

I tested Vault Escape Challenge locally and after deploying it to the CODD server.

My testing focused on registration, login, puzzle functionality, scoring, hints, adaptive difficulty, game statistics, security, and session management.

I used automated PHP tests to check the application logic and manual browser testing to verify that the deployed website worked correctly.

## 2. Testing Environment

Local Development:
- macOS
- PHP development server
- Localhost: http://localhost:8000

Deployment:
- Georgia State University CODD server
- PHP web application

Live Website:
https://codd.cs.gsu.edu/~iowusu2/WP/PW/Owusu_VaultEscapeChallenge/index.php

GitHub Repository:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge

Note: CODD restricts external access, so deployment testing was performed through my own browser.

## 3. Automated PHP Tests

### Test Group A - Application Tests

Test File: tests/application_test.php

Command:

    php tests/application_test.php

Result:
- Passed: 32
- Failed: 0

The application tests cover the main PHP functionality, including:

- Registration validation
- Duplicate account handling
- Game initialization
- Puzzle answer checking
- Incorrect answer penalties
- Hint deductions
- Score calculation
- Adaptive difficulty
- Game completion
- Game history and statistics

Status: PASSED

### Test Group B - Security Tests

Test File: tests/security_test.php

Command:

    php tests/security_test.php

Result:
- Passed: 11
- Failed: 0

These tests cover security-related functionality, including:

- Input validation
- HTML output escaping
- CSRF protection
- Invalid form submissions
- Request validation
- Rejection of invalid data

Status: PASSED

### Test Group C - Graduate AI Tests

Test File: tests/graduate_ai_test.php

Command:

    php tests/graduate_ai_test.php

Result:
- Passed: 17
- Failed: 0

These tests cover the graduate-level adaptive difficulty system and related feedback.

The tests check that the system responds to player performance and handles different difficulty scenarios correctly.

Status: PASSED

## 4. Automated Testing Summary

| Test Group | Passed | Failed | Status |
|---|---:|---:|---|
| Application tests | 32 | 0 | Passed |
| Security tests | 11 | 0 | Passed |
| Graduate AI tests | 17 | 0 | Passed |
| Total | 60 | 0 | Passed |

All 60 automated test cases passed during local testing.

## 5. CODD Deployment Tests

### Test 1 - Homepage and UI

Steps:
1. Opened the deployed website.
2. Checked that the homepage loaded.
3. Verified the updated colors and layout.
4. Checked the registration and login links.

Expected Result:
The homepage should display correctly without PHP errors.

Actual Result:
The website loaded successfully, and the updated interface was displayed.

Status: PASSED

### Test 2 - Registration and Login

Steps:
1. Opened the registration page.
2. Created a new test account.
3. Logged out.
4. Logged back in with the registered account.
5. Opened the dashboard.

Expected Result:
The account should be created successfully, and the user should be able to log in.

Actual Result:
Registration and login worked correctly. The dashboard was accessible after authentication.

Status: PASSED

### Test 3 - Puzzle Rooms and Scoring

Steps:
1. Started a new game.
2. Submitted an incorrect answer.
3. Checked the 50-point penalty.
4. Requested a hint.
5. Checked the 100-point deduction.
6. Requested the same hint again.
7. Confirmed that the hint was not charged twice.
8. Completed all three rooms.
9. Checked the final results and vault door animation.

Expected Result:
All rooms should work, scores should update correctly, and the final results should display.

Actual Result:
All three rooms worked correctly. Wrong-answer penalties, hint deductions, final scoring, and the vault door animation behaved as expected.

Status: PASSED

### Test 4 - Adaptive AI Difficulty

Steps:
1. Started a new game.
2. Completed the first room quickly.
3. Avoided incorrect answers and hints.
4. Continued to the second room.
5. Checked the selected difficulty.

Expected Result:
Strong performance should result in Hard difficulty for the next room.

Actual Result:
Room 2 displayed Hard difficulty after strong performance in Room 1.

Status: PASSED

### Test 5 - Leaderboard and Statistics

Steps:
1. Completed a game.
2. Returned to the dashboard.
3. Checked the number of completed games.
4. Checked recent game history.
5. Compared the saved score with the final score.
6. Opened the leaderboard.
7. Refreshed the page.

Expected Result:
Completed games and scores should be saved and remain available after refreshing.

Actual Result:
The dashboard, game history, and leaderboard displayed the correct information. The data remained available after refreshing.

Status: PASSED

### Test 6 - Restart, Logout, and Sessions

Steps:
1. Started another game.
2. Confirmed that the game started from Room 1 with a fresh score.
3. Refreshed the dashboard while logged in.
4. Confirmed that the session remained active.
5. Logged out.
6. Attempted to access the dashboard directly.
7. Logged in again.
8. Checked that previous game statistics were preserved.

Expected Result:
Restart should reset the current game, authenticated sessions should persist correctly, and protected pages should require login.

Actual Result:
Restart, logout, session persistence, and protected page access worked correctly. Previous game statistics were preserved.

Status: PASSED

## 6. CODD Testing Summary

| Test ID | Test Description | Result |
|---|---|---|
| T1 | Homepage and updated UI | Passed |
| T2 | Registration and login | Passed |
| T3 | Puzzle rooms and scoring | Passed |
| T4 | Adaptive AI difficulty | Passed |
| T5 | Leaderboard and player statistics | Passed |
| T6 | Restart, logout, and sessions | Passed |

Total CODD Test Scenarios: 6  
Passed: 6  
Failed: 0

## 7. Final Testing Summary

| Testing Category | Result |
|---|---|
| Automated application tests | 32 passed |
| Automated security tests | 11 passed |
| Automated graduate AI tests | 17 passed |
| CODD deployment scenarios | 6 passed |

The automated tests and the manual CODD deployment tests completed successfully.

No failures were reported during the final testing described in this log.

