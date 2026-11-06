<?php
// app/models/OrderModel.php
require_once __DIR__ . '/../core/Database.php';

class OrderModel {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Lấy danh sách toàn bộ đơn hàng trong Database kèm thông tin Khách, Bàn, Voucher, Món ăn
     */
    public function getAllOrders($limit = 100) {
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
                ORDER BY o.orderId DESC
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

        // Chuẩn hóa dữ liệu sang cấu trúc giao diện
        $formattedOrders = [];
        $now = new DateTime();
        $todayStr = $now->format('Y-m-d');
        $yesterdayStr = (clone $now)->modify('-1 day')->format('Y-m-d');
        $mondayStr = (clone $now)->modify('monday this week')->format('Y-m-d');

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

