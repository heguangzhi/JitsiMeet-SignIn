<?php
// Include session configuration
// 引入会话配置
require_once 'config/session.php';

// Initialize session (if exists)
// 初始化会话（如果存在）
SessionConfig::init();

// Securely clear all session data
// 安全地清除所有会话数据
SessionConfig::destroy();

// Redirect to homepage
// 重定向到首页
header('Location: index.php');
exit;
?>