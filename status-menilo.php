<?php
/**
 * Athlete Results System - Menilo Status Dashboard
 * Based on: Menilo CakePHP Admin & Dashboard Template
 */

session_start();

$installLockFile = __DIR__ . '/install/.installed';
$configFile = __DIR__ . '/config/database.php';

$status = [
    'installed' => file_exists($installLockFile),
    'config_exists' => file_exists($configFile),
    'db_connected' => false,
    'installation_date' => null,
    'php_version' => phpversion(),
    'checks' => []
];

// Load installation info
if ($status['installed']) {
    $lockData = json_decode(file_get_contents($installLockFile), true);
    $status['installation_date'] = $lockData['installed_at'] ?? null;
    $status['database_name'] = $lockData['database'] ?? null;
}

// Check database connection
if ($status['config_exists']) {
    try {
        require_once $configFile;
        $result = @$pdo->query("SELECT 1");
        $status['db_connected'] = $result !== false;
    } catch (Exception $e) {
        $status['db_connected'] = false;
    }
}

// System checks
$status['checks'] = [
    [
        'name' => 'Installation Locked',
        'status' => $status['installed'],
        'icon' => $status['installed'] ? '✓' : '✗'
    ],
    [
        'name' => 'Configuration File',
        'status' => $status['config_exists'],
        'icon' => $status['config_exists'] ? '✓' : '✗'
    ],
    [
        'name' => 'Database Connected',
        'status' => $status['db_connected'],
        'icon' => $status['db_connected'] ? '✓' : '✗'
    ],
    [
        'name' => 'PHP 7.4+',
        'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'icon' => version_compare(PHP_VERSION, '7.4.0', '>=') ? '✓' : '✗'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Status - Menilo Dashboard</title>
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --menilo-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            padding: 20px;
        }

        .menilo-nav {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .menilo-nav-brand {
            font-size: 20px;
            font-weight: 700;
            background: var(--menilo-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .menilo-nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: #6b7280;
            font-weight: 500;
            transition: color 0.3s;
        }

        .menilo-nav-links a:hover {
            color: var(--primary-color);
        }

        .menilo-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .menilo-header {
            background: var(--menilo-gradient);
            color: white;
            padding: 40px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }

        .menilo-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .menilo-header p {
            font-size: 14px;
            opacity: 0.95;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .card h3 {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .card-value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #7f1d1d;
        }

        .badge-warning {
            background: #fef3c7;
            color: #78350f;
        }

        .checklist {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .checklist h2 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
            font-weight: 700;
        }

        .check-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .check-item:last-child {
            border-bottom: none;
        }

        .check-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 15px;
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
        }

        .check-icon.success {
            background: #d1fae5;
            color: var(--success-color);
        }

        .check-icon.danger {
            background: #fee2e2;
            color: var(--danger-color);
        }

        .check-name {
            flex: 1;
            color: #374151;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--menilo-gradient);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
            margin-top: 40px;
        }

        .footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .info-box {
            background: #dbeafe;
            border-left: 4px solid var(--info-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #1e40af;
            font-size: 14px;
        }

        .success-box {
            background: #d1fae5;
            border-left: 4px solid var(--success-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #065f46;
            font-size: 14px;
        }

        .danger-box {
            background: #fee2e2;
            border-left: 4px solid var(--danger-color);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #7f1d1d;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .menilo-nav {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .menilo-nav-links {
                width: 100%;
            }

            .menilo-nav-links a {
                margin: 0 10px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="menilo-nav">
        <div class="menilo-nav-brand">🚀 Menilo Dashboard</div>
        <div class="menilo-nav-links">
            <a href="index.php">Home</a>
            <a href="status.php">Status</a>
            <a href="update.php">Updates</a>
        </div>
    </div>

    <div class="menilo-container">
        <div class="menilo-header">
            <h1>System Status</h1>
            <p>Installation and system health information</p>
        </div>

        <!-- Status Cards -->
        <div class="grid">
            <div class="card">
                <h3>Installation Status</h3>
                <div class="card-value">
                    <?= $status['installed'] ? '✓' : '✗' ?>
                </div>
                <span class="status-badge <?= $status['installed'] ? 'badge-success' : 'badge-danger' ?>">
                    <?= $status['installed'] ? 'Installed' : 'Not Installed' ?>
                </span>
            </div>

            <div class="card">
                <h3>Database Connection</h3>
                <div class="card-value">
                    <?= $status['db_connected'] ? '✓' : '✗' ?>
                </div>
                <span class="status-badge <?= $status['db_connected'] ? 'badge-success' : 'badge-danger' ?>">
                    <?= $status['db_connected'] ? 'Connected' : 'Disconnected' ?>
                </span>
            </div>

            <div class="card">
                <h3>PHP Version</h3>
                <div class="card-value"><?= phpversion() ?></div>
                <span class="status-badge <?= version_compare(PHP_VERSION, '7.4.0', '>=') ? 'badge-success' : 'badge-warning' ?>">
                    <?= version_compare(PHP_VERSION, '7.4.0', '>=') ? 'Compatible' : 'Upgrade Needed' ?>
                </span>
            </div>
        </div>

        <!-- Installation Info -->
        <?php if ($status['installed']): ?>
            <div class="success-box">
                <strong>✓ System is fully installed and ready to use!</strong><br>
                <small>Installed: <?= $status['installation_date'] ?> | Database: <?= $status['database_name'] ?></small>
            </div>
        <?php else: ?>
            <div class="danger-box">
                <strong>✗ System installation is incomplete.</strong><br>
                <small>Please run the installation wizard to complete setup.</small>
            </div>
        <?php endif; ?>

        <!-- System Checks -->
        <div class="checklist">
            <h2>System Checks</h2>
            <?php foreach ($status['checks'] as $check): ?>
                <div class="check-item">
                    <div class="check-icon <?= $check['status'] ? 'success' : 'danger' ?>">
                        <?= $check['icon'] ?>
                    </div>
                    <div class="check-name"><?= $check['name'] ?></div>
                    <span class="status-badge <?= $check['status'] ? 'badge-success' : 'badge-danger' ?>">
                        <?= $check['status'] ? 'OK' : 'FAILED' ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <div class="action-buttons">
                <?php if ($status['installed']): ?>
                    <a href="index.php" class="btn btn-primary">→ Go to Dashboard</a>
                    <a href="update.php" class="btn btn-secondary">Check Updates</a>
                <?php else: ?>
                    <a href="installer-menilo.php" class="btn btn-primary">→ Start Installation</a>
                <?php endif; ?>
                <a href="status.php?refresh=1" class="btn btn-secondary">Refresh Status</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Developed by <strong>Mwamiri Computers</strong> | <a href="mailto:support@mwamiri.co.ke">support@mwamiri.co.ke</a></p>
        <p>Powered by Menilo CakePHP Admin & Dashboard Template</p>
    </div>
</body>

</html>
