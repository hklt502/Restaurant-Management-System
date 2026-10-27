<?php
// app/models/FoodModel.php
require_once __DIR__ . '/../core/Database.php';

class FoodModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Lấy tất cả món đang bán, kèm tên danh mục
    public function getAvailable() {
        $sql = "SELECT f.*, c.categoryName
                FROM food f
                JOIN category c ON f.categoryId = c.categoryId
                WHERE f.isDeleted = 0 AND f.foodStatus = 'AVAILABLE'
                ORDER BY f.categoryId, f.foodId";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy món đang bán theo danh mục
    public function getByCategory($categoryId) {
        $sql = "SELECT f.*, c.categoryName
                FROM food f
                JOIN category c ON f.categoryId = c.categoryId
                WHERE f.isDeleted = 0 AND f.foodStatus = 'AVAILABLE' AND f.categoryId = ?
                ORDER BY f.foodId";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy món nổi bật cho trang chủ
    public function getFeatured($limit = 3) {
        $sql = "SELECT f.*, c.categoryName
                FROM food f
                JOIN category c ON f.categoryId = c.categoryId
                WHERE f.isDeleted = 0 AND f.foodStatus = 'AVAILABLE' AND f.isFeatured = 1
                ORDER BY f.foodId
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM food WHERE foodId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

