<?php
require_once __DIR__ . '/../config/jitsi.php';

/**
 * 会议室管理辅助类
 * Meeting Room Management Helper Class
 *
 * This class provides utility methods for managing meeting rooms,
 * including room name generation, validation, URL building, and logging.
 */
class MeetingRoom {
    
    /**
     * 生成随机会议室名称
     * Generate a random meeting room name
     *
     * Creates a unique room name with a prefix, timestamp, and random component.
     *
     * @param string $prefix Prefix for the room name (default: 'Meeting')
     * @return string Generated room name
     */
    public static function generateRoomName($prefix = 'Meeting') {
        $timestamp = date('Ymd-His');
        $random = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        return $prefix . '-' . $timestamp . '-' . strtoupper($random);
    }
    
    /**
     * 验证会议室名称格式
     * Validate meeting room name format
     *
     * Checks if a room name meets the required format rules.
     *
     * @param string $roomName The room name to validate
     * @return bool True if valid, false otherwise
     */
    public static function validateRoomName($roomName) {
        // 移除空格
        $roomName = trim($roomName);
        
        // 使用配置类进行验证
        return JitsiConfig::validateRoomName($roomName);
    }
    
    /**
     * 构建 Jitsi Meet URL
     * Build Jitsi Meet URL
     *
     * Constructs a complete URL for accessing a Jitsi Meet room.
     *
     * @param string $roomName The room name
     * @param array $options Additional options for URL building
     * @return string The complete Jitsi Meet URL
     */
    public static function buildJitsiUrl($roomName, $options = []) {
        // 使用配置类构建URL
        return JitsiConfig::buildJitsiUrl($roomName, $options);
    }
    
    /**
     * 生成会议室分享链接
     * Generate meeting room share link
     *
     * Creates a shareable link for accessing a specific meeting room.
     *
     * @param string $roomName The room name
     * @param string $baseUrl Base URL for the application (optional)
     * @return string The shareable link
     */
    public static function generateShareLink($roomName, $baseUrl = null) {
        if ($baseUrl === null) {
            $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') 
                     . '://' . $_SERVER['HTTP_HOST'] 
                     . dirname($_SERVER['SCRIPT_NAME']);
        }
        
        return $baseUrl . '/meeting.php?room=' . urlencode($roomName);
    }
    
    /**
     * 获取推荐的会议室名称列表
     * Get suggested meeting room names
     *
     * Returns a list of commonly used room name suggestions.
     *
     * @return array Array of suggested room names
     */
    public static function getSuggestedRoomNames() {
        $suggestions = [
            '团队会议-' . date('md'),
            '项目讨论-' . date('md'),
            '部门例会-' . date('md'),
            '客户沟通-' . date('md'),
            '培训课程-' . date('md'),
        ];
        
        return $suggestions;
    }
    
    /**
     * 记录会议室使用情况（可扩展到数据库）
     * Log meeting room access (can be extended to database)
     *
     * Records access information for a meeting room to a log file.
     *
     * @param string $roomName The accessed room name
     * @param string $inviteCode The invitation code used
     * @param string $userAgent User agent string (optional)
     * @return bool True on success
     */
    public static function logMeetingAccess($roomName, $inviteCode, $userAgent = null) {
        $logConfig = JitsiConfig::getLogConfig();
        
        if (!$logConfig['enable_access_log']) {
            return true;
        }
        
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'room_name' => $roomName,
            'invite_code' => $inviteCode,
            'user_agent' => $userAgent ?: $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        ];
        
        $logFile = $logConfig['log_file_path'];
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // 检查日志文件大小
        if (file_exists($logFile) && filesize($logFile) > $logConfig['max_log_size']) {
            // 轮转日志文件
            $backupFile = $logFile . '.' . date('Y-m-d-H-i-s') . '.bak';
            rename($logFile, $backupFile);
        }
        
        $logEntry = json_encode($logData) . "\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        return true;
    }
    
    /**
     * 获取Jitsi Meet iframe配置
     * Get Jitsi Meet iframe configuration
     *
     * Returns configuration options for embedding Jitsi Meet in an iframe.
     *
     * @param string $roomName The room name
     * @param array $options Additional configuration options
     * @return array Complete configuration array
     */
    public static function getJitsiConfig($roomName, $options = []) {
        $defaultOptions = [
            'width' => '100%',
            'height' => '100%',
            'parentNode' => null,
            'configOverwrite' => [
                'prejoinPageEnabled' => false,
                'disableDeepLinking' => true,
            ],
            'interfaceConfigOverwrite' => [
                'TOOLBAR_BUTTONS' => [
                    'microphone', 'camera', 'closedcaptions', 'desktop', 'fullscreen',
                    'fodeviceselection', 'hangup', 'profile', 'recording',
                    'livestreaming', 'etherpad', 'sharedvideo', 'settings', 'raisehand',
                    'videoquality', 'filmstrip', 'invite', 'feedback', 'stats', 'shortcuts',
                    'tileview', 'videobackgroundblur', 'download', 'help', 'mute-everyone',
                    'security'
                ],
                'SETTINGS_SECTIONS' => ['devices', 'language', 'moderator', 'profile', 'calendar'],
                'SHOW_JITSI_WATERMARK' => false,
                'SHOW_WATERMARK_FOR_GUESTS' => false,
            ]
        ];
        
        return array_merge_recursive($defaultOptions, $options);
    }
}
?>