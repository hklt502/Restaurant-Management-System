<?php
// app/models/OrderModel.php
require_once __DIR__ . '/../core/Database.php';

class OrderModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Lấy danh sách toàn bộ đơn hàng trong Database kèm thông tin Khách, Bàn, Voucher, Món ăn
     */
    public function getAllOrders()
    {
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
                ORDER BY o.orderDate DESC, o.orderId DESC";

        $stmt = $this->db->prepare($sql);
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
                    $itemSt = strtoupper(trim($it['itemStatus'] ?? 'ORDERED'));
                    $dishStatus = in_array($itemSt, ['ORDERED', 'ORDER', 'COOKING'], true) ? 'cooking' : ($status === 'cancelled' ? 'cancelled' : 'done');
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

            $formattedOrders[] = [
                'id'          => 'DH-' . str_pad($row['orderId'], 4, '0', STR_PAD_LEFT),
                'dbId'        => (int)$row['orderId'],
                'period'      => $period,
                'type'        => $type,
                'typeName'    => $typeName,
                'table'       => $tableFull,
                'tableShort'  => $tableShort,
                'customer'    => $row['customerName'] ?: ($row['guestCount'] ? "Khách đi {$row['guestCount']} người" : 'Khách lẻ'),
                'phone'       => $row['customerPhone'] ?: '—',
                'guests'      => (int)($row['guestCount'] ?: 2),
                'time'        => $orderDate->format('H:i d/m/Y'),
                'dishes'      => $dishes,
                'subtotal'    => $subtotal,
                'voucherCode' => $row['voucherCode'] ?: '',
                'discount'    => $discount,
                'total'       => $total,
                'payment'     => $payment,
                'paymentName' => $paymentName,
                'status'      => $status,
                'statusName'  => $statusName,
                'server'      => $row['adminName'] ?: 'Quản trị viên'
            ];
        }

        return $formattedOrders;
    }

    /**
     * Cập nhật trạng thái đơn hàng (Đồng bộ với Database)
     */
    public function updateStatus($orderId, $status)
    {
        $statusMap = [
            'cooking'     => 'OPEN',
            'serving'     => 'SERVING',
            'pending_pay' => 'PENDING_PAY',
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
    public function createOrder($data, $items = [])
    {
        $this->db->begin_transaction();
        try {
            $sql = "INSERT INTO orders (customerId, adminId, tableId, voucherId, guestCount, subtotal, discountAmount, totalAmount, paymentMethod, status, orderDate)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);

            $custId    = !empty($data['customerId']) ? (int)$data['customerId'] : null;
            $adminId   = max(1, (int)($data['adminId'] ?? 1));
            $tableId   = !empty($data['tableId']) ? (int)$data['tableId'] : null;
            $voucherId = !empty($data['voucherId']) ? (int)$data['voucherId'] : null;
            $guests    = !empty($data['guestCount']) ? (int)$data['guestCount'] : 2;
            $subtotal  = !empty($data['subtotal']) ? (float)$data['subtotal'] : 0;
            $discount  = !empty($data['discount']) ? (float)$data['discount'] : 0;
            $total     = !empty($data['total']) ? (float)$data['total'] : 0;
            $payment   = strtoupper($data['paymentMethod'] ?? 'CASH');
            $status    = 'OPEN';

            $stmt->bind_param("iiiiiddsss", $custId, $adminId, $tableId, $voucherId, $guests, $subtotal, $discount, $total, $payment, $status);
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

    /**
     * Tạo đơn hàng mới từ POS (OrderController) và trả về dữ liệu chuẩn cho giao diện
     */
    public function createOrderFromPos(array $post)
    {
        $dishesRaw = json_decode($post['dishes'] ?? '[]', true);
        if (!is_array($dishesRaw) || empty($dishesRaw)) {
            throw new RuntimeException('Vui lòng chọn ít nhất 1 món ăn!');
        }

        $orderType  = $post['orderType'] ?? 'dinein';
        $rawTable   = trim($post['table'] ?? 'Bàn 01');
        $tableId    = null;
        $tableShort = 'Mang về';
        $tableFull  = 'Quầy Takeaway';
        $typeName   = 'Mang về';

        if ($orderType === 'dinein') {
            $typeName = 'Ăn tại bàn';
            $num = (int)preg_replace('/\D/', '', $rawTable);
            if (stripos($rawTable, 'VIP') !== false && $num === 1) {
                $num = 9;
            } elseif (stripos($rawTable, 'VIP') !== false && $num === 2) {
                $num = 10;
            }
            if ($num > 0) {
                $tCode = 'T' . str_pad((string)$num, 2, '0', STR_PAD_LEFT);
                $tStmt = $this->db->prepare("SELECT tableId, tableNumber, area FROM service_table WHERE (tableId = ? OR tableNumber = ?) AND isDeleted = 0 LIMIT 1");
                $tStmt->bind_param("is", $num, $tCode);
                $tStmt->execute();
                if ($tRow = $tStmt->get_result()->fetch_assoc()) {
                    $tableId    = (int)$tRow['tableId'];
                    $tableShort = 'Bàn ' . $tRow['tableNumber'];
                    $tableFull  = 'Bàn ' . $tRow['tableNumber'] . ' (' . ($tRow['area'] ?? 'Trong nhà') . ')';
                }
            }
            if (!$tableId) {
                $tableShort = $rawTable ?: 'Bàn 01';
                $tableFull  = $rawTable ?: 'Bàn 01';
            }
        } elseif ($orderType === 'delivery') {
            $tableShort = 'Giao hàng';
            $tableFull  = 'Đơn Online Delivery';
            $typeName   = 'Giao hàng';
        }

        $subtotal = 0;
        $items    = [];
        $dishes   = [];
        foreach ($dishesRaw as $d) {
            if (!is_array($d)) {
                continue;
            }
            $fId   = (int)($d['id'] ?? $d['foodId'] ?? 0);
            $qty   = max(1, (int)($d['qty'] ?? $d['quantity'] ?? 1));
            $price = max(0, (float)($d['price'] ?? 0));
            $note  = trim($d['note'] ?? '');
            if ($fId <= 0) {
                continue;
            }
            $subtotal += $price * $qty;
            $items[] = ['foodId' => $fId, 'quantity' => $qty, 'price' => $price, 'note' => $note];
            $dishes[] = [
                'name'   => $d['name'] ?? 'Món ăn',
                'qty'    => $qty,
                'price'  => $price,
                'note'   => $note,
                'status' => 'cooking'
            ];
        }

        if (empty($items)) {
            throw new RuntimeException('Danh sách món ăn không hợp lệ!');
        }

        $voucherVal  = (string)($post['voucherVal'] ?? '0');
        $discount    = 0;
        $voucherCode = '';
        $voucherId   = null;
        if ($voucherVal === '10') {
            $discount    = round($subtotal * 0.1);
            $voucherCode = 'GIAM10%';
            $voucherId   = 1;
        } elseif ($voucherVal === '20') {
            $discount    = round($subtotal * 0.2);
            $voucherCode = 'VIP20%';
            $voucherId   = 3;
        } elseif ($voucherVal === '50000') {
            $discount    = 50000;
            $voucherCode = 'TIENMAT50K';
            $voucherId   = 2;
        }
        if ($voucherId !== null) {
            $vRes = $this->db->query("SELECT voucherId, voucherCode FROM voucher WHERE voucherId = " . (int)$voucherId . " LIMIT 1");
            if ($vRes && ($vRow = $vRes->fetch_assoc())) {
                $voucherId   = (int)$vRow['voucherId'];
                $voucherCode = $vRow['voucherCode'] ?: $voucherCode;
            } else {
                $voucherId = null;
            }
        }

        $discount = min($subtotal, $discount);
        $total    = max(0, $subtotal - $discount);

        $payment = strtolower(trim($post['paymentMethod'] ?? 'cash'));
        $paymentName = 'Tiền mặt';
        if ($payment === 'transfer') $paymentName = 'Chuyển khoản QR';
        elseif ($payment === 'card') $paymentName = 'Thẻ ngân hàng';

        $guests    = max(1, (int)($post['guests'] ?? 2));
        $custInput = trim($post['customerName'] ?? 'Khách lẻ') ?: 'Khách lẻ';
        $custId    = null;
        $custName  = $custInput;
        $custPhone = trim($post['customerPhone'] ?? '');

        if (mb_strtolower($custInput, 'UTF-8') !== 'khách lẻ') {
            $cStmt = $this->db->prepare("SELECT customerId, customerName, customerPhone FROM customer WHERE (LOWER(customerName) = LOWER(?) OR customerPhone = ?) AND isDeleted = 0 LIMIT 1");
            $cStmt->bind_param("ss", $custInput, $custInput);
            $cStmt->execute();
            if ($cRow = $cStmt->get_result()->fetch_assoc()) {
                $custId    = (int)$cRow['customerId'];
                $custName  = $cRow['customerName'];
                $custPhone = $cRow['customerPhone'] ?: $custPhone;
            }
        }

        $orderId = $this->createOrder([
            'customerId'    => $custId,
            'adminId'       => max(1, (int)($_SESSION['admin']['adminId'] ?? 1)),
            'tableId'       => $tableId,
            'voucherId'     => $voucherId,
            'guestCount'    => $guests,
            'subtotal'      => $subtotal,
            'discount'      => $discount,
            'total'         => $total,
            'paymentMethod' => strtoupper($payment),
        ], $items);

        if (!$orderId) {
            throw new RuntimeException('Không thể lưu đơn hàng vào cơ sở dữ liệu!');
        }

        return [
            'id'          => 'DH-' . str_pad((string)$orderId, 4, '0', STR_PAD_LEFT),
            'dbId'        => (int)$orderId,
            'period'      => 'today',
            'type'        => $orderType,
            'typeName'    => $typeName,
            'table'       => $tableFull,
            'tableShort'  => $tableShort,
            'customer'    => $custName,
            'phone'       => $custPhone,
            'guests'      => $guests,
            'time'        => date('H:i d/m/Y'),
            'dishes'      => $dishes,
            'subtotal'    => $subtotal,
            'voucherCode' => $voucherCode,
            'discount'    => $discount,
            'total'       => $total,
            'payment'     => $payment,
            'paymentName' => $paymentName,
            'status'      => 'cooking',
            'statusName'  => 'Đang chế biến',
            'server'      => $_SESSION['admin']['adminName'] ?? 'Quản trị viên'
        ];
    }

    public function getOrders($begin, $end)
    {
        $baseSql = "SELECT o.*, c.customerName, c.customerPhone, t.tableNumber, t.area, v.voucherCode, a.adminName
                    FROM orders o
                    LEFT JOIN customer c ON o.customerId = c.customerId
                    LEFT JOIN service_table t ON o.tableId = t.tableId
                    LEFT JOIN voucher v ON o.voucherId = v.voucherId
                    LEFT JOIN `admin` a ON o.adminId = a.adminId";

        if (is_numeric($begin) && is_numeric($end)) {
            $offset = max(0, (int)$begin);
            $limit  = max(1, (int)$end);
            $stmt   = $this->db->prepare($baseSql . " ORDER BY o.orderId DESC LIMIT ?, ?");
            $stmt->bind_param("ii", $offset, $limit);
        } else {
            $stmt = $this->db->prepare($baseSql . " WHERE o.orderDate >= ? AND o.orderDate <= ? ORDER BY o.orderId DESC");
            $stmt->bind_param("ss", $begin, $end);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBill($orderId)
    {
        $sql = "SELECT o.*,
                       c.customerName, c.customerPhone, c.customerEmail,
                       t.tableNumber, t.area, t.capacity,
                       a.adminName, a.adminPhone,
                       v.voucherCode, v.discountType, v.discountValue
                FROM orders o
                LEFT JOIN customer c ON o.customerId = c.customerId
                LEFT JOIN service_table t ON o.tableId = t.tableId
                LEFT JOIN `admin` a ON o.adminId = a.adminId
                LEFT JOIN voucher v ON o.voucherId = v.voucherId
                WHERE o.orderId = ?";
        $stmt = $this->db->prepare($sql);
        $id   = (int)$orderId;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $bill = $stmt->get_result()->fetch_assoc();

        if (!$bill) {
            return null;
        }

        $itemSql = "SELECT oi.orderItemId, oi.foodId, f.foodName, f.imageURL,
                           oi.quantity, oi.unitPrice, oi.lineTotal, oi.itemStatus, oi.note
                    FROM order_items oi
                    JOIN food f ON oi.foodId = f.foodId
                    WHERE oi.orderId = ?
                    ORDER BY oi.orderItemId ASC";
        $itemStmt = $this->db->prepare($itemSql);
        $itemStmt->bind_param("i", $id);
        $itemStmt->execute();
        $bill['items'] = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        return $bill;
    }

    public function revenue()
    {
        $res = $this->db->query("SELECT COALESCE(SUM(totalAmount), 0) AS total FROM orders WHERE status = 'PAID' AND DATE(orderDate) = CURDATE()");
        return (float)($res ? ($res->fetch_assoc()['total'] ?? 0) : 0);
    }

    public function grossIncome()
    {
        return $this->revenue();
    }

    public function countOrder()
    {
        $res = $this->db->query("SELECT COUNT(*) AS total FROM orders WHERE DATE(orderDate) = CURDATE()");
        return (int)($res ? ($res->fetch_assoc()['total'] ?? 0) : 0);
    }
}
