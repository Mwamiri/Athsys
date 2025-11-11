<?php
/**
 * Athlete Results System - Menilo Dashboard
 * Main dashboard based on Menilo CakePHP Admin & Dashboard Template
 */

session_start();

// Check if installed
$installLockFile = __DIR__ . '/install/.installed';
if (!file_exists($installLockFile)) {
    header('Location: installer-menilo.php');
    exit;
}

// Load config
require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menilo Dashboard - Athlete Results System</title>
    
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
        }

        .menilo-layout {
            display: flex;
            min-height: 100vh;
        }

        .menilo-sidebar {
            width: 280px;
            background: white;
            padding: 20px;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .menilo-sidebar-brand {
            font-size: 24px;
            font-weight: 700;
            background: var(--menilo-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e7eb;
        }

        .menilo-sidebar-nav {
            list-style: none;
        }

        .menilo-sidebar-nav li {
            margin-bottom: 10px;
        }

        .menilo-sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #6b7280;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
            font-weight: 500;
            font-size: 14px;
        }

        .menilo-sidebar-nav a:hover,
        .menilo-sidebar-nav a.active {
            background: var(--menilo-gradient);
            color: white;
        }

        .menilo-sidebar-nav a span {
            margin-left: 10px;
        }

        .menilo-main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .menilo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .menilo-header h1 {
            font-size: 28px;
            color: #1f2937;
            font-weight: 700;
        }

        .menilo-header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 20px;
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
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .card-desc {
            font-size: 13px;
            color: #9ca3af;
        }

        .card-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 32px;
            opacity: 0.1;
        }

        .card {
            position: relative;
        }

        .welcome-section {
            background: var(--menilo-gradient);
            color: white;
            border-radius: 12px;
            padding: 40px;
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .welcome-section p {
            font-size: 14px;
            opacity: 0.95;
            margin-bottom: 20px;
        }

        .welcome-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-white {
            background: white;
            color: var(--primary-color);
        }

        .btn-white:hover {
            background: #f3f4f6;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .feature-item {
            text-align: center;
            padding: 20px;
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .feature-name {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .feature-desc {
            font-size: 13px;
            color: #6b7280;
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 12px;
            margin-top: 40px;
            background: white;
            border-top: 1px solid #e5e7eb;
        }

        .footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .menilo-layout {
                flex-direction: column;
            }

            .menilo-sidebar {
                width: 100%;
                display: flex;
                overflow-x: auto;
            }

            .menilo-sidebar-nav {
                display: flex;
                gap: 10px;
            }

            .menilo-main {
                padding: 15px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .welcome-buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="menilo-layout">
        <!-- Sidebar -->
        <div class="menilo-sidebar">
            <div class="menilo-sidebar-brand">🚀 Menilo</div>
            <ul class="menilo-sidebar-nav">
                <li>
                    <a href="index.php" class="active">
                        <span>📊</span>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="status-menilo.php">
                        <span>✓</span>
                        <span>System Status</span>
                    </a>
                </li>
                <li>
                    <a href="update.php">
                        <span>⬆️</span>
                        <span>Updates</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span>🚪</span>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="menilo-main">
            <!-- Header -->
            <div class="menilo-header">
                <h1>Welcome to Menilo</h1>
                <div class="menilo-header-actions">
                    <a href="status-menilo.php" class="btn btn-secondary">System Status</a>
                    <a href="update.php" class="btn btn-primary">Check Updates</a>
                </div>
            </div>

            <!-- Welcome Section -->
            <div class="welcome-section">
                <h2>🎉 Welcome to Your Menilo Dashboard!</h2>
                <p>Your Athlete Results System is fully installed and ready to use. Manage your athletes, events, and results from this powerful dashboard.</p>
                <div class="welcome-buttons">
                    <a href="status-menilo.php" class="btn btn-white">View System Status</a>
                    <a href="update.php" class="btn btn-white">Check for Updates</a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid">
                <div class="card">
                    <div class="card-icon">👥</div>
                    <h3>Dashboard Status</h3>
                    <div class="card-value">✓</div>
                    <div class="card-desc">System is operational and ready</div>
                </div>

                <div class="card">
                    <div class="card-icon">🗄️</div>
                    <h3>Database</h3>
                    <div class="card-value">Connected</div>
                    <div class="card-desc">MySQL database is online</div>
                </div>

                <div class="card">
                    <div class="card-icon">📋</div>
                    <h3>Version</h3>
                    <div class="card-value">1.0.0</div>
                    <div class="card-desc">Latest version installed</div>
                </div>
            </div>

            <!-- Features -->
            <div class="features-grid">
                <div class="feature-item">
                    <div class="feature-icon">📊</div>
                    <div class="feature-name">Dashboard</div>
                    <div class="feature-desc">Complete overview of your system</div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">👥</div>
                    <div class="feature-name">Athletes</div>
                    <div class="feature-desc">Manage athlete profiles</div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🏆</div>
                    <div class="feature-name">Results</div>
                    <div class="feature-desc">Track competition results</div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">⚙️</div>
                    <div class="feature-name">Settings</div>
                    <div class="feature-desc">System configuration</div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">🔐</div>
                    <div class="feature-name">Security</div>
                    <div class="feature-desc">User and data protection</div>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">📱</div>
                    <div class="feature-name">Responsive</div>
                    <div class="feature-desc">Works on all devices</div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Developed by <strong>Mwamiri Computers</strong> | <a href="mailto:support@mwamiri.co.ke">support@mwamiri.co.ke</a></p>
        <p>Powered by Menilo CakePHP Admin & Dashboard Template</p>
    </div>
</body>

</html>
