<?php
// app/models/ReservationModel.php
require_once __DIR__ . '/../core/Database.php';

class ReservationModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Tạo đặt bàn mới (trạng thái mặc định PENDING). $customerId có thể null (khách vãng lai)
    public function create($customerId, $name, $phone, $email, $date, $time, $guestCount, $branch, $note)
    {
        $sql = "INSERT INTO reservation
                    (customerId, guestName, guestPhone, guestEmail, reservationDate, reservationTime, guestCount, branch, note)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssssiss", $customerId, $name, $phone, $email, $date, $time, $guestCount, $branch, $note);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    // Lịch sử đặt bàn của một khách hàng
    public function getByCustomer($customerId)
    {
        $sql = "SELECT * FROM reservation
                WHERE customerId = ? AND isDeleted = 0
                ORDER BY reservationDate DESC, reservationTime DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Khách tự hủy đặt bàn của mình (chỉ khi chưa bị hủy)
    public function cancel($reservationId, $customerId)
    {
        $sql = "UPDATE reservation SET status = 'CANCELLED'
                WHERE reservationId = ? AND customerId = ? AND status <> 'CANCELLED'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $reservationId, $customerId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // Lấy toàn bộ danh sách đặt bàn cho admin (kèm thông tin bàn nếu đã xếp)
    public function getAllAdmin(): array
    {
        $sql = "SELECT r.*, t.tableNumber, t.area 
                FROM reservation r 
                LEFT JOIN service_table t ON r.tableId = t.tableId AND t.isDeleted = 0
                WHERE r.isDeleted = 0 
                ORDER BY r.reservationDate DESC, r.reservationTime DESC, r.reservationId DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Admin xác nhận lịch hẹn đặt bàn
    public function confirmByAdmin(int $reservationId): ?array
    {
        $stmt = $this->db->prepare("SELECT tableId, status FROM reservation WHERE reservationId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if (!$res) {
            return null;
        }

        // Đã hủy thì không cho xác nhận
        if ($res['status'] === 'CANCELLED') {
            return null;
        }

        // Đã xác nhận trước đó thì trả về thành công (idempotent)
        if ($res['status'] === 'CONFIRMED') {
            return [
                'success' => true,
                'tableId' => !empty($res['tableId']) ? (int)$res['tableId'] : null,
                'tableStatus' => null
            ];
        }

        $update = $this->db->prepare("UPDATE reservation SET status = 'CONFIRMED' WHERE reservationId = ?");
        $update->bind_param("i", $reservationId);
        $ok = $update->execute();

        $tableUpdated = false;
        if ($ok && !empty($res['tableId'])) {
            $tableId = (int)$res['tableId'];
            $reserveTable = $this->db->prepare("UPDATE service_table SET status = 'RESERVED' WHERE tableId = ? AND status = 'AVAILABLE'");
            $reserveTable->bind_param("i", $tableId);
            $reserveTable->execute();
            if ($reserveTable->affected_rows > 0) {
                $tableUpdated = true;
            }
        }

        return [
            'success' => $ok,
            'tableId' => !empty($res['tableId']) ? (int)$res['tableId'] : null,
            'tableStatus' => $tableUpdated ? 'RESERVED' : null
        ];
    }

    // Admin hủy lịch hẹn đặt bàn
    public function cancelByAdmin(int $reservationId): ?array
    {
        $stmt = $this->db->prepare("SELECT tableId, status FROM reservation WHERE reservationId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $reservationId);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if (!$res) {
            return null;
        }

        // Đã hủy trước đó thì trả về thành công (idempotent)
        if ($res['status'] === 'CANCELLED') {
            return [
                'success' => true,
                'tableId' => !empty($res['tableId']) ? (int)$res['tableId'] : null,
                'tableStatus' => null
            ];
        }

        $update = $this->db->prepare("UPDATE reservation SET status = 'CANCELLED' WHERE reservationId = ?");
        $update->bind_param("i", $reservationId);
        $ok = $update->execute();

        $tableUpdated = false;
        if ($ok && !empty($res['tableId'])) {
            $tableId = (int)$res['tableId'];
            $freeTable = $this->db->prepare("UPDATE service_table SET status = 'AVAILABLE' WHERE tableId = ? AND status = 'RESERVED'");
            $freeTable->bind_param("i", $tableId);
            $freeTable->execute();
            if ($freeTable->affected_rows > 0) {
                $tableUpdated = true;
            }
        }

        return [
            'success' => $ok,
            'tableId' => !empty($res['tableId']) ? (int)$res['tableId'] : null,
            'tableStatus' => $tableUpdated ? 'AVAILABLE' : null
        ];
    }
}
