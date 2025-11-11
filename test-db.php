<?php
/**
 * Database Connection Diagnostic Tool
 * Run this to test your database configuration
 */

echo "<h2>🔍 Database Diagnostic Report</h2>";
echo "<hr>";

// Test 1: Check if PDO is available
echo "<h3>Test 1: PDO Extension</h3>";
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension is loaded<br>";
} else {
    echo "❌ PDO MySQL extension is NOT loaded<br>";
}

// Test 2: Check config values
echo "<h3>Test 2: Current Configuration</h3>";
echo "DB_HOST: <code>" . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "</code><br>";
echo "DB_NAME: <code>" . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "</code><br>";
echo "DB_USER: <code>" . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "</code><br>";
echo "DB_PASS: <code>" . (defined('DB_PASS') ? (DB_PASS ? '***' : '(empty)') : 'NOT DEFINED') . "</code><br>";
echo "DB_CHARSET: <code>" . (defined('DB_CHARSET') ? DB_CHARSET : 'NOT DEFINED') . "</code><br>";

// Test 3: Try to connect
echo "<h3>Test 3: Connection Test</h3>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "✅ Connection to MySQL server successful<br>";
    
    // Test 4: Check if database exists
    echo "<h3>Test 4: Database Check</h3>";
    $dbName = DB_NAME;
    $query = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$dbName]);
    
    if ($stmt->fetch()) {
        echo "✅ Database '<code>$dbName</code>' exists<br>";
        
        // Test 5: Check tables
        echo "<h3>Test 5: Tables in Database</h3>";
        $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$dbName]);
        $tables = $stmt->fetchAll();
        
        if (count($tables) > 0) {
            echo "✅ Database has " . count($tables) . " table(s):<br>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li><code>" . $table['TABLE_NAME'] . "</code></li>";
            }
            echo "</ul>";
        } else {
            echo "⚠️ Database exists but is EMPTY (no tables)<br>";
            echo "<p><strong>ACTION REQUIRED:</strong> Run the installation wizard to import the schema.</p>";
        }
    } else {
        echo "❌ Database '<code>$dbName</code>' does NOT exist<br>";
        echo "<p><strong>ACTION REQUIRED:</strong> Create the database and run the installation wizard.</p>";
    }
    
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "<br>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Check if MySQL/MariaDB is running</li>";
    echo "<li>Verify DB_HOST, DB_USER, and DB_PASS are correct</li>";
    echo "<li>Make sure the database user has proper permissions</li>";
    echo "<li>Check if you're using the correct port (usually 3306)</li>";
    echo "</ul>";
}

// Test 6: PHP Info
echo "<h3>Test 6: PHP Configuration</h3>";
echo "PHP Version: <code>" . phpversion() . "</code><br>";
echo "MySQL/MariaDB Extensions: <br>";
echo "<ul>";
if (extension_loaded('mysqli')) echo "<li>✅ MySQLi extension</li>";
if (extension_loaded('pdo_mysql')) echo "<li>✅ PDO MySQL extension</li>";
if (extension_loaded('mysql')) echo "<li>⚠️ Old MySQL extension (deprecated)</li>";
echo "</ul>";

echo "<hr>";
echo "<h3>📋 Summary</h3>";
echo "<p>If all tests pass (✅), your database is properly configured.</p>";
echo "<p>If you see ❌ or ⚠️ errors, follow the troubleshooting steps above.</p>";
?>
