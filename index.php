<?php
session_start();

// Check if system is installed
$installLockFile = __DIR__ . '/install/.installed';
if (!file_exists($installLockFile)) {
    header('Location: setup.php');
    exit();
}

// Check if database configuration exists
if (!file_exists(__DIR__ . '/config/database.php')) {
    header('Location: setup.php');
    exit();
}

require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = getUserById($_SESSION['user_id']);
$page_title = 'Dashboard - Athlete Results System';

include 'includes/header.php';
?>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>🏃‍♂️ ARS</h2>
        </div>
        
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Overview</span>
            </a>
            <a href="results.php" class="nav-item">
                <span class="nav-icon">🏃‍♂️</span>
                <span class="nav-label">Results</span>
            </a>
            <?php if ($user['role'] !== 'athlete'): ?>
            <a href="athletes.php" class="nav-item">
                <span class="nav-icon">👥</span>
                <span class="nav-label">Athletes</span>
            </a>
            <a href="competitions.php" class="nav-item">
                <span class="nav-icon">🏆</span>
                <span class="nav-label">Competitions</span>
            </a>
            <?php endif; ?>
            <a href="rankings.php" class="nav-item">
                <span class="nav-icon">📈</span>
                <span class="nav-label">Rankings</span>
            </a>
            <?php if ($user['role'] !== 'athlete'): ?>
            <a href="analytics.php" class="nav-item">
                <span class="nav-icon">📊</span>
                <span class="nav-label">Analytics</span>
            </a>
            <a href="add-data.php" class="nav-item">
                <span class="nav-icon">➕</span>
                <span class="nav-label">Add Data</span>
            </a>
            <a href="excel-import.php" class="nav-item">
                <span class="nav-icon">📁</span>
                <span class="nav-label">Excel Import</span>
            </a>
            <a href="reports.php" class="nav-item">
                <span class="nav-icon">📋</span>
                <span class="nav-label">Reports</span>
            </a>
            <?php endif; ?>
            <a href="settings.php" class="nav-item">
                <span class="nav-icon">⚙️</span>
                <span class="nav-label">Settings</span>
            </a>
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                <div class="user-role"><?php echo htmlspecialchars($user['role']); ?></div>
            </div>
            <a href="logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="main-content">
        <header class="content-header">
            <h1>Dashboard Overview</h1>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="location.reload()">🔄 Refresh</button>
            </div>
        </header>
        
        <div class="content-body">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <h2>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h2>
                <p>Here's what's happening with your athletics data</p>
            </div>
            
            <!-- Statistics Grid -->
            <div class="stats-grid">
                <?php
                $stats = getDashboardStats();
                ?>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <h3>Athletes</h3>
                        <div class="stat-number"><?php echo $stats['athletes']; ?></div>
                        <div class="stat-change">Active athletes</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🏃‍♂️</div>
                    <div class="stat-content">
                        <h3>Results</h3>
                        <div class="stat-number"><?php echo $stats['results']; ?></div>
                        <div class="stat-change">Total recorded</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🏆</div>
                    <div class="stat-content">
                        <h3>Competitions</h3>
                        <div class="stat-number"><?php echo $stats['competitions']; ?></div>
                        <div class="stat-change">This season</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📈</div>
                    <div class="stat-content">
                        <h3>Personal Records</h3>
                        <div class="stat-number"><?php echo $stats['personal_records']; ?></div>
                        <div class="stat-change">All time</div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="recent-activity">
                <h3>Recent Activity</h3>
                <div class="activity-list">
                    <?php
                    $recent_activities = getRecentActivities(5);
                    foreach ($recent_activities as $activity):
                    ?>
                    <div class="activity-item">
                        <span class="activity-icon"><?php echo $activity['icon']; ?></span>
                        <span class="activity-text"><?php echo htmlspecialchars($activity['text']); ?></span>
                        <span class="activity-time"><?php echo timeAgo($activity['created_at']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Recent Results -->
            <div class="recent-results">
                <h3>Recent Results</h3>
                <div class="results-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Athlete</th>
                                <th>Event</th>
                                <th>Performance</th>
                                <th>Competition</th>
                                <th>Date</th>
                                <th>Records</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $recent_results = getRecentResults(10);
                            foreach ($recent_results as $result):
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($result['athlete_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['event_name']); ?></td>
                                <td><?php echo htmlspecialchars($result['performance'] . ' ' . $result['unit']); ?></td>
                                <td><?php echo htmlspecialchars($result['competition_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($result['competition_date'])); ?></td>
                                <td>
                                    <?php if ($result['is_personal_record']): ?>
                                    <span class="badge pr">PR</span>
                                    <?php endif; ?>
                                    <?php if ($result['is_season_best']): ?>
                                    <span class="badge sb">SB</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>