<?php
// Include required configuration files
// 引入必要的配置文件
require_once 'config/session.php';
require_once 'config/language.php';

// Initialize session and language support
// 初始化会话和语言支持
if (!SessionConfig::init()) {
    die('System initialization failed, please contact administrator');
}
Language::init();
?>
<!DOCTYPE html>
<html lang="<?php echo Language::getPageLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::get('login_title'); ?> - <?php echo Language::get('site_title'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <!-- Language Switch - 语言切换 -->
            <div class="language-switch">
                <?php if (Language::getCurrentLanguage() === 'zh-CN'): ?>
                    <a href="<?php echo Language::getSwitchUrl('en-US'); ?>">English</a>
                <?php else: ?>
                    <a href="<?php echo Language::getSwitchUrl('zh-CN'); ?>">中文</a>
                <?php endif; ?>
            </div>
            
            <h1><?php echo Language::get('login_title'); ?></h1>
            <p class="subtitle"><?php echo Language::get('login_subtitle'); ?></p>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php
                    switch ($_GET['error']) {
                        case 'invalid':
                            echo Language::get('error_invalid');
                            break;
                        case 'empty':
                            echo Language::get('error_empty');
                            break;
                        case 'system':
                            echo Language::get('error_system');
                            break;
                        default:
                            echo Language::get('error_default');
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="verify.php" class="login-form">
                <div class="form-group">
                    <label for="invite_code"><?php echo Language::get('invite_code'); ?></label>
                    <input type="text" 
                           id="invite_code" 
                           name="invite_code" 
                           placeholder="<?php echo Language::get('invite_code_placeholder'); ?>" 
                           required 
                           maxlength="32"
                           style="text-transform: uppercase;">
                </div>
                
                <button type="submit" class="btn-submit"><?php echo Language::get('verify_and_enter'); ?></button>
            </form>
            
            <div class="footer">
                <p><?php echo Language::get('copyright'); ?></p>
            </div>
        </div>
    </div>
    
    <script>
        // Automatically convert input to uppercase
        // 自动转换为大写
        document.getElementById('invite_code').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });
        
        // Form validation before submission
        // 表单提交前验证
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const code = document.getElementById('invite_code').value.trim();
            if (!code) {
                e.preventDefault();
                alert('<?php echo Language::get('error_empty'); ?>');
                return false;
            }
        });
    </script>
</body>
</html>