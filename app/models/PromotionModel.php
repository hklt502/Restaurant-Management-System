<?php
// app/models/PromotionModel.php
require_once __DIR__ . '/../core/Database.php';

class PromotionModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllActive() {
        $sql = "SELECT * FROM promotion WHERE isActive = 1 ORDER BY promotionId DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}

