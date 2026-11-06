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
        $sql = "SELECT * FROM category WHERE isDeleted = 0 ORDER BY categoryId ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Lấy chi tiết 1 danh mục theo ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM category WHERE categoryId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tạo danh mục mới
    public function create($name, $description = '') {
        $stmt = $this->db->prepare("INSERT INTO category (categoryName, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $description);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    // Cập nhật danh mục
    public function update($id, $name, $description = '') {
        $stmt = $this->db->prepare("UPDATE category SET categoryName = ?, description = ? WHERE categoryId = ?");
        $stmt->bind_param("ssi", $name, $description, $id);
        return $stmt->execute();
    }

    // Xóa mềm danh mục
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE category SET isDeleted = 1 WHERE categoryId = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
