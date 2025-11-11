<?php
/**
 * Database User Fixer & Password Reset Tool
 */

require_once 'config/database.php';

// Password for demo accounts
$demo_password = 'password123';
$password_hash = password_hash($demo_password, PASSWORD_BCRYPT);

// Demo users to insert/update
$demo_users = [
    ['email' => 'admin@example.com', 'first' => 'Admin', 'last' => 'User', 'role' => 'administrator'],
    ['email' => 'coach@example.com', 'first' => 'John', 'last' => 'Coach', 'role' => 'coach'],
    ['email' => 'athlete@example.com', 'first' => 'Jane', 'last' => 'Runner', 'role' => 'athlete']
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Fixer</title>
    <style>
        body { font-family: Arial; max-width: 900px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { background: #d4edda; border: 1px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #dc3545; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #0c5460; color: #0c5460; }
        .button { background: #667eea; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .button:hover { background: #5568d3; }
        .button-danger { background: #dc3545; }
        .button-danger:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #667eea; color: white; }
        tr:hover { background: #f9f9f9; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Demo User Setup Tool</h1>

        <?php

        // Check if form submitted
        $action = $_POST['action'] ?? '';

        if ($action === 'fix_users') {
            echo "<div class='info'><strong>⏳ Processing...</strong> Setting up demo users...</div>";

            try {
                // Delete existing demo users (if they exist)
                $delete_emails = ['admin@example.com', 'coach@example.com', 'athlete@example.com'];
                
                // Delete existing
                $stmt = $pdo->prepare("DELETE FROM users WHERE email IN (?, ?, ?)");
                $stmt->execute($delete_emails);
                echo "<div class='status success'>✅ Cleared old users</div>";

                // Insert new demo users
                $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, first_name, last_name, role, is_active) VALUES (?, ?, ?, ?, ?, 1)");

                foreach ($demo_users as $user) {
                    $stmt->execute([
                        $user['email'],
                        $password_hash,
                        $user['first'],
                        $user['last'],
                        $user['role']
                    ]);
                }

                echo "<div class='status success'>✅ Successfully created 3 demo users!</div>";

                echo "<h2>Demo Accounts Ready</h2>";
                echo "<table>";
                echo "<tr><th>Email</th><th>Password</th><th>Role</th></tr>";
                foreach ($demo_users as $user) {
                    echo "<tr>";
                    echo "<td><code>" . $user['email'] . "</code></td>";
                    echo "<td><code>" . $demo_password . "</code></td>";
                    echo "<td>" . ucfirst($user['role']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                echo "<div class='warning'>";
                echo "<strong>⚠️ Important:</strong> Change these passwords immediately after logging in!<br>";
                echo "<a href='login.php'>→ Go to Login</a>";
                echo "</div>";

            } catch (Exception $e) {
                echo "<div class='status error'>❌ Error: " . $e->getMessage() . "</div>";
            }
        } else {
            // Show current state
            echo "<h2>Current Demo Users</h2>";

            try {
                $stmt = $pdo->query("SELECT id, email, first_name, last_name, role, is_active FROM users ORDER BY role");
                $users = $stmt->fetchAll();

                if (count($users) > 0) {
                    echo "<table>";
                    echo "<tr><th>Email</th><th>Name</th><th>Role</th><th>Active</th></tr>";
                    foreach ($users as $user) {
                        $active = $user['is_active'] ? '✅' : '❌';
                        echo "<tr>";
                        echo "<td><code>" . $user['email'] . "</code></td>";
                        echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
                        echo "<td>" . ucfirst($user['role']) . "</td>";
                        echo "<td>{$active}</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    
                    echo "<div class='warning'>";
                    echo "<strong>⚠️ Current users might have wrong password hashes.</strong><br>";
                    echo "Click the button below to fix them with the correct password: <code>" . $demo_password . "</code>";
                    echo "</div>";
                } else {
                    echo "<div class='status error'>❌ No users found in database</div>";
                    echo "<p>The users table is empty. This usually means the schema import didn't complete.</p>";
                }

            } catch (Exception $e) {
                echo "<div class='status error'>❌ Error: " . $e->getMessage() . "</div>";
            }

            // Show fix button
            echo "<h2>Fix Demo Users</h2>";
            echo "<form method='POST' style='margin: 20px 0;'>";
            echo "<input type='hidden' name='action' value='fix_users'>";
            echo "<button type='submit' class='button button-danger'>🔧 Fix/Reset Demo Users</button>";
            echo "</form>";

            echo "<div class='info'>";
            echo "<strong>What this does:</strong><br>";
            echo "1. Deletes old demo users (admin, coach, athlete)<br>";
            echo "2. Creates fresh accounts with correct password hashes<br>";
            echo "3. All accounts use password: <code>" . $demo_password . "</code><br>";
            echo "4. All accounts are set to active<br>";
            echo "</div>";
        }

        ?>

        <hr style="margin: 30px 0;">
        
        <h2>📝 Manual Password Reset</h2>
        
        <form method="POST" style="margin: 20px 0;">
            <p>If you know a user's email and want to reset their password to "<?php echo $demo_password; ?>":</p>
            <input type="hidden" name="action" value="reset_password">
            <label>User Email:</label>
            <input type="email" name="email" placeholder="Enter email" style="padding: 8px; width: 300px; margin: 0 0 10px 0;">
            <button type="submit" class="button">Reset Password</button>
        </form>

        <?php

        if ($action === 'reset_password' && isset($_POST['email'])) {
            $email = $_POST['email'];
            try {
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                $stmt->execute([$password_hash, $email]);

                if ($stmt->rowCount() > 0) {
                    echo "<div class='status success'>✅ Password reset for: <code>" . $email . "</code><br>";
                    echo "New password: <code>" . $demo_password . "</code></div>";
                } else {
                    echo "<div class='status error'>❌ User not found: <code>" . $email . "</code></div>";
                }
            } catch (Exception $e) {
                echo "<div class='status error'>❌ Error: " . $e->getMessage() . "</div>";
            }
        }

        ?>

        <hr style="margin: 30px 0;">

        <h2>🧪 Test Login</h2>
        <p>After fixing the users, test login with:</p>
        <ul>
            <li><strong>Email:</strong> admin@example.com</li>
            <li><strong>Password:</strong> <?php echo $demo_password; ?></li>
        </ul>
        <a href="login.php" class="button" style="display: inline-block; text-decoration: none;">→ Go to Login</a>

    </div>
</body>
</html>
