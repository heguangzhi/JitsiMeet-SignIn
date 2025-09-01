<?php
/**
 * Jitsi Meet 配置文件
 * Jitsi Meet Configuration File
 *
 * This file contains configuration settings for integrating with Jitsi Meet,
 * including domain settings, iframe configuration, interface settings, 
 * room name validation rules, and logging configuration.
 */

class JitsiConfig {
    // Jitsi Meet 服务器域名
    // Jitsi Meet server domain
    const JITSI_DOMAIN = '';
    
    // iframe 配置选项
    // iframe configuration options
    const IFRAME_CONFIG = [
        'prejoinPageEnabled' => false,
        'disableDeepLinking' => true,
        'startWithAudioMuted' => false,
        'startWithVideoMuted' => false,
        'enableWelcomePage' => false,
        'enableClosePage' => false,
    ];
    
    // 界面配置
    // Interface configuration
    const INTERFACE_CONFIG = [
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
        'SHOW_BRAND_WATERMARK' => false,
        'BRAND_WATERMARK_LINK' => '',
        'SHOW_POWERED_BY' => false,
        'DEFAULT_BACKGROUND' => '#474747',
    ];
    
    // 会议室名称验证规则
    // Meeting room name validation rules
    const ROOM_NAME_RULES = [
        'min_length' => 3,
        'max_length' => 50,
        'allowed_chars' => '/^[\w\-\u4e00-\u9fa5]+$/u',
        'forbidden_words' => ['admin', 'root', 'system', 'test'],
    ];
    
    // 日志配置
    // Log configuration
    const LOG_CONFIG = [
        'enable_access_log' => true,
        'log_file_path' => __DIR__ . '/../logs/meeting_access.log',
        'max_log_size' => 10485760, // 10MB
        'log_retention_days' => 30,
    ];
    
    /**
     * 获取 Jitsi Meet 域名
     * Get Jitsi Meet domain
     *
     * Returns the configured Jitsi Meet server domain.
     *
     * @return string The Jitsi Meet domain
     */
    public static function getJitsiDomain() {
        return self::JITSI_DOMAIN;
    }
    
    /**
     * 构建完整的 Jitsi Meet URL
     * Build complete Jitsi Meet URL
     *
     * Constructs a complete URL for accessing a Jitsi Meet room with optional parameters.
     *
     * @param string $roomName The room name
     * @param array $options Additional configuration options
     * @return string The complete Jitsi Meet URL
     */
    public static function buildJitsiUrl($roomName, $options = []) {
        $domain = self::getJitsiDomain();
        $cleanRoomName = urlencode($roomName);
        $url = "https://{$domain}/{$cleanRoomName}";
        
        // 添加配置参数
        if (!empty($options)) {
            $params = [];
            foreach ($options as $key => $value) {
                if (is_bool($value)) {
                    $params[] = $key . '=' . ($value ? 'true' : 'false');
                } else {
                    $params[] = $key . '=' . urlencode($value);
                }
            }
            if (!empty($params)) {
                $url .= '#config.' . implode('&config.', $params);
            }
        }
        
        return $url;
    }
    
    /**
     * 获取 iframe 配置
     * Get iframe configuration
     *
     * Returns the iframe configuration settings for Jitsi Meet integration.
     *
     * @return array The iframe configuration array
     */
    public static function getIframeConfig() {
        return self::IFRAME_CONFIG;
    }
    
    /**
     * 获取界面配置
     * Get interface configuration
     *
     * Returns the interface configuration settings for Jitsi Meet.
     *
     * @return array The interface configuration array
     */
    public static function getInterfaceConfig() {
        return self::INTERFACE_CONFIG;
    }
    
    /**
     * 验证会议室名称
     * Validate meeting room name
     *
     * Checks if a room name meets the configured validation rules.
     *
     * @param string $roomName The room name to validate
     * @return array Validation result with validity status and error message if invalid
     */
    public static function validateRoomName($roomName) {
        $rules = self::ROOM_NAME_RULES;
        
        // 检查长度
        if (strlen($roomName) < $rules['min_length'] || strlen($roomName) > $rules['max_length']) {
            return [
                'valid' => false,
                'error' => "会议室名称长度必须在{$rules['min_length']}-{$rules['max_length']}字符之间"
            ];
        }
        
        // 检查字符
        if (!preg_match($rules['allowed_chars'], $roomName)) {
            return [
                'valid' => false,
                'error' => '会议室名称只能包含字母、数字、中文、横线和下划线'
            ];
        }
        
        // 检查禁用词
        $lowerRoomName = strtolower($roomName);
        foreach ($rules['forbidden_words'] as $word) {
            if (strpos($lowerRoomName, $word) !== false) {
                return [
                    'valid' => false,
                    'error' => '会议室名称包含禁用词汇'
                ];
            }
        }
        
        return [
            'valid' => true,
            'roomName' => $roomName
        ];
    }
    
    /**
     * 获取日志配置
     * Get log configuration
     *
     * Returns the logging configuration settings.
     *
     * @return array The log configuration array
     */
    public static function getLogConfig() {
        return self::LOG_CONFIG;
    }
}
?>