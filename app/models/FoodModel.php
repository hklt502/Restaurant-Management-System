<?php
// app/models/FoodModel.php
require_once __DIR__ . '/../core/Database.php';

class FoodModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Lấy tất cả món ăn cho Admin (kể cả đang bán và ngưng bán, chưa bị xóa)
    public function getAllAdmin($categoryId = null, $keyword = null) {
        $sql = "SELECT f.*, c.categoryName
                FROM food f
                LEFT JOIN category c ON f.categoryId = c.categoryId
                WHERE f.isDeleted = 0";
        $params = [];
        $types = "";

        if (!empty($categoryId)) {
            $sql .= " AND f.categoryId = ?";
            $params[] = (int)$categoryId;
            $types .= "i";
        }

        if (!empty($keyword)) {
            $sql .= " AND (f.foodName LIKE ? OR f.foodId LIKE ?)";
            $like = "%" . $keyword . "%";
            $params[] = $like;
            $params[] = $like;
            $types .= "ss";
        }

        $sql .= " ORDER BY f.foodId ASC";

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }

        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Lấy tất cả món đang bán, kèm tên danh mục (cho client menu)
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

    // Lấy chi tiết 1 món theo ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT f.*, c.categoryName FROM food f LEFT JOIN category c ON f.categoryId = c.categoryId WHERE f.foodId = ? AND f.isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Thêm món ăn mới (CRUD Admin)
    public function create($categoryId, $name, $description, $price, $discount = 0, $status = 'AVAILABLE', $imageURL = null, $isFeatured = 0) {
        $sql = "INSERT INTO food (categoryId, foodName, foodDescription, foodPrice, foodDiscount, foodStatus, imageURL, isFeatured)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("issddssi", $categoryId, $name, $description, $price, $discount, $status, $imageURL, $isFeatured);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    // Cập nhật món ăn (CRUD Admin)
    public function update($foodId, $categoryId, $name, $description, $price, $discount = 0, $status = 'AVAILABLE', $imageURL = null, $isFeatured = 0) {
        if ($imageURL !== null && $imageURL !== '') {
            $sql = "UPDATE food 
                    SET categoryId = ?, foodName = ?, foodDescription = ?, foodPrice = ?, foodDiscount = ?, foodStatus = ?, imageURL = ?, isFeatured = ?
                    WHERE foodId = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("issddssii", $categoryId, $name, $description, $price, $discount, $status, $imageURL, $isFeatured, $foodId);
        } else {
            $sql = "UPDATE food 
                    SET categoryId = ?, foodName = ?, foodDescription = ?, foodPrice = ?, foodDiscount = ?, foodStatus = ?, isFeatured = ?
                    WHERE foodId = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("issddsii", $categoryId, $name, $description, $price, $discount, $status, $isFeatured, $foodId);
        }
        return $stmt->execute();
    }

    // Xóa mềm món ăn
    public function delete($foodId) {
        $stmt = $this->db->prepare("UPDATE food SET isDeleted = 1 WHERE foodId = ?");
        $stmt->bind_param("i", $foodId);
        return $stmt->execute();
    }

    // Bật/tắt trạng thái món ăn (AVAILABLE <-> UNAVAILABLE)
    public function toggleStatus($foodId) {
        $food = $this->getById($foodId);
        if (!$food) return false;
        $newStatus = ($food['foodStatus'] === 'AVAILABLE') ? 'UNAVAILABLE' : 'AVAILABLE';
        $stmt = $this->db->prepare("UPDATE food SET foodStatus = ? WHERE foodId = ?");
        $stmt->bind_param("si", $newStatus, $foodId);
        return $stmt->execute() ? $newStatus : false;
    }
}
