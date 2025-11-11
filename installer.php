<?php
/**
 * Athlete Results System - Installation Wizard
 * Features: Progress tracking, database creation/reset, error handling
 */

session_start();

// Check if installation is already complete
$configFile = __DIR__ . '/config/database.php';
$installLockFile = __DIR__ . '/install/.installed';

if (file_exists($installLockFile) && !isset($_GET['force'])) {
    header('Location: index.php');
    exit;
}

// Get current step
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$action = isset($_POST['action']) ? $_POST['action'] : '';

// Handle form submissions
if ($action === 'check_database') {
    handleDatabaseCheck();
}
if ($action === 'create_database') {
    handleDatabaseCreation();
}
if ($action === 'import_schema') {
    handleSchemaImport();
}
if ($action === 'save_config') {
    handleConfigSave();
}

// Functions
function handleDatabaseCheck() {
    $host = $_POST['db_host'] ?? 'localhost';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host}", $user, $pass);
        $_SESSION['db_config'] = compact('host', 'user', 'pass');
        echo json_encode(['success' => true, 'message' => 'Database connection successful']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    }
    exit;
}

function handleDatabaseCreation() {
    $dbName = $_POST['db_name'] ?? '';
    $resetDb = isset($_POST['reset_db']) && $_POST['reset_db'] === '1';
    
    if (empty($dbName)) {
        echo json_encode(['success' => false, 'message' => 'Database name required']);
        exit;
    }
    
    $config = $_SESSION['db_config'] ?? [];
    $host = $config['host'] ?? 'localhost';
    $user = $config['user'] ?? '';
    $pass = $config['pass'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host}", $user, $pass);
        
        if ($resetDb) {
            $pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
        }
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $_SESSION['db_config']['name'] = $dbName;
        
        echo json_encode(['success' => true, 'message' => 'Database created successfully', 'progress' => 50]);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

function handleSchemaImport() {
    $config = $_SESSION['db_config'] ?? [];
    $host = $config['host'] ?? 'localhost';
    $user = $config['user'] ?? '';
    $pass = $config['pass'] ?? '';
    $dbName = $config['name'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbName}", $user, $pass);
        
        $sqlFile = __DIR__ . '/install/database.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
        }
        
        echo json_encode(['success' => true, 'message' => 'Database schema imported', 'progress' => 85]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Import error: ' . $e->getMessage()]);
    }
    exit;
}

function handleConfigSave() {
    $config = $_SESSION['db_config'] ?? [];
    
    $configContent = "<?php\n";
    $configContent .= "// Database configuration\n";
    $configContent .= "define('DB_HOST', '{$config['host']}');\n";
    $configContent .= "define('DB_NAME', '{$config['name']}');\n";
    $configContent .= "define('DB_USER', '{$config['user']}');\n";
    $configContent .= "define('DB_PASS', '{$config['pass']}');\n";
    $configContent .= "define('DB_CHARSET', 'utf8mb4');\n\n";
    $configContent .= file_get_contents(__DIR__ . '/config/database.php');
    
    if (file_put_contents(__DIR__ . '/config/database.php', $configContent)) {
        // Create installation lock
        @mkdir(__DIR__ . '/install', 0755, true);
        file_put_contents(__DIR__ . '/install/.installed', date('Y-m-d H:i:s'));
        
        echo json_encode(['success' => true, 'message' => 'Configuration saved', 'progress' => 100]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save configuration']);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athlete Results System - Installation</title>
    <link rel="stylesheet" href="assets/menilo/static/assets/scss/bootstrap.min.css">
    <link rel="stylesheet" href="assets/menilo/static/assets/scss/style.css">
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --menilo-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            background: var(--menilo-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .installation-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            margin: 20px;
            overflow: hidden;
        }
        
        .installation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .installation-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .installation-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        
        .installation-body {
            padding: 40px;
        }
        
        .progress-section {
            margin-bottom: 30px;
        }
        
        .progress {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        
        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 13px;
            color: #666;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .form-check input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }
        
        .form-check label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            margin-right: 10px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .step {
            display: none;
        }
        
        .step.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .step-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }
        
        .step-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.6;
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 15px 0;
        }
        
        .feature-list li {
            padding: 10px 0;
            color: #666;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .feature-list li:before {
            content: "✓";
            color: #667eea;
            font-weight: bold;
            margin-right: 10px;
            font-size: 16px;
        }
        
        .success-message {
            text-align: center;
            padding: 20px;
        }
        
        .success-icon {
            width: 60px;
            height: 60px;
            background: #d4edda;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }
        
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="installation-container">
        <div class="installation-header">
            <h1>⚽ Athlete Results System</h1>
            <p>Installation Wizard</p>
            <div style="font-size: 11px; opacity: 0.7; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.2); padding-top: 10px;">
                Developed by <strong>Mwamiri Computers</strong> | 
                <a href="mailto:support@mwamiri.co.ke" style="color: white; text-decoration: underline;">support@mwamiri.co.ke</a>
            </div>
        </div>
        
        <div class="installation-body">
            <!-- Progress Bar -->
            <div class="progress-section">
                <div class="progress-label">
                    <span>Installation Progress</span>
                    <span id="progressPercent">0%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar" id="progressBar" style="width: 0%"></div>
                </div>
            </div>
            
            <!-- Alert Messages -->
            <div id="alertBox" class="alert"></div>
            
            <!-- Step 1: Welcome -->
            <div class="step active" id="step1">
                <h2 class="step-title">Welcome to Installation</h2>
                <p class="step-description">
                    This wizard will help you set up the Athlete Results System. We'll configure your database 
                    and import the necessary tables.
                </p>
                
                <ul class="feature-list">
                    <li>🗄️ Create or reset your database</li>
                    <li>📊 Import database schema</li>
                    <li>⚙️ Save configuration</li>
                    <li>✅ System ready to use</li>
                </ul>
                
                <div style="margin-top: 30px;">
                    <button class="btn btn-primary" onclick="nextStep()">Start Installation</button>
                </div>
            </div>
            
            <!-- Step 2: Database Credentials -->
            <div class="step" id="step2">
                <h2 class="step-title">Database Credentials</h2>
                <p class="step-description">
                    Enter your MySQL database connection details. You can find these in your hosting control panel.
                </p>
                
                <div class="form-group">
                    <label for="dbHost">Database Host</label>
                    <input type="text" id="dbHost" value="localhost" placeholder="e.g., localhost">
                </div>
                
                <div class="form-group">
                    <label for="dbUser">Database Username</label>
                    <input type="text" id="dbUser" placeholder="e.g., root">
                </div>
                
                <div class="form-group">
                    <label for="dbPass">Database Password</label>
                    <input type="password" id="dbPass" placeholder="Enter password">
                </div>
                
                <button class="btn btn-primary" onclick="checkDatabase()">
                    <span class="spinner hidden" id="checkSpinner"></span>
                    <span id="checkText">Test Connection</span>
                </button>
            </div>
            
            <!-- Step 3: Database Setup -->
            <div class="step" id="step3">
                <h2 class="step-title">Database Setup</h2>
                <p class="step-description">
                    Configure your new database. Choose a name and decide if you want to reset an existing database.
                </p>
                
                <div class="form-group">
                    <label for="dbName">Database Name</label>
                    <input type="text" id="dbName" value="athletes" placeholder="e.g., athletes_db">
                </div>
                
                <div class="form-check">
                    <input type="checkbox" id="resetDb">
                    <label for="resetDb">Reset existing database (deletes all data)</label>
                </div>
                
                <button class="btn btn-primary" onclick="createDatabase()">
                    <span class="spinner hidden" id="createSpinner"></span>
                    <span id="createText">Create Database</span>
                </button>
            </div>
            
            <!-- Step 4: Import Schema -->
            <div class="step" id="step4">
                <h2 class="step-title">Import Database Schema</h2>
                <p class="step-description">
                    Setting up database tables and initial data...
                </p>
                
                <div style="text-align: center; padding: 30px;">
                    <div class="spinner" style="width: 40px; height: 40px; border-width: 3px;"></div>
                    <p id="importStatus" style="margin-top: 15px; color: #666;">Importing schema...</p>
                </div>
                
                <button class="btn btn-primary hidden" id="importNextBtn" onclick="nextStep()">Continue</button>
            </div>
            
            <!-- Step 5: Save Configuration -->
            <div class="step" id="step5">
                <h2 class="step-title">Save Configuration</h2>
                <p class="step-description">
                    Finalizing your installation. Your database configuration will be saved.
                </p>
                
                <div style="text-align: center; padding: 30px;">
                    <div class="spinner" style="width: 40px; height: 40px; border-width: 3px;"></div>
                    <p id="saveStatus" style="margin-top: 15px; color: #666;">Saving configuration...</p>
                </div>
                
                <button class="btn btn-primary hidden" id="finishBtn" onclick="finishInstallation()">Complete Installation</button>
            </div>
            
            <!-- Step 6: Success -->
            <div class="step" id="step6">
                <div class="success-message">
                    <div class="success-icon">✓</div>
                    <h2 class="step-title">Installation Complete!</h2>
                    <p class="step-description">
                        Your Athlete Results System is now ready to use. All tables have been created and configured.
                    </p>
                    
                    <div style="margin-top: 30px;">
                        <a href="index.php" class="btn btn-primary">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let currentStep = 1;
        const totalSteps = 6;
        
        function updateProgress(percent) {
            document.getElementById('progressBar').style.width = percent + '%';
            document.getElementById('progressPercent').textContent = percent + '%';
        }
        
        function showAlert(message, type = 'error') {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = 'alert alert-' + type;
            alertBox.style.display = 'block';
            
            if (type === 'success') {
                setTimeout(() => alertBox.style.display = 'none', 3000);
            }
        }
        
        function showStep(stepNum) {
            document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + stepNum).classList.add('active');
            
            const progress = Math.round((stepNum / totalSteps) * 100);
            updateProgress(progress);
            
            currentStep = stepNum;
            
            // Auto-trigger actions for certain steps
            if (stepNum === 4) {
                setTimeout(importSchema, 500);
            } else if (stepNum === 5) {
                setTimeout(saveConfiguration, 500);
            }
        }
        
        function nextStep() {
            if (currentStep < totalSteps) {
                showStep(currentStep + 1);
            }
        }
        
        function checkDatabase() {
            const dbHost = document.getElementById('dbHost').value;
            const dbUser = document.getElementById('dbUser').value;
            const dbPass = document.getElementById('dbPass').value;
            
            if (!dbUser) {
                showAlert('Please enter database username');
                return;
            }
            
            document.getElementById('checkSpinner').classList.remove('hidden');
            document.getElementById('checkText').textContent = 'Testing...';
            
            fetch('installer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=check_database&db_host=' + encodeURIComponent(dbHost) +
                      '&db_user=' + encodeURIComponent(dbUser) +
                      '&db_pass=' + encodeURIComponent(dbPass)
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('checkSpinner').classList.add('hidden');
                document.getElementById('checkText').textContent = 'Test Connection';
                
                if (data.success) {
                    showAlert('Database connection successful!', 'success');
                    setTimeout(() => showStep(3), 1500);
                } else {
                    showAlert(data.message);
                }
            })
            .catch(err => {
                document.getElementById('checkSpinner').classList.add('hidden');
                document.getElementById('checkText').textContent = 'Test Connection';
                showAlert('Connection test failed: ' + err.message);
            });
        }
        
        function createDatabase() {
            const dbName = document.getElementById('dbName').value;
            const resetDb = document.getElementById('resetDb').checked;
            
            if (!dbName) {
                showAlert('Please enter database name');
                return;
            }
            
            document.getElementById('createSpinner').classList.remove('hidden');
            document.getElementById('createText').textContent = 'Creating...';
            
            fetch('installer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=create_database&db_name=' + encodeURIComponent(dbName) +
                      '&reset_db=' + (resetDb ? '1' : '0')
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('createSpinner').classList.add('hidden');
                document.getElementById('createText').textContent = 'Create Database';
                
                if (data.success) {
                    showAlert('Database created successfully!', 'success');
                    updateProgress(data.progress);
                    setTimeout(() => showStep(4), 1500);
                } else {
                    showAlert(data.message);
                }
            })
            .catch(err => {
                document.getElementById('createSpinner').classList.add('hidden');
                document.getElementById('createText').textContent = 'Create Database';
                showAlert('Database creation failed: ' + err.message);
            });
        }
        
        function importSchema() {
            fetch('installer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=import_schema'
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('importStatus').textContent = data.message;
                document.querySelectorAll('.spinner').forEach(el => el.classList.add('hidden'));
                document.getElementById('importNextBtn').classList.remove('hidden');
                updateProgress(data.progress);
            })
            .catch(err => {
                document.getElementById('importStatus').textContent = 'Error: ' + err.message;
            });
        }
        
        function saveConfiguration() {
            fetch('installer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=save_config'
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('saveStatus').textContent = data.message;
                document.querySelectorAll('.spinner').forEach(el => el.classList.add('hidden'));
                updateProgress(data.progress);
                
                if (data.success) {
                    setTimeout(() => showStep(6), 1000);
                } else {
                    showAlert(data.message);
                }
            })
            .catch(err => {
                document.getElementById('saveStatus').textContent = 'Error: ' + err.message;
            });
        }
        
        function finishInstallation() {
            window.location.href = 'index.php';
        }
        
        // Initialize
        updateProgress(16);
    </script>
</body>
</html>
