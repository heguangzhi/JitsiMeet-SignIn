<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统错误 - 在线会议</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        
        .error-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        
        .error-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .error-title {
            color: #dc3545;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .error-message {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .error-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            font-size: 14px;
            color: #495057;
            text-align: left;
        }
        
        .btn-home {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-box">
            <div class="error-icon">⚠️</div>
            <h1 class="error-title">系统暂时不可用</h1>
            <p class="error-message">
                抱歉，系统遇到了一些技术问题。我们正在努力解决这个问题。
            </p>
            
            <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
            <div class="error-details">
                <strong>错误详情：</strong><br>
                <?php
                $error_type = $_GET['type'] ?? 'unknown';
                switch ($error_type) {
                    case 'session':
                        echo '会话系统初始化失败，请检查服务器配置。';
                        break;
                    case 'database':
                        echo '数据库连接失败，请检查数据库服务。';
                        break;
                    case 'permission':
                        echo '文件权限错误，请检查目录权限设置。';
                        break;
                    default:
                        echo '未知系统错误，请联系管理员。';
                }
                ?>
            </div>
            <?php endif; ?>
            
            <div class="actions">
                <a href="index.php" class="btn-home">返回首页</a>
            </div>
            
            <div style="margin-top: 30px; font-size: 12px; color: #adb5bd;">
                如果问题持续存在，请联系技术支持<br>
                错误时间: <?php echo date('Y-m-d H:i:s'); ?>
            </div>
        </div>
    </div>
</body>
</html>