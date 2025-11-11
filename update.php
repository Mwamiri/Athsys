<?php
/**
 * System Update Checker
 * Check and manage system updates
 * 
 * URL: http://domain.com/update.php
 * Developed by: Mwamiri Computers
 * Email: support@mwamiri.co.ke
 */

require_once __DIR__ . '/install/failsafe.php';
require_once __DIR__ . '/install/update-manager.php';

session_start();

// Check if system is installed
$installLockFile = __DIR__ . '/install/.installed';
if (!file_exists($installLockFile)) {
    header('Location: installer.php');
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$result = null;

// Handle actions
if ($action === 'check') {
    $result = UpdateManager::checkForUpdates(isset($_POST['force']));
} elseif ($action === 'download' && isset($_POST['version'])) {
    $result = UpdateManager::downloadUpdate($_POST['version']);
} elseif ($action === 'install' && isset($_POST['temp_file'])) {
    $result = UpdateManager::installUpdate($_POST['temp_file'], $_POST['version']);
} elseif ($action === 'rollback' && isset($_POST['backup_path'])) {
    $result = UpdateManager::rollback($_POST['backup_path']);
}

$currentVersion = UpdateManager::getCurrentVersion();
$requirementsCheck = SystemFailsafe::checkSystemRequirements();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Updates - Athlete Results System</title>
    <link rel="stylesheet" href="assets/menilo/static/assets/scss/bootstrap.min.css">
    <link rel="stylesheet" href="assets/menilo/static/assets/scss/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .update-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
        }
        
        .card-header h2 {
            margin: 0;
            font-size: 24px;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .version-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
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
        
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #dc3545;
        }
        
        .update-item {
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #f8f9fa;
        }
        
        .update-item.available {
            background: #d4edda;
            border-color: #28a745;
        }
        
        .update-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .update-version {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .update-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-available {
            background: #28a745;
            color: white;
        }
        
        .badge-current {
            background: #007bff;
            color: white;
        }
        
        .update-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
            line-height: 1.6;
        }
        
        .update-details {
            font-size: 12px;
            color: #999;
            margin-bottom: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
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
            background: #f0f0f0;
            color: #333;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        
        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .requirement-item {
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid;
            background: #f8f9fa;
        }
        
        .requirement-item.pass {
            border-left-color: #28a745;
        }
        
        .requirement-item.fail {
            border-left-color: #dc3545;
            background: #fdf8f7;
        }
        
        .requirement-name {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .requirement-status {
            font-size: 12px;
            color: #666;
        }
        
        .developer-footer {
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #999;
        }
        
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="update-card">
            <div class="card-header">
                <h2>🔄 System Updates</h2>
                <p style="margin: 10px 0 0; opacity: 0.9;">Check and manage system updates from Mwamiri Computers</p>
            </div>
        </div>
        
        <!-- Current Version Info -->
        <div class="update-card">
            <div class="card-body">
                <h3 style="margin-top: 0;">Current Installation</h3>
                
                <div class="version-info">
                    <div class="info-box">
                        <label>Current Version</label>
                        <value><?php echo htmlspecialchars($currentVersion['version'] ?? 'Unknown'); ?></value>
                    </div>
                    <div class="info-box">
                        <label>Updated</label>
                        <value><?php echo htmlspecialchars($currentVersion['updated_at'] ?? 'Initial Install'); ?></value>
                    </div>
                    <div class="info-box">
                        <label>Update Server</label>
                        <value>mwamiri.co.ke</value>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Result Messages -->
        <?php if ($result): ?>
            <?php if (isset($result['success'])): ?>
                <?php if ($result['success']): ?>
                    <div class="alert alert-success">
                        ✓ <?php echo htmlspecialchars($result['message'] ?? 'Operation completed successfully'); ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        ✗ <?php echo htmlspecialchars($result['error'] ?? 'Operation failed'); ?>
                        <?php if (isset($result['message'])): ?>
                            <br><small><?php echo htmlspecialchars($result['message']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php elseif (isset($result['status'])): ?>
                <?php if ($result['status'] === 'error'): ?>
                    <div class="alert alert-error">
                        ✗ <?php echo htmlspecialchars($result['message'] ?? 'Error checking for updates'); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Check Updates Section -->
        <div class="update-card">
            <div class="card-body">
                <h3 style="margin-top: 0;">Check for Updates</h3>
                <p>Click the button below to check for available updates from Mwamiri Computers.</p>
                
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="check">
                    <button type="submit" class="btn btn-primary">🔍 Check for Updates</button>
                    <button type="submit" name="force" value="1" class="btn btn-secondary">Force Check</button>
                </form>
            </div>
        </div>
        
        <!-- System Requirements -->
        <div class="update-card">
            <div class="card-body">
                <h3 style="margin-top: 0;">System Requirements</h3>
                <p>Verify your system meets the requirements for updates.</p>
                
                <div class="requirements-grid">
                    <?php foreach ($requirementsCheck['requirements'] as $name => $req): ?>
                        <div class="requirement-item <?php echo ($req['status'] ?? false) ? 'pass' : 'fail'; ?>">
                            <div class="requirement-name">
                                <?php echo ($req['status'] ?? false) ? '✓' : '✗'; ?> <?php echo htmlspecialchars($name); ?>
                            </div>
                            <div class="requirement-status">
                                <?php if (isset($req['actual'])): ?>
                                    <?php echo htmlspecialchars($req['actual']); ?> (Required: <?php echo htmlspecialchars($req['required']); ?>)
                                <?php elseif (isset($req['writable'])): ?>
                                    <?php echo $req['writable'] ? 'Writable' : 'Not writable'; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Support Information -->
        <div class="update-card">
            <div class="card-body" style="text-align: center;">
                <h3>Need Help?</h3>
                <p>
                    <strong>Mwamiri Computers</strong><br>
                    <a href="mailto:support@mwamiri.co.ke" style="color: #667eea;">support@mwamiri.co.ke</a><br>
                    <a href="https://mwamiri.co.ke/athsys" target="_blank" style="color: #667eea;">https://mwamiri.co.ke/athsys</a>
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="text-align: center; margin-top: 40px; padding: 20px; color: white; font-size: 12px;">
            <p>Athlete Results System | Update Manager v1.0</p>
            <p>Developed by Mwamiri Computers</p>
        </div>
    </div>
</body>
</html>
