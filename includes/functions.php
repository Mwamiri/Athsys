<?php
// Helper functions for the Athlete Results System

// Get user by ID
function getUserById($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

// Get dashboard statistics
function getDashboardStats() {
    global $pdo;
    
    $stats = [];
    
    // Count athletes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM athletes WHERE is_active = 1");
    $stats['athletes'] = $stmt->fetch()['count'];
    
    // Count results
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM results");
    $stats['results'] = $stmt->fetch()['count'];
    
    // Count competitions
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM competitions");
    $stats['competitions'] = $stmt->fetch()['count'];
    
    // Count personal records
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM results WHERE is_personal_record = 1");
    $stats['personal_records'] = $stmt->fetch()['count'];
    
    return $stats;
}

// Get recent activities
function getRecentActivities($limit = 5) {
    global $pdo;
    
    $activities = [];
    
    // Get recent results
    $stmt = $pdo->prepare("
        SELECT r.*, 
               CONCAT(a.first_name, ' ', a.last_name) as athlete_name,
               e.name as event_name,
               r.created_at
        FROM results r
        JOIN athletes a ON r.athlete_id = a.id
        JOIN events e ON r.event_id = e.id
        ORDER BY r.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    $results = $stmt->fetchAll();
    
    foreach ($results as $result) {
        $activities[] = [
            'icon' => '🏃‍♂️',
            'text' => "New result added for {$result['athlete_name']} - {$result['event_name']}",
            'created_at' => $result['created_at']
        ];
    }
    
    return $activities;
}

// Get recent results
function getRecentResults($limit = 10) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT r.*, 
               CONCAT(a.first_name, ' ', a.last_name) as athlete_name,
               e.name as event_name,
               c.name as competition_name,
               c.date as competition_date
        FROM results r
        JOIN athletes a ON r.athlete_id = a.id
        JOIN events e ON r.event_id = e.id
        JOIN competitions c ON r.competition_id = c.id
        ORDER BY c.date DESC, r.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// Time ago function
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' minutes ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' days ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}

// Check if user has permission
function hasPermission($required_role) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    $role_hierarchy = [
        'athlete' => 1,
        'coach' => 2,
        'administrator' => 3
    ];
    
    $user_level = $role_hierarchy[$_SESSION['user_role']] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 999;
    
    return $user_level >= $required_level;
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Format performance time
function formatPerformance($performance, $unit) {
    if ($unit === 'seconds' && strpos($performance, ':') === false) {
        // Convert seconds to time format if needed
        $seconds = floatval($performance);
        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            $secs = $seconds - ($minutes * 60);
            return sprintf('%d:%05.2f', $minutes, $secs);
        }
    }
    return $performance;
}

// Calculate ranking
function calculateRanking($event_id, $filters = []) {
    global $pdo;
    
    $sql = "
        SELECT r.*, 
               CONCAT(a.first_name, ' ', a.last_name) as athlete_name,
               a.gender,
               e.record_type,
               c.date as competition_date
        FROM results r
        JOIN athletes a ON r.athlete_id = a.id
        JOIN events e ON r.event_id = e.id
        JOIN competitions c ON r.competition_id = c.id
        WHERE r.event_id = ?
    ";
    
    $params = [$event_id];
    
    if (!empty($filters['gender'])) {
        $sql .= " AND a.gender = ?";
        $params[] = $filters['gender'];
    }
    
    if (!empty($filters['year'])) {
        $sql .= " AND YEAR(c.date) = ?";
        $params[] = $filters['year'];
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    
    // Sort by performance
    if (!empty($results)) {
        $record_type = $results[0]['record_type'];
        usort($results, function($a, $b) use ($record_type) {
            if ($record_type === 'Time') {
                return $a['performance_numeric'] <=> $b['performance_numeric']; // Lower is better
            } else {
                return $b['performance_numeric'] <=> $a['performance_numeric']; // Higher is better
            }
        });
        
        // Add rank
        foreach ($results as $index => &$result) {
            $result['rank'] = $index + 1;
        }
    }
    
    return $results;
}

// Get all athletes
function getAllAthletes($filters = []) {
    global $pdo;
    
    $sql = "SELECT a.*, t.name as team_name 
            FROM athletes a 
            LEFT JOIN teams t ON a.team_id = t.id 
            WHERE a.is_active = 1";
    
    $params = [];
    
    if (!empty($filters['gender'])) {
        $sql .= " AND a.gender = ?";
        $params[] = $filters['gender'];
    }
    
    if (!empty($filters['team_id'])) {
        $sql .= " AND a.team_id = ?";
        $params[] = $filters['team_id'];
    }
    
    $sql .= " ORDER BY a.last_name, a.first_name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Get all events
function getAllEvents($category = null) {
    global $pdo;
    
    $sql = "SELECT * FROM events WHERE is_active = 1";
    $params = [];
    
    if ($category) {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY category, name";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Get all competitions
function getAllCompetitions($year = null) {
    global $pdo;
    
    $sql = "SELECT * FROM competitions";
    $params = [];
    
    if ($year) {
        $sql .= " WHERE YEAR(date) = ?";
        $params[] = $year;
    }
    
    $sql .= " ORDER BY date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Parse performance to numeric
function parsePerformanceToNumeric($performance, $record_type) {
    if ($record_type === 'Time') {
        // Handle time formats: "10.50", "1:23.45", "2:15:30"
        $parts = explode(':', $performance);
        if (count($parts) === 1) {
            return floatval($performance);
        } elseif (count($parts) === 2) {
            return floatval($parts[0]) * 60 + floatval($parts[1]);
        } elseif (count($parts) === 3) {
            return floatval($parts[0]) * 3600 + floatval($parts[1]) * 60 + floatval($parts[2]);
        }
    }
    
    return floatval($performance);
}
?>