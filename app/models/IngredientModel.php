<?php
// app/models/IngredientModel.php
require_once __DIR__ . '/../core/Database.php';

class IngredientModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll()
    {
        $res = $this->db->query("SELECT * FROM ingredient WHERE isDeleted = 0 ORDER BY ingredientId ASC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM ingredient WHERE ingredientId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAlerts()
    {
        $sql = "SELECT * FROM ingredient
                WHERE isDeleted = 0 AND quantityInStock <= minQuantity
                ORDER BY quantityInStock ASC, ingredientId ASC";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function create($name, $unit, $quantity, $minQuantity, $adminId = 1)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO ingredient (ingredientName, unit, quantityInStock, minQuantity) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("ssdd", $name, $unit, $quantity, $minQuantity);
        if (!$stmt->execute()) {
            return false;
        }
        $id = $this->db->insert_id;
        if ($quantity > 0) {
            $this->logInventory($id, max(1, (int)$adminId), 'IMPORT', $quantity, 'Khởi tạo tồn kho ban đầu');
        }
        return $id;
    }

    public function update($id, $name, $unit, $quantity, $minQuantity)
    {
        $stmt = $this->db->prepare(
            "UPDATE ingredient SET ingredientName = ?, unit = ?, quantityInStock = ?, minQuantity = ?
             WHERE ingredientId = ? AND isDeleted = 0"
        );
        $stmt->bind_param("ssddi", $name, $unit, $quantity, $minQuantity, $id);
        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE ingredient SET isDeleted = 1 WHERE ingredientId = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function adjustStock($ingredientId, $logType, $quantity, $note = '', $adminId = 1)
    {
        $item = $this->getById($ingredientId);
        if (!$item || $quantity <= 0) {
            return false;
        }
        $logType = strtoupper($logType) === 'EXPORT' ? 'EXPORT' : 'IMPORT';
        $curQty  = (float)$item['quantityInStock'];
        if ($logType === 'EXPORT' && $quantity > $curQty) {
            return false;
        }
        $newQty = ($logType === 'EXPORT') ? ($curQty - $quantity) : ($curQty + $quantity);

        $this->db->begin_transaction();
        try {
            $stmt = $this->db->prepare("UPDATE ingredient SET quantityInStock = ? WHERE ingredientId = ?");
            $stmt->bind_param("di", $newQty, $ingredientId);
            $stmt->execute();

            $this->logInventory($ingredientId, max(1, (int)$adminId), $logType, $quantity, $note);
            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function logInventory($ingredientId, $adminId, $logType, $quantity, $note = '')
    {
        $stmt = $this->db->prepare(
            "INSERT INTO inventory_log (ingredientId, adminId, logType, quantity, note) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iisds", $ingredientId, $adminId, $logType, $quantity, $note);
        return $stmt->execute();
    }

    public function getLogs($limit = 20)
    {
        $sql = "SELECT l.*, i.ingredientName, i.unit, a.adminName
                FROM inventory_log l
                JOIN ingredient i ON l.ingredientId = i.ingredientId
                LEFT JOIN `admin` a ON l.adminId = a.adminId
                ORDER BY l.logId DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
