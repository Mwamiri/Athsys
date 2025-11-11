<?php
/**
 * System Failsafe & Error Recovery Handler
 * Athlete Results System - Error Management Module
 * 
 * Developed by: Mwamiri Computers
 * Email: support@mwamiri.co.ke
 * 
 * Features:
 * - Error logging and tracking
 * - Database connection recovery
 * - Automatic rollback on failure
 * - Session recovery
 * - Configuration validation
 * - File integrity checking
 */

class SystemFailsafe {
    
    private static $logFile = '/install/error.log';
    private static $backupDir = '/install/backups';
    private static $maxRetries = 3;
    private static $retryDelay = 500; // milliseconds
    
    /**
     * Initialize error handling
     */
    public static function initialize() {
        set_error_handler([self::class, 'errorHandler']);
        set_exception_handler([self::class, 'exceptionHandler']);
        register_shutdown_function([self::class, 'shutdownHandler']);
    }
    
    /**
     * Log error with context
     */
    public static function log($message, $level = 'ERROR', $context = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $message,
            'file' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'CLI',
            'context' => $context,
            'php_version' => phpversion(),
            'memory' => memory_get_usage(true) / 1024 / 1024 . ' MB'
        ];
        
        $logPath = __DIR__ . self::$logFile;
        @file_put_contents($logPath, json_encode($logEntry) . "\n", FILE_APPEND);
        
