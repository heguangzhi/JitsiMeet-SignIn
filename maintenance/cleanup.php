<?php
/**
 * 日志清理脚本
 * Log Cleanup Script
 *
 * 定期清理过期的日志文件和会话文件
 * Regularly cleans up expired log files and session files
 */

require_once __DIR__ . '/config/jitsi.php';

class LogCleaner {
    
    /**
     * 清理过期的日志文件
     * Clean expired log files
     *
     * Removes log files that are older than the retention period
     */
    public static function cleanOldLogs() {
        $logConfig = JitsiConfig::getLogConfig();
        
        if (!$logConfig['enable_access_log']) {
            return;
        }
        
        $logDir = dirname($logConfig['log_file_path']);
        $retentionDays = $logConfig['log_retention_days'];
        
        if (!is_dir($logDir)) {
            return;
        }
        
        $files = glob($logDir . '/*.log*');
        $cutoffTime = time() - ($retentionDays * 24 * 3600);
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                echo "删除过期日志文件: " . basename($file) . "\n";
            }
        }
    }
    
    /**
     * 清理过期的会话文件
     * Clean expired session files
     *
     * Removes session files that are older than 2 hours
     */
    public static function cleanOldSessions() {
        $sessionPath = __DIR__ . '/sessions';
        
        if (!is_dir($sessionPath)) {
            return;
        }
        
        $files = glob($sessionPath . '/sess_*');
        $cutoffTime = time() - 7200; // 2小时
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                echo "删除过期会话文件: " . basename($file) . "\n";
            }
        }
    }
    
    /**
     * 运行所有清理任务
     * Run all cleanup tasks
     *
     * Executes both log and session cleanup functions
     */
    public static function runAll() {
        echo "开始清理任务...\n";
        
        self::cleanOldLogs();
        self::cleanOldSessions();
        
        echo "清理任务完成。\n";
    }
}

// 如果直接运行此脚本
// If running this script directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    LogCleaner::runAll();
}
?>