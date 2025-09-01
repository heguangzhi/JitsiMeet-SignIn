<?php
/**
 * 多语言支持配置文件
 * Multi-language Support Configuration
 *
 * This class provides language switching functionality and translation services
 * for the Jitsi Meet invitation system. It supports both Chinese (zh-CN) and 
 * English (en-US) languages.
 */

class Language {
    private static $current_lang = 'zh-CN';
    private static $translations = [];
    
    /**
     * 初始化语言设置
     * Initialize language settings
     *
     * Loads translations and determines the current language based on:
     * 1. Session variable 'language'
     * 2. Cookie 'language'
     * 3. URL parameter 'lang'
     * Priority: URL parameter > Session > Cookie
     */
    public static function init() {
        self::loadTranslations();
        
        // 从session或cookie获取语言设置
        if (isset($_SESSION['language'])) {
            self::$current_lang = $_SESSION['language'];
        } elseif (isset($_COOKIE['language'])) {
            self::$current_lang = $_COOKIE['language'];
        }
        
        // 处理语言切换请求
        if (isset($_GET['lang'])) {
            $requested_lang = $_GET['lang'];
            if (in_array($requested_lang, ['zh-CN', 'en-US'])) {
                self::setLanguage($requested_lang);
            }
        }
    }
    
    /**
     * 设置语言
     * Set language
     *
     * Sets the current language and stores it in both session and cookie.
     * The cookie will expire in 30 days.
     *
     * @param string $lang Language code (zh-CN or en-US)
     */
    public static function setLanguage($lang) {
        self::$current_lang = $lang;
        $_SESSION['language'] = $lang;
        setcookie('language', $lang, time() + (86400 * 30), '/'); // 30天
    }
    
    /**
     * 获取当前语言
     * Get current language
     *
     * Returns the currently active language code.
     *
     * @return string Current language code (zh-CN or en-US)
     */
    public static function getCurrentLanguage() {
        return self::$current_lang;
    }
    
    /**
     * 获取翻译文本
     * Get translation text
     *
     * Retrieves the translated text for a given key in the current language.
     * If no translation is found, returns the default value or the key itself.
     *
     * @param string $key Translation key
     * @param string $default Default value if translation not found
     * @return string Translated text
     */
    public static function get($key, $default = '') {
        $lang = self::$current_lang;
        return self::$translations[$lang][$key] ?? $default ?: $key;
    }
    
