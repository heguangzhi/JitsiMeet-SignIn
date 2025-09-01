# Online Meeting Invitation Code Verification System (iframe Integration)

This is an invitation code verification system for online meetings that ensures only users with valid invitation codes can access the online meeting system. The system integrates Jitsi Meet using iframe technology, allowing users to use meeting features directly on the current page without redirecting to external pages.

## Features

- ✅ Invitation Code Verification: Users must enter valid invitation codes to access online meetings
- ✅ iframe Integration: Seamlessly embed Jitsi Meet without page redirects
- ✅ Meeting Room Management: Create new meeting rooms or join existing ones
- ✅ Recommended Meeting Rooms: Provide suggestions for commonly used meeting room names
- ✅ Meeting Room Sharing: One-click copy meeting room share links
- ✅ Fullscreen Support: Support fullscreen display of meeting interface
- ✅ Admin Interface: Generate, manage, enable/disable invitation codes
- ✅ Expiration Time Setting: Support setting invitation code expiration times
- ✅ Usage Records: Record invitation code and meeting room usage
- ✅ Responsive Design: Support both mobile and desktop access
- ✅ Security Verification: Prevent unauthorized access

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)

## Installation Steps

### 1. Database Setup

1. Create a MySQL database
2. Import the `sql/init.sql` file to create data tables
```sql
mysql -u root -p < sql/init.sql
```

### 2. Configure Database Connection

Edit the `config/database.php` file and modify the database connection information:

```php
private $host = 'localhost';        // Database host
private $db_name = 'jitsi_invite_system'; // Database name
private $username = 'root';         // Database username
private $password = '';             // Database password
```

### 3. Configure Web Server

Deploy project files to the web server directory, ensuring:
- `index.php` as the entry file is accessible (corresponds to https://example.com)
- `admin.php` admin interface is accessible
- `assets/` directory is accessible (CSS and JS files)

### 4. Modify Administrator Password

Edit the `admin.php` file and modify the administrator password:

```php
$admin_password = 'your_secure_password'; // Please change to a secure password
```

## Usage Instructions

### User Access Flow

1. User visits `https://example.com` (your verification page)
2. Enter invitation code
3. After successful verification, enter meeting room selection page
4. Choose to create a new meeting room or join an existing one
5. Use online meeting features directly on the current page

#### Meeting Room Features
- Create New Meeting Room: System automatically generates unique meeting room names
- Join Existing Meeting Room: Enter meeting room name to join
- Recommended Meeting Rooms: Select commonly used meeting room names recommended by the system
- Share Meeting Room: One-click copy meeting room share links
- Fullscreen Mode: Support fullscreen display of meeting interface

### Administrator Operations

1. Visit the `admin.php` page
2. Enter administrator password to login
3. Available operations:
   - Generate new invitation codes
   - Set invitation code expiration times
   - Enable/disable invitation codes
   - Delete invitation codes
   - View usage records

## File Structure

```
/
├── index.php              # Invitation code verification entry page
├── verify.php             # Invitation code verification processing script
├── meeting.php            # Meeting room page (iframe integration)
├── admin.php              # Admin interface
├── logout.php             # Logout functionality
├── config/
│   ├── database.php       # Database configuration
│   ├── language.php       # Multi-language support
│   └── jitsi.php          # Jitsi Meet configuration
├── models/
│   ├── InviteCode.php     # Invitation code management class
│   └── MeetingRoom.php    # Meeting room management class
├── assets/
│   ├── css/
│   │   ├── style.css      # Main page styles
│   │   ├── admin.css      # Admin interface styles
│   │   └── meeting.css    # Meeting room page styles
│   └── js/
│       └── admin.js       # Admin interface scripts
├── sql/
│   └── init.sql           # Database initialization script
├── logs/
│   └── meeting_access.log # Meeting room access logs
├── README.md              # Documentation (English)
├── README-CN.md           # Documentation (Chinese)
└── INSTALL-EN.md          # Installation guide (English)
```

## Security Considerations

1. **Change Default Password**: Be sure to change the default administrator password in `admin.php`
2. **Database Security**: Ensure database user permissions are minimized
3. **HTTPS**: It's recommended to use HTTPS in production environments
4. **Access Control**: Consider adding IP whitelist for admin interface

## Custom Configuration

### Jitsi Meet Configuration
Edit the [`config/jitsi.php`](config/jitsi.php) file to customize:
- **Domain Settings**: Modify the `JITSI_DOMAIN` constant
- **iframe Configuration**: Adjust `IFRAME_CONFIG` options
- **Interface Configuration**: Customize `INTERFACE_CONFIG` toolbar and settings
- **Meeting Room Validation Rules**: Modify `ROOM_NAME_RULES`

### Modify Redirect URL

Modify the redirect address in the `verify.php` file:

```php
// Redirect to meeting room selection page
header('Location: meeting.php');
```

### Modify Invitation Code Generation Rules

Modify the generation rules in the `generateRandomCode()` method of the `models/InviteCode.php` file.

### Style Customization

- Modify `assets/css/style.css` to customize verification page styles
- Modify `assets/css/meeting.css` to customize meeting room page styles
- Modify `assets/css/admin.css` to customize admin interface styles

## Language Support

The system supports both Chinese and English languages:

- Language switching is available on all pages
- Language preference is stored in session and cookies
- All text and error messages are localized
- [中文文档](README-CN.md) | [English Documentation](README-EN.md)

## Common Issues

### Q: Invitation code verification failed?
A: Check database connection configuration, ensure invitation code exists and status is valid.

### Q: Admin interface inaccessible?
A: Check if administrator password is correct, ensure database connection is normal.

### Q: Style display issues?
A: Ensure `assets/` directory is accessible by the web server.

## Support & Maintenance

For technical support or feature customization, please contact the development team.

## License

This project is for internal use only, please do not use for commercial purposes.