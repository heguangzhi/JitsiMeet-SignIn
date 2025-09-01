<?php
/**
 * 会话配置文件
 * Session Configuration File
 *
 * This file contains the session management class to solve session permission 
 * and security issues. It provides a unified interface for session operations.
 * 解决会话权限和安全性问题
 */

class SessionConfig {
    
    /**
     * 初始化会话配置
     * Initialize session configuration
     *
     * Sets up session parameters and starts the session. Attempts to use a custom
     * session directory in the project, with fallbacks to system temp directory
     * and default configuration if needed.
     *
     * @return bool True on success, false on failure
     */
    public static function init() {
        // 检查是否已经启动会话
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }
        
        try {
            // 设置会话保存路径到项目目录
            $sessionPath = __DIR__ . '/../sessions';
            if (!is_dir($sessionPath)) {
                mkdir($sessionPath, 0755, true);
            }
            
            // 确保目录可写
            if (!is_writable($sessionPath)) {
                chmod($sessionPath, 0755);
            }
            
            // 设置会话参数
            ini_set('session.save_path', $sessionPath);
            ini_set('session.gc_maxlifetime', 7200); // 2小时
            ini_set('session.cookie_lifetime', 0); // 浏览器关闭时过期
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_strict_mode', 1);
            
            // 设置会话名称
            session_name('JITSI_SESSION');
            
            // 启动会话
            return session_start();
            
        } catch (Exception $e) {
            // 如果自定义路径失败，尝试使用临时目录
            try {
                $tempPath = sys_get_temp_dir() . '/jitsi_sessions';
                if (!is_dir($tempPath)) {
                    mkdir($tempPath, 0755, true);
                }
                
                ini_set('session.save_path', $tempPath);
                session_name('JITSI_SESSION');
                return session_start();
                
            } catch (Exception $e2) {
                // 最后尝试使用默认配置
                error_log('会话配置失败: ' . $e2->getMessage());
                session_name('JITSI_SESSION');
                return @session_start();
            }
        }
    }
    
    /**
     * 安全地销毁会话
     * Securely destroy session
     *
     * Completely destroys the session and removes session cookie.
     *
     * @return void
     */
    public static function destroy() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = array();
            
            // 删除会话cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            session_destroy();
        }
    }
    
    /**
     * 验证会话安全性
     * Validate session security
     *
     * Checks if the session is active and not expired. Also performs optional
     * IP address validation for additional security.
     *
     * @return bool True if session is valid, false otherwise
     */
    public static function validateSession() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }
        
        // 检查会话是否过期
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity'] > 7200)) {
            self::destroy();
            return false;
        }
        
        // 更新最后活动时间
        $_SESSION['last_activity'] = time();
        
        // 检查IP地址（可选的安全检查）
        if (isset($_SESSION['ip_address']) && 
            $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            self::destroy();
            return false;
        }
        
        return true;
    }
    
    /**
     * 设置会话变量
     * Set session variable
     *
     * Stores a value in the session. Also records IP address on first set.
     *
     * @param string $key The session key
     * @param mixed $value The value to store
     * @return void
     */
    public static function set($key, $value) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION[$key] = $value;
            
            // 首次设置时记录IP地址
            if (!isset($_SESSION['ip_address'])) {
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['last_activity'] = time();
            }
        }
    }
    
    /**
     * 获取会话变量
     * Get session variable
     *
     * Retrieves a value from the session, returning a default if not found.
     *
     * @param string $key The session key
     * @param mixed $default Default value if key not found
     * @return mixed The stored value or default
     */
    public static function get($key, $default = null) {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }
    
    /**
     * 检查会话变量是否存在
     * Check if session variable exists
     *
     * Determines if a session key is set and the session is active.
     *
     * @param string $key The session key to check
     * @return bool True if key exists, false otherwise
     */
    public static function has($key) {
        return session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key]);
    }
    
    /**
     * 删除会话变量
     * Remove session variable
     *
     * Unsets a session key if it exists and the session is active.
     *
     * @param string $key The session key to remove
     * @return void
     */
    public static function remove($key) {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
}
?>