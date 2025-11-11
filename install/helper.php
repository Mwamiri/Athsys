<?php
/**
 * Installation Quick Reference & Utilities
 * Helper functions and information for installation
 */

class InstallationHelper {
    
    /**
     * Check if system is installed
     */
    public static function isInstalled() {
        $lockFile = __DIR__ . '/install/.installed';
        return file_exists($lockFile);
    }
    
    /**
     * Get installation info
     */
    public static function getInstallationInfo() {
        $lockFile = __DIR__ . '/install/.installed';
        
        if (!file_exists($lockFile)) {
            return null;
        }
        
        return json_decode(file_get_contents($lockFile), true);
    }
    
    /**
     * Check database connectivity
     */
    public static function checkDatabase() {
        try {
            require_once __DIR__ . '/config/database.php';
            if (isset($pdo)) {
                $pdo->query("SELECT 1");
                return ['status' => 'connected', 'message' => 'Database is accessible'];
            }
            return ['status' => 'error', 'message' => 'Database connection failed'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get system requirements
     */
    public static function getRequirements() {
        return [
            'PHP' => [
                'required' => '7.4',
                'current' => phpversion(),
                'status' => version_compare(phpversion(), '7.4', '>=') ? 'pass' : 'fail'
            ],
            'MySQL' => [
                'required' => '5.7',
                'info' => 'Check with your hosting provider'
            ],
            'Extensions' => [
                'PDO' => extension_loaded('pdo') ? 'installed' : 'missing',
                'PDO MySQL' => extension_loaded('pdo_mysql') ? 'installed' : 'missing',
                'JSON' => extension_loaded('json') ? 'installed' : 'missing',
                'Filter' => extension_loaded('filter') ? 'installed' : 'missing'
            ]
        ];
    }
    
    /**
     * Get installation URLs
     */
    public static function getUrls() {
        $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = str_replace('install/helper.php', '', $_SERVER['SCRIPT_NAME'] ?? '');
        $baseUrl = $protocol . '://' . $host . $basePath;
        
        return [
            'installer' => $baseUrl . 'installer.php',
            'status' => $baseUrl . 'status.php',
            'dashboard' => $baseUrl . 'index.php',
            'documentation' => $baseUrl . 'INSTALLATION-GUIDE.md'
        ];
    }
    
    /**
     * Force reset installation
     */
    public static function resetInstallation() {
        $lockFile = __DIR__ . '/install/.installed';
        if (file_exists($lockFile)) {
            unlink($lockFile);
            return true;
        }
        return false;
    }
    
    /**
     * Get file permissions
     */
    public static function checkPermissions() {
        $checks = [
            'config/' => is_writable(__DIR__ . '/config'),
            'install/' => is_writable(__DIR__ . '/install'),
            'logs/' => is_writable(__DIR__ . '/logs'),
            'tmp/' => is_writable(__DIR__ . '/tmp'),
        ];
        
        return $checks;
    }
    
    /**
     * Generate database connection string for copying
     */
    public static function generateConnectionCode($host, $name, $user, $pass) {
        $code = "<?php\n";
        $code .= "define('DB_HOST', '{$host}');\n";
        $code .= "define('DB_NAME', '{$name}');\n";
        $code .= "define('DB_USER', '{$user}');\n";
        $code .= "define('DB_PASS', '{$pass}');\n";
        $code .= "?>\n";
        
        return $code;
    }
}

// Handle requests
if (php_sapi_name() === 'cli') {
    // Command line usage
    $action = $argv[1] ?? '';
    
    switch ($action) {
        case 'status':
            echo "Installation Status:\n";
            echo InstallationHelper::isInstalled() ? "✓ Installed\n" : "✗ Not Installed\n";
            echo "\nDatabase: " . json_encode(InstallationHelper::checkDatabase(), JSON_PRETTY_PRINT) . "\n";
            break;
            
        case 'reset':
            if (InstallationHelper::resetInstallation()) {
                echo "Installation reset complete. Run installer.php to reinstall.\n";
            } else {
                echo "Installation was not found.\n";
            }
            break;
            
        case 'requirements':
            echo json_encode(InstallationHelper::getRequirements(), JSON_PRETTY_PRINT) . "\n";
            break;
            
        default:
            echo "Installation Helper Utility\n\n";
            echo "Usage:\n";
            echo "  php install/helper.php status       - Check installation status\n";
            echo "  php install/helper.php reset        - Reset installation\n";
            echo "  php install/helper.php requirements - Check system requirements\n";
    }
} else {
    // Web request - show as JSON API
    header('Content-Type: application/json');
    
    $action = $_GET['action'] ?? 'status';
    
    switch ($action) {
        case 'status':
            echo json_encode([
                'installed' => InstallationHelper::isInstalled(),
                'info' => InstallationHelper::getInstallationInfo(),
                'database' => InstallationHelper::checkDatabase()
            ], JSON_PRETTY_PRINT);
            break;
            
        case 'requirements':
            echo json_encode(InstallationHelper::getRequirements(), JSON_PRETTY_PRINT);
            break;
            
        case 'urls':
            echo json_encode(InstallationHelper::getUrls(), JSON_PRETTY_PRINT);
            break;
            
        case 'permissions':
            echo json_encode(InstallationHelper::checkPermissions(), JSON_PRETTY_PRINT);
            break;
            
        default:
            echo json_encode([
                'error' => 'Unknown action',
                'available_actions' => ['status', 'requirements', 'urls', 'permissions']
            ], JSON_PRETTY_PRINT);
    }
}

?>
