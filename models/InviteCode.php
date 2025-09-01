<?php
require_once 'config/database.php';

/**
 * 邀请码管理类
 * Invitation Code Management Class
 *
 * This class handles all operations related to invitation codes including
 * validation, generation, management, and database interactions.
 */
class InviteCode {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    /**
     * 验证邀请码是否有效
     * Validate if an invitation code is valid
     *
     * Checks if the provided code exists, is active, and not expired.
     * If valid, marks the code as used.
     *
     * @param string $code The invitation code to validate
     * @return bool True if valid, false otherwise
     */
    public function validateCode($code) {
        try {
            $sql = "SELECT * FROM invite_codes 
                   WHERE invite_code = :code 
                   AND is_active = 1 
                   AND (expires_at IS NULL OR expires_at > NOW())";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                // 更新使用时间
                $this->markAsUsed($result['id']);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log('邀请码验证失败: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 标记邀请码为已使用
     * Mark invitation code as used
     *
     * Updates the used_at timestamp for a given invitation code.
     *
     * @param int $id The ID of the invitation code
     * @return void
     */
    private function markAsUsed($id) {
        try {
            $sql = "UPDATE invite_codes SET used_at = NOW() WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (Exception $e) {
            error_log('更新邀请码使用时间失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 获取所有邀请码
     * Get all invitation codes
     *
     * Retrieves all invitation codes from the database, ordered by creation time.
     *
     * @return array Array of invitation codes
     */
    public function getAllCodes() {
        try {
            $sql = "SELECT * FROM invite_codes ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log('获取邀请码列表失败: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 生成新的邀请码
     * Generate a new invitation code
     *
     * Creates a new unique invitation code and stores it in the database.
     *
     * @param string $notes Optional notes for the invitation code
     * @param string $expires_at Optional expiration date/time
     * @return string|bool The generated code or false on failure
     */
    public function generateCode($notes = '', $expires_at = null) {
        try {
            // 生成唯一邀请码
            do {
                $code = $this->generateRandomCode();
                $exists = $this->codeExists($code);
            } while ($exists);
            
            $sql = "INSERT INTO invite_codes (invite_code, notes, expires_at) VALUES (:code, :notes, :expires_at)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':expires_at', $expires_at);
            
            if ($stmt->execute()) {
                return $code;
            }
            
            return false;
        } catch (Exception $e) {
            error_log('生成邀请码失败: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 生成随机邀请码
     * Generate a random invitation code
     *
     * Creates a random string of specified length using alphanumeric characters.
     *
     * @param int $length Length of the generated code (default: 8)
     * @return string The generated random code
     */
    private function generateRandomCode($length = 8) {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }
    
    /**
     * 检查邀请码是否已存在
     * Check if an invitation code already exists
     *
     * Verifies if a given invitation code is already present in the database.
     *
     * @param string $code The code to check
     * @return bool True if exists, false otherwise
     */
    private function codeExists($code) {
        try {
            $sql = "SELECT COUNT(*) FROM invite_codes WHERE invite_code = :code";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            return true; // 出错时假设存在，重新生成
        }
    }
    
    /**
     * 启用/禁用邀请码
     * Toggle invitation code status
     *
     * Switches the active status of an invitation code (1 to 0 or 0 to 1).
     *
     * @param int $id The ID of the invitation code
     * @return bool True on success, false on failure
     */
    public function toggleCodeStatus($id) {
        try {
            $sql = "UPDATE invite_codes SET is_active = 1 - is_active WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('切换邀请码状态失败: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 删除邀请码
     * Delete an invitation code
     *
     * Removes an invitation code from the database.
     *
     * @param int $id The ID of the invitation code to delete
     * @return bool True on success, false on failure
     */
    public function deleteCode($id) {
        try {
            $sql = "DELETE FROM invite_codes WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log('删除邀请码失败: ' . $e->getMessage());
            return false;
        }
    }
}
?>