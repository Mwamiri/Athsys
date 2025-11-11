<?php
/**
 * Reset Installation Tool
 * Use this to completely reset the installation and start over
 * 
 * WARNING: This will delete your database configuration and installation lock
 * You will need to run setup.php again after using this
 */

$configFile = __DIR__ . '/config/database.php';
$lockFile = __DIR__ . '/install/.installed';

$deleted = [];
$errors = [];

// Delete config file
if (file_exists($configFile)) {
    if (unlink($configFile)) {
        $deleted[] = 'config/database.php';
    } else {
        $errors[] = 'Failed to delete config/database.php';
    }
}

// Delete lock file
if (file_exists($lockFile)) {
    if (unlink($lockFile)) {
        $deleted[] = 'install/.installed';
    } else {
        $errors[] = 'Failed to delete install/.installed';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Installation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .success {
            background: #d1fae5;
            color: #065f46;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #10b981;
        }
        .error {
            background: #fee2e2;
            color: #7f1d1d;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #ef4444;
        }
        .info {
            background: #dbeafe;
            color: #1e3a8a;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
            border-left: 4px solid #3b82f6;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 15px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Reset Installation</h1>

        <?php if (!empty($deleted)): ?>
            <div class="success">
                <strong>✓ Installation Reset Complete</strong>
                <p>The following files were deleted:</p>
                <ul>
                    <?php foreach ($deleted as $file): ?>
                        <li><?php echo htmlspecialchars($file); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>❌ Errors Occurred</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (empty($deleted) && empty($errors)): ?>
            <div class="info">
                <strong>ℹ️ Nothing to Reset</strong>
                <p>No installation files found. The system is already in a fresh state.</p>
            </div>
        <?php endif; ?>

        <div class="info">
            <strong>📝 What was reset:</strong>
            <ul>
                <li>Database configuration file removed</li>
                <li>Installation lock file removed</li>
            </ul>
            <p><strong>Note:</strong> Your database and data are NOT deleted. Only the configuration files are removed.</p>
        </div>

        <div class="info">
            <strong>🎯 Next Steps:</strong>
            <p>1. Run the setup wizard again to reconfigure the system</p>
            <p>2. You can use the same database or create a new one</p>
            <p>3. Create a new administrator account</p>
        </div>

        <a href="setup.php" class="btn">→ Go to Setup Wizard</a>
    </div>
</body>
</html>
