# Jitsi Meet 邀请码系统 - 安装与权限配置指南

[English Version](INSTALL-EN.md)

您遇到的错误是由于 PHP 无法访问默认的会话文件目录导致的。以下是几种解决方案：

### 方案一：修复系统会话目录权限（推荐）

```bash
# 1. 检查当前 PHP 会话目录
php -r "echo 'Session path: ' . session_save_path() . \"\n\";"

# 2. 修复权限（需要 root 权限）
sudo chmod 755 /var/opt/remi/php74/lib/php/session
sudo chown apache:apache /var/opt/remi/php74/lib/php/session

# 3. 重启 Web 服务器
sudo systemctl restart nginx
sudo systemctl restart php74-php-fpm
```

### 方案二：使用项目自定义会话目录（已实现）

系统已经自动创建了会话配置文件 `config/session.php`，它会：

1. 尝试在项目目录下创建 `sessions` 文件夹
2. 如果失败，使用系统临时目录
3. 提供会话安全性验证

### 方案三：手动创建会话目录

```bash
# 在项目根目录执行
mkdir -p sessions
chmod 755 sessions
chown www-data:www-data sessions  # Ubuntu/Debian
# 或
chown apache:apache sessions      # CentOS/RHEL
```

## 完整部署步骤

### 1. 文件权限设置

```bash
# 设置项目目录权限
cd /www/example.com
chmod -R 755 .
chown -R apache:apache .

# 创建必要的目录
mkdir -p sessions logs
chmod 755 sessions logs
```

### 2. 数据库配置

```sql
-- 创建数据库
CREATE DATABASE jitsi_invite_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 创建用户（可选）
CREATE USER 'jitsi_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON jitsi_invite_system.* TO 'jitsi_user'@'localhost';
FLUSH PRIVILEGES;

-- 导入数据表
USE jitsi_invite_system;
SOURCE sql/init.sql;
```

### 3. 配置文件修改

编辑 `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'jitsi_invite_system';
private $username = 'jitsi_user';  // 或 'root'
private $password = 'secure_password';  // 您的数据库密码
```

编辑 `config/jitsi.php`:
```php
const JITSI_DOMAIN = 'm.example.com';  // 您的 Jitsi Meet 域名
```

### 4. 安全设置

编辑 `admin.php` 修改管理员密码:
```php
$admin_password = 'your_secure_admin_password';
```

### 5. Web 服务器配置

#### Nginx 配置示例
```nginx
server {
    listen 443 ssl http2;
    server_name example.com;
    
    root /www/example.com;
    index index.php;
    
    # SSL 配置
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9010;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # 保护敏感目录
    location ~ ^/(config|models|logs|sessions)/ {
        deny all;
    }
    
    location ~ /\. {
        deny all;
    }
}
```

### 6. PHP 配置优化

在 `php.ini` 或创建 `.htaccess` 文件:
```ini
# 会话配置
session.gc_maxlifetime = 7200
session.cookie_lifetime = 0
session.cookie_secure = 1
session.cookie_httponly = 1
session.use_strict_mode = 1

# 错误报告
display_errors = Off
log_errors = On
error_log = /www/example.com/logs/php_errors.log

# 安全设置
expose_php = Off
allow_url_fopen = Off
```

### 7. 定期维护

创建 crontab 任务清理过期文件:
```bash
# 编辑 crontab
crontab -e

# 添加清理任务（每天凌晨2点执行）
0 2 * * * /usr/bin/php /www/example.com/maintenance/cleanup.php
```

## 故障排除

### 检查权限
```bash
# 检查文件权限
ls -la /www/example.com/

# 检查 PHP 进程用户
ps aux | grep php-fpm

# 检查 Web 服务器用户example.com
ps aux | grep nginx
```

### 查看错误日志
```bash
# PHP 错误日志
tail -f /var/log/php-fpm/www-error.log

# Nginx 错误日志
tail -f /var/log/nginx/error.log

# 项目日志
tail -f /www/example.com/logs/meeting_access.log
```

### 测试会话功能
创建测试文件 `test_session.php`:
```php
<?php
require_once 'config/session.php';

if (SessionConfig::init()) {
    echo "会话系统正常工作\n";
    SessionConfig::set('test', 'success');
    echo "测试数据: " . SessionConfig::get('test') . "\n";
} else {
    echo "会话系统初始化失败\n";
}
?>
```

## 安全建议

1. **使用 HTTPS**: 确保网站使用 SSL 证书
2. **定期更新**: 保持 PHP 和服务器软件更新
3. **备份数据**: 定期备份数据库和配置文件
4. **监控日志**: 定期检查访问和错误日志
5. **限制访问**: 使用防火墙限制不必要的端口访问

## 支持

如果仍然遇到问题，请提供以下信息：
- 服务器操作系统版本
- PHP 版本 (`php -v`)
- Web 服务器类型和版本
- 具体的错误日志内容

通过以上配置，应该可以完全解决您遇到的会话权限问题。