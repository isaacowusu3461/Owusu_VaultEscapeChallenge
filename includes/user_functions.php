
<?php
// Helper functions for user registration.
// Keeping these functions separate makes the registration page easier to read.

// The user file is stored outside the public project folder when possible.
// Set VAULT_DATA_DIR on CODD to a private writable directory.
function userFilePath(): string
{
    $directory = getenv('VAULT_DATA_DIR');

    if (!$directory) {
        $directory = dirname(__DIR__) . '/data';
    }

    return rtrim($directory, '/') . '/users.json';
}

// Load registered users from the JSON file.
function loadUsers(): array
{
    $file = userFilePath();

    if (!file_exists($file)) {
        return [];
    }

    $contents = file_get_contents($file);

    if ($contents === false) {
        throw new RuntimeException('Unable to read the user file.');
    }

    $users = json_decode($contents, true);

    if (!is_array($users)) {
        throw new RuntimeException('The user file is not valid.');
    }

    return $users;
}

// Validate the form before saving a new account.
function validateRegistration(
    string $username,
    string $email,
    string $password,
    string $confirmPassword
): array {
    $errors = [];

    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $errors[] = 'Username must be 3 to 20 characters and contain only letters, numbers, or underscores.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 254) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8 || strlen($password) > 128) {
        $errors[] = 'Password must be between 8 and 128 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    return $errors;
}

// Save an account while locking the file to avoid conflicting writes.
function registerUser(
    string $username,
    string $email,
    string $password
): array {
    $file = userFilePath();
    $directory = dirname($file);

    if (!is_dir($directory)) {
        if (!mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create the user directory.');
        }
    }

    $handle = fopen($file, 'c+');

    if ($handle === false) {
        throw new RuntimeException('Unable to open the user file.');
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException('Unable to lock the user file.');
        }

        rewind($handle);
        $contents = stream_get_contents($handle);

        if ($contents === false) {
            throw new RuntimeException('Unable to read registered users.');
        }

        $users = [];

        if (trim($contents) !== '') {
            $users = json_decode($contents, true);

            if (!is_array($users)) {
                throw new RuntimeException('Invalid user data.');
            }
        }

        foreach ($users as $user) {
            if (strcasecmp($user['username'], $username) === 0) {
                return ['success' => false, 'message' => 'Username is already taken.'];
            }

            if (strcasecmp($user['email'], $email) === 0) {
                return ['success' => false, 'message' => 'Email is already registered.'];
            }
        }

        $users[] = [
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $json = json_encode($users, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        rewind($handle);

        if (!ftruncate($handle, 0)) {
            throw new RuntimeException('Unable to update the user file.');
        }

        $written = fwrite($handle, $json);

        if ($written === false || $written !== strlen($json)) {
            throw new RuntimeException('Unable to save the account.');
        }

        fflush($handle);

        return ['success' => true, 'message' => 'Account created successfully.'];
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}
