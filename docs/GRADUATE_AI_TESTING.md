
# Graduate AI Testing — Vault Escape Challenge

Course: CSC 6370 — Web Programming  
Student: Isaac Owusu

## 1. AI Feature Overview

I implemented a rule-based adaptive system called Vault Security Advisor. It uses the player's wrong answers, hint usage, and time spent solving puzzles to adjust the next room's difficulty.

The system does not use machine learning or an external AI service. All decisions are made using PHP conditions and information stored in the current session.

## 2. Adaptive Difficulty Rules

The game starts at Normal difficulty.

After completing a room, the system checks the player's performance:

- Easy: Three or more wrong answers, or at least two wrong answers with a hint, or a completion time of 300 seconds or more.
- Hard: No wrong answers, no hint used, and a completion time below 120 seconds.
- Normal: All other situations.

The selected difficulty is saved in the PHP session and stays the same throughout the next room.

## 3. Attempt-Aware Feedback

The game changes its assistance based on the player's attempts.

- Zero wrong answers: General instructions.
- One wrong answer: Encouragement to review the clues.
- Two wrong answers: A smaller clue specific to the current puzzle.
- Three or more wrong answers: A more detailed clue specific to the current puzzle.

The feedback also considers time spent in the room and whether a hint has been used.

Each room has Easy, Normal, and Hard puzzles, so the feedback is matched to the actual puzzle being displayed.

## 4. AI Test Scenarios

### Test AI-1: Fast and Accurate Player

Setup:
- Wrong answers: 0
- Hint used: No
- Completion time: 60 seconds

Expected result:
- Next room difficulty: Hard
- Explanation: The player solved the previous room quickly without mistakes or hints.

### Test AI-2: Player Needing Assistance

Setup:
- Wrong answers: 3
- Hint used: Yes
- Completion time: 180 seconds

Expected result:
- Next room difficulty: Easy
- Explanation: The previous room required several attempts or additional assistance.

### Test AI-3: Normal Player

Setup:
- Wrong answers: 1
- Hint used: No
- Completion time: 150 seconds

Expected result:
- Next room difficulty: Normal
- Explanation: The player's performance suggests that the standard puzzle is suitable.

### Test AI-4: Time Boundary

Setup:
- Wrong answers: 0
- Hint used: No
- Completion time: 120 seconds

Expected result:
- Next room difficulty: Normal
- The Hard rule requires a completion time below 120 seconds.

### Test AI-5: Slow Completion

Setup:
- Wrong answers: 0
- Hint used: No
- Completion time: 300 seconds

Expected result:
- Next room difficulty: Easy
- The Easy rule applies when completion time reaches 300 seconds.

## 5. Player-Facing Feedback

During gameplay, the player can see:

- Current difficulty.
- Reason for the selected difficulty.
- Adaptive assistance.
- Wrong-answer count.
- Hint usage.
- Time spent in the room.

After completing all three rooms, the final results screen displays:

- Final score and completion time.
- Wrong answers and hints used.
- Player profile and its explanation.
- Difficulty history for all three rooms.
- The reason each room's difficulty was selected.

## 6. Limitations

The Vault Security Advisor is a simple rule-based system. It does not learn from previous players or train a machine-learning model.

The rules use fixed thresholds, so they cannot fully measure a player's reasoning ability. For example, a player may take longer because they were interrupted rather than because the puzzle was difficult.

The system also uses only three difficulty levels and adjusts difficulty between rooms, not during an active puzzle.

## 7. AI Tool Disclosure

I used AI assistance during development to help plan the implementation, review PHP code, troubleshoot errors, and prepare testing documentation.

I reviewed the suggested changes, tested the application locally, and verified that the final implementation followed the project requirements.
