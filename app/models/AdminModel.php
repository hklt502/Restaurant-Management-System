<?php
require_once __DIR__ . '/../core/Database.php';

class AdminModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Tìm tài khoản admin
    public function findByUsername($username) {
        $stmt = $this->db->prepare(
            "SELECT * FROM admin WHERE username = ? AND isDeleted = 0"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // Kiểm tra đăng nhập
    public function login($username, $password) {
        $admin = $this->findByUsername($username);

        if (!$admin) {
            return null;
        }

        $hash = $admin['passwordHash'];

        if (password_verify($password, $hash) || $hash === md5($password)) {
            return $admin;
        }

        return null;
    }
// Lấy danh sách Admin
    public function getAll() {
        $sql = "SELECT * FROM admin WHERE isDeleted = 0";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    }
    
