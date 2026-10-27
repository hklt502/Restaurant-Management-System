<?php
// app/models/CategoryModel.php
require_once __DIR__ . '/../core/Database.php';

class CategoryModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Lấy tất cả danh mục chưa bị xóa
    public function getAll() {
        $sql = "SELECT * FROM category WHERE isDeleted = 0 ORDER BY categoryId";
        return $this->db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM category WHERE categoryId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

