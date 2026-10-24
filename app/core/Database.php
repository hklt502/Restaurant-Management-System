<?php
/**
 * Lớp quản lý kết nối CSDL (MySQLi Singleton)
 * app/core/Database.php
 */

require_once __DIR__ . '/../config/database.php';

class Database {
    private static $connection = null;

    // Không cho phép khởi tạo đối tượng trực tiếp từ bên ngoài
    private function __construct() {}

    /**
     * Lấy kết nối MySQLi dùng chung cho toàn bộ ứng dụng (Singleton)
     * @return mysqli
     */
    public static function getConnection() {
        if (self::$connection === null) {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            if ($conn->connect_error) {
                die("Kết nối CSDL thất bại: " . $conn->connect_error);
            }
            $conn->set_charset("utf8mb4");
            self::$connection = $conn;
        }
        return self::$connection;
    }
}

