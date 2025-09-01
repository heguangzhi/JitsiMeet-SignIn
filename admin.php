<?php
// Include required configuration and model files
// 引入必要的配置文件和模型
require_once 'config/session.php';
require_once 'config/language.php';
require_once 'models/InviteCode.php';

// Initialize session and language support
// 初始化会话和语言支持
if (!SessionConfig::init()) {
    die(Language::get('error_system'));
}
Language::init();

// Simple admin authentication (in real projects, a more secure authentication should be used)
// 简单的管理员验证（实际项目中应该使用更安全的认证）
$admin_password = 'admin123'; // Please change this password in actual use // 在实际使用中请修改此密码

if (!SessionConfig::has('admin_logged_in')) {
    if (($_POST['admin_password'] ?? '') === $admin_password) {
        SessionConfig::set('admin_logged_in', true);
    } else {
        $show_login = true;
    }
}

// 处理管理操作
if (SessionConfig::has('admin_logged_in') && SessionConfig::get('admin_logged_in')) {
    $inviteCodeManager = new InviteCode();
    
    // Handle AJAX requests
    // 处理AJAX请求
    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        switch ($_POST['action']) {
            case 'generate':
                $notes = $_POST['notes'] ?? '';
                $expires_at = $_POST['expires_at'] ?? null;
                if (empty($expires_at)) $expires_at = null;
                
                $code = $inviteCodeManager->generateCode($notes, $expires_at);
                echo json_encode(['success' => $code !== false, 'code' => $code]);
                exit;
                
            case 'toggle':
                $id = $_POST['id'] ?? 0;
                $result = $inviteCodeManager->toggleCodeStatus($id);
                echo json_encode(['success' => $result]);
                exit;
                
            case 'delete':
                $id = $_POST['id'] ?? 0;
                $result = $inviteCodeManager->deleteCode($id);
                echo json_encode(['success' => $result]);
                exit;
        }
    }
    
    // 获取所有邀请码
    $codes = $inviteCodeManager->getAllCodes();
}

if (isset($show_login)) {
    $login_error = isset($_POST['admin_password']) ? Language::get('password_error') : '';
}
?>

<!DOCTYPE html>
<html lang="<?php echo Language::getPageLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Language::get('admin_title'); ?></title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php if (isset($show_login)): ?>
        <!-- 管理员登录界面 -->
        <div class="login-container">
            <div class="login-box">
                <!-- 语言切换 -->
                <div class="language-switch">
                    <?php if (Language::getCurrentLanguage() === 'zh-CN'): ?>
                        <a href="<?php echo Language::getSwitchUrl('en-US'); ?>">English</a>
                    <?php else: ?>
                        <a href="<?php echo Language::getSwitchUrl('zh-CN'); ?>">中文</a>
                    <?php endif; ?>
                </div>
                
                <h1><?php echo Language::get('admin_login'); ?></h1>
                <?php if ($login_error): ?>
                    <div class="error"><?php echo $login_error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label><?php echo Language::get('admin_password'); ?>：</label>
                        <input type="password" name="admin_password" required>
                    </div>
                    <button type="submit"><?php echo Language::get('login'); ?></button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <!-- 管理界面 -->
        <div class="admin-container">
            <header class="admin-header">
                <!-- Language Switch - 语言切换 -->
                <div class="language-switch">
                    <?php if (Language::getCurrentLanguage() === 'zh-CN'): ?>
                        <a href="<?php echo Language::getSwitchUrl('en-US'); ?>">English</a>
                    <?php else: ?>
                        <a href="<?php echo Language::getSwitchUrl('zh-CN'); ?>">中文</a>
                    <?php endif; ?>
                </div>
                
                <h1><?php echo Language::get('admin_title'); ?></h1>
                <div class="header-actions">
                    <a href="index.php" target="_blank"><?php echo Language::get('visit_verification_page'); ?></a>
                    <a href="?logout=1"><?php echo Language::get('logout'); ?></a>
                </div>
            </header>
            
            <main class="admin-main">
                <!-- 生成邀请码区域 -->
                <section class="generate-section">
                    <h2><?php echo Language::get('generate_new_code'); ?></h2>
                    <form id="generateForm" class="generate-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo Language::get('notes'); ?>：</label>
                                <input type="text" name="notes" placeholder="<?php echo Language::get('optional'); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo Language::get('expiry_time'); ?>：</label>
                                <input type="datetime-local" name="expires_at">
                            </div>
                            <button type="submit"><?php echo Language::get('generate_code'); ?></button>
                        </div>
                    </form>
                </section>
                
                <!-- 邀请码列表 -->
                <section class="codes-section">
                    <h2><?php echo Language::get('code_list'); ?></h2>
                    <div class="table-container">
                        <table class="codes-table">
                            <thead>
                                <tr>
                                    <th><?php echo Language::get('invite_code_col'); ?></th>
                                    <th><?php echo Language::get('status_col'); ?></th>
                                    <th><?php echo Language::get('created_time'); ?></th>
                                    <th><?php echo Language::get('expiry_time_col'); ?></th>
                                    <th><?php echo Language::get('used_time'); ?></th>
                                    <th><?php echo Language::get('notes_col'); ?></th>
                                    <th><?php echo Language::get('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody id="codesTableBody">
                                <?php foreach ($codes as $code): ?>
                                <tr data-id="<?php echo $code['id']; ?>">
                                    <td class="code-cell"><?php echo htmlspecialchars($code['invite_code']); ?></td>
                                    <td>
                                        <span class="status <?php echo $code['is_active'] ? 'active' : 'inactive'; ?>">
                                            <?php echo $code['is_active'] ? Language::get('active') : Language::get('inactive'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $code['created_at']; ?></td>
                                    <td><?php echo $code['expires_at'] ?: Language::get('never_expires'); ?></td>
                                    <td><?php echo $code['used_at'] ?: Language::get('not_used'); ?></td>
                                    <td><?php echo htmlspecialchars($code['notes']); ?></td>
                                    <td class="actions">
                                        <button onclick="toggleCode(<?php echo $code['id']; ?>)" 
                                                class="btn-toggle">
                                            <?php echo $code['is_active'] ? Language::get('disable') : Language::get('enable'); ?>
                                        </button>
                                        <button onclick="deleteCode(<?php echo $code['id']; ?>)" 
                                                class="btn-delete"><?php echo Language::get('delete'); ?></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
        
        <script src="assets/js/admin.js"></script>
    <?php endif; ?>
    
    <?php
    // 处理退出登录
    if (isset($_GET['logout'])) {
        SessionConfig::remove('admin_logged_in');
        header('Location: admin.php');
        exit;
    }
    ?>
</body>
</html>