        return $logEntry;
    }
    
    /**
     * Custom error handler
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline) {
        self::log($errstr, 'PHP_ERROR', [
            'errno' => $errno,
            'file' => $errfile,
            'line' => $errline
        ]);
        
        return false; // Default error handling
    }
    
    /**
     * Custom exception handler
     */
    public static function exceptionHandler($exception) {
        self::log(
            $exception->getMessage(),
            'EXCEPTION',
            [
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString()
            ]
        );
        
        // Display user-friendly error
        self::displayError(
            'System Error',
            $exception->getMessage(),
            'An error occurred during processing. Please try again or contact support.'
        );
    }
    
    /**
     * Shutdown handler for fatal errors
     */
    public static function shutdownHandler() {
        $error = error_get_last();
        if ($error !== null) {
            self::log('Fatal error detected', 'FATAL', $error);
        }
    }
    
    /**
     * Test database connection with retry
     */
    public static function testDatabaseWithRetry($host, $user, $pass, $retries = 3) {
        $lastError = null;
        
        for ($i = 0; $i < $retries; $i++) {
            try {
                $pdo = new PDO(
                    "mysql:host={$host}",
                    $user,
                    $pass,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                // Test query
                $pdo->query("SELECT 1");
                return ['success' => true, 'attempt' => $i + 1];
                
            } catch (PDOException $e) {
                $lastError = $e->getMessage();
                
                // Log retry attempt
                self::log(
                    "Database connection attempt " . ($i + 1) . " failed",
                    'WARNING',
                    ['error' => $lastError]
                );
                
                // Wait before retry
                if ($i < $retries - 1) {
                    usleep(self::$retryDelay * 1000);
                }
            }
        }
        
        return [
            'success' => false,
            'error' => $lastError,
            'attempts' => $retries
        ];
    }
    
    /**
     * Validate database structure
     */
    public static function validateDatabaseStructure($pdo, $requiredTables = []) {
        $schema = $pdo->query("
            SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE()
        ")->fetchAll(PDO::FETCH_COLUMN);
        
        $missing = [];
        foreach ($requiredTables as $table) {
            if (!in_array($table, $schema)) {
                $missing[] = $table;
            }
        }
        
        return [
            'valid' => empty($missing),
            'tables_found' => count($schema),
            'missing_tables' => $missing,
            'all_tables' => $schema
        ];
    }
    
    /**
     * Backup current configuration
     */
    public static function backupConfiguration() {
        $backupDir = __DIR__ . self::$backupDir;
        @mkdir($backupDir, 0755, true);
        
        $configFile = __DIR__ . '/config/database.php';
        if (file_exists($configFile)) {
            $backup = $backupDir . '/database.php.' . time() . '.bak';
            if (copy($configFile, $backup)) {
                self::log('Configuration backed up', 'INFO', ['backup' => $backup]);
                return $backup;
            }
        }
        
        return false;
    }
    
    /**
     * Restore configuration from backup
     */
    public static function restoreConfiguration($backupFile) {
        $configFile = __DIR__ . '/config/database.php';
        
        if (!file_exists($backupFile)) {
            self::log('Backup file not found', 'ERROR', ['file' => $backupFile]);
            return false;
        }
        
        if (copy($backupFile, $configFile)) {
            self::log('Configuration restored from backup', 'INFO', ['backup' => $backupFile]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Validate configuration file
     */
    public static function validateConfigFile($filePath) {
        if (!file_exists($filePath)) {
            return ['valid' => false, 'error' => 'Configuration file not found'];
        }
        
        $content = file_get_contents($filePath);
        
        $requiredFields = [
            'DB_HOST' => 'define(\'DB_HOST\'',
            'DB_NAME' => 'define(\'DB_NAME\'',
            'DB_USER' => 'define(\'DB_USER\'',
            'DB_PASS' => 'define(\'DB_PASS\''
        ];
        
        $missing = [];
        foreach ($requiredFields as $field => $pattern) {
            if (strpos($content, $pattern) === false) {
                $missing[] = $field;
            }
        }
        
        return [
            'valid' => empty($missing),
            'missing_fields' => $missing,
            'file_readable' => is_readable($filePath),
            'file_writable' => is_writable(dirname($filePath))
        ];
    }
    
    /**
     * Check system requirements
     */
    public static function checkSystemRequirements() {
        $requirements = [
            'PHP Version' => [
                'required' => '7.4',
                'actual' => phpversion(),
                'status' => version_compare(phpversion(), '7.4', '>='),
                'critical' => true
            ],
            'PDO Extension' => [
                'status' => extension_loaded('pdo'),
                'critical' => true
            ],
            'PDO MySQL' => [
                'status' => extension_loaded('pdo_mysql'),
                'critical' => true
            ],
            'JSON Extension' => [
                'status' => extension_loaded('json'),
                'critical' => true
            ],
            'Config Directory' => [
                'writable' => is_writable(__DIR__ . '/config'),
                'status' => is_writable(__DIR__ . '/config'),
                'critical' => true
            ],
            'Install Directory' => [
                'writable' => is_writable(__DIR__ . '/install'),
                'status' => is_writable(__DIR__ . '/install'),
                'critical' => false
            ]
        ];
        
        $allPassed = true;
        foreach ($requirements as $name => $req) {
            if (isset($req['critical']) && $req['critical'] && !$req['status']) {
                $allPassed = false;
            }
        }
        
        return [
            'all_passed' => $allPassed,
            'requirements' => $requirements
        ];
    }
    
    /**
     * Get error logs
     */
    public static function getErrorLogs($limit = 50) {
        $logPath = __DIR__ . self::$logFile;
        
        if (!file_exists($logPath)) {
            return [];
        }
        
        $lines = file($logPath, FILE_IGNORE_NEW_LINES);
        $logs = [];
        
        foreach (array_reverse($lines) as $line) {
            if ($limit-- <= 0) break;
            if (!empty($line)) {
                $logs[] = json_decode($line, true);
            }
        }
        
        return $logs;
    }
    
    /**
     * Clear error logs
     */
    public static function clearErrorLogs() {
        $logPath = __DIR__ . self::$logFile;
        if (file_exists($logPath)) {
            @unlink($logPath);
            self::log('Error logs cleared', 'INFO');
            return true;
        }
        return false;
    }
    
    /**
     * Display error page
     */
    public static function displayError($title, $message, $suggestion = '') {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>System Error</title>
            <style>
                body {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }
                .error-container {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    max-width: 600px;
                    padding: 40px;
                    text-align: center;
                }
                .error-icon {
                    font-size: 60px;
                    margin-bottom: 20px;
                }
                h1 {
                    color: #dc3545;
                    margin-bottom: 10px;
                }
                .message {
                    color: #666;
                    margin-bottom: 20px;
                    line-height: 1.6;
                }
                .suggestion {
                    background: #fdf8f7;
                    border-left: 4px solid #ffc107;
                    padding: 15px;
                    margin: 20px 0;
                    text-align: left;
                    border-radius: 5px;
                }
                .actions {
                    margin-top: 30px;
                }
                .btn {
                    padding: 12px 30px;
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-block;
                    margin: 5px;
                }
                .btn-primary {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                }
                .btn-secondary {
                    background: #f0f0f0;
                    color: #333;
                }
                .developer-info {
                    font-size: 12px;
                    color: #999;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">⚠️</div>
                <h1><?php echo htmlspecialchars($title); ?></h1>
                <p class="message"><?php echo htmlspecialchars($message); ?></p>
                
                <?php if (!empty($suggestion)): ?>
                    <div class="suggestion">
                        <strong>💡 Suggestion:</strong><br>
                        <?php echo htmlspecialchars($suggestion); ?>
                    </div>
                <?php endif; ?>
                
                <div class="actions">
                    <a href="installer.php" class="btn btn-primary">← Try Again</a>
                    <a href="status.php" class="btn btn-secondary">Check Status</a>
                </div>
                
                <div class="developer-info">
                    <strong>Support</strong><br>
                    Mwamiri Computers<br>
                    <a href="mailto:support@mwamiri.co.ke">support@mwamiri.co.ke</a>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    
    /**
     * Get system diagnostics
     */
    public static function getDiagnostics() {
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'php_version' => phpversion(),
            'memory_usage' => memory_get_usage(true) / 1024 / 1024 . ' MB',
            'peak_memory' => memory_get_peak_usage(true) / 1024 / 1024 . ' MB',
            'error_reporting' => error_reporting(),
            'display_errors' => ini_get('display_errors'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'os' => php_uname('s')
        ];
    }
}

// Initialize failsafe on include
SystemFailsafe::initialize();

?>
