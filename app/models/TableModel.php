<?php
require_once __DIR__ . '/../core/Database.php';

class TableModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $sql = "SELECT * FROM service_table WHERE isDeleted = 0";
        $result = $this->db->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
