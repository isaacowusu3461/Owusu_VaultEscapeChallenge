
# Vault Escape Challenge - Deployment Guide

Course: CSC 6370 - Web Programming  
Student: Isaac Owusu

## Project Overview

Vault Escape Challenge is a PHP escape room game with user registration, login, three puzzle rooms, scoring, hints, adaptive difficulty, and player statistics.

This document explains how to run the project locally and prepare it for deployment.

## 1. Requirements

The project requires:

- PHP 8.3 or a compatible version
- A web server that supports PHP
- A web browser
- File-system write access for application data

The application uses JSON files for storing data, so a database server is not required.

## 2. Running Locally

Clone the repository:

```bash
git clone https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge.git
```

Open the project folder:

```bash
cd Owusu_VaultEscapeChallenge
```

Start the PHP development server:

```bash
php -S localhost:8000
```

Open the application:

http://localhost:8000

## 3. Preparing for Deployment

Before deploying the application, I need to check that:

- All PHP files pass syntax validation.
- Registration and login work.
- Players can complete all three rooms.
- Scores and hints are calculated correctly.
- Adaptive difficulty works.
- Player statistics display correctly.
- Application data can be saved.
- The automated tests pass.

## 4. Application Data

The application stores user information and game results in JSON files.

Passwords are stored as hashes rather than plain text.

The data directory must be writable by PHP.

For deployment, private JSON data should be stored outside the publicly accessible web directory, or protected by server configuration.

The repository should not contain real player credentials or private user data.

## 5. Security Checks

The application includes:

- Password hashing
- PHP session-based authentication
- CSRF protection
- Server-side input validation
- HTML output escaping

Before publishing the application, I should also confirm that HTTPS is enabled and that session cookies are configured securely.

## 6. Automated Testing

Run the application tests:

```bash
php tests/application_test.php
```

Run the security tests:

```bash
php tests/security_test.php
```

The latest verified local results were:

Application tests:
32 passed, 0 failed

Security tests:
11 passed, 0 failed

GitHub Actions also runs PHP syntax checks and both test suites automatically.

## 7. GitHub Actions

Workflow file:

`.github/workflows/php-tests.yml`

GitHub Actions page:

https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge/actions

The workflow checks the code whenever changes are pushed to the repository.

## 8. Deployment Checklist

- [x] Project source code uploaded to GitHub
- [x] README updated
- [x] Application tests implemented
- [x] Security tests implemented
- [x] GitHub Actions configured
- [x] Testing report created
- [ ] Production hosting configured
- [ ] Private data storage verified
- [ ] Production HTTPS verified
- [ ] Public deployment tested
- [ ] Final submission completed

## 9. Project Links

GitHub Repository:
https://github.com/isaacowusu3461/Owusu_VaultEscapeChallenge

ClickUp Board:
https://sharing.clickup.com/90141777100/b/h/6-901423080973-2/76f0227e30f6a62

## Conclusion

This guide documents the steps needed to run and deploy Vault Escape Challenge.

The project has automated testing and version control in place. Production deployment and final submission checks will be completed separately.
