<?php
// Include required configuration and model files
// 引入必要的配置文件和模型
require_once 'config/session.php';
require_once 'models/InviteCode.php';

// Initialize session
// 初始化会话
if (!SessionConfig::init()) {
    error_log('会话初始化失败 - Session initialization failed');
    header('Location: index.php?error=system');
    exit;
}

// Check if form was submitted
// 检查是否提交了表单
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Get invitation code
// 获取邀请码
$invite_code = trim($_POST['invite_code'] ?? '');

// Validate input
// 验证输入
if (empty($invite_code)) {
    header('Location: index.php?error=empty');
    exit;
}

try {
    // Create invitation code manager instance
    // 创建邀请码管理实例
    $inviteCodeManager = new InviteCode();
    
    // Validate invitation code
    // 验证邀请码
    if ($inviteCodeManager->validateCode($invite_code)) {
        // Verification successful, record session and redirect to meeting page
        // 验证成功，记录会话并跳转到会议室页面
        SessionConfig::set('verified', true);
        SessionConfig::set('invite_code', $invite_code);
        SessionConfig::set('verify_time', time());
        
        // Redirect to meeting selection page
        // 跳转到会议室选择页面
        header('Location: meeting.php');
        exit;
    } else {
        // Verification failed
        // 验证失败
        header('Location: index.php?error=invalid');
        exit;
    }
    
} catch (Exception $e) {
    // Log error
    // 记录错误日志
    error_log('邀请码验证出错 - Invitation code verification error: ' . $e->getMessage());
    
    // Redirect to error page
    // 跳转到错误页面
    header('Location: index.php?error=system');
    exit;
}
?>