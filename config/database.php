<?php
/**
 * 数据库配置文件
 * Database Configuration File
 *
 * This file contains the database connection class and configuration.
 */

class Database {
    private $host = 'localhost';
    private $db_name = '';
    private $username = '';
    private $password = '';
    private $charset = 'utf8mb4';
    private $pdo;
    
    /**
     * 获取数据库连接
     * Get database connection
     *
     * Establishes and returns a PDO database connection.
     * Uses singleton pattern to avoid multiple connections.
     *
     * @return PDO The database connection object
     * @throws Exception If connection fails
     */
    public function connect() {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                throw new Exception('数据库连接失败: ' . $e->getMessage());
            }
        }
        
        return $this->pdo;
    }
    
    /**
     * 关闭数据库连接
     * Close database connection
     *
     * Closes the current database connection by setting PDO object to null.
     *
     * @return void
     */
    public function disconnect() {
        $this->pdo = null;
    }
}
?>