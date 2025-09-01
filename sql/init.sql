CREATE DATABASE IF NOT EXISTS jitsi_auth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE jitsi_auth;

-- 创建邀请码表
CREATE TABLE IF NOT EXISTS invite_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invite_code VARCHAR(32) NOT NULL UNIQUE COMMENT '邀请码',
    is_active TINYINT(1) DEFAULT 1 COMMENT '是否有效 1-有效 0-无效',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    expires_at TIMESTAMP NULL COMMENT '过期时间',
    used_at TIMESTAMP NULL COMMENT '使用时间',
    notes TEXT COMMENT '备注信息',
    INDEX idx_invite_code (invite_code),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='邀请码表';

-- 插入一些测试数据
INSERT INTO invite_codes (invite_code, notes) VALUES 
('TES2T2024', '测试邀请码1'),
('DEM2O2024', '测试邀请码2'),
('ADMI2N001', '管理员测试码');