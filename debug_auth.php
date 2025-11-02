<?php
// GoSocially Authentication Debug Helper
// Enhanced debugging for login issues

require_once 'includes/auth.php';

// Enable detailed debugging
function debugLogin($username, $password) {
    echo "<div class='debug-section'>";
    echo "<h3>Login Debug Information</h3>";

    echo "<p><strong>Input:</strong></p>";
    echo "<pre>";
    echo "Username: '" . htmlspecialchars($username) . "'\n";
    echo "Password: '" . (empty($password) ? "(empty)" : str_repeat('*', strlen($password))) . "'\n";
    echo "Password Length: " . strlen($password) . " characters\n";
    echo "</pre>";

    try {
        // Database connection test
        $db = Database::getInstance();
        $connection = $db->getConnection();
        echo "<p class='success'>✓ Database connection successful</p>";

        // Query user
        $sql = "SELECT id, username, email, password_hash, full_name, role, is_active
                FROM users
                WHERE username = ? OR email = ?
                LIMIT 1";

        echo "<p><strong>Query:</strong> " . htmlspecialchars($sql) . "</p>";
        echo "<p><strong>Parameters:</strong> ['" . htmlspecialchars($username) . "', '" . htmlspecialchars($username) . "']</p>";

        $stmt = $connection->prepare($sql);
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "<p class='error'>✗ No user found with username/email: '" . htmlspecialchars($username) . "'</p>";

            // Show available users
            $allUsers = $connection->query("SELECT username, email FROM users LIMIT 5")->fetchAll();
            if ($allUsers) {
                echo "<p class='info'><strong>Available users:</strong></p>";
                echo "<ul>";
                foreach ($allUsers as $u) {
                    echo "<li>" . htmlspecialchars($u['username']) . " (" . htmlspecialchars($u['email']) . ")</li>";
                }
                echo "</ul>";
            }
            return false;
        }

        echo "<p class='success'>✓ User found: " . htmlspecialchars($user['username']) . "</p>";
        echo "<p><strong>User details:</strong></p>";
        echo "<pre>";
        echo "ID: " . $user['id'] . "\n";
        echo "Username: " . htmlspecialchars($user['username']) . "\n";
        echo "Email: " . htmlspecialchars($user['email']) . "\n";
        echo "Role: " . htmlspecialchars($user['role']) . "\n";
        echo "Active: " . ($user['is_active'] ? 'Yes' : 'No') . "\n";
        echo "Password Hash: " . substr($user['password_hash'], 0, 20) . "...\n";
        echo "</pre>";

        // Check if user is active
        if (!$user['is_active']) {
            echo "<p class='error'>✗ Account is disabled (is_active = 0)</p>";
            return false;
        }

        echo "<p class='success'>✓ User account is active</p>";

        // Test password verification
        echo "<p><strong>Testing password verification:</strong></p>";

        if (password_verify($password, $user['password_hash'])) {
            echo "<p class='success'>✓ Password verification successful</p>";
        } else {
            echo "<p class='error'>✗ Password verification failed</p>";

            // Test with known password
            if (password_verify('password123', $user['password_hash'])) {
                echo "<p class='info'>ℹ Hint: The correct password for this user is 'password123'</p>";
            }

            return false;
        }

        // Check user restrictions
        echo "<p><strong>Checking user restrictions...</strong></p>";
        $loginCheck = canUserLogin($user['id']);

        if (!$loginCheck['can_login']) {
            echo "<p class='error'>✗ Login blocked: " . htmlspecialchars($loginCheck['reason']) . "</p>";
            return false;
        }

        echo "<p class='success'>✓ No user restrictions found</p>";

        echo "<p class='success'>✓ Login should be successful!</p>";
        return true;

    } catch (Exception $e) {
        echo "<p class='error'>✗ Debug error: " . htmlspecialchars($e->getMessage()) . "</p>";
        return false;
    }

    echo "</div>";
}

// Test specific user credentials
function testCredentials($username, $password) {
    echo "<div class='test-section'>";
    echo "<h2>Testing Login Credentials</h2>";

    $result = debugLogin($username, $password);

    if ($result) {
        echo "<p class='success'><strong>✓ These credentials should work for login!</strong></p>";
    } else {
        echo "<p class='error'><strong>✗ These credentials will fail login.</strong></p>";
    }

    echo "</div>";
}

// Show database users
function showAllUsers() {
    try {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $stmt = $connection->query("SELECT id, username, email, role, is_active, created_at FROM users ORDER BY id");
        $users = $stmt->fetchAll();

        echo "<div class='users-section'>";
        echo "<h2>All Users in Database</h2>";

        if (empty($users)) {
            echo "<p class='error'>No users found in database. Please import seed_data.sql</p>";
        } else {
            echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'>";
            echo "<th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Created</th><th>Test Login</th>";
            echo "</tr>";

            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>" . ($user['is_active'] ? '✓' : '✗') . "</td>";
                echo "<td>" . $user['created_at'] . "</td>";
                echo "<td><a href='?test_user=" . urlencode($user['username']) . "'>Test</a></td>";
                echo "</tr>";
            }
            echo "</table>";

            echo "<p class='info'><strong>All test accounts use password: password123</strong></p>";
        }

        echo "</div>";

    } catch (Exception $e) {
        echo "<p class='error'>Error fetching users: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>