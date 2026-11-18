
<?php
// Basic security tests for Vault Escape Challenge.
// Run this file from the command line using PHP.

require_once __DIR__ . '/../includes/auth.php';

$passed = 0;
$failed = 0;

function checkSecurityTest(string $name, bool $condition): void
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

// Start a session before testing the token functions.
startGameSession();

// Test 1: A CSRF token should be generated.
$token = getCsrfToken();

checkSecurityTest(
    'CSRF token is 64 characters',
    strlen($token) === 64
);

// Test 2: A valid token should be accepted.
checkSecurityTest(
    'Valid CSRF token is accepted',
    validCsrfToken($token)
);

// Test 3: An incorrect token should be rejected.
checkSecurityTest(
    'Invalid CSRF token is rejected',
    !validCsrfToken(str_repeat('a', 64))
        || $token === str_repeat('a', 64)
);

// Test 4: An empty token should be rejected.
checkSecurityTest(
    'Empty CSRF token is rejected',
    !validCsrfToken('')
);

// Test 5: HTML should be escaped before output.
$unsafe = '<script>alert("test")</script>';
$escaped = escapeHtml($unsafe);

checkSecurityTest(
    'HTML output is escaped',
    !str_contains($escaped, '<script>')
        && str_contains($escaped, '&lt;script&gt;')
);

// Test 6: A normal string should be accepted.
$_POST['test_value'] = '  hello  ';

checkSecurityTest(
    'Normal POST string is accepted and trimmed',
    getPostString('test_value') === 'hello'
);

// Test 7: An array should be rejected.
$_POST['test_value'] = ['unexpected', 'array'];

checkSecurityTest(
    'Array POST value is rejected',
    getPostString('test_value') === null
);

// Test 8: An oversized value should be rejected.
$_POST['test_value'] = str_repeat('x', 250);

checkSecurityTest(
    'Oversized POST value is rejected',
    getPostString('test_value', 200) === null
);

// Test 9: Missing POST values should be rejected.
unset($_POST['test_value']);

checkSecurityTest(
    'Missing POST value is rejected',
    getPostString('test_value') === null
);

// Test 10: Request method checking should work.
$_SERVER['REQUEST_METHOD'] = 'POST';

checkSecurityTest(
    'POST method is recognized',
    isPostRequest()
);

$_SERVER['REQUEST_METHOD'] = 'GET';

checkSecurityTest(
    'GET method is not treated as POST',
    !isPostRequest()
);

// Print the final test summary.
echo "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

exit($failed === 0 ? 0 : 1);
