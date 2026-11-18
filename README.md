
# Vault Escape Challenge

CSC 6370 - Web Programming  
Student: Isaac Owusu  
Georgia State University  
Fall 2026

## Project Overview

Vault Escape Challenge is a web-based escape room game that I developed using PHP, HTML, and CSS.

The game allows players to register, log in, and solve puzzles across three different rooms to escape a virtual vault.

Each room contains a puzzle that must be solved before the player can move forward. The application tracks scores, wrong answers, hints, and completion time.

I also implemented an adaptive difficulty system that changes the next puzzle based on the player's performance.

## Project Links

Live Website:
https://codd.cs.gsu.edu/~iowusu2/WP/PW/Owusu_VaultEscapeChallenge/index.php

GitHub Repository:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge

GitHub Actions:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge/actions

ClickUp Board:
https://sharing.clickup.com/90141777100/b/h/6-901423080973-2/76f0227e30f6a62

Note: The live CODD website may require access through the Georgia State University network.

## Technologies Used

- PHP
- HTML5
- CSS3
- JSON for storing account and game data
- PHP sessions for authentication and game progress
- Git and GitHub
- GitHub Actions for automated testing

## Main Features

### 1. User Registration and Login

Players can create accounts using a username, email address, and password.

The application validates registration information, prevents duplicate accounts, and hashes passwords before storing them.

Players can log in, access their dashboards, and log out securely.

### 2. Three Puzzle Rooms

The game contains three puzzle rooms.

Players must solve the current puzzle before moving to the next room.

The application checks submitted answers on the server and uses PHP sessions to maintain game progress.

### 3. Scoring System

Players begin with 1,000 points.

The scoring rules are:

- Starting score: 1,000 points
- Incorrect answer: -50 points
- Hint request: -100 points
- Fast completion: Up to 300 bonus points

Players are only charged once for requesting the same room's hint.

The final score depends on the player's performance throughout the game.

### 4. Adaptive Difficulty System

I implemented a rule-based adaptive difficulty system that evaluates player performance.

The system considers:

- Number of incorrect answers
- Number of hints used
- Time spent completing a room

Based on these factors, the next puzzle can be assigned one of three difficulty levels:

- Easy
- Normal
- Hard

For example, a player who completes a room quickly without mistakes or hints may receive a harder puzzle.

A player who struggles may receive an easier puzzle.

The difficulty is selected when the player enters a room and does not change while that puzzle is being solved.

The application also provides feedback explaining the selected difficulty.

This feature uses local PHP logic rather than an external AI service.

### 5. Hint System

Players can request hints when they need help.

Each hint costs 100 points.

The application prevents repeated deductions for the same hint.

### 6. Game Progress

The game displays the player's current room and progress through the challenge.

The progress indicator updates as the player completes rooms.

### 7. Vault Door Animation

I created a vault door using CSS.

After completing the final puzzle, the door opens using a CSS animation.

The animation does not require JavaScript.

### 8. Dashboard and Player Statistics

The dashboard displays information about completed games.

Player statistics include:

- Completed games
- Best score
- Average score
- Fastest escape time
- Average completion time
- Total incorrect answers
- Total hints used

Players can also review their recent game results.

### 9. Leaderboard

The leaderboard allows players to compare their results with other players.

Completed game information is stored and remains available after logging out.

### 10. Game Restart

Players can start a new game without registering again.

Restarting resets the current game progress while preserving the player's account and previous game history.

### 11. Security

I implemented several security measures, including:

- Password hashing
- Server-side input validation
- CSRF token protection
- HTML output escaping
- PHP session authentication
- Protected pages
- Request method validation

These measures help protect user information and reject invalid requests.

## Running the Project Locally

### Requirements

- PHP 8.3 or a compatible PHP version
- Web browser
- Terminal

### Instructions

1. Clone the repository.

```bash
git clone https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge.git
```

2. Open the project directory.

```bash
cd Owusu_VaultEscapeChallenge
```

3. Start the PHP development server.

```bash
php -S localhost:8000
```

4. Open the following address in a browser.

http://localhost:8000

5. Register an account or log in to begin playing.

## Automated Testing

I created automated tests to check application functionality, security, and adaptive difficulty.

### Application Tests

File:

`tests/application_test.php`

The application tests cover registration, login-related validation, puzzle answers, scoring, hints, game initialization, adaptive difficulty, restarting, and player statistics.

Run:

```bash
php tests/application_test.php
```

Result:

- Passed: 32
- Failed: 0

### Security Tests

File:

`tests/security_test.php`

The security tests cover CSRF protection, HTML escaping, invalid form values, request validation, and related security checks.

Run:

```bash
php tests/security_test.php
```

Result:

- Passed: 11
- Failed: 0

### Graduate AI Tests

File:

`tests/graduate_ai_test.php`

I added separate tests for the adaptive difficulty improvements.

These tests verify the behavior of the rule-based difficulty system and its feedback.

Run:

```bash
php tests/graduate_ai_test.php
```

Result:

- Passed: 17
- Failed: 0

### Automated Test Summary

| Test Category | Passed | Failed |
|---|---:|---:|
| Application tests | 32 | 0 |
| Security tests | 11 | 0 |
| Graduate AI tests | 17 | 0 |
| Total | 60 | 0 |

## GitHub Actions

I configured GitHub Actions to run automated PHP checks when changes are pushed to GitHub.

The workflow includes PHP syntax validation and automated application checks.

Workflow location:

`.github/workflows/php-tests.yml`

GitHub Actions:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge/actions

## CODD Deployment Testing

After completing development, I uploaded the application to Georgia State University's CODD server.

I tested the deployed application through my browser.

### Test 1: Homepage and UI

Result: Passed

The homepage loaded correctly with the updated layout and colors.

### Test 2: Registration and Login

Result: Passed

I created a test account, logged out, and logged back in successfully.

### Test 3: Puzzle Rooms and Scoring

Result: Passed

I completed all three puzzle rooms and verified incorrect-answer penalties, hint deductions, final scoring, and the vault door animation.

### Test 4: Adaptive Difficulty

Result: Passed

I completed a room quickly without mistakes or hints and confirmed that the next room displayed Hard difficulty.

### Test 5: Leaderboard and Statistics

Result: Passed

I verified that completed games appeared in player statistics and the leaderboard.

The information remained available after refreshing the page.

### Test 6: Restart and Session Security

Result: Passed

I verified that restarting created a fresh game, authenticated sessions persisted correctly, logout worked, protected pages required authentication, and previous game results were preserved.

### CODD Testing Summary

| Test | Result |
|---|---|
| Homepage and UI | Passed |
| Registration and login | Passed |
| Puzzle rooms and scoring | Passed |
| Adaptive difficulty | Passed |
| Leaderboard and statistics | Passed |
| Restart and session security | Passed |

All six CODD deployment tests passed.

## Project Management

I developed this project individually.

I used ClickUp to organize tasks and monitor development progress.

ClickUp Board:
https://sharing.clickup.com/90141777100/b/h/6-901423080973-2/76f0227e30f6a62

I used GitHub to track changes through meaningful commits.

GitHub Repository:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge

## What I Learned

Working on this project helped me understand how to build a complete PHP web application.

I gained more experience with PHP sessions, account authentication, form validation, JSON data storage, and server-side processing.

One of the most interesting parts was implementing adaptive difficulty. I had to use information about the player's performance to determine which puzzle should appear next.

I also learned how to write automated tests and use GitHub Actions to check my code.

Deploying the application to CODD helped me understand the difference between running a PHP project locally and running it on a university web server.

Overall, this project allowed me to combine several topics from Web Programming into one working application.

## Author

Isaac Owusu  
M.S. Computer Science  
Georgia State University
