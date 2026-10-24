<?php
// app/models/CustomerModel.php
require_once __DIR__ . '/../core/Database.php';

class CustomerModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customerId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findByPhone($phone) {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customerPhone = ? AND isDeleted = 0");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT 1 FROM customer WHERE customerEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Đăng ký tài khoản mới. Trả về customerId hoặc false
    public function create($name, $phone, $email, $password, $birthDate, $gender) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare(
            "INSERT INTO customer (customerName, customerPhone, customerEmail, passwordHash, birthDate, gender)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $name, $phone, $email, $hash, $birthDate, $gender);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    // Xác thực đăng nhập bằng SĐT + mật khẩu. Trả về customer hoặc null
    public function login($phone, $password) {
        $customer = $this->findByPhone($phone);
        if (!$customer) return null;

        $hash = $customer['passwordHash'];
        // Hỗ trợ cả hash mới (password_hash) và MD5 của dữ liệu mẫu
        if (password_verify($password, $hash) || $hash === md5($password)) {
            return $customer;
        }
        return null;
    }

    public function updateProfile($id, $name, $email, $birthDate, $gender, $address) {
        $stmt = $this->db->prepare(
            "UPDATE customer SET customerName = ?, customerEmail = ?, birthDate = ?, gender = ?, address = ?
             WHERE customerId = ?"
        );
        $stmt->bind_param("sssssi", $name, $email, $birthDate, $gender, $address, $id);
        return $stmt->execute();
    }

    // Đổi mật khẩu: kiểm tra mật khẩu cũ rồi cập nhật
    public function changePassword($id, $oldPassword, $newPassword) {
        $customer = $this->getById($id);
        if (!$customer) return false;

        $hash = $customer['passwordHash'];
        if (!password_verify($oldPassword, $hash) && $hash !== md5($oldPassword)) {
            return false;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE customer SET passwordHash = ? WHERE customerId = ?");
        $stmt->bind_param("si", $newHash, $id);
        return $stmt->execute();
    }
}

