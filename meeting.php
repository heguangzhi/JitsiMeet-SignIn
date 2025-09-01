<?php
// Include required configuration and model files
// 引入必要的配置文件和模型
require_once 'config/session.php';
require_once 'config/language.php';
require_once 'models/MeetingRoom.php';

// Initialize session and language support
// 初始化会话和语言支持
if (!SessionConfig::init()) {
    header('Location: index.php?error=system');
    exit;
}
Language::init();

// Validate session security
// 验证会话安全性
if (!SessionConfig::validateSession()) {
    header('Location: index.php');
    exit;
}

// Check if user is verified
// 检查用户是否已验证
if (!SessionConfig::has('verified') || !SessionConfig::get('verified')) {
    header('Location: index.php');
    exit;
}

// Handle meeting room creation/joining
// 处理会议室创建/加入
$room_name = Language::getCurrentLanguage() === 'zh-CN' ? '在线面试' : 'Online Interview';
$jitsi_url = '';
$share_link = '';

// 直接构建固定会议室的URL
$jitsi_url = MeetingRoom::buildJitsiUrl($room_name);
$share_link = MeetingRoom::generateShareLink($room_name);

// 记录会议室信息到session
SessionConfig::set('current_room', $room_name);
SessionConfig::set('room_join_time', time());

// 记录访问日志
MeetingRoom::logMeetingAccess($room_name, SessionConfig::get('invite_code'));
?>

<!DOCTYPE html>
<html lang="<?php echo Language::getPageLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room_name); ?></title>
    <link rel="stylesheet" href="assets/css/meeting.css">
</head>
<body>
    <!-- 直接显示在线面试 iframe界面 -->
    <div class="jitsi-container">
        <div class="meeting-header">
            <!-- Language Switch - 语言切换 -->
            <div class="language-switch">
                <?php if (Language::getCurrentLanguage() === 'zh-CN'): ?>
                    <a href="<?php echo Language::getSwitchUrl('en-US'); ?>">English</a>
                <?php else: ?>
                    <a href="<?php echo Language::getSwitchUrl('zh-CN'); ?>">中文</a>
                <?php endif; ?>
            </div>
            
            <div class="meeting-info">
                <h2><?php echo Language::get('meeting_room'); ?>：<?php echo htmlspecialchars($room_name); ?></h2>
                <span class="meeting-status"><?php echo Language::get('meeting_in_progress'); ?></span>
            </div>
            <div class="meeting-controls">
                <button onclick="copyRoomLink()" class="btn-control" title="<?php echo Language::get('copy_link'); ?>">
                    📋 <?php echo Language::get('copy_link'); ?>
                </button>
                <button onclick="toggleFullscreen()" class="btn-control" title="<?php echo Language::get('fullscreen'); ?>">
                    🔲 <?php echo Language::get('fullscreen'); ?>
                </button>
                <a href="logout.php" class="btn-control logout" title="<?php echo Language::get('exit'); ?>">
                    🚪 <?php echo Language::get('exit'); ?>
                </a>
            </div>
        </div>
        
        <div class="jitsi-frame-container" id="jitsiContainer">
            <iframe 
                id="jitsiFrame"
                src="<?php echo htmlspecialchars($jitsi_url); ?>"
                frameborder="0"
                allow="camera; microphone; fullscreen; display-capture; autoplay"
                allowfullscreen>
            </iframe>
            
            <!-- 加载指示器 -->
            <div class="loading-indicator" id="loadingIndicator">
                <div class="spinner"></div>
                <p><?php echo Language::get('connecting'); ?></p>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
        
        <script>
            const roomName = '<?php echo addslashes($room_name); ?>';
            const jitsiUrl = '<?php echo addslashes($jitsi_url); ?>';
            const shareLink = '<?php echo addslashes($share_link); ?>';
            
            // Copy meeting room link
            // 复制会议链接
            function copyRoomLink() {
                const link = shareLink || (window.location.origin + window.location.pathname);
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(link).then(() => {
                        showNotification('<?php echo Language::get('link_copied'); ?>');
                    });
                } else {
                    // Fallback copy method
                    // 备用复制方法
                    const textArea = document.createElement('textarea');
                    textArea.value = link;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    showNotification('<?php echo Language::get('link_copied'); ?>');
                }
            }
            
            // Toggle fullscreen mode
            // 全屏切换
            function toggleFullscreen() {
                const container = document.getElementById('jitsiContainer');
                if (!document.fullscreenElement) {
                    container.requestFullscreen().catch(err => {
                        console.log('无法进入全屏模式:', err);
                        showNotification('<?php echo Language::get('fullscreen_error'); ?>', 'error');
                    });
                } else {
                    document.exitFullscreen();
                }
            }
            
            // Show notification message
            // 显示通知
            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.textContent = message;
                
                const bgColor = type === 'error' ? '#dc3545' : '#28a745';
                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${bgColor};
                    color: white;
                    padding: 12px 20px;
                    border-radius: 6px;
                    z-index: 1000;
                    opacity: 0;
                    transform: translateX(100px);
                    transition: all 0.3s ease;
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                `;
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '1';
                    notification.style.transform = 'translateX(0)';
                }, 100);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(100px)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
            
            // Hide loading indicator after iframe loads
            // iframe 加载完成后隐藏加载指示器
            document.getElementById('jitsiFrame').addEventListener('load', function() {
                const loadingIndicator = document.getElementById('loadingIndicator');
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
            });
            
            // Listen for fullscreen state changes
            // 监听全屏状态变化
            document.addEventListener('fullscreenchange', function() {
                const container = document.getElementById('jitsiContainer');
                if (document.fullscreenElement) {
                    container.classList.add('fullscreen-mode');
                } else {
                    container.classList.remove('fullscreen-mode');
                }
            });
        </script>
</body>
</html>