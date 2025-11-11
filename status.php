<?php
/**
 * Installation Status Page
 * Displays installation progress and status information
 */

session_start();

$installLockFile = __DIR__ . '/install/.installed';
$configFile = __DIR__ . '/config/database.php';

$installationStatus = [
    'installed' => false,
    'installation_date' => null,
    'database_name' => null,
    'php_version' => phpversion(),
    'config_exists' => file_exists($configFile),
    'lock_exists' => file_exists($installLockFile),
    'writable' => is_writable(__DIR__ . '/config'),
    'steps' => []
];

// Check installation lock
if (file_exists($installLockFile)) {
    $installationStatus['installed'] = true;
    $lockData = json_decode(file_get_contents($installLockFile), true);
    $installationStatus['installation_date'] = $lockData['installed_at'] ?? null;
    $installationStatus['database_name'] = $lockData['database'] ?? null;
}

// Check database connection
$dbConnected = false;
if (file_exists($configFile)) {
    try {
        require_once $configFile;
        if (isset($pdo)) {
            $result = $pdo->query("SELECT 1");
            $dbConnected = true;
        }
    } catch (Exception $e) {
        // Connection failed
    }
}

$installationStatus['database_connected'] = $dbConnected;

// Steps summary
$installationStatus['steps'] = [
    [
        'name' => 'Configuration File',
        'status' => file_exists($configFile) ? 'complete' : 'incomplete',
        'icon' => file_exists($configFile) ? '✓' : '✗'
    ],
    [
        'name' => 'Database Connection',
        'status' => $dbConnected ? 'complete' : 'incomplete',
        'icon' => $dbConnected ? '✓' : '✗'
    ],
    [
        'name' => 'Installation Lock',
        'status' => file_exists($installLockFile) ? 'complete' : 'incomplete',
        'icon' => file_exists($installLockFile) ? '✓' : '✗'
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Status - Athlete Results System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .status-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .status-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .status-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .status-header .badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-body {
            padding: 30px;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #ddd;
        }
        
        .status-item.complete {
            border-left-color: #28a745;
            background: #f0f8f5;
        }
        
        .status-item.incomplete {
            border-left-color: #dc3545;
            background: #fdf8f7;
        }
        
        .status-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
            text-align: center;
        }
        
        .status-icon.complete {
            color: #28a745;
        }
        
        .status-icon.incomplete {
            color: #dc3545;
        }
        
        .status-info {
            flex: 1;
        }
        
        .status-info h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .status-info p {
            font-size: 13px;
            color: #666;
            margin: 0;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .info-box {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .info-box label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #667eea;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-box value {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        
        .actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #e9ecef;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #dee2e6;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #28a745;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-left-color: #ffc107;
        }
        
        .progress-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
        
        .progress-circle.complete {
            background: #d4edda;
            color: #28a745;
        }
        
        .progress-circle.incomplete {
            background: #fdf8f7;
            color: #dc3545;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-20 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Main Status Card -->
        <div class="status-card">
            <div class="status-header">
                <h1>⚽ Athlete Results System</h1>
                <div class="badge">
                    <?php echo $installationStatus['installed'] ? '✓ Installation Complete' : '⚠ Not Installed'; ?>
                </div>
            </div>
            
            <div class="status-body">
                <!-- Overall Status -->
                <?php if ($installationStatus['installed']): ?>
                    <div class="alert alert-success">
                        ✓ Your system is installed and ready to use!
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        ⚠ Installation is not complete. Please run the installer.
                    </div>
                <?php endif; ?>
                
                <!-- System Information -->
                <div class="info-grid">
                    <div class="info-box">
                        <label>PHP Version</label>
                        <value><?php echo $installationStatus['php_version']; ?></value>
                    </div>
                    <div class="info-box">
                        <label>Database</label>
                        <value><?php echo $installationStatus['database_name'] ?? 'Not Set'; ?></value>
                    </div>
                    <div class="info-box">
                        <label>Status</label>
                        <value><?php echo $installationStatus['installed'] ? 'Ready' : 'Pending'; ?></value>
                    </div>
                    <div class="info-box">
                        <label>Installed</label>
                        <value><?php echo $installationStatus['installation_date'] ?? 'Never'; ?></value>
                    </div>
                </div>
                
                <!-- Installation Steps -->
                <h3 style="margin-top: 30px; margin-bottom: 15px;">Installation Steps</h3>
                <?php foreach ($installationStatus['steps'] as $step): ?>
                    <div class="status-item <?php echo $step['status']; ?>">
                        <div class="status-icon <?php echo $step['status']; ?>">
                            <?php echo $step['icon']; ?>
                        </div>
                        <div class="status-info">
                            <h3><?php echo $step['name']; ?></h3>
                            <p><?php echo ucfirst($step['status']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <!-- Actions -->
                <div class="actions mt-20">
                    <?php if (!$installationStatus['installed']): ?>
                        <a href="installer.php" class="btn btn-primary">
                            🚀 Start Installation
                        </a>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-primary">
                            📊 Go to Dashboard
                        </a>
                        <a href="installer.php?force=1" class="btn btn-secondary">
                            🔄 Re-run Installation
                        </a>
                    <?php endif; ?>
                    <a href="INSTALL-WIZARD.md" class="btn btn-secondary">
                        📖 Help & Documentation
                    </a>
                </div>
            </div>
        </div>
        
        <!-- System Checks -->
        <div class="status-card">
            <div class="status-header">
                <h1>System Checks</h1>
            </div>
            
            <div class="status-body">
                <?php
                $checks = [
                    [
                        'name' => 'Config Directory Writable',
                        'status' => $installationStatus['writable'],
                        'message' => $installationStatus['writable'] 
                            ? 'Configuration directory is writable'
                            : 'Configuration directory is not writable'
                    ],
                    [
                        'name' => 'Database Connection',
                        'status' => $dbConnected,
                        'message' => $dbConnected
                            ? 'Database connection successful'
                            : 'Database connection failed or not configured'
                    ],
                    [
                        'name' => 'PHP Version',
                        'status' => version_compare(phpversion(), '7.4', '>='),
                        'message' => phpversion() . ' (Required: 7.4+)'
                    ]
                ];
                
                foreach ($checks as $check):
                ?>
                    <div class="status-item <?php echo $check['status'] ? 'complete' : 'incomplete'; ?>">
                        <div class="status-icon <?php echo $check['status'] ? 'complete' : 'incomplete'; ?>">
                            <?php echo $check['status'] ? '✓' : '✗'; ?>
                        </div>
                        <div class="status-info">
                            <h3><?php echo $check['name']; ?></h3>
                            <p><?php echo $check['message']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>
