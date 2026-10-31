<?php
// app/models/VoucherModel.php
require_once __DIR__ . '/../core/Database.php';

class VoucherModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lấy toàn bộ voucher trong hệ thống (kèm tên chương trình khuyến mãi nếu có)
     */
    public function getAll()
    {
        $sql = "SELECT v.*, p.title AS promotionTitle 
                FROM voucher v
                LEFT JOIN promotion p ON v.promotionId = p.promotionId
                WHERE v.isDeleted = 0
                ORDER BY v.voucherId DESC";
        $result = $this->db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lấy chi tiết 1 voucher theo ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM voucher WHERE voucherId = ? AND isDeleted = 0");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Tìm kiếm và kiểm tra mã voucher hợp lệ khi khách nhập mã
     */
    public function findByCode($code)
    {
        $sql = "SELECT * FROM voucher 
                WHERE voucherCode = ? 
                  AND isActive = 1 
                  AND isDeleted = 0 
                  AND (expiryDate IS NULL OR expiryDate >= CURDATE())
                  AND usedCount < usageLimit";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * LẤY CHI TIẾT VÍ VOUCHER CỦA TỪNG KHÁCH HÀNG
     * Trả về danh sách voucher khách đang sở hữu, trạng thái (UNUSED / USED)
     */
    public function getByCustomer($customerId)
    {
        $sql = "SELECT cv.id AS customerVoucherId, cv.status, cv.assignedAt, cv.usedAt,
                       v.voucherId, v.voucherCode, v.discountType, v.discountValue, v.minOrderValue, v.expiryDate,
                       p.title AS promotionTitle
                FROM customer_voucher cv
                JOIN voucher v ON cv.voucherId = v.voucherId
                LEFT JOIN promotion p ON v.promotionId = p.promotionId
                WHERE cv.customerId = ? AND v.isDeleted = 0
                ORDER BY (cv.status = 'UNUSED') DESC, cv.assignedAt DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Đếm số lượng voucher còn dùng được (UNUSED) của một khách hàng
     */
    public function countUnusedByCustomer($customerId)
    {
        $sql = "SELECT COUNT(*) AS total 
                FROM customer_voucher 
                WHERE customerId = ? AND status = 'UNUSED'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Lấy danh sách các voucher thuộc một chương trình khuyến mãi
     */
    public function getByPromotion($promotionId)
    {
        $sql = "SELECT * FROM voucher 
                WHERE promotionId = ? 
                  AND isDeleted = 0 
                  AND isActive = 1 
                  AND (expiryDate IS NULL OR expiryDate >= CURDATE())
                ORDER BY voucherId ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $promotionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Kiểm tra xem khách hàng đã nhận mã voucher này trong ví chưa (và chưa dùng)
     */
    public function hasCustomerVoucher($customerId, $voucherId)
    {
        $sql = "SELECT 1 FROM customer_voucher 
                WHERE customerId = ? AND voucherId = ? AND status = 'UNUSED' 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $customerId, $voucherId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Cấp phát / tặng 1 voucher vào ví của khách hàng
     */
    public function assignToCustomer($customerId, $voucherId)
    {
        $stmt = $this->db->prepare(
            "INSERT INTO customer_voucher (customerId, voucherId, status) VALUES (?, ?, 'UNUSED')"
        );
        $stmt->bind_param("ii", $customerId, $voucherId);
        return $stmt->execute();
    }

    /**
     * Đánh dấu voucher trong ví khách hàng là ĐÃ SỬ DỤNG
     */
    public function useVoucher($customerId, $voucherId)
    {
        $sql = "UPDATE customer_voucher 
                SET status = 'USED', usedAt = NOW() 
                WHERE customerId = ? AND voucherId = ? AND status = 'UNUSED' 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $customerId, $voucherId);
        return $stmt->execute() && $stmt->affected_rows > 0;
    }

    /**
     * Thêm mới một voucher (dành cho Admin)
     */
    public function create($code, $discountType, $discountValue, $minOrderValue = 0, $expiryDate = null, $usageLimit = 100, $promotionId = null)
    {
        $sql = "INSERT INTO voucher (voucherCode, discountType, discountValue, minOrderValue, expiryDate, usageLimit, promotionId)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssddssi", $code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId);
        return $stmt->execute() ? $this->db->insert_id : false;
    }

    /**
     * Cập nhật thông tin voucher
     */
    public function update($id, $code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId = null)
    {
        $sql = "UPDATE voucher 
                SET voucherCode = ?, discountType = ?, discountValue = ?, minOrderValue = ?, expiryDate = ?, usageLimit = ?, promotionId = ?
                WHERE voucherId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssddssii", $code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId, $id);
        return $stmt->execute();
    }

    /**
     * Xóa mềm voucher (isDeleted = 1)
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("UPDATE voucher SET isDeleted = 1 WHERE voucherId = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
