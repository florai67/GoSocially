<?php
// GoSocially Database Connection Test
// This script helps diagnose database connectivity issues

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>GoSocially Database Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>GoSocially Database Connection Test</h1>

    <div class='test-section'>
        <h2>Step 1: Testing Database Connection</h2>";

try {
    // Test basic connection
    require_once 'config/database.php';
    echo "<p class='success'>✓ Database configuration file loaded successfully</p>";

    // Show database configuration (without password)
    echo "<p class='info'><strong>Database Configuration:</strong></p>";
    echo "<pre>";
    echo "Host: " . DB_HOST . "\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Username: " . DB_USER . "\n";
    echo "Password: " . (empty(DB_PASS) ? "(empty)" : "(hidden)") . "\n";
    echo "Charset: " . DB_CHARSET . "\n";
    echo "</pre>";

    // Test database connection
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "<p class='success'>✓ Database connection: SUCCESS</p>";

    // Test database selection
    $stmt = $connection->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch();
    echo "<p class='info'>Current database: <strong>" . htmlspecialchars($result['current_db']) . "</strong></p>";

    if ($result['current_db'] !== DB_NAME) {
        echo "<p class='warning'>⚠ Warning: Connected to different database than expected</p>";
    } else {
        echo "<p class='success'>✓ Connected to correct database</p>";
    }

} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection: FAILED</p>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";

    // Provide troubleshooting hints
    echo "<div class='warning'>";
    echo "<h3>Troubleshooting Tips:</h3>";
    echo "<ul>";
    echo "<li>Make sure XAMMP MySQL service is running</li>";
    echo "<li>Check that database 'gosocially' exists</li>";
    echo "<li>Verify MySQL credentials (root/empty password by default)</li>";
    echo "<li>Check if MySQL is running on default port 3306</li>";
    echo "</ul>";
    echo "</div>";
    echo "</body></html>";
    exit;
}

echo "</div>";

// Test table existence
echo "<div class='test-section'>
        <h2>Step 2: Testing Table Structure</h2>";

try {
    $tables = ['users', 'messages', 'invite_codes', 'user_restrictions', 'reports'];

    foreach ($tables as $table) {
        $stmt = $connection->query("SHOW TABLES LIKE '" . $table . "'");
        $exists = $stmt->rowCount() > 0;

        if ($exists) {
            echo "<p class='success'>✓ Table '$table' exists</p>";
        } else {
            echo "<p class='error'>✗ Table '$table' missing</p>";
        }
    }

    // Get table structures
    $stmt = $connection->query("SHOW TABLES");
    $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo "<p class='info'><strong>All tables in database:</strong></p>";
    echo "<pre>" . implode("\n", $allTables) . "</pre>";

} catch (Exception $e) {
    echo "<p class='error'>Error checking tables: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div>";

// Test seed data
echo "<div class='test-section'>
        <h2>Step 3: Testing Seed Data</h2>";

try {
    // Check users table
    $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $userCount = $result['count'];

    echo "<p class='info'>Users in database: <strong>$userCount</strong></p>";

    if ($userCount > 0) {
        echo "<p class='success'>✓ Seed data: FOUND</p>";

        // Show sample users
        $stmt = $connection->query("SELECT id, username, email, is_active, role FROM users LIMIT 5");
        $users = $stmt->fetchAll();

        echo "<p class='info'><strong>Sample users:</strong></p>";
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th></tr>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Test password verification with known credentials
        echo "<p class='info'><strong>Testing password verification:</strong></p>";
        $stmt = $connection->query("SELECT username, password_hash FROM users LIMIT 1");
        $testUser = $stmt->fetch();

        if ($testUser && password_verify('password123', $testUser['password_hash'])) {
            echo "<p class='success'>✓ Password verification works for user: " . htmlspecialchars($testUser['username']) . "</p>";
        } else {
            echo "<p class='error'>✗ Password verification failed</p>";
        }

    } else {
        echo "<p class='error'>✗ Seed data: NOT FOUND</p>";
        echo "<p class='warning'>Please import seed_data.sql to create test accounts</p>";
    }

    // Check invite codes
    $stmt = $connection->query("SELECT COUNT(*) as count FROM invite_codes WHERE is_used = 0");
    $result = $stmt->fetch();
    $inviteCodeCount = $result['count'];

    echo "<p class='info'>Available invite codes: <strong>$inviteCodeCount</strong></p>";

    // Check messages
    $stmt = $connection->query("SELECT COUNT(*) as count FROM messages");
    $result = $stmt->fetch();
    $messageCount = $result['count'];

    echo "<p class='info'>Sample messages: <strong>$messageCount</strong></p>";

} catch (Exception $e) {
    echo "<p class='error'>Error checking seed data: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div>";

// Test PHP requirements
echo "<div class='test-section'>
        <h2>Step 4: Testing PHP Requirements</h2>";

// Check PHP version
echo "<p class='info'>PHP Version: <strong>" . PHP_VERSION . "</strong></p>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p class='success'>✓ PHP version is compatible</p>";
} else {
    echo "<p class='warning'>⚠ Consider upgrading to PHP 7.4 or higher</p>";
}

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'session', 'hash'];
foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ Extension '$ext' is loaded</p>";
    } else {
        echo "<p class='error'>✗ Extension '$ext' is NOT loaded</p>";
    }
}

echo "</div>";

// Test file permissions
echo "<div class='test-section'>
        <h2>Step 5: Testing File Permissions</h2>";

$importantFiles = [
    'config/database.php',
    'includes/auth.php',
    'index.php'
];

foreach ($importantFiles as $file) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "<p class='success'>✓ File '$file' is readable</p>";
        } else {
            echo "<p class='error'>✗ File '$file' is NOT readable</p>";
        }
    } else {
        echo "<p class='error'>✗ File '$file' does not exist</p>";
    }
}

echo "</div>";

// Provide next steps
echo "<div class='test-section'>
        <h2>Step 6: Next Steps</h2>";

echo "<div class='info'>";
echo "<h3>If all tests passed:</h3>";
echo "<ol>";
echo "<li>Try logging in with: username = <strong>admin</strong>, password = <strong>password123</strong></li>";
echo "<li>Or use: email = <strong>admin@example.com</strong>, password = <strong>password123</strong></li>";
echo "<li>Other test accounts: moderator, user1, user2, user3 (all with password: password123)</li>";
echo "</ol>";

echo "<h3>If tests failed:</h3>";
echo "<ol>";
echo "<li>Start XAMMP MySQL service if it's not running</li>";
echo "<li>Import database schema: <code>database/schema.sql</code></li>";
echo "<li>Import seed data: <code>database/seed_data.sql</code></li>";
echo "<li>Check MySQL credentials in config/database.php</li>";
echo "<li>Verify database name is 'gosocially'</li>";
echo "</ol>";

echo "<h3>Common Issues:</h3>";
echo "<ul>";
echo "<li><strong>\"Connection refused\"</strong> - MySQL service not running</li>";
echo "<li><strong>\"Unknown database\"</strong> - Database 'gosocially' doesn't exist</li>";
echo "<li><strong>\"Access denied\"</strong> - Wrong MySQL credentials</li>";
echo "<li><strong>\"No users found\"</strong> - Seed data not imported</li>";
echo "</ul>";
echo "</div>";

echo "</div>";

echo "</body>
</html>";
?>