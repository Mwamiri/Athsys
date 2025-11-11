<?php
require_once 'config/database.php';

header('Content-Type: text/plain');

echo "=== USERS TABLE DIAGNOSTIC ===\n\n";

try {
    // Check if users table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (empty($tables)) {
        echo "❌ ERROR: users table does NOT exist!\n";
        echo "ACTION: Run installer.php to import the schema\n";
        exit;
    }
    echo "✅ users table exists\n\n";

    // Get all users
    $stmt = $pdo->query("SELECT id, email, first_name, last_name, role, is_active, password_hash FROM users");
    $users = $stmt->fetchAll();

    if (empty($users)) {
        echo "❌ ERROR: No users in database!\n";
        echo "ACTION: Run fix-users.php to create demo users\n";
        exit;
    }

    echo "Found " . count($users) . " user(s):\n\n";

    foreach ($users as $user) {
        echo "ID: {$user['id']}\n";
        echo "Email: {$user['email']}\n";
        echo "Name: {$user['first_name']} {$user['last_name']}\n";
        echo "Role: {$user['role']}\n";
        echo "Active: " . ($user['is_active'] ? 'Yes' : 'No') . "\n";
        echo "Password Hash: " . substr($user['password_hash'], 0, 30) . "...\n";
        
        // Test password
        $test_password = 'password123';
        $verify = password_verify($test_password, $user['password_hash']);
        echo "Password 'password123' works: " . ($verify ? '✅ YES' : '❌ NO') . "\n";
        echo str_repeat('-', 50) . "\n\n";
    }

    echo "\n=== RECOMMENDED ACTION ===\n\n";
    
    $admin = array_filter($users, fn($u) => $u['email'] === 'admin@example.com');
    if (empty($admin)) {
        echo "❌ admin@example.com does NOT exist\n";
        echo "   → Visit: http://your-domain.com/fix-users.php\n";
    } else {
        $admin = reset($admin);
        if (password_verify('password123', $admin['password_hash'])) {
            echo "✅ admin@example.com password is CORRECT\n";
            echo "   → Try clearing browser cache and login again\n";
        } else {
            echo "❌ admin@example.com password hash is WRONG\n";
            echo "   → Visit: http://your-domain.com/fix-users.php\n";
        }
    }

} catch (Exception $e) {
    echo "❌ DATABASE ERROR: " . $e->getMessage() . "\n";
    echo "Check config/database.php credentials\n";
}
