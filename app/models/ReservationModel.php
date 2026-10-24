<?php
// app/models/ReservationModel.php
require_once __DIR__ . '/../core/Database.php';

class ReservationModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Tạo đặt bàn mới (trạng thái mặc định PENDING). $customerId có thể null (khách vãng lai)
    public function create($customerId, $name, $phone, $email, $date, $time, $guestCount, $branch, $note) {
        $sql = "INSERT INTO reservation
                    (customerId, guestName, guestPhone, guestEmail, reservationDate, reservationTime, guestCount, branch, note)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isssssiss", $customerId, $name, $phone, $email, $date, $time, $guestCount, $branch, $note);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    // Lịch sử đặt bàn của một khách hàng
    public function getByCustomer($customerId) {
        $sql = "SELECT * FROM reservation
                WHERE customerId = ? AND isDeleted = 0
                ORDER BY reservationDate DESC, reservationTime DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Khách tự hủy đặt bàn của mình (chỉ khi chưa bị hủy)
    public function cancel($reservationId, $customerId) {
        $sql = "UPDATE reservation SET status = 'CANCELLED'
                WHERE reservationId = ? AND customerId = ? AND status <> 'CANCELLED'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $reservationId, $customerId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
}

