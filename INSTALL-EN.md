# Jitsi Meet Invitation Code System - Installation & Permissions Configuration Guide

## Solving PHP Session Permission Issues

The error you encountered is caused by PHP being unable to access the default session file directory. Here are several solutions:

### Solution 1: Fix System Session Directory Permissions (Recommended)

```bash
# 1. Check current PHP session directory
php -r "echo 'Session path: ' . session_save_path() . \"\n\";"

# 2. Fix permissions (requires root privileges)
sudo chmod 755 /var/opt/remi/php74/lib/php/session
sudo chown apache:apache /var/opt/remi/php74/lib/php/session

# 3. Restart web server
sudo systemctl restart nginx
sudo systemctl restart php74-php-fpm
```

### Solution 2: Use Project Custom Session Directory (Already Implemented)

The system has automatically created a session configuration file `config/session.php`, which will:

1. Try to create a `sessions` folder in the project directory
2. Use system temporary directory if it fails
3. Provide session security verification

### Solution 3: Manually Create Session Directory

```bash
# Execute in project root directory
mkdir -p sessions
chmod 755 sessions
chown www-data:www-data sessions  # Ubuntu/Debian
# or
chown apache:apache sessions      # CentOS/RHEL
```

## Complete Deployment Steps

### 1. File Permission Settings

```bash
# Set project directory permissions
cd /www/example.com
chmod -R 755 .
chown -R apache:apache .

# Create necessary directories
mkdir -p sessions logs
chmod 755 sessions logs
```

### 2. Database Configuration

```sql
-- Create database
CREATE DATABASE jitsi_invite_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (optional)
CREATE USER 'jitsi_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON jitsi_invite_system.* TO 'jitsi_user'@'localhost';
FLUSH PRIVILEGES;

-- Import data tables
USE jitsi_invite_system;
SOURCE sql/init.sql;
```

### 3. Configuration File Modifications

Edit `config/database.php`:
```php
private $host = 'localhost';
private $db_name = 'jitsi_invite_system';
private $username = 'jitsi_user';  // or 'root'
private $password = 'secure_password';  // Your database password
```

Edit `config/jitsi.php`:
```php
const JITSI_DOMAIN = 'm.example.com';  // Your Jitsi Meet domain
```

### 4. Security Settings

Edit `admin.php` to modify administrator password:
```php
$admin_password = 'your_secure_admin_password';
```

### 5. Web Server Configuration

#### Nginx Configuration Example
```nginx
server {
    listen 443 ssl http2;
    server_name example.com;
    
    root /www/example.com;
    index index.php;
    
    # SSL configuration
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
    
    # Protect sensitive directories
    location ~ ^/(config|models|logs|sessions)/ {
        deny all;
    }
    
    location ~ /\. {
        deny all;
    }
}
```

### 6. PHP Configuration Optimization

In `php.ini` or create `.htaccess` file:
```ini
# Session configuration
session.gc_maxlifetime = 7200
session.cookie_lifetime = 0
session.cookie_secure = 1
session.cookie_httponly = 1
session.use_strict_mode = 1

# Error reporting
display_errors = Off
log_errors = On
error_log = /www/example.com/logs/php_errors.log

# Security settings
expose_php = Off
allow_url_fopen = Off
```

### 7. Regular Maintenance

Create crontab task to clean expired files:
```bash
# Edit crontab
crontab -e

# Add cleanup task (execute at 2 AM daily)
0 2 * * * /usr/bin/php /www/example.com/maintenance/cleanup.php
```

## Multi-language Support Configuration

### Language Files

The system includes comprehensive multi-language support:

- **Language Configuration**: `config/language.php`
- **Supported Languages**: Chinese (zh-CN) and English (en-US)
- **Language Switching**: Available on all pages
- **Persistence**: Language preference stored in session and cookies

### Language Switching Features

1. **Automatic Detection**: System detects browser language preference
2. **Manual Switching**: Language switch buttons on all pages
3. **Session Storage**: Language preference persists across sessions
4. **Cookie Backup**: 30-day cookie storage for language preference

### Adding New Languages

To add support for additional languages:

1. Edit `config/language.php`
2. Add new language array in `$translations`
3. Update language detection logic
4. Add new language option to switch buttons

## Troubleshooting

### Check Permissions
```bash
# Check file permissions
ls -la /www/example.com/

# Check PHP process user
ps aux | grep php-fpm

# Check web server user
ps aux | grep nginx
```

### View Error Logs
```bash
# PHP error log
tail -f /var/log/php-fpm/www-error.log

# Nginx error log
tail -f /var/log/nginx/error.log

# Project logs
tail -f /www/example.com/logs/meeting_access.log
```

### Test Session Functionality
Create test file `test_session.php`:
```php
<?php
require_once 'config/session.php';
require_once 'config/language.php';

if (SessionConfig::init()) {
    echo "Session system working normally\n";
    SessionConfig::set('test', 'success');
    echo "Test data: " . SessionConfig::get('test') . "\n";
} else {
    echo "Session system initialization failed\n";
}

// Test language functionality
Language::init();
echo "Current language: " . Language::getCurrentLanguage() . "\n";
echo "Test translation: " . Language::get('login_title') . "\n";
?>
```

### Performance Optimization

1. **PHP OpCache**: Enable PHP OpCache for better performance
2. **File Permissions**: Ensure proper file ownership and permissions
3. **Database Indexing**: Add indexes for frequently queried columns
4. **Session Cleanup**: Regular cleanup of expired session files

### Security Hardening

1. **Directory Protection**: Protect config and models directories
2. **Input Validation**: Validate all user inputs
3. **SQL Injection Prevention**: Use prepared statements
4. **XSS Protection**: Escape output data
5. **CSRF Protection**: Implement CSRF tokens for forms

## Language-Specific Configuration

### Chinese Language Features
- Traditional and Simplified Chinese support
- Chinese-specific date/time formatting
- Chinese character encoding (UTF-8)
- Cultural considerations for UI elements

### English Language Features
- International English terminology
- Standard date/time formatting
- ASCII-compatible encoding
- Western UI conventions

## Advanced Configuration

### Custom Language Strings

To customize language strings:

1. Edit `config/language.php`
2. Modify the appropriate language array
3. Clear any cached language files
4. Test language switching functionality

### Dynamic Language Loading

For better performance with many languages:

1. Implement lazy loading of language files
2. Cache frequently used translations
3. Optimize translation lookup performance

## Support

If you continue to experience issues, please provide:
- Server operating system version
- PHP version (`php -v`)
- Web server type and version
- Specific error log content
- Language preference settings

Through the above configuration, you should be able to completely resolve the session permission issues you encountered and enjoy full multi-language support.