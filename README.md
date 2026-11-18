
# Vault Escape Challenge

CSC 6370 - Web Programming  
Student: Isaac Owusu  
Georgia State University

## Project Overview

Vault Escape Challenge is a web-based escape room game that I developed using PHP, HTML, and CSS.

The game allows players to create an account, log in, and solve puzzles across three different rooms to escape a virtual vault.

Each room presents a puzzle that the player must solve before moving forward. The game tracks the player's score, time, wrong answers, and hints used.

I also implemented an adaptive difficulty system that adjusts the next puzzle based on how well the player performs.

## Technologies Used

- PHP
- HTML5
- CSS3
- JSON for storing user and game data
- PHP sessions for tracking player progress
- Git and GitHub for version control
- GitHub Actions for automated testing

## Features

### 1. User Registration and Login

Players can register for an account using a username, email address, and password.

The application validates registration information, prevents duplicate accounts, and securely hashes passwords before storing them.

Registered players can log in and access the game.

### 2. Three Puzzle Rooms

The game contains three rooms.

Players must solve the puzzle in the current room before moving to the next room.

The application checks answers on the server and tracks the player's progress using PHP sessions.

### 3. Scoring System

Players begin each game with 1,000 points.

The scoring system works as follows:

- Starting score: 1,000 points
- Wrong answer: -50 points
- Using a hint: -100 points
- Fast completion: up to 300 bonus points

The completion bonus depends on how quickly the player finishes the game.

The final score reflects the player's performance throughout the escape challenge.

### 4. Adaptive Difficulty

I implemented an adaptive difficulty system that evaluates the player's performance after completing a room.

The system considers:

- Number of wrong answers
- Whether the player used a hint
- Time spent solving the room

Based on these factors, the next puzzle can be set to easy, normal, or hard difficulty.

This allows the game to respond to different player performance levels.

### 5. Hints

Players can request hints when they are having difficulty solving a puzzle.

Using a hint deducts 100 points from the player's score.

The game also prevents the same hint from charging the player multiple times.

### 6. Leaderboard and Player Statistics

The application keeps track of completed games and player performance.

Player statistics include:

- Number of completed games
- Best score
- Average score
- Fastest escape time
- Average completion time
- Total wrong answers
- Total hints used

Players can also review their recent completed games.

### 7. Game Restart

Players can restart the challenge without creating a new account or logging in again.

Restarting resets the current room, score, and game progress while keeping the player logged in.

### 8. Security and Input Validation

I implemented several security measures in the application:

- Password hashing
- Server-side input validation
- CSRF token protection
- HTML output escaping
- Session-based authentication
- Validation of submitted form values

These measures help protect player information and prevent invalid requests.

## Running the Project Locally

### Requirements

- PHP 8.3 or a compatible PHP version
- A web browser
- A terminal

### Instructions

1. Clone the repository:

   ```bash
   git clone https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge.git
   ```

2. Open the project folder:

   ```bash
   cd Owusu_VaultEscapeChallenge
   ```

3. Start the PHP development server:

   ```bash
   php -S localhost:8000
   ```

4. Open the following address in a browser:

   http://localhost:8000

5. Register an account or log in to start playing.

## Automated Testing

I created automated tests to verify the main application features and security protections.

### Application Tests

The application test file is:

`tests/application_test.php`

It contains 32 tests covering:

- Registration validation
- Password hashing
- Duplicate account prevention
- Puzzle answer checking
- Game initialization
- Wrong-answer penalties
- Hint penalties
- Completion bonuses
- Adaptive difficulty
- Puzzle selection
- Game restarting
- Player statistics
- Recent game results
- HTML escaping

To run the tests:

```bash
php tests/application_test.php
```

Verified result:

```text
Application Test Summary
Passed: 32
Failed: 0
```

### Security Tests

The security test file is:

`tests/security_test.php`

It contains 11 tests covering:

- CSRF token generation and validation
- Invalid and empty CSRF tokens
- HTML escaping
- POST input validation
- Array input rejection
- Oversized input rejection
- Missing input handling
- HTTP request method validation

To run the security tests:

```bash
php tests/security_test.php
```

Verified result:

```text
Passed: 11
Failed: 0
```

### GitHub Actions

I configured GitHub Actions to automatically run the PHP tests whenever changes are pushed to GitHub.

The workflow file is:

`.github/workflows/php-tests.yml`

The workflow performs three main checks:

1. PHP syntax validation
2. Application tests
3. Security tests

The workflow also runs for pull requests and can be started manually.

The GitHub Actions workflow completed successfully.

GitHub Actions:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge/actions

## Testing Summary

I tested the application features manually during development and added automated tests to check the main functionality.

The automated tests currently cover 43 test cases.

| Test Category | Tests | Result |
|---|---:|---|
| Application functionality | 32 | Passed |
| Security validation | 11 | Passed |
| GitHub Actions workflow | 1 workflow | Passed |

I also tested the registration process, login, puzzle progression, scoring, hints, and game restarting through the browser during development.

## Project Management

I developed this project individually and used ClickUp to organize my tasks and track development progress.

ClickUp Board:
https://sharing.clickup.com/90141777100/b/h/6-901423080973-2/76f0227e30f6a62

I used GitHub to manage my source code and track changes through commits.

GitHub Repository:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge

## What I Learned

Working on this project helped me better understand how PHP sessions work in a web application.

I learned how to manage player progress across multiple pages, validate user input, store account information, and implement a scoring system.

One of the more interesting parts was building the adaptive difficulty system because I had to use the player's performance to decide which puzzle should appear next.

I also gained more experience writing automated tests and using GitHub Actions to check my code after pushing changes.

Overall, this project helped me connect several concepts from Web Programming into one working application.
