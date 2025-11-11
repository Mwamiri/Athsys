<?php
/**
 * Athlete Results System - Update Manager
 * Handles system updates from mwamiri.co.ke
 * 
 * Developed by: Mwamiri Computers
 * Email: support@mwamiri.co.ke
 * Website: https://mwamiri.co.ke/athsys
 */

require_once __DIR__ . '/failsafe.php';

class UpdateManager {
    
    const UPDATE_SERVER = 'https://mwamiri.co.ke/athsys/api';
    const CURRENT_VERSION = '1.0.0';
    const UPDATE_CHECK_INTERVAL = 86400; // 24 hours
    
    private static $updateCacheFile = '/install/.update_cache';
    private static $updateLogFile = '/install/updates.log';
    
    /**
     * Check for available updates
     */
    public static function checkForUpdates($force = false) {
        $cacheFile = __DIR__ . self::$updateCacheFile;
        
        // Check cache first
        if (!$force && file_exists($cacheFile)) {
            $cache = json_decode(file_get_contents($cacheFile), true);
            if ($cache && isset($cache['checked_at'])) {
                if (time() - $cache['checked_at'] < self::UPDATE_CHECK_INTERVAL) {
                    return $cache['updates'];
                }
            }
        }
        
        // Fetch from update server
        $updates = self::fetchUpdatesFromServer();
        
        // Cache result
        $cacheData = [
            'checked_at' => time(),
            'updates' => $updates
        ];
        @file_put_contents($cacheFile, json_encode($cacheData));
        
        return $updates;
    }
    
