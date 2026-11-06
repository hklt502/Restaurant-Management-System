<?php
// app/models/StatisticModel.php
require_once __DIR__ . '/../core/Database.php';

class StatisticModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Tổng hợp 4 chỉ số KPI trong khoảng thời gian [$from, $to)
    public function getSummary($from, $to) {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN status = 'PAID' THEN totalAmount ELSE 0 END), 0) AS revenue,
                    COUNT(*) AS totalOrders,
                    COALESCE(SUM(CASE WHEN status <> 'CANCELLED' THEN guestCount ELSE 0 END), 0) AS totalGuests,
                    COALESCE(SUM(CASE WHEN status = 'PAID' THEN 1 ELSE 0 END), 0) AS paidOrders
                FROM orders
                WHERE orderDate >= ? AND orderDate < ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        // Giá trị đơn trung bình = Doanh thu / Số đơn đã thanh toán
        $row['avgOrder'] = $row['paidOrders'] > 0 ? round($row['revenue'] / $row['paidOrders']) : 0;
        return $row;
    }

    // Doanh thu theo từng mốc thời gian: giờ / ngày / tuần / tháng
    // Trả về mảng dạng ['mốc' => doanh thu]
    public function getRevenueByPeriod($from, $to, $group) {
        // Biểu thức nhóm lấy từ danh sách cố định, không lấy trực tiếp từ người dùng -> an toàn
        $groupExpr = [
            'hour'  => "DATE_FORMAT(orderDate, '%H')",
            'day'   => "DATE_FORMAT(orderDate, '%Y-%m-%d')",
            'week'  => "DATE_FORMAT(DATE_SUB(DATE(orderDate), INTERVAL WEEKDAY(orderDate) DAY), '%Y-%m-%d')",
            'month' => "DATE_FORMAT(orderDate, '%Y-%m')",
        ];
        $expr = $groupExpr[$group] ?? $groupExpr['day'];

        $sql = "SELECT $expr AS period, SUM(totalAmount) AS revenue
                FROM orders
                WHERE status = 'PAID' AND orderDate >= ? AND orderDate < ?
                GROUP BY period
                ORDER BY period";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $result[$row['period']] = (float) $row['revenue'];
        }
        return $result;
    }

    // Đếm đơn theo 3 nhóm trạng thái: Hoàn thành / Đang xử lý / Đã hủy
    public function getOrderStatus($from, $to) {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN status = 'PAID' THEN 1 ELSE 0 END), 0) AS completed,
                    COALESCE(SUM(CASE WHEN status NOT IN ('PAID', 'CANCELLED') THEN 1 ELSE 0 END), 0) AS processing,
                    COALESCE(SUM(CASE WHEN status = 'CANCELLED' THEN 1 ELSE 0 END), 0) AS cancelled
                FROM orders
                WHERE orderDate >= ? AND orderDate < ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Top món bán chạy (không tính đơn đã hủy), kèm tên danh mục
    public function getTopFoods($from, $to, $limit = 4) {
        $sql = "SELECT f.foodId, f.foodName, c.categoryName,
                       SUM(oi.quantity) AS totalSold,
                       SUM(oi.lineTotal) AS revenue
                FROM order_items oi
                JOIN orders o ON oi.orderId = o.orderId
                JOIN food f ON oi.foodId = f.foodId
                JOIN category c ON f.categoryId = c.categoryId
                WHERE o.status <> 'CANCELLED' AND o.orderDate >= ? AND o.orderDate < ?
                GROUP BY f.foodId, f.foodName, c.categoryName
                ORDER BY totalSold DESC, revenue DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $from, $to, $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Nguồn khách: Đặt bàn trước / Khách thành viên / Khách vãng lai (không tính đơn đã hủy)
    public function getCustomerSource($from, $to) {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN reservationId IS NOT NULL THEN 1 ELSE 0 END), 0) AS reservation,
                    COALESCE(SUM(CASE WHEN reservationId IS NULL AND customerId IS NOT NULL THEN 1 ELSE 0 END), 0) AS member,
                    COALESCE(SUM(CASE WHEN reservationId IS NULL AND customerId IS NULL THEN 1 ELSE 0 END), 0) AS walkin
                FROM orders
                WHERE status <> 'CANCELLED' AND orderDate >= ? AND orderDate < ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

