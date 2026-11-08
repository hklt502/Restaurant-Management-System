<?php
// app/models/TableModel.php
require_once __DIR__ . '/../core/Database.php';

class TableModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(): array
    {
        $sql = "SELECT tableId, tableNumber, area, capacity, status 
                FROM service_table 
                WHERE isDeleted = 0 
                ORDER BY tableId ASC";
        $result = $this->db->query($sql);

        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM service_table WHERE tableId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE service_table SET status = ? WHERE tableId = ? AND isDeleted = 0");
        $stmt->bind_param("si", $status, $id);
        if (!$stmt->execute()) {
            return false;
        }

        if ($stmt->affected_rows === 0) {
            return $this->getById($id) !== null;
        }

        return true;
    }

    public function saveTable(int $id, string $number, string $area, int $capacity, string $status): bool
    {
        if ($id > 0) {
            $stmt = $this->db->prepare("UPDATE service_table SET tableNumber = ?, area = ?, capacity = ?, status = ? WHERE tableId = ? AND isDeleted = 0");
            $stmt->bind_param("ssisi", $number, $area, $capacity, $status, $id);
        } else {
            $stmt = $this->db->prepare("INSERT INTO service_table (tableNumber, area, capacity, status, isDeleted) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("ssis", $number, $area, $capacity, $status);
        }

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE service_table SET isDeleted = 1 WHERE tableId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}
