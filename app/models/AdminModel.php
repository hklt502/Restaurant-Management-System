<?php
require_once __DIR__ . '/../core/Database.php';

class AdminModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Tìm tài khoản Admin trong database
    public function findByUsername($username)
    {
        $sql = "SELECT * FROM admin
                WHERE username = ? AND isDeleted = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    // Thêm tài khoản Admin mới (output bool)
    public function create($username, $pass, $name, $phone): bool
    {
        if ($this->findByUsername($username)) {
            return false;
        }

        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO admin (username, passwordHash, adminName, adminPhone, isDeleted) VALUES (?, ?, ?, ?, 0)"
        );

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ssss", $username, $hash, $name, $phone);
        return (bool)$stmt->execute();
    }

    // Lấy thông tin admin theo ID
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE adminId = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Cập nhật thông tin admin
    public function update($id, $username, $name, $phone): bool
    {
        $stmt = $this->db->prepare("UPDATE admin SET username = ?, adminName = ?, adminPhone = ? WHERE adminId = ?");
        if (!$stmt) return false;
        $stmt->bind_param("sssi", $username, $name, $phone, $id);
        return (bool)$stmt->execute();
    }

    // Đổi trạng thái khóa / kích hoạt
    public function toggleStatus($id): bool
    {
        $stmt = $this->db->prepare("UPDATE admin SET isDeleted = 1 - isDeleted WHERE adminId = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return (bool)$stmt->execute();
    }

    // Xóa mềm admin
    public function delete($id): bool
    {
        $stmt = $this->db->prepare("UPDATE admin SET isDeleted = 1 WHERE adminId = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return (bool)$stmt->execute();
    }

    // Kiểm tra đăng nhập
    public function login($username, $password)
    {
        $admin = $this->findByUsername($username);

        if (!$admin) {
            return null;
        }

        $hash = $admin['passwordHash'];

        // Kiểm tra mật khẩu đã mã hóa
        if (password_verify($password, $hash)) {
            return $admin;
        }

        // Hỗ trợ tài khoản cũ sử dụng MD5
        if ($hash === md5($password)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare(
                "UPDATE admin SET passwordHash = ? WHERE adminId = ?"
            );

            $stmt->bind_param("si", $newHash, $admin['adminId']);
            $stmt->execute();

            $admin['passwordHash'] = $newHash;
            return $admin;
        }

        return null;
    }

    // Lấy danh sách Admin (hỗ trợ lấy tất cả bao gồm tài khoản bị khóa)
    public function getAll($onlyActive = false)
    {
        $sql = "SELECT * FROM admin" . ($onlyActive ? " WHERE isDeleted = 0" : "") . " ORDER BY adminId DESC";
        $result = $this->db->query($sql);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
