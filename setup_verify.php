<?php
// GoSocially Setup Verification Script
// Helps verify that SQL files were imported correctly

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>GoSocially Setup Verification</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
        .info { color: #17a2b8; }
        .section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .section h2 { margin-top: 0; color: #495057; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .step { margin: 15px 0; padding: 15px; border-left: 4px solid #007bff; background: #f8f9fa; }
        .file-list { background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .code { background: #f1f3f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #1e7e34; }
    </style>
</head>
<body>
    <h1>🔧 GoSocially Setup Verification</h1>
    <p>This script helps verify that your GoSocially application is properly set up.</p>

    <?php
    // Step 1: Check if we're in XAMMP
    echo "<div class='section'>";
    echo "<h2>Step 1: Environment Check</h2>";

    $isXampp = (strpos($_SERVER['SERVER_SOFTWARE'] ?? '', 'Apache') !== false) ||
               (strpos(strtolower($_SERVER['SERVER_SOFTWARE'] ?? ''), 'xampp') !== false) ||
               ($_SERVER['SERVER_NAME'] === 'localhost');

    if ($isXampp) {
        echo "<p class='success'>✓ Running on localhost/XAMMP environment</p>";
    } else {
        echo "<p class='warning'>⚠ May not be running on XAMMP localhost</p>";
    }

    echo "<p><strong>Server Info:</strong></p>";
    echo "<pre>";
    echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
    echo "Host: " . $_SERVER['HTTP_HOST'] . "\n";
    echo "Port: " . $_SERVER['SERVER_PORT'] . "\n";
    echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo "Current Path: " . __DIR__ . "\n";
    echo "</pre>";
    echo "</div>";

    // Step 2: Check required files
    echo "<div class='section'>";
    echo "<h2>Step 2: Required Files Check</h2>";

    $requiredFiles = [
        'config/database.php' => 'Database configuration',
        'includes/auth.php' => 'Authentication functions',
        'index.php' => 'Main login page',
        'database/schema.sql' => 'Database structure',
        'database/seed_data.sql' => 'Test data',
    ];

    $allFilesExist = true;
    foreach ($requiredFiles as $file => $description) {
        if (file_exists($file)) {
            echo "<p class='success'>✓ $file - $description</p>";
        } else {
            echo "<p class='error'>✗ $file - Missing! $description</p>";
            $allFilesExist = false;
        }
    }

    if (!$allFilesExist) {
        echo "<div class='warning'>";
        echo "<h4>Missing Files Detected!</h4>";
        echo "<p>Make sure you have copied all GoSocially files to your XAMMP htdocs directory.</p>";
        echo "</div>";
    }

    echo "</div>";

    // Step 3: Database connection test
    echo "<div class='section'>";
    echo "<h2>Step 3: Database Connection Test</h2>";

    try {
        require_once 'config/database.php';
        $db = Database::getInstance();
        $connection = $db->getConnection();
        echo "<p class='success'>✓ Database connection successful</p>";

        // Check database name
        $stmt = $connection->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch();
        $currentDb = $result['db_name'];

        echo "<p>Connected to database: <strong>$currentDb</strong></p>";

        if ($currentDb === 'gosocially') {
            echo "<p class='success'>✓ Connected to correct database</p>";
        } else {
            echo "<p class='error'>✗ Wrong database! Expected 'gosocially', got '$currentDb'</p>";
            echo "<p class='info'>Create database named 'gosocially' in phpMyAdmin</p>";
        }

    } catch (Exception $e) {
        echo "<p class='error'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<div class='warning'>";
        echo "<h4>Database Connection Issues:</h4>";
        echo "<ul>";
        echo "<li>Make sure MySQL is running in XAMMP</li>";
        echo "<li>Check that database 'gosocially' exists</li>";
        echo "<li>Verify MySQL credentials (root/empty password by default)</li>";
        echo "</ul>";
        echo "</div>";
    }

    echo "</div>";

    // Step 4: Table structure verification
    echo "<div class='section'>";
    echo "<h2>Step 4: Database Tables Check</h2>";

    try {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        // Get all tables
        $stmt = $connection->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $expectedTables = ['users', 'messages', 'invite_codes', 'user_restrictions', 'reports'];
        $missingTables = [];

        echo "<h3>Expected Tables:</h3>";
        foreach ($expectedTables as $table) {
            if (in_array($table, $tables)) {
                echo "<p class='success'>✓ $table</p>";
            } else {
                echo "<p class='error'>✗ $table - Missing!</p>";
                $missingTables[] = $table;
            }
        }

        if (!empty($missingTables)) {
            echo "<div class='error'>";
            echo "<h4>Missing Tables!</h4>";
            echo "<p>You need to import the schema.sql file:</p>";
            echo "<ol>";
            echo "<li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>";
            echo "<li>Select the 'gosocially' database</li>";
            echo "<li>Click 'Import' tab</li>";
            echo "<li>Choose file: <span class='code'>database/schema.sql</span></li>";
            echo "<li>Click 'Go' to import</li>";
            echo "</ol>";
            echo "</div>";
        }

        if (empty($missingTables) && !empty($tables)) {
            echo "<p class='success'>✓ All required tables exist</p>";
        }

        echo "<h3>All Tables Found:</h3>";
        echo "<div class='file-list'>";
        echo implode("<br>", $tables);
        echo "</div>";

    } catch (Exception $e) {
        echo "<p class='error'>Error checking tables: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    echo "</div>";

    // Step 5: Seed data verification
    echo "<div class='section'>";
    echo "<h2>Step 5: Seed Data Check</h2>";

    try {
        $db = Database::getInstance();
        $connection = $db->getConnection();

        // Check users
        $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        $userCount = $result['count'];

        echo "<p><strong>Users in database:</strong> $userCount</p>";

        if ($userCount > 0) {
            echo "<p class='success'>✓ Seed data found</p>";

            // Show users
            $stmt = $connection->query("SELECT id, username, email, role, is_active FROM users ORDER BY id");
            $users = $stmt->fetchAll();

            echo "<h3>Test Accounts Available:</h3>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Password</th></tr>";

            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>" . $user['id'] . "</td>";
                echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                echo "<td>" . htmlspecialchars($user['role']) . "</td>";
                echo "<td>" . ($user['is_active'] ? '✓' : '✗') . "</td>";
                echo "<td>password123</td>";
                echo "</tr>";
            }
            echo "</table>";

            echo "<div class='info'>";
            echo "<h4>🎯 Ready to Test Login!</h4>";
            echo "<p>You can now try logging in with any of the accounts above.</p>";
            echo "<p><strong>All accounts use password:</strong> <code>password123</code></p>";
            echo "</div>";

        } else {
            echo "<p class='error'>✗ No users found - seed data not imported</p>";
            echo "<div class='warning'>";
            echo "<h4>Import Seed Data:</h4>";
            echo "<ol>";
            echo "<li>Open phpMyAdmin (http://localhost/phpmyadmin)</li>";
            echo "<li>Select the 'gosocially' database</li>";
            echo "<li>Click 'Import' tab</li>";
            echo "<li>Choose file: <span class='code'>database/seed_data.sql</span></li>";
            echo "<li>Click 'Go' to import</li>";
            echo "</ol>";
            echo "</div>";
        }

    } catch (Exception $e) {
        echo "<p class='error'>Error checking seed data: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    echo "</div>";

    // Step 6: Final verification
    echo "<div class='section'>";
    echo "<h2>Step 6: Setup Complete?</h2>";

    $setupComplete = true;

    try {
        // Quick checks
        $db = Database::getInstance();
        $connection = $db->getConnection();

        $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        $userCount = $result['count'];

        if ($userCount === 0) {
            $setupComplete = false;
        }

    } catch (Exception $e) {
        $setupComplete = false;
    }

    if ($setupComplete) {
        echo "<div class='success'>";
        echo "<h3>🎉 Setup Complete!</h3>";
        echo "<p>Your GoSocially application is properly set up and ready to use.</p>";
        echo "<a href='index.php' class='btn btn-success'>Go to Login Page</a>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>⚠ Setup Incomplete</h3>";
        echo "<p>Please complete the missing steps above before testing login.</p>";
        echo "<a href='?refresh=1' class='btn'>Re-check Setup</a>";
        echo "</div>";
    }

    echo "</div>";
    ?>

    <div class="section">
        <h2>📚 Additional Resources</h2>
        <ul>
            <li><a href="debug.php">Go to Debug Center</a> - Advanced troubleshooting tools</li>
            <li><a href="test_db.php">Database Test</a> - Simple database connectivity test</li>
            <li><a href="index.php">Login Page</a> - Go to main login page</li>
        </ul>
    </div>

    <div class="section">
        <h2>🚀 Quick Setup Commands</h2>
        <p>If you have command line access to MySQL:</p>
        <pre># Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS gosocially;"

# Import schema (from GoSocially directory)
mysql -u root -p gosocially < database/schema.sql

# Import seed data
mysql -u root -p gosocially < database/seed_data.sql</pre>
    </div>

    <footer style="margin-top: 50px; padding: 20px; background: #f8f9fa; text-align: center;">
        <p><strong>GoSocially Setup Verification</strong></p>
        <p>Run this script after setting up your database to verify everything is working.</p>
    </footer>
</body>
</html>