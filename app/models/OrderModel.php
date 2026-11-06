<?php
// app/models/OrderModel.php
require_once __DIR__ . '/../core/Database.php';

class OrderModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Tự động nạp bổ sung các đơn hàng mẫu từ restaurant_insert.sql nếu bảng orders mới chỉ có <= 7 bản ghi
     */
    private function syncInsertSqlIfNeeded() {
        $res = $this->db->query("SELECT COUNT(*) AS cnt FROM orders");
        if (!$res) return;
        $row = $res->fetch_assoc();
        if ((int)($row['cnt'] ?? 0) > 7) return;

        $this->db->query("UPDATE `orders` SET `status` = 'PENDING_PAY' WHERE `orderId` = 7 AND `status` = 'SERVING'");
        $this->db->query("INSERT IGNORE INTO `orders` (`orderId`, `customerId`, `adminId`, `tableId`, `reservationId`, `voucherId`, `guestCount`, `subtotal`, `discountAmount`, `totalAmount`, `paymentMethod`, `status`, `orderDate`, `paidAt`) VALUES
            (8,  1, 1, 6,    NULL, NULL, 2,  350000,      0,  350000, 'CASH',     'CANCELLED', '2026-10-01 09:30:00', NULL),
            (9,  3, 1, 10,   NULL, 1,    8, 2680000, 268000, 2412000, 'CARD',     'PAID',      '2026-09-30 19:20:00', '2026-09-30 21:00:00'),
            (10, 4, 2, NULL, NULL, NULL, 1,  335000,      0,  335000, 'CASH',     'PAID',      '2026-09-30 18:10:00', '2026-09-30 18:40:00'),
            (11, 5, 3, 1,    NULL, NULL, 4,  800000,      0,  800000, 'TRANSFER', 'PAID',      '2026-09-30 17:30:00', '2026-09-30 19:00:00'),
            (12, 1, 1, NULL, NULL, NULL, 1,  350000,      0,  350000, 'TRANSFER', 'PAID',      '2026-09-30 14:15:00', '2026-09-30 14:50:00'),
            (13, 2, 2, 6,    NULL, NULL, 2,  180000,      0,  180000, 'CASH',     'CANCELLED', '2026-09-30 12:30:00', NULL),
            (14, 3, 1, 9,    NULL, 3,    6, 2500000, 500000, 2000000, 'CARD',     'PAID',      '2026-09-29 20:00:00', '2026-09-29 21:45:00'),
            (15, 5, 3, 2,    NULL, NULL, 4,  790000,      0,  790000, 'TRANSFER', 'PAID',      '2026-09-29 19:10:00', '2026-09-29 20:30:00'),
            (16, 1, 2, NULL, NULL, NULL, 1,  360000,      0,  360000, 'CASH',     'PAID',      '2026-09-29 12:15:00', '2026-09-29 12:45:00'),
            (17, 4, 1, NULL, NULL, 2,    2,  460000,  50000,  410000, 'TRANSFER', 'PAID',      '2026-09-28 18:40:00', '2026-09-28 19:20:00'),
            (18, 2, 4, 7,    NULL, NULL, 4, 1020000,      0, 1020000, 'CARD',     'PAID',      '2026-09-28 19:00:00', '2026-09-28 20:50:00'),
            (19, 3, 1, 9,    NULL, 3,   10, 5400000,1080000, 4320000, 'CARD',     'PAID',      '2026-09-22 18:30:00', '2026-09-22 21:15:00'),
            (20, 1, 3, 3,    NULL, NULL, 4,  730000,      0,  730000, 'TRANSFER', 'PAID',      '2026-09-18 19:45:00', '2026-09-18 21:00:00'),
            (21, 5, 2, NULL, NULL, NULL, 1,  480000,      0,  480000, 'CASH',     'PAID',      '2026-09-15 11:50:00', '2026-09-15 12:20:00'),
            (22, 2, 1, NULL, NULL, NULL, 2,  405000,      0,  405000, 'TRANSFER', 'PAID',      '2026-09-12 13:10:00', '2026-09-12 13:45:00'),
            (23, 4, 3, 5,    NULL, NULL, 3,  640000,      0,  640000, 'CASH',     'PAID',      '2026-09-08 20:15:00', '2026-09-08 21:30:00'),
            (24, 1, 2, 2,    NULL, NULL, 4,  320000,      0,  320000, 'CASH',     'CANCELLED', '2026-09-05 18:00:00', NULL)");

        $this->db->query("INSERT IGNORE INTO `order_items` (`orderItemId`, `orderId`, `foodId`, `quantity`, `unitPrice`, `lineTotal`, `itemStatus`, `note`) VALUES
            (21, 7, 1, 3, 350000,1050000, 'SERVED', NULL),
            (22, 7, 6, 2, 150000, 300000, 'SERVED', NULL),
            (23, 8, 1, 1, 350000, 350000, 'ORDER',  'Khách đổi ý hủy đơn'),
            (24, 9, 1, 4, 350000,1400000, 'SERVED', NULL),
            (25, 9, 7, 4, 250000,1000000, 'SERVED', NULL),
            (26, 9,15, 8,  35000, 280000, 'SERVED', NULL),
            (27,10, 3, 2, 120000, 240000, 'SERVED', 'Mang về'),
            (28,10, 8, 1,  70000,  70000, 'SERVED', NULL),
            (29,11, 2, 2, 280000, 560000, 'SERVED', NULL),
            (30,11, 3, 2, 120000, 240000, 'SERVED', NULL),
            (31,12, 5, 1, 180000, 180000, 'SERVED', 'Giao hàng'),
            (32,12, 6, 1, 150000, 150000, 'SERVED', NULL),
            (33,13, 6, 1, 150000, 150000, 'ORDER',  'Đã hủy'),
            (34,14, 1, 5, 350000,1750000, 'SERVED', NULL),
            (35,14, 7, 3, 250000, 750000, 'SERVED', NULL),
            (36,15, 2, 2, 280000, 560000, 'SERVED', NULL),
            (37,15, 9, 3,  75000, 225000, 'SERVED', NULL),
            (38,16, 3, 3, 120000, 360000, 'SERVED', NULL),
            (39,17, 5, 2, 180000, 360000, 'SERVED', NULL),
            (40,18, 1, 2, 350000, 700000, 'SERVED', NULL),
            (41,19, 1, 8, 350000,2800000, 'SERVED', 'Tiệc công ty'),
            (42,19, 7, 8, 250000,2000000, 'SERVED', NULL),
            (43,20, 2, 2, 280000, 560000, 'SERVED', NULL),
            (44,21, 6, 3, 150000, 450000, 'SERVED', NULL),
            (45,22, 5, 2, 180000, 360000, 'SERVED', NULL),
            (46,23, 1, 1, 350000, 350000, 'SERVED', NULL),
            (47,24, 2, 1, 280000, 280000, 'ORDER',  'Khách hủy bàn')");
    }

    /**
     * Lấy danh sách toàn bộ đơn hàng trong Database kèm thông tin Khách, Bàn, Voucher, Món ăn
     */
    public function getAllOrders($limit = 100) {
        $this->syncInsertSqlIfNeeded();

        $sql = "SELECT o.*, 
                       c.customerName, c.customerPhone,
                       t.tableNumber, t.area,
                       v.voucherCode,
                       a.adminName
                FROM orders o
                LEFT JOIN customer c ON o.customerId = c.customerId
                LEFT JOIN service_table t ON o.tableId = t.tableId
                LEFT JOIN voucher v ON o.voucherId = v.voucherId
                LEFT JOIN `admin` a ON o.adminId = a.adminId
                ORDER BY o.orderDate DESC, o.orderId DESC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        if (empty($orders)) {
            return [];
        }

        // Lấy danh sách orderId để truy vấn các món ăn trong 1 query
        $orderIds = array_column($orders, 'orderId');
        $inClause = implode(',', array_map('intval', $orderIds));

        $itemSql = "SELECT oi.*, f.foodName
                    FROM order_items oi
                    JOIN food f ON oi.foodId = f.foodId
                    WHERE oi.orderId IN ($inClause)
                    ORDER BY oi.orderItemId ASC";
        $itemRes = $this->db->query($itemSql);
        $itemsByOrder = [];
        if ($itemRes) {
            while ($item = $itemRes->fetch_assoc()) {
                $itemsByOrder[$item['orderId']][] = $item;
            }
        }

        // Chuẩn hóa dữ liệu sang cấu trúc giao diện (neo mốc ngày mới nhất trong DB làm today để luôn hiển thị ngay)
        $formattedOrders = [];
        $anchorDate   = !empty($orders[0]['orderDate']) ? new DateTime($orders[0]['orderDate']) : new DateTime();
        $todayStr     = $anchorDate->format('Y-m-d');
        $yesterdayStr = (clone $anchorDate)->modify('-1 day')->format('Y-m-d');
        $mondayStr    = (clone $anchorDate)->modify('monday this week')->format('Y-m-d');

        foreach ($orders as $row) {
            $orderDate = new DateTime($row['orderDate']);
            $dateOnly = $orderDate->format('Y-m-d');

            // Xác định kỳ: today / yesterday / week / month
            $period = 'month';
            if ($dateOnly === $todayStr) {
                $period = 'today';
            } elseif ($dateOnly === $yesterdayStr) {
                $period = 'yesterday';
            } elseif ($dateOnly >= $mondayStr) {
                $period = 'week';
            }

            // Map trạng thái DB (OPEN, SERVING, PAID, CANCELLED) sang giao diện
            $dbStatus = strtoupper(trim($row['status']));
            $status = 'serving';
            $statusName = 'Đang phục vụ';
            if ($dbStatus === 'OPEN' || $dbStatus === 'COOKING') {
                $status = 'cooking';
                $statusName = 'Đang chế biến';
            } elseif ($dbStatus === 'SERVING') {
                $status = 'serving';
                $statusName = 'Đang phục vụ';
            } elseif ($dbStatus === 'PENDING' || $dbStatus === 'PENDING_PAY') {
                $status = 'pending_pay';
                $statusName = 'Chờ thanh toán';
            } elseif ($dbStatus === 'PAID') {
                $status = 'paid';
                $statusName = 'Đã thanh toán';
            } elseif ($dbStatus === 'CANCELLED') {
                $status = 'cancelled';
                $statusName = 'Đã hủy';
            }

            // Map phương thức thanh toán
            $payMethod = strtoupper(trim($row['paymentMethod'] ?? 'CASH'));
            $payment = 'cash';
            $paymentName = 'Tiền mặt';
            if ($payMethod === 'TRANSFER' || $payMethod === 'QR') {
                $payment = 'transfer';
                $paymentName = 'Chuyển khoản QR';
            } elseif ($payMethod === 'CARD' || $payMethod === 'POS') {
                $payment = 'card';
                $paymentName = 'Thẻ ngân hàng';
            }

            // Bàn / Hình thức
            $tableShort = !empty($row['tableNumber']) ? 'Bàn ' . $row['tableNumber'] : 'Mang về';
            $tableFull  = !empty($row['tableNumber']) ? ('Bàn ' . $row['tableNumber'] . ' (' . ($row['area'] ?? 'Trong nhà') . ')') : 'Quầy Takeaway';
            $type = !empty($row['tableNumber']) ? 'dinein' : 'takeaway';
            $typeName = !empty($row['tableNumber']) ? 'Ăn tại bàn' : 'Mang về';

            // Danh sách món ăn
            $dishes = [];
            if (!empty($itemsByOrder[$row['orderId']])) {
                foreach ($itemsByOrder[$row['orderId']] as $it) {
                    $itemSt = strtoupper($it['itemStatus'] ?? 'SERVED');
                    $dishStatus = ($itemSt === 'COOKING' || $itemSt === 'ORDER') ? 'cooking' : ($status === 'cancelled' ? 'cancelled' : 'done');
                    $dishes[] = [
                        'name'   => $it['foodName'],
                        'qty'    => (int)$it['quantity'],
                        'price'  => (float)$it['unitPrice'],
                        'note'   => $it['note'] ?? '',
                        'status' => $dishStatus
                    ];
                }
            } else {
                $dishes[] = [
                    'name'   => 'Món phục vụ tại bàn',
                    'qty'    => 1,
                    'price'  => (float)$row['totalAmount'],
                    'note'   => '',
                    'status' => ($status === 'cancelled') ? 'cancelled' : 'done'
                ];
            }

            $subtotal = (float)$row['subtotal'];
            $discount = (float)$row['discountAmount'];
            $total    = (float)$row['totalAmount'];
            $vat      = max(0, round(($subtotal - $discount) * 0.08));

            $formattedOrders[] = [
                'id'          => 'DH-' . str_pad($row['orderId'], 4, '0', STR_PAD_LEFT),
                'dbId'        => (int)$row['orderId'],
                'period'      => $period,
                'type'        => $type,
                'typeName'    => $typeName,
                'table'       => $tableFull,
                'tableShort'  => $tableShort,
                'customer'    => $row['customerName'] ?: ($row['guestCount'] ? "Khách đi {$row['guestCount']} người" : 'Khách lẻ'),
                'phone'       => $row['customerPhone'] ?: '0901 234 567',
                'guests'      => (int)($row['guestCount'] ?: 2),
                'time'        => $orderDate->format('H:i d/m/Y'),
                'dishes'      => $dishes,
                'subtotal'    => $subtotal,
                'voucherCode' => $row['voucherCode'] ?: '',
                'discount'    => $discount,
                'vat'         => $vat,
                'total'       => $total,
                'payment'     => $payment,
                'paymentName' => $paymentName,
                'status'      => $status,
                'statusName'  => $statusName,
                'server'      => $row['adminName'] ?: 'Hồ Kim Lưu Thống'
            ];
        }

        return $formattedOrders;
    }

    /**
     * Cập nhật trạng thái đơn hàng (Đồng bộ với Database)
     */
    public function updateStatus($orderId, $status) {
        $statusMap = [
            'cooking'     => 'OPEN',
            'serving'     => 'SERVING',
            'pending_pay' => 'SERVING',
            'paid'        => 'PAID',
            'cancelled'   => 'CANCELLED'
        ];
        $dbStatus = $statusMap[$status] ?? 'SERVING';

        $paidAtSql = ($dbStatus === 'PAID') ? ", paidAt = NOW()" : "";
        $sql = "UPDATE orders SET status = ? $paidAtSql WHERE orderId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $dbStatus, $orderId);
        return $stmt->execute();
    }

    /**
     * Tạo đơn hàng mới vào Database
     */
    public function createOrder($data, $items = []) {
        $this->db->begin_transaction();
        try {
            $sql = "INSERT INTO orders (customerId, adminId, tableId, guestCount, subtotal, discountAmount, totalAmount, paymentMethod, status, orderDate)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            
            $custId   = !empty($data['customerId']) ? (int)$data['customerId'] : null;
            $adminId  = !empty($data['adminId']) ? (int)$data['adminId'] : 1;
            $tableId  = !empty($data['tableId']) ? (int)$data['tableId'] : null;
            $guests   = !empty($data['guestCount']) ? (int)$data['guestCount'] : 2;
            $subtotal = !empty($data['subtotal']) ? (float)$data['subtotal'] : 0;
            $discount = !empty($data['discount']) ? (float)$data['discount'] : 0;
            $total    = !empty($data['total']) ? (float)$data['total'] : 0;
            $payment  = strtoupper($data['paymentMethod'] ?? 'CASH');
            $status   = 'OPEN';

            $stmt->bind_param("iiiiddsss", $custId, $adminId, $tableId, $guests, $subtotal, $discount, $total, $payment, $status);
            $stmt->execute();
            $orderId = $this->db->insert_id;

            // Chèn danh sách món ăn
            if (!empty($items)) {
                $itemStmt = $this->db->prepare("INSERT INTO order_items (orderId, foodId, quantity, unitPrice, lineTotal, itemStatus, note)
                                               VALUES (?, ?, ?, ?, ?, 'ORDER', ?)");
                foreach ($items as $item) {
                    $foodId   = (int)$item['foodId'];
                    $qty      = (int)$item['quantity'];
                    $price    = (float)$item['price'];
                    $lineTot  = $qty * $price;
                    $note     = $item['note'] ?? '';
                    $itemStmt->bind_param("iiidds", $orderId, $foodId, $qty, $price, $lineTot, $note);
                    $itemStmt->execute();
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }
}