    /**
     * 加载翻译数据
     * Load translation data
     *
     * Loads all translation strings for both supported languages:
     * - Chinese (zh-CN)
     * - English (en-US)
     * Each language contains key-value pairs for UI elements, messages, etc.
     */
    private static function loadTranslations() {
        self::$translations = [
            'zh-CN' => [
                // 通用
                'site_title' => '在线会议邀请系统',
                'language' => '语言',
                'switch_to_english' => 'English',
                'switch_to_chinese' => '中文',
                
                // 登录页面
                'login_title' => '在线会议',
                'login_subtitle' => '请输入邀请码以访问会议系统',
                'invite_code' => '邀请码',
                'invite_code_placeholder' => '请输入邀请码',
                'verify_and_enter' => '验证并进入',
                'copyright' => '© 2025 在线会议邀请系统',
                
                // 错误信息
                'error_invalid' => '邀请码无效或已过期，请检查后重试',
                'error_empty' => '请输入邀请码',
                'error_system' => '系统错误，请稍后重试或联系管理员',
                'error_default' => '验证失败，请重试',
                
                // 会议室页面
                'meeting_room' => '会议室',
                'meeting_in_progress' => '会议进行中',
                'copy_link' => '复制链接',
                'fullscreen' => '全屏',
                'exit' => '退出',
                'connecting' => '正在连接会议室...',
                'link_copied' => '会议链接已复制到剪贴板',
                'fullscreen_error' => '无法进入全屏模式',
                
                // 管理页面
                'admin_title' => '邀请码管理系统',
                'admin_login' => '管理员登录',
                'admin_password' => '管理员密码',
                'login' => '登录',
                'logout' => '退出',
                'password_error' => '密码错误',
                'visit_verification_page' => '访问验证页面',
                'generate_new_code' => '生成新邀请码',
                'notes' => '备注信息',
                'optional' => '选填',
                'expiry_time' => '过期时间',
                'generate_code' => '生成邀请码',
                'code_list' => '邀请码列表',
                'invite_code_col' => '邀请码',
                'status_col' => '状态',
                'created_time' => '创建时间',
                'expiry_time_col' => '过期时间',
                'used_time' => '使用时间',
                'notes_col' => '备注',
                'actions' => '操作',
                'active' => '有效',
                'inactive' => '无效',
                'never_expires' => '永不过期',
                'not_used' => '未使用',
                'enable' => '启用',
                'disable' => '禁用',
                'delete' => '删除',
            ],
            
            'en-US' => [
                // Common
                'site_title' => 'Online Meeting Invitation System',
                'language' => 'Language',
                'switch_to_english' => 'English',
                'switch_to_chinese' => '中文',
                
                // Login page
                'login_title' => 'Online Meeting',
                'login_subtitle' => 'Please enter invitation code to access the meeting system',
                'invite_code' => 'Invitation Code',
                'invite_code_placeholder' => 'Enter invitation code',
                'verify_and_enter' => 'Verify & Enter',
                'copyright' => '© 2025 Online Meeting Invitation System',
                
                // Error messages
                'error_invalid' => 'Invalid or expired invitation code, please check and try again',
                'error_empty' => 'Please enter invitation code',
                'error_system' => 'System error, please try again later or contact administrator',
                'error_default' => 'Verification failed, please try again',
                
                // Meeting room page
                'meeting_room' => 'Meeting Room',
                'meeting_in_progress' => 'Meeting in Progress',
                'copy_link' => 'Copy Link',
                'fullscreen' => 'Fullscreen',
                'exit' => 'Exit',
                'connecting' => 'Connecting to meeting room...',
                'link_copied' => 'Meeting link copied to clipboard',
                'fullscreen_error' => 'Unable to enter fullscreen mode',
                
                // Admin page
                'admin_title' => 'Invitation Code Management System',
                'admin_login' => 'Administrator Login',
                'admin_password' => 'Administrator Password',
                'login' => 'Login',
                'logout' => 'Logout',
                'password_error' => 'Incorrect password',
                'visit_verification_page' => 'Visit Verification Page',
                'generate_new_code' => 'Generate New Invitation Code',
                'notes' => 'Notes',
                'optional' => 'Optional',
                'expiry_time' => 'Expiry Time',
                'generate_code' => 'Generate Code',
                'code_list' => 'Invitation Code List',
                'invite_code_col' => 'Invitation Code',
                'status_col' => 'Status',
                'created_time' => 'Created Time',
                'expiry_time_col' => 'Expiry Time',
                'used_time' => 'Used Time',
                'notes_col' => 'Notes',
                'actions' => 'Actions',
                'active' => 'Active',
                'inactive' => 'Inactive',
                'never_expires' => 'Never expires',
                'not_used' => 'Not used',
                'enable' => 'Enable',
                'disable' => 'Disable',
                'delete' => 'Delete',
            ]
        ];
    }
    
    /**
     * 获取语言切换的URL
     * Get language switch URL
     *
     * Generates a URL for switching to a different language while preserving
     * the current page and other query parameters.
     *
     * @param string $target_lang Target language code (zh-CN or en-US)
     * @return string URL with language parameter
     */
    public static function getSwitchUrl($target_lang) {
        $current_url = $_SERVER['REQUEST_URI'];
        $separator = strpos($current_url, '?') !== false ? '&' : '?';
        return $current_url . $separator . 'lang=' . $target_lang;
    }
    
    /**
     * 获取当前页面的页面标题语言属性
     * Get page language attribute for HTML tag
     *
     * Returns the appropriate language code for use in the HTML 'lang' attribute.
     * This helps with accessibility and SEO.
     *
     * @return string HTML language code (zh-CN or en-US)
     */
    public static function getPageLang() {
        return self::$current_lang === 'zh-CN' ? 'zh-CN' : 'en-US';
    }
}