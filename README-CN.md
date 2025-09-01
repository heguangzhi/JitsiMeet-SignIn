# 在线会议邀请码验证系统（iframe集成）

这是一个用于在线会议的邀请码验证系统，确保只有拥有有效邀请码的用户才能访问在线会议系统。系统使用iframe技术集成Jitsi Meet，让用户可以直接在当前页面使用会议功能，无需跳转到外部页面。

## 功能特点

- ✅ 邀请码验证：用户必须输入有效的邀请码才能访问在线会议
- ✅ iframe集成：无缝嵌入Jitsi Meet，无需页面跳转
- ✅ 会议室管理：创建新会议室或加入现有会议室
- ✅ 推荐会议室：提供常用会议室名称建议
- ✅ 会议室分享：一键复制会议室分享链接
- ✅ 全屏支持：支持会议界面全屏显示
- ✅ 管理员界面：生成、管理、启用/禁用邀请码
- ✅ 过期时间设置：支持设置邀请码过期时间
- ✅ 使用记录：记录邀请码和会议室使用情况
- ✅ 响应式设计：支持移动端和桌面端访问
- ✅ 安全验证：防止未授权访问

## 系统要求

- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Web服务器（Apache/Nginx）

## 安装步骤

### 1. 数据库设置

1. 创建MySQL数据库
2. 导入 `sql/init.sql` 文件创建数据表
```sql
mysql -u root -p < sql/init.sql
```

### 2. 配置数据库连接

编辑 `config/database.php` 文件，修改数据库连接信息：

```php
private $host = 'localhost';        // 数据库主机
private $db_name = 'jitsi_invite_system'; // 数据库名
private $username = 'root';         // 数据库用户名
private $password = '';             // 数据库密码
```

### 3. 配置Web服务器

将项目文件部署到Web服务器目录，确保：
- `index.php` 作为入口文件可访问（对应 https://example.com）
- `admin.php` 管理界面可访问
- `assets/` 目录可访问（CSS和JS文件）

### 4. 修改管理员密码

编辑 `admin.php` 文件，修改管理员密码：

```php
$admin_password = 'your_secure_password'; // 请更改为安全密码
```

## 使用说明

### 用户访问流程

1. 用户访问 `https://example.com`（您的验证页面）
2. 输入邀请码
3. 验证成功后进入会议室选择页面
4. 选择创建新会议室或加入现有会议室
5. 直接在当前页面使用在线会议功能

#### 会议室功能
- 创建新会议室：系统自动生成唯一的会议室名称
- 加入现有会议室：输入会议室名称即可加入
- 推荐会议室：选择系统推荐的常用会议室名称
- 分享会议室：一键复制会议室分享链接
- 全屏模式：支持会议界面全屏显示

### 管理员操作

1. 访问 `admin.php` 页面
2. 输入管理员密码登录
3. 可进行操作：
   - 生成新的邀请码
   - 设置邀请码过期时间
   - 启用/禁用邀请码
   - 删除邀请码
   - 查看使用记录

## 文件结构

```
/
├── index.php              # 邀请码验证入口页面
├── verify.php             # 邀请码验证处理脚本
├── meeting.php            # 会议室页面（iframe集成）
├── admin.php              # 管理界面
├── logout.php             # 登出功能
├── config/
│   ├── database.php       # 数据库配置
│   ├── language.php       # 多语言支持
│   └── jitsi.php          # Jitsi Meet配置
├── models/
│   ├── InviteCode.php     # 邀请码管理类
│   └── MeetingRoom.php    # 会议室管理类
├── assets/
│   ├── css/
│   │   ├── style.css      # 主页面样式
│   │   ├── admin.css      # 管理界面样式
│   │   └── meeting.css    # 会议室页面样式
│   └── js/
│       └── admin.js       # 管理界面脚本
├── sql/
│   └── init.sql           # 数据库初始化脚本
├── logs/
│   └── meeting_access.log # 会议室访问日志
├── README.md              # 文档（中文）
├── README-EN.md           # 文档（英文）
└── INSTALL.md             # 安装指南（中文）
```

## 安全注意事项

1. **修改默认密码**：务必修改 `admin.php` 中的默认管理员密码
2. **数据库安全**：确保数据库用户权限最小化
3. **HTTPS**：生产环境中建议使用HTTPS
4. **访问控制**：可考虑为管理界面添加IP白名单

## 自定义配置

### Jitsi Meet配置
编辑 [`config/jitsi.php`](config/jitsi.php) 文件可自定义：
- **域名设置**：修改 `JITSI_DOMAIN` 常量
- **iframe配置**：调整 `IFRAME_CONFIG` 选项
- **界面配置**：自定义 `INTERFACE_CONFIG` 工具栏和设置
- **会议室验证规则**：修改 `ROOM_NAME_RULES`

### 修改跳转URL

修改 `verify.php` 文件中的跳转地址：

```php
// 跳转到会议室选择页面
header('Location: meeting.php');
```

### 修改邀请码生成规则

修改 `models/InviteCode.php` 文件中 `generateRandomCode()` 方法的生成规则。

### 样式自定义

- 修改 `assets/css/style.css` 自定义验证页面样式
- 修改 `assets/css/meeting.css` 自定义会议室页面样式
- 修改 `assets/css/admin.css` 自定义管理界面样式

## 语言支持

系统支持中英文两种语言：

- 所有页面都提供语言切换功能
- 语言偏好存储在会话和Cookie中
- 所有文本和错误信息都已本地化
- [English Documentation](README-EN.md) | [中文文档](README-CN.md)

## 常见问题

### Q: 邀请码验证失败？
A: 检查数据库连接配置，确保邀请码存在且状态有效。

### Q: 管理界面无法访问？
A: 检查管理员密码是否正确，确保数据库连接正常。

### Q: 样式显示异常？
A: 确保Web服务器可以访问 `assets/` 目录。

## 支持与维护

如需技术支持或功能定制，请联系开发团队。

## 许可证

本项目仅供内部使用，请勿用于商业用途。