    /**
     * Fetch updates from remote server
     */
    private static function fetchUpdatesFromServer() {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'header' => [
                        'User-Agent: AthletesResultsSystem/' . self::CURRENT_VERSION,
                        'X-System-Version: ' . self::CURRENT_VERSION
                    ]
                ],
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true
                ]
            ]);
            
            $url = self::UPDATE_SERVER . '/check?version=' . urlencode(self::CURRENT_VERSION);
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                SystemFailsafe::log('Failed to fetch updates from server', 'WARNING', ['url' => $url]);
                return ['status' => 'error', 'message' => 'Could not connect to update server'];
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                SystemFailsafe::log('Invalid update server response', 'WARNING');
                return ['status' => 'error', 'message' => 'Invalid response from update server'];
            }
            
            return $data;
            
        } catch (Exception $e) {
            SystemFailsafe::log('Update check failed: ' . $e->getMessage(), 'ERROR');
            return ['status' => 'error', 'message' => 'Update check failed'];
        }
    }
    
    /**
     * Download and verify update
     */
    public static function downloadUpdate($version) {
        try {
            $updateUrl = self::UPDATE_SERVER . '/download?version=' . urlencode($version);
            $tempFile = sys_get_temp_dir() . '/athsys_update_' . $version . '.zip';
            
            // Download update
            $context = stream_context_create([
                'http' => ['timeout' => 60]
            ]);
            
            $updateData = @file_get_contents($updateUrl, false, $context);
            
            if ($updateData === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to download update',
                    'message' => 'Could not download update file from server'
                ];
            }
            
            // Save to temp file
            if (file_put_contents($tempFile, $updateData) === false) {
                return [
                    'success' => false,
                    'error' => 'Failed to save update',
                    'message' => 'Could not save update file to temporary location'
                ];
            }
            
            // Verify update integrity
            $verifyUrl = self::UPDATE_SERVER . '/verify?version=' . urlencode($version);
            $expectedHash = @file_get_contents($verifyUrl);
            $actualHash = hash('sha256', $updateData);
            
            if ($expectedHash && trim($expectedHash) !== $actualHash) {
                @unlink($tempFile);
                return [
                    'success' => false,
                    'error' => 'Update verification failed',
                    'message' => 'Update file integrity check failed'
                ];
            }
            
            SystemFailsafe::log('Update downloaded successfully', 'INFO', ['version' => $version, 'file' => $tempFile]);
            
            return [
                'success' => true,
                'temp_file' => $tempFile,
                'version' => $version,
                'size' => filesize($tempFile),
                'hash' => $actualHash
            ];
            
        } catch (Exception $e) {
            SystemFailsafe::log('Download failed: ' . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => 'Download failed',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Install update
     */
    public static function installUpdate($tempFile, $version) {
        try {
            // Backup current installation
            $backup = SystemFailsafe::backupConfiguration();
            
            if (!file_exists($tempFile)) {
                return [
                    'success' => false,
                    'error' => 'Update file not found'
                ];
            }
            
            // Extract update
            $zip = new ZipArchive();
            if ($zip->open($tempFile) !== true) {
                return [
                    'success' => false,
                    'error' => 'Failed to open update archive'
                ];
            }
            
            // Extract to temporary directory
            $extractDir = sys_get_temp_dir() . '/athsys_update_extract_' . time();
            @mkdir($extractDir, 0755, true);
            
            if (!$zip->extractTo($extractDir)) {
                return [
                    'success' => false,
                    'error' => 'Failed to extract update files'
                ];
            }
            
            $zip->close();
            
            // Backup current files
            self::createBackup($version);
            
            // Copy update files
            self::copyUpdateFiles($extractDir, __DIR__ . '/../');
            
            // Update version
            self::updateVersionFile($version);
            
            // Clean up
            self::cleanupUpdateFiles($extractDir, $tempFile);
            
            SystemFailsafe::log('Update installed successfully', 'INFO', [
                'version' => $version,
                'backup' => $backup
            ]);
            
            return [
                'success' => true,
                'message' => 'Update installed successfully',
                'version' => $version,
                'backup' => $backup
            ];
            
        } catch (Exception $e) {
            SystemFailsafe::log('Installation failed: ' . $e->getMessage(), 'ERROR');
            return [
                'success' => false,
                'error' => 'Installation failed',
                'message' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Create installation backup
     */
    private static function createBackup($version) {
        $backupDir = __DIR__ . '/backups/v' . str_replace('.', '_', $version) . '_' . time();
        @mkdir($backupDir, 0755, true);
        
        $filesToBackup = [
            __DIR__ . '/../installer.php',
            __DIR__ . '/../status.php',
            __DIR__ . '/../install/api.php'
        ];
        
        foreach ($filesToBackup as $file) {
            if (file_exists($file)) {
                @copy($file, $backupDir . '/' . basename($file));
            }
        }
        
        return $backupDir;
    }
    
    /**
     * Copy update files
     */
    private static function copyUpdateFiles($source, $destination) {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($files as $file) {
            if ($file->isDir()) continue;
            
            $destPath = $destination . str_replace($source, '', $file->getRealPath());
            @mkdir(dirname($destPath), 0755, true);
            @copy($file->getRealPath(), $destPath);
        }
    }
    
    /**
     * Update version file
     */
    private static function updateVersionFile($version) {
        $versionInfo = [
            'version' => $version,
            'updated_at' => date('Y-m-d H:i:s'),
            'installed_by' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        @file_put_contents(
            __DIR__ . '/.version',
            json_encode($versionInfo, JSON_PRETTY_PRINT)
        );
    }
    
    /**
     * Clean up temporary files
     */
    private static function cleanupUpdateFiles($extractDir, $tempFile) {
        if (is_dir($extractDir)) {
            self::removeDirectory($extractDir);
        }
        if (file_exists($tempFile)) {
            @unlink($tempFile);
        }
    }
    
    /**
     * Remove directory recursively
     */
    private static function removeDirectory($dir) {
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $path = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($path)) {
                        self::removeDirectory($path);
                    } else {
                        @unlink($path);
                    }
                }
            }
            @rmdir($dir);
        }
    }
    
    /**
     * Get current version info
     */
    public static function getCurrentVersion() {
        $versionFile = __DIR__ . '/.version';
        
        if (file_exists($versionFile)) {
            return json_decode(file_get_contents($versionFile), true);
        }
        
        return [
            'version' => self::CURRENT_VERSION,
            'updated_at' => 'Initial Installation'
        ];
    }
    
    /**
     * Log update action
     */
    private static function logUpdate($action, $data = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'data' => $data,
            'user_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ];
        
        $logFile = __DIR__ . self::$updateLogFile;
        @file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND);
    }
    
    /**
     * Rollback to previous version
     */
    public static function rollback($backupPath) {
        try {
            if (!is_dir($backupPath)) {
                return ['success' => false, 'error' => 'Backup not found'];
            }
            
            $files = scandir($backupPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $src = $backupPath . '/' . $file;
                    $dst = __DIR__ . '/../' . $file;
                    @copy($src, $dst);
                }
            }
            
            SystemFailsafe::log('Rollback completed successfully', 'INFO');
            self::logUpdate('ROLLBACK', ['backup_path' => $backupPath]);
            
            return ['success' => true, 'message' => 'Rolled back to previous version'];
            
        } catch (Exception $e) {
            SystemFailsafe::log('Rollback failed: ' . $e->getMessage(), 'ERROR');
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

?>
