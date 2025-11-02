<?php
// GoSocially Complete Debug and Test Page
// Comprehensive troubleshooting for login issues

error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>GoSocially Debug Center</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; overflow-x: auto; }
        .section { margin: 30px 0; padding: 20px; border: 1px solid #dee2e6; border-radius: 8px; background: #fff; }
        .section h2 { margin-top: 0; color: #495057; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .section h3 { color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #dee2e6; }
        th { background: #f8f9fa; font-weight: bold; }
        .test-form { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .form-group { margin: 10px 0; }
        .form-group label { display: inline-block; width: 100px; font-weight: bold; }
        .form-group input { padding: 8px; border: 1px solid #ced4da; border-radius: 4px; width: 200px; }
        .btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .nav { margin: 20px 0; }
        .nav a { margin-right: 15px; padding: 8px 15px; background: #e9ecef; text-decoration: none; border-radius: 4px; }
        .nav a:hover { background: #dee2e6; }
        .nav a.active { background: #007bff; color: white; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <h1>🔍 GoSocially Debug Center</h1>
    <p>This page helps diagnose and fix login issues with your GoSocially application.</p>

    <div class="nav">
        <a href="?section=database" <?php echo ($_GET['section'] ?? '') === 'database' ? 'class="active"' : ''; ?>>Database Test</a>
        <a href="?section=auth" <?php echo ($_GET['section'] ?? '') === 'auth' ? 'class="active"' : ''; ?>>Auth Debug</a>
        <a href="?section=users" <?php echo ($_GET['section'] ?? '') === 'users' ? 'class="active"' : ''; ?>>All Users</a>
        <a href="?section=test" <?php echo ($_GET['section'] ?? '') === 'test' ? 'class="active"' : ''; ?>>Test Login</a>
        <a href="?section=guide" <?php echo ($_GET['section'] ?? '') === 'guide' ? 'class="active"' : ''; ?>>Troubleshooting Guide</a>
    </div>

    <?php
    $section = $_GET['section'] ?? 'database';

    switch ($section) {
        case 'database':
            include 'test_db.php';
            break;

        case 'auth':
            ?>
            <div class="section">
                <h2>🔐 Authentication Debug</h2>

                <?php
                if (isset($_GET['test_user'])) {
                    require_once 'debug_auth.php';
                    testCredentials($_GET['test_user'], 'password123');
                    echo '<p><a href="?section=auth">← Back to Auth Debug</a></p>';
                } else {
                    echo '<p>Use the "All Users" section to test specific user credentials.</p>';
                    echo '<p><a href="?section=users">View All Users →</a></p>';
                }
                ?>

                <h3>Test Custom Credentials</h3>
                <div class="test-form">
                    <form method="get" action="">
                        <input type="hidden" name="section" value="auth">
                        <div class="form-group">
                            <label>Username/Email:</label>
                            <input type="text" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" name="password" required>
                        </div>
                        <button type="submit" class="btn">Test Login</button>
                    </form>
                </div>

                <?php
                if (isset($_GET['username']) && isset($_GET['password'])) {
                    require_once 'debug_auth.php';
                    debugLogin($_GET['username'], $_GET['password']);
                }
                ?>
            </div>
            <?php
            break;

        case 'users':
            ?>
            <div class="section">
                <h2>👥 Database Users</h2>
                <?php
                require_once 'debug_auth.php';
                showAllUsers();
                ?>
            </div>
            <?php
            break;

        case 'test':
            ?>
            <div class="section">
                <h2>🧪 Quick Login Test</h2>
                <p>Try these known test credentials:</p>

                <div class="grid">
                    <div class="test-form">
                        <h4>Admin Account</h4>
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" name="username" value="admin" readonly>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" name="password" value="password123">
                            </div>
                            <button type="submit" class="btn">Test Admin Login</button>
                        </form>
                    </div>

                    <div class="test-form">
                        <h4>Moderator Account</h4>
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" name="username" value="moderator" readonly>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" name="password" value="password123">
                            </div>
                            <button type="submit" class="btn">Test Moderator Login</button>
                        </form>
                    </div>

                    <div class="test-form">
                        <h4>Test User 1</h4>
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" name="username" value="user1" readonly>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" name="password" value="password123">
                            </div>
                            <button type="submit" class="btn">Test User1 Login</button>
                        </form>
                    </div>

                    <div class="test-form">
                        <h4>Test User 2</h4>
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <label>Username:</label>
                                <input type="text" name="username" value="user2" readonly>
                            </div>
                            <div class="form-group">
                                <label>Password:</label>
                                <input type="password" name="password" value="password123">
                            </div>
                            <button type="submit" class="btn">Test User2 Login</button>
                        </form>
                    </div>
                </div>

                <div class="info">
                    <h4>💡 Important Notes:</h4>
                    <ul>
                        <li>All test accounts use the same password: <strong>password123</strong></li>
                        <li>You can use either username or email to login</li>
                        <li>If login fails, check the error message on the login page</li>
                        <li>Come back to this debug page to investigate issues</li>
                    </ul>
                </div>
            </div>
            <?php
            break;

        case 'guide':
            ?>
            <div class="section">
                <h2>📚 Complete Troubleshooting Guide</h2>

                <h3>Step 1: XAMMP Setup</h3>
                <div class="warning">
                    <h4>Required Services:</h4>
                    <ol>
                        <li><strong>Apache:</strong> Must be running (green status in XAMMP)</li>
                        <li><strong>MySQL:</strong> Must be running (green status in XAMMP)</li>
                        <li><strong>File Placement:</strong> GoSocially folder must be in XAMMP/htdocs/</li>
                    </ol>

                    <h4>Common Issues:</h4>
                    <ul>
                        <li><strong>Port 80 blocked:</strong> Change Apache port to 8080 in XAMMP config</li>
                        <li><strong>MySQL won't start:</strong> Another MySQL instance might be running</li>
                        <li><strong>Access denied:</strong> Check file permissions on htdocs folder</li>
                    </ul>
                </div>

                <h3>Step 2: Database Setup</h3>
                <div class="info">
                    <h4>Required Actions:</h4>
                    <ol>
                        <li>Open phpMyAdmin: http://localhost/phpmyadmin</li>
                        <li>Create database named: <strong>gosocially</strong></li>
                        <li>Import: <code>database/schema.sql</code></li>
                        <li>Import: <code>database/seed_data.sql</code></li>
                    </ol>

                    <h4>Verification:</h4>
                    <ul>
                        <li>Database should have 5+ tables</li>
                        <li>Users table should have 5 users</li>
                        <li>All users should have <code>is_active = 1</code></li>
                    </ul>
                </div>

                <h3>Step 3: Test Login Process</h3>
                <div class="success">
                    <h4>Working Credentials:</h4>
                    <ul>
                        <li><strong>Username:</strong> admin, <strong>Password:</strong> password123</li>
                        <li><strong>Username:</strong> moderator, <strong>Password:</strong> password123</li>
                        <li><strong>Username:</strong> user1, <strong>Password:</strong> password123</li>
                        <li><strong>Username:</strong> user2, <strong>Password:</strong> password123</li>
                        <li><strong>Username:</strong> user3, <strong>Password:</strong> password123</li>
                    </ul>

                    <h4>Email Login (also works):</h4>
                    <ul>
                        <li><strong>Email:</strong> admin@example.com, <strong>Password:</strong> password123</li>
                        <li><strong>Email:</strong> moderator@example.com, <strong>Password:</strong> password123</li>
                    </ul>
                </div>

                <h3>Step 4: Common Error Messages</h3>
                <div class="grid">
                    <div>
                        <h4 class="error">"Invalid username or password"</h4>
                        <ul>
                            <li>Database connection failed</li>
                            <li>No users in database</li>
                            <li>Wrong password used</li>
                            <li>Username doesn't exist</li>
                            <li>User account disabled</li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="error">"Database connection failed"</h4>
                        <ul>
                            <li>MySQL service not running</li>
                            <li>Wrong database credentials</li>
                            <li>Database doesn't exist</li>
                            <li>Port number incorrect</li>
                        </ul>
                    </div>
                </div>

                <h3>Step 5: Debugging Tools</h3>
                <div class="info">
                    <h4>Use This Debug Center:</h4>
                    <ol>
                        <li><strong>Database Test:</strong> Verify database connectivity and data</li>
                        <li><strong>All Users:</strong> See all users in database</li>
                        <li><strong>Auth Debug:</strong> Test specific credentials</li>
                        <li><strong>Test Login:</strong> Quick login with test accounts</li>
                    </ol>
                </div>

                <h3>Step 6: Advanced Issues</h3>
                <div class="warning">
                    <h4>If Still Not Working:</h4>
                    <ul>
                        <li>Check XAMMP error logs</li>
                        <li>Verify PHP has required extensions (pdo_mysql)</li>
                        <li>Check session.save_path in php.ini</li>
                        <li>Try different web browser</li>
                        <li>Clear browser cookies and cache</li>
                        <li>Restart XAMMP services</li>
                    </ul>
                </div>

                <h3>🚀 Quick Start Checklist</h3>
                <div class="success">
                    <ol>
                        <li>☐ XAMMP Apache and MySQL running</li>
                        <li>☐ Database 'gosocially' created</li>
                        <li>☐ schema.sql imported</li>
                        <li>☐ seed_data.sql imported</li>
                        <li>☐ GoSocially folder in htdocs</li>
                        <li>☐ Access http://localhost/GoSocially/debug.php</li>
                        <li>☐ Run database test - all should pass</li>
                        <li>☐ Try login: admin / password123</li>
                    </ol>
                </div>
            </div>
            <?php
            break;
    }
    ?>

    <div class="section">
        <h2>🔗 Quick Links</h2>
        <ul>
            <li><a href="index.php">→ Go to Login Page</a></li>
            <li><a href="?section=database">→ Test Database Connection</a></li>
            <li><a href="?section=users">→ View All Users</a></li>
            <li><a href="?section=test">→ Test Login Credentials</a></li>
        </ul>
    </div>

    <footer style="margin-top: 50px; padding: 20px; background: #f8f9fa; text-align: center;">
        <p><strong>GoSocially Debug Center</strong> - Troubleshooting login issues</p>
        <p>If you need further help, check the error logs in XAMMP or contact support.</p>
    </footer>
</body>
</html>