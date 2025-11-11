<?php
/**
 * Athlete Results System - Setup Wizard with Admin Account Creation
 * Creates database, imports schema, and sets up administrator account
 */

session_start();

// Check if installation is already complete
$installLockFile = __DIR__ . '/install/.installed';

if (file_exists($installLockFile) && !isset($_GET['force'])) {
    header('Location: login.php');
    exit;
}

// Get current step
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
if ($action === 'create_admin') {
    handleAdminCreation();
}
if ($action === 'save_config') {
    handleConfigSave();
}

// API Response Functions
function handleDatabaseCheck() {
    header('Content-Type: application/json');
    $host = $_POST['db_host'] ?? 'localhost';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host}", $user, $pass);
        $_SESSION['db_config'] = compact('host', 'user', 'pass');
        echo json_encode(['success' => true, 'message' => 'Database connection successful']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    }
    exit;
}

function handleDatabaseCreation() {
    header('Content-Type: application/json');
    $dbName = $_POST['db_name'] ?? '';
    $resetDb = isset($_POST['reset_db']) && $_POST['reset_db'] === '1';
    
    if (empty($dbName)) {
        http_response_code(400);
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
        
        echo json_encode(['success' => true, 'message' => 'Database created successfully']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

function handleSchemaImport() {
    header('Content-Type: application/json');
    $config = $_SESSION['db_config'] ?? [];
    $host = $config['host'] ?? 'localhost';
    $user = $config['user'] ?? '';
    $pass = $config['pass'] ?? '';
    $dbName = $config['name'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create tables without demo users
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            role ENUM('coach', 'athlete', 'administrator') NOT NULL DEFAULT 'athlete',
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_role (role)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS teams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            code VARCHAR(10) UNIQUE,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS athletes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            date_of_birth DATE,
            gender ENUM('M', 'F', 'Other') NOT NULL,
            team_id INT,
            category VARCHAR(50),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL,
            INDEX idx_name (last_name, first_name),
            INDEX idx_gender (gender),
            INDEX idx_team (team_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            category ENUM('Track', 'Field', 'CrossCountry') NOT NULL,
            discipline VARCHAR(50),
            unit VARCHAR(20) NOT NULL,
            record_type ENUM('Time', 'Distance', 'Height', 'Score') NOT NULL,
            gender ENUM('M', 'F', 'Mixed'),
            age_category VARCHAR(50),
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_category (category),
            INDEX idx_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS competitions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(300) NOT NULL,
            date DATE NOT NULL,
            location VARCHAR(200),
            type VARCHAR(50),
            status ENUM('Scheduled', 'InProgress', 'Completed') DEFAULT 'Scheduled',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_date (date),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS competition_events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            competition_id INT NOT NULL,
            event_id INT NOT NULL,
            scheduled_time DATETIME,
            status VARCHAR(20) DEFAULT 'Scheduled',
            FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            UNIQUE KEY unique_comp_event (competition_id, event_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

        CREATE TABLE IF NOT EXISTS results (
            id INT AUTO_INCREMENT PRIMARY KEY,
            athlete_id INT NOT NULL,
            competition_id INT NOT NULL,
            event_id INT NOT NULL,
            performance VARCHAR(50) NOT NULL,
            performance_numeric DECIMAL(10,3),
            unit VARCHAR(20) NOT NULL,
            placement INT,
            is_personal_record TINYINT(1) DEFAULT 0,
            is_season_best TINYINT(1) DEFAULT 0,
            is_national_record TINYINT(1) DEFAULT 0,
            wind_speed DECIMAL(3,1),
            weather_conditions TEXT,
            course_info TEXT,
            recorded_by INT,
            recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (athlete_id) REFERENCES athletes(id) ON DELETE CASCADE,
            FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_athlete_event (athlete_id, event_id),
            INDEX idx_competition (competition_id),
            INDEX idx_performance (performance_numeric)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($sql);
        
        echo json_encode(['success' => true, 'message' => 'Database schema imported successfully']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Import error: ' . $e->getMessage()]);
    }
    exit;
}

function handleAdminCreation() {
    header('Content-Type: application/json');
    
    $email = trim($_POST['admin_email'] ?? '');
    $firstName = trim($_POST['admin_first_name'] ?? '');
    $lastName = trim($_POST['admin_last_name'] ?? '');
    $password = $_POST['admin_password'] ?? '';
    $confirmPassword = $_POST['admin_confirm_password'] ?? '';
    
    // Validation
    if (empty($email) || empty($firstName) || empty($lastName) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit;
    }
    
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }
    
    if ($password !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Passwords do not match']);
        exit;
    }
    
    $config = $_SESSION['db_config'] ?? [];
    $host = $config['host'] ?? 'localhost';
    $user = $config['user'] ?? '';
    $pass = $config['pass'] ?? '';
    $dbName = $config['name'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check if admin already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }
        
        // Create admin user
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, first_name, last_name, role, is_active) VALUES (?, ?, ?, ?, 'administrator', 1)");
        $stmt->execute([$email, $passwordHash, $firstName, $lastName]);
        
        $_SESSION['admin_created'] = true;
        
        echo json_encode(['success' => true, 'message' => 'Administrator account created successfully']);
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

function handleConfigSave() {
    header('Content-Type: application/json');
    $config = $_SESSION['db_config'] ?? [];
    
    $configContent = "<?php\n";
    $configContent .= "// Database configuration - Auto-generated by Setup Wizard\n";
    $configContent .= "define('DB_HOST', '{$config['host']}');\n";
    $configContent .= "define('DB_NAME', '{$config['name']}');\n";
    $configContent .= "define('DB_USER', '{$config['user']}');\n";
    $configContent .= "define('DB_PASS', '{$config['pass']}');\n";
    $configContent .= "define('DB_CHARSET', 'utf8mb4');\n\n";
    $configContent .= "// Create PDO connection\n";
    $configContent .= "try {\n";
    $configContent .= "    \$dsn = \"mysql:host=\" . DB_HOST . \";dbname=\" . DB_NAME . \";charset=\" . DB_CHARSET;\n";
    $configContent .= "    \$options = [\n";
    $configContent .= "        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
    $configContent .= "        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
    $configContent .= "        PDO::ATTR_EMULATE_PREPARES => false,\n";
    $configContent .= "    ];\n";
    $configContent .= "    \$pdo = new PDO(\$dsn, DB_USER, DB_PASS, \$options);\n";
    $configContent .= "} catch (PDOException \$e) {\n";
    $configContent .= "    error_log('Database Connection Error: ' . \$e->getMessage());\n";
    $configContent .= "    die('Database connection failed. Please contact the administrator.');\n";
    $configContent .= "}\n\n";
    $configContent .= "function getDB() {\n";
    $configContent .= "    global \$pdo;\n";
    $configContent .= "    return \$pdo;\n";
    $configContent .= "}\n";
    
    if (!is_dir(__DIR__ . '/config')) {
        mkdir(__DIR__ . '/config', 0755, true);
    }
    
    if (file_put_contents(__DIR__ . '/config/database.php', $configContent)) {
        @mkdir(__DIR__ . '/install', 0755, true);
        $lockData = [
            'installed_at' => date('Y-m-d H:i:s'),
            'database' => $config['name'],
            'version' => '1.0.0'
        ];
        file_put_contents(__DIR__ . '/install/.installed', json_encode($lockData));
        
        // Clear session
        session_destroy();
        
        echo json_encode(['success' => true, 'message' => 'Configuration saved successfully']);
    } else {
        http_response_code(400);
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
    <title>Setup - Athlete Results System</title>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            --success-color: #10b981;
            --danger-color: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .setup-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
        }

        .setup-header {
            background: var(--gradient);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .setup-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .setup-header p {
            font-size: 14px;
            opacity: 0.95;
        }

        .setup-body {
            padding: 40px 30px;
            min-height: 400px;
        }

        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .progress-fill {
            height: 100%;
            background: var(--gradient);
            width: 0%;
            transition: width 0.3s ease;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input[type="checkbox"] {
            width: auto;
            margin-right: 8px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            font-weight: 500;
            color: #4b5563;
        }

        .btn {
            display: inline-block;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background: var(--gradient);
            color: white;
            width: 100%;
            margin-top: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-color: var(--success-color);
        }

        .alert-danger {
            background: #fee2e2;
            color: #7f1d1d;
            border-color: var(--danger-color);
        }

        .alert-info {
            background: #dbeafe;
            color: #1e3a8a;
            border-color: #3b82f6;
        }

        .step-header {
            margin-bottom: 30px;
        }

        .step-header h2 {
            font-size: 24px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .step-header p {
            color: #6b7280;
            font-size: 14px;
        }

        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .hidden {
            display: none !important;
        }

        .setup-footer {
            background: #f9fafb;
            padding: 20px 30px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .step-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .step-info strong {
            color: #1f2937;
        }

        .step-info p {
            margin: 5px 0;
            font-size: 13px;
            color: #6b7280;
        }

        .required {
            color: var(--danger-color);
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>🚀 System Setup</h1>
            <p>Athlete Results System - Installation Wizard</p>
        </div>

        <div class="setup-body">
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%;"></div>
            </div>

            <!-- Step 1: Welcome -->
            <div class="step-content active" id="step1">
                <div class="step-header">
                    <h2>Welcome to Setup</h2>
                    <p>Let's configure your system in 5 easy steps</p>
                </div>

                <div class="step-info">
                    <strong>ℹ️ What will happen:</strong>
                    <p>1. Test database connection</p>
                    <p>2. Create database</p>
                    <p>3. Import database schema</p>
                    <p>4. Create administrator account</p>
                    <p>5. Save configuration</p>
                </div>

                <button class="btn btn-primary" onclick="showStep(2)">Start Setup →</button>
            </div>

            <!-- Step 2: Database Credentials -->
            <div class="step-content" id="step2">
                <div class="step-header">
                    <h2>Database Connection</h2>
                    <p>Enter your MySQL database credentials</p>
                </div>

                <div id="credAlert"></div>

                <div class="form-group">
                    <label for="dbHost">Database Host <span class="required">*</span></label>
                    <input type="text" id="dbHost" value="localhost" placeholder="localhost">
                </div>

                <div class="form-group">
                    <label for="dbUser">Database Username <span class="required">*</span></label>
                    <input type="text" id="dbUser" value="root" placeholder="root">
                </div>

                <div class="form-group">
                    <label for="dbPass">Database Password</label>
                    <input type="password" id="dbPass" placeholder="Leave empty if no password">
                </div>

                <button class="btn btn-primary" onclick="testConnection()">
                    <span class="spinner hidden" id="testSpinner"></span>
                    <span id="testText">Test Connection</span>
                </button>
            </div>

            <!-- Step 3: Database Setup -->
            <div class="step-content" id="step3">
                <div class="step-header">
                    <h2>Database Setup</h2>
                    <p>Create your application database</p>
                </div>

                <div id="createAlert"></div>

                <div class="form-group">
                    <label for="dbName">Database Name <span class="required">*</span></label>
                    <input type="text" id="dbName" value="athletes" placeholder="athletes">
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="resetDb">
                        <span>Reset existing database (⚠️ deletes all data)</span>
                    </label>
                </div>

                <button class="btn btn-primary" onclick="createDatabase()">
                    <span class="spinner hidden" id="createSpinner"></span>
                    <span id="createText">Create Database</span>
                </button>
            </div>

            <!-- Step 4: Import Schema -->
            <div class="step-content" id="step4">
                <div class="step-header">
                    <h2>Import Database Schema</h2>
                    <p>Creating tables and structure</p>
                </div>

                <div id="importStatus" class="alert alert-info">
                    <span class="spinner"></span> Importing database schema...
                </div>
            </div>

            <!-- Step 5: Create Admin Account -->
            <div class="step-content" id="step5">
                <div class="step-header">
                    <h2>Create Administrator Account</h2>
                    <p>Set up your admin credentials</p>
                </div>

                <div id="adminAlert"></div>

                <div class="form-group">
                    <label for="adminEmail">Email Address <span class="required">*</span></label>
                    <input type="email" id="adminEmail" placeholder="admin@example.com" required>
                </div>

                <div class="form-group">
                    <label for="adminFirstName">First Name <span class="required">*</span></label>
                    <input type="text" id="adminFirstName" placeholder="John" required>
                </div>

                <div class="form-group">
                    <label for="adminLastName">Last Name <span class="required">*</span></label>
                    <input type="text" id="adminLastName" placeholder="Doe" required>
                </div>

                <div class="form-group">
                    <label for="adminPassword">Password <span class="required">*</span></label>
                    <input type="password" id="adminPassword" placeholder="Minimum 6 characters" required>
                </div>

                <div class="form-group">
                    <label for="adminConfirmPassword">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="adminConfirmPassword" placeholder="Re-enter password" required>
                </div>

                <button class="btn btn-primary" onclick="createAdmin()">
                    <span class="spinner hidden" id="adminSpinner"></span>
                    <span id="adminText">Create Administrator</span>
                </button>
            </div>

            <!-- Step 6: Save Configuration -->
            <div class="step-content" id="step6">
                <div class="step-header">
                    <h2>Finalizing Setup</h2>
                    <p>Saving configuration files</p>
                </div>

                <div id="saveStatus" class="alert alert-info">
                    <span class="spinner"></span> Saving configuration...
                </div>
            </div>

            <!-- Step 7: Complete -->
            <div class="step-content" id="step7">
                <div class="step-header">
                    <h2>✓ Setup Complete!</h2>
                    <p>Your system is ready to use</p>
                </div>

                <div class="step-info">
                    <strong>🎉 Success!</strong>
                    <p>✓ Database configured</p>
                    <p>✓ Schema imported</p>
                    <p>✓ Administrator account created</p>
                    <p>✓ Configuration saved</p>
                </div>

                <button class="btn btn-primary" onclick="window.location.href='login.php'">
                    Go to Login →
                </button>
            </div>
        </div>

        <div class="setup-footer">
            <p>Athlete Results System © 2025</p>
        </div>
    </div>

    <script>
        function showStep(stepNum) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
            document.getElementById('step' + stepNum).classList.add('active');
            updateProgress((stepNum - 1) * 16.67);
        }

        function updateProgress(percentage) {
            document.getElementById('progressFill').style.width = percentage + '%';
        }

        function showAlert(message, type = 'danger', elementId = 'credAlert') {
            const alertClass = 'alert-' + type;
            document.getElementById(elementId).innerHTML = 
                `<div class="alert ${alertClass}">${message}</div>`;
        }

        function testConnection() {
            const host = document.getElementById('dbHost').value;
            const user = document.getElementById('dbUser').value;
            const pass = document.getElementById('dbPass').value;

            if (!user) {
                showAlert('Username is required', 'danger', 'credAlert');
                return;
            }

            document.getElementById('testSpinner').classList.remove('hidden');
            document.getElementById('testText').textContent = 'Testing...';

            fetch('setup.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=check_database&db_host=' + encodeURIComponent(host) +
                      '&db_user=' + encodeURIComponent(user) +
                      '&db_pass=' + encodeURIComponent(pass)
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('testSpinner').classList.add('hidden');
                document.getElementById('testText').textContent = 'Test Connection';

                if (data.success) {
                    showAlert('✓ Connection successful!', 'success', 'credAlert');
                    setTimeout(() => showStep(3), 1500);
                } else {
                    showAlert(data.message, 'danger', 'credAlert');
                }
            })
            .catch(err => {
                document.getElementById('testSpinner').classList.add('hidden');
                document.getElementById('testText').textContent = 'Test Connection';
                showAlert('Connection error: ' + err.message, 'danger', 'credAlert');
            });
        }

        function createDatabase() {
            const dbName = document.getElementById('dbName').value;
            const resetDb = document.getElementById('resetDb').checked;

            if (!dbName) {
                showAlert('Database name is required', 'danger', 'createAlert');
                return;
            }

            document.getElementById('createSpinner').classList.remove('hidden');
            document.getElementById('createText').textContent = 'Creating...';

            fetch('setup.php', {
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
                    showAlert('✓ Database created successfully!', 'success', 'createAlert');
                    setTimeout(() => {
                        showStep(4);
                        importSchema();
                    }, 1500);
                } else {
                    showAlert(data.message, 'danger', 'createAlert');
                }
            })
            .catch(err => {
                document.getElementById('createSpinner').classList.add('hidden');
                document.getElementById('createText').textContent = 'Create Database';
                showAlert('Error: ' + err.message, 'danger', 'createAlert');
            });
        }

        function importSchema() {
            fetch('setup.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=import_schema'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('importStatus').innerHTML = 
                        '<div class="alert alert-success">✓ ' + data.message + '</div>';
                    setTimeout(() => showStep(5), 1500);
                } else {
                    document.getElementById('importStatus').innerHTML = 
                        '<div class="alert alert-danger">Error: ' + data.message + '</div>';
                }
            })
            .catch(err => {
                document.getElementById('importStatus').innerHTML = 
                    '<div class="alert alert-danger">Error: ' + err.message + '</div>';
            });
        }

        function createAdmin() {
            const email = document.getElementById('adminEmail').value;
            const firstName = document.getElementById('adminFirstName').value;
            const lastName = document.getElementById('adminLastName').value;
            const password = document.getElementById('adminPassword').value;
            const confirmPassword = document.getElementById('adminConfirmPassword').value;

            if (!email || !firstName || !lastName || !password || !confirmPassword) {
                showAlert('All fields are required', 'danger', 'adminAlert');
                return;
            }

            if (password !== confirmPassword) {
                showAlert('Passwords do not match', 'danger', 'adminAlert');
                return;
            }

            if (password.length < 6) {
                showAlert('Password must be at least 6 characters', 'danger', 'adminAlert');
                return;
            }

            document.getElementById('adminSpinner').classList.remove('hidden');
            document.getElementById('adminText').textContent = 'Creating...';

            fetch('setup.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=create_admin' +
                      '&admin_email=' + encodeURIComponent(email) +
                      '&admin_first_name=' + encodeURIComponent(firstName) +
                      '&admin_last_name=' + encodeURIComponent(lastName) +
                      '&admin_password=' + encodeURIComponent(password) +
                      '&admin_confirm_password=' + encodeURIComponent(confirmPassword)
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('adminSpinner').classList.add('hidden');
                document.getElementById('adminText').textContent = 'Create Administrator';

                if (data.success) {
                    showAlert('✓ Administrator account created!', 'success', 'adminAlert');
                    setTimeout(() => {
                        showStep(6);
                        saveConfiguration();
                    }, 1500);
                } else {
                    showAlert(data.message, 'danger', 'adminAlert');
                }
            })
            .catch(err => {
                document.getElementById('adminSpinner').classList.add('hidden');
                document.getElementById('adminText').textContent = 'Create Administrator';
                showAlert('Error: ' + err.message, 'danger', 'adminAlert');
            });
        }

        function saveConfiguration() {
            fetch('setup.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=save_config'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('saveStatus').innerHTML = 
                        '<div class="alert alert-success">✓ ' + data.message + '</div>';
                    setTimeout(() => showStep(7), 1500);
                } else {
                    document.getElementById('saveStatus').innerHTML = 
                        '<div class="alert alert-danger">Error: ' + data.message + '</div>';
                }
            })
            .catch(err => {
                document.getElementById('saveStatus').innerHTML = 
                    '<div class="alert alert-danger">Error: ' + err.message + '</div>';
            });
        }
    </script>
</body>
</html>
