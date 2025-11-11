<?php
/**
 * Login Troubleshooting Diagnostic Script
 * Tests database, users table, and demo accounts
 */

require_once 'config/database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Troubleshooting</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 20px auto; padding: 20px; }
        .test { margin: 15px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .pass { background: #d4edda; border-color: #28a745; }
        .fail { background: #f8d7da; border-color: #dc3545; }
        .warn { background: #fff3cd; border-color: #ffc107; }
        h2 { color: #333; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h1>🔐 Login Troubleshooting Report</h1>
    <hr>

    <?php
    
    // Test 1: Check Users Table Exists
    echo "<div class='test'>";
    echo "<h2>Test 1: Users Table Check</h2>";
    
    try {
        $query = "SHOW TABLES LIKE 'users'";
        $stmt = $pdo->query($query);
        $table_exists = $stmt->fetch();
        
        if ($table_exists) {
            echo "<div class='pass'>✅ Users table EXISTS</div>";
            
            // Check table structure
            echo "<h3>Table Structure:</h3>";
            $query = "DESCRIBE users";
            $columns = $pdo->query($query)->fetchAll();
            echo "<table>";
            echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td><code>" . $col['Field'] . "</code></td>";
                echo "<td>" . $col['Type'] . "</td>";
                echo "<td>" . $col['Null'] . "</td>";
                echo "<td>" . $col['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "<div class='fail'>❌ Users table DOES NOT EXIST</div>";
            echo "<p><strong>ACTION REQUIRED:</strong> Run the installer to import the database schema.</p>";
            echo "<ol>";
            echo "<li>Visit: <code>http://your-domain.com/installer.php</code></li>";
            echo "<li>Follow all installation steps</li>";
            echo "<li>Ensure schema import completes successfully</li>";
            echo "<li>Then return here to verify</li>";
            echo "</ol>";
        }
    } catch (Exception $e) {
        echo "<div class='fail'>❌ Error checking users table: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 2: Check Users in Database
    echo "<div class='test'>";
    echo "<h2>Test 2: Demo Users Check</h2>";
    
    try {
        $query = "SELECT COUNT(*) as count FROM users";
        $stmt = $pdo->query($query);
        $result = $stmt->fetch();
        $user_count = $result['count'] ?? 0;
        
        if ($user_count > 0) {
            echo "<div class='pass'>✅ Database has {$user_count} user(s)</div>";
            
            echo "<h3>Users in Database:</h3>";
            $query = "SELECT id, email, first_name, last_name, role, is_active FROM users";
            $users = $pdo->query($query)->fetchAll();
            
            echo "<table>";
            echo "<tr><th>Email</th><th>Name</th><th>Role</th><th>Active</th></tr>";
            foreach ($users as $user) {
                $active = $user['is_active'] ? '✅' : '❌';
                echo "<tr>";
                echo "<td><code>" . $user['email'] . "</code></td>";
                echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
                echo "<td>" . $user['role'] . "</td>";
                echo "<td>{$active}</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "<div class='fail'>❌ No users found in database</div>";
            echo "<p><strong>ACTION REQUIRED:</strong> The demo users were not created.</p>";
            echo "<p>This happens when the schema import didn't complete successfully.</p>";
            echo "<ol>";
            echo "<li>Go to installer: <code>http://your-domain.com/installer.php</code></li>";
            echo "<li>Go to Step 4: Import Schema</li>";
            echo "<li>Wait for it to complete (may take 30-60 seconds)</li>";
            echo "<li>Check for error messages</li>";
            echo "</ol>";
        }
    } catch (Exception $e) {
        echo "<div class='fail'>❌ Error checking users: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 3: Verify Demo Accounts Can Login
    echo "<div class='test'>";
    echo "<h2>Test 3: Test Login Credentials</h2>";
    
    $demo_creds = [
        ['email' => 'admin@example.com', 'password' => 'password123', 'role' => 'Admin'],
        ['email' => 'coach@example.com', 'password' => 'password123', 'role' => 'Coach'],
        ['email' => 'athlete@example.com', 'password' => 'password123', 'role' => 'Athlete']
    ];
    
    try {
        echo "<h3>Testing Demo Credentials:</h3>";
        echo "<table>";
        echo "<tr><th>Email</th><th>Password</th><th>Status</th><th>Can Login</th></tr>";
        
        foreach ($demo_creds as $cred) {
            $query = "SELECT * FROM users WHERE email = ? AND is_active = 1";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$cred['email']]);
            $user = $stmt->fetch();
            
            $can_login = "❌ No";
            $status = "Not found";
            
            if ($user) {
                $status = "Found (" . $user['role'] . ")";
                if (password_verify($cred['password'], $user['password_hash'])) {
                    $can_login = "✅ Yes";
                } else {
                    $can_login = "❌ No (wrong password)";
                }
            }
            
            echo "<tr>";
            echo "<td><code>" . $cred['email'] . "</code></td>";
            echo "<td><code>" . $cred['password'] . "</code></td>";
            echo "<td>" . $status . "</td>";
            echo "<td>" . $can_login . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<div class='fail'>❌ Error testing credentials: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 4: Connection Info
    echo "<div class='test'>";
    echo "<h2>Test 4: Database Connection Info</h2>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
    echo "<li><strong>Database:</strong> " . DB_NAME . "</li>";
    echo "<li><strong>User:</strong> " . DB_USER . "</li>";
    echo "<li><strong>Charset:</strong> " . DB_CHARSET . "</li>";
    echo "</ul>";
    echo "</div>";
    
    // Summary
    echo "<div class='test warn'>";
    echo "<h2>📋 Summary</h2>";
    if ($table_exists && $user_count > 0) {
        echo "<p>✅ Database is properly configured and contains users.</p>";
        echo "<p>If login is still not working:</p>";
        echo "<ol>";
        echo "<li>Try clearing your browser cache</li>";
        echo "<li>Try incognito/private browsing mode</li>";
        echo "<li>Check browser console for JavaScript errors (F12)</li>";
        echo "<li>Use the demo credentials shown above</li>";
        echo "</ol>";
    } else {
        echo "<p>❌ Database schema is incomplete.</p>";
        echo "<p><strong>NEXT STEP:</strong> Run the installer to complete schema import:</p>";
        echo "<code>http://your-domain.com/installer.php</code>";
    }
    echo "</div>";
    
    ?>

</body>
</html>
