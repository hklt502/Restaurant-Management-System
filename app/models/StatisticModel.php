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

    // Nguồn khách & hình thức dùng bữa từ bảng orders + service_table (không tính đơn đã hủy)
    public function getCustomerSource($from, $to) {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN o.tableId IS NOT NULL AND (t.area = 'Trong nhà' OR t.area IS NULL) THEN 1 ELSE 0 END), 0) AS indoor,
                    COALESCE(SUM(CASE WHEN o.tableId IS NOT NULL AND t.area = 'Ngoài trời' THEN 1 ELSE 0 END), 0) AS outdoor,
                    COALESCE(SUM(CASE WHEN o.tableId IS NOT NULL AND t.area = 'VIP' THEN 1 ELSE 0 END), 0) AS vip,
                    COALESCE(SUM(CASE WHEN o.tableId IS NULL THEN 1 ELSE 0 END), 0) AS takeaway,
                    COALESCE(SUM(CASE WHEN o.customerId IS NOT NULL THEN 1 ELSE 0 END), 0) AS member,
                    COALESCE(SUM(CASE WHEN o.reservationId IS NOT NULL THEN 1 ELSE 0 END), 0) AS reservation,
                    COALESCE(SUM(CASE WHEN o.reservationId IS NULL AND o.customerId IS NULL THEN 1 ELSE 0 END), 0) AS walkin
                FROM orders o
                LEFT JOIN service_table t ON o.tableId = t.tableId
                WHERE o.status <> 'CANCELLED' AND o.orderDate >= ? AND o.orderDate < ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $from, $to);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Lấy ngày đơn hàng mới nhất trong DB làm mốc tham chiếu (anchor date)
    public function getAnchorDate() {
        $res = $this->db->query("SELECT MAX(orderDate) AS maxDate FROM orders");
        if ($res && ($row = $res->fetch_assoc()) && !empty($row['maxDate'])) {
            return new DateTime(substr($row['maxDate'], 0, 10) . ' 00:00:00');
        }
        return new DateTime('today');
    }

    // Xây dựng gói dữ liệu thống kê đầy đủ cho 1 khoảng thời gian [$from, $to)
    public function buildPeriodDataset(DateTime $from, DateTime $to, $compareLabel, $subtitle, $defaultGroup = 'day', $prevDaysOverride = null) {
        $days     = max(1, (int) $from->diff($to)->days);
        $prevSpan = $prevDaysOverride ?? $days;
        $prevTo   = clone $from;
        $prevFrom = (clone $from)->modify("-{$prevSpan} days");

        $fromStr     = $from->format('Y-m-d H:i:s');
        $toStr       = $to->format('Y-m-d H:i:s');
        $prevFromStr = $prevFrom->format('Y-m-d H:i:s');
        $prevToStr   = $prevTo->format('Y-m-d H:i:s');

        $cur  = $this->getSummary($fromStr, $toStr);
        $prev = $this->getSummary($prevFromStr, $prevToStr);

        $calcChange = function ($c, $p) {
            $c = (float) $c;
            $p = (float) $p;
            if ($p > 0) {
                $pct = round((($c - $p) / $p) * 100, 1);
            } else {
                $pct = $c > 0 ? 100.0 : 0.0;
            }
            return [
                'change' => abs($pct),
                'isUp'   => $pct >= 0
            ];
        };

        $revCmp    = $calcChange($cur['revenue'], $prev['revenue']);
        $ordCmp    = $calcChange($cur['totalOrders'], $prev['totalOrders']);
        $guestCmp  = $calcChange($cur['totalGuests'], $prev['totalGuests']);
        $avgCmp    = $calcChange($cur['avgOrder'], $prev['avgOrder']);

        $fmtVnd = function ($val) {
            return number_format((float) $val, 0, ',', '.') . 'đ';
        };

        $kpi = [
            'revenue' => [
                'value'   => $fmtVnd($cur['revenue']),
                'change'  => $revCmp['change'],
                'isUp'    => $revCmp['isUp'],
                'compare' => "{$compareLabel}: " . $fmtVnd($prev['revenue'])
            ],
            'orders' => [
                'value'   => number_format((int) $cur['totalOrders']),
                'change'  => $ordCmp['change'],
                'isUp'    => $ordCmp['isUp'],
                'compare' => "{$compareLabel}: " . number_format((int) $prev['totalOrders']) . " đơn"
            ],
            'guests' => [
                'value'   => number_format((int) $cur['totalGuests']),
                'change'  => $guestCmp['change'],
                'isUp'    => $guestCmp['isUp'],
                'compare' => "{$compareLabel}: " . number_format((int) $prev['totalGuests']) . " khách"
            ],
            'avgOrder' => [
                'value'   => $fmtVnd($cur['avgOrder']),
                'change'  => $avgCmp['change'],
                'isUp'    => $avgCmp['isUp'],
                'compare' => "{$compareLabel}: " . $fmtVnd($prev['avgOrder'])
            ]
        ];

        // 1. Chuỗi biểu đồ theo ngày (hoặc theo giờ nếu chỉ xem 1 ngày)
        $dayLabels = [];
        $dayData   = [];
        if ($days === 1) {
            $hourRev = $this->getRevenueByPeriod($fromStr, $toStr, 'hour');
            $hours   = array_map('intval', array_keys($hourRev));
            $minH    = !empty($hours) ? min(min($hours), 9) : 9;
            $maxH    = !empty($hours) ? max(max($hours), 20) : 20;
            for ($h = $minH; $h <= $maxH; $h++) {
                $k = str_pad($h, 2, '0', STR_PAD_LEFT);
                $dayLabels[] = $k . ':00';
                $dayData[]   = round(($hourRev[$k] ?? 0) / 1000000, 2);
            }
        } else {
            $rawDayRev = $this->getRevenueByPeriod($fromStr, $toStr, 'day');
            if ($days <= 14) {
                $cursor = clone $from;
                while ($cursor < $to) {
                    $k = $cursor->format('Y-m-d');
                    $dayLabels[] = $cursor->format('d/m');
                    $dayData[]   = round(($rawDayRev[$k] ?? 0) / 1000000, 2);
                    $cursor->modify('+1 day');
                }
            } else {
                // Nếu khoảng dài (như 30 ngày), hiển thị các ngày có doanh thu hoặc chia mốc đều
                if (!empty($rawDayRev)) {
                    foreach ($rawDayRev as $dateKey => $revVal) {
                        $dt = new DateTime($dateKey);
                        $dayLabels[] = $dt->format('d/m');
                        $dayData[]   = round($revVal / 1000000, 2);
                    }
                } else {
                    $dayLabels = [$from->format('d/m'), (clone $to)->modify('-1 day')->format('d/m')];
                    $dayData   = [0, 0];
                }
            }
        }

        // 2. Chuỗi biểu đồ theo tuần (4 tuần gần nhất tính tới mốc kết thúc)
        $weekLabels = [];
        $weekData   = [];
        $wEnd       = clone $to;
        $wStart     = (clone $to)->modify('-28 days');
        $cursorW    = clone $wStart;
        $weekIdx    = 1;
        while ($cursorW < $wEnd) {
            $nextW = (clone $cursorW)->modify('+7 days');
            if ($nextW > $wEnd) $nextW = clone $wEnd;
            $wSum  = $this->getSummary($cursorW->format('Y-m-d H:i:s'), $nextW->format('Y-m-d H:i:s'));
            $weekLabels[] = 'Tuần ' . $weekIdx . ' (' . $cursorW->format('d/m') . '-' . (clone $nextW)->modify('-1 day')->format('d/m') . ')';
            $weekData[]   = round(($wSum['revenue'] ?? 0) / 1000000, 2);
            $cursorW      = $nextW;
            $weekIdx++;
        }

        // 3. Chuỗi biểu đồ theo tháng (trong năm của mốc đang xem)
        $monthLabels = [];
        $monthData   = [];
        $yearStart   = new DateTime($to->format('Y') . '-01-01 00:00:00');
        $yearEnd     = new DateTime(((int)$to->format('Y') + 1) . '-01-01 00:00:00');
        $rawMonthRev = $this->getRevenueByPeriod($yearStart->format('Y-m-d H:i:s'), $yearEnd->format('Y-m-d H:i:s'), 'month');
        foreach ($rawMonthRev as $ym => $revVal) {
            $parts = explode('-', $ym);
            $monthLabels[] = 'Tháng ' . ($parts[1] ?? $ym) . '/' . ($parts[0] ?? '');
            $monthData[]   = round($revVal / 1000000, 2);
        }
        if (empty($monthLabels)) {
            $monthLabels = ['Tháng ' . $from->format('m/Y')];
            $monthData   = [0];
        }

        // 4. Trạng thái đơn hàng (%)
        $st = $this->getOrderStatus($fromStr, $toStr);
        $cComp = (int) ($st['completed'] ?? 0);
        $cProc = (int) ($st['processing'] ?? 0);
        $cCanc = (int) ($st['cancelled'] ?? 0);
        $cTot  = $cComp + $cProc + $cCanc;
        if ($cTot > 0) {
            $pComp = (int) round(($cComp / $cTot) * 100);
            $pProc = (int) round(($cProc / $cTot) * 100);
            $pCanc = max(0, 100 - $pComp - $pProc);
        } else {
            $pComp = 0;
            $pProc = 0;
            $pCanc = 0;
        }

        // 5. Top món bán chạy
        $rawTop = $this->getTopFoods($fromStr, $toStr, 4);
        $topProducts = [];
        foreach ($rawTop as $idx => $f) {
            $catName = $f['categoryName'] ?? 'Thực đơn';
            $icon = 'bx-dish';
            if (stripos($catName, 'uống') !== false || stripos($catName, 'bia') !== false || stripos($catName, 'nước') !== false) {
                $icon = 'bx-drink';
            } elseif (stripos($catName, 'bò') !== false || stripos($catName, 'thịt') !== false || stripos($catName, 'lẩu') !== false) {
                $icon = 'bx-bowl-hot';
            }
            $rev = (float) ($f['revenue'] ?? 0);
            $revFormatted = $rev >= 1000000
                ? round($rev / 1000000, 2) . 'M'
                : number_format($rev / 1000, 0, ',', '.') . 'K';

            $topProducts[] = [
                'rank'     => $idx + 1,
                'icon'     => $icon,
                'name'     => $f['foodName'],
                'category' => $catName,
                'sold'     => ((int) ($f['totalSold'] ?? 0)) . ' phần',
                'revenue'  => $revFormatted
            ];
        }

        // 6. Nguồn khách hàng
        $src = $this->getCustomerSource($fromStr, $toStr);
        $customerSource = [
            (int) ($src['indoor'] ?? 0),
            (int) ($src['outdoor'] ?? 0),
            (int) ($src['vip'] ?? 0),
            (int) ($src['takeaway'] ?? 0),
            (int) ($src['member'] ?? 0)
        ];

        return [
            'subtitle'          => $subtitle,
            'defaultChartGroup' => $defaultGroup,
            'fromDate'          => $from->format('Y-m-d'),
            'toDate'            => (clone $to)->modify('-1 day')->format('Y-m-d'),
            'kpi'               => $kpi,
            'revenueSeries'     => [
                'day'   => ['labels' => $dayLabels,   'data' => $dayData],
                'week'  => ['labels' => $weekLabels,  'data' => $weekData],
                'month' => ['labels' => $monthLabels, 'data' => $monthData],
            ],
            'orderStatus'       => [$pComp, $pProc, $pCanc],
            'orderStatusCounts' => [$cComp, $cProc, $cCanc],
            'customerSource'    => $customerSource,
            'topProducts'       => $topProducts
        ];
    }

    // Lấy toàn bộ dữ liệu thống kê cho 3 kỳ (today, week, month) từ Database
    public function getAllPeriodsData() {
        $anchor = $this->getAnchorDate();
        $tomorrowAnchor = (clone $anchor)->modify('+1 day');

        // 1. Hôm nay (ngày mới nhất có đơn trong DB)
        $todayData = $this->buildPeriodDataset(
            $anchor,
            $tomorrowAnchor,
            'So với hôm qua',
            'Số liệu thống kê chi tiết ngày ' . $anchor->format('d/m/Y') . ' (từ Cơ sở dữ liệu).',
            'day'
        );

        // 2. Tuần này (từ Thứ 2 của tuần chứa ngày anchor đến ngày anchor)
        $weekStart = (clone $anchor)->modify('monday this week');
        $weekData  = $this->buildPeriodDataset(
            $weekStart,
            $tomorrowAnchor,
            'So với tuần trước',
            'Số liệu thống kê tuần (' . $weekStart->format('d/m/Y') . ' - ' . $anchor->format('d/m/Y') . ').',
            'day',
            7
        );

        // 3. Tháng này (30 ngày gần nhất tính tới ngày anchor để bao phủ trọn bộ dữ liệu đơn hàng trong tháng)
        $monthStart = (clone $anchor)->modify('-29 days');
        $monthData  = $this->buildPeriodDataset(
            $monthStart,
            $tomorrowAnchor,
            'So với tháng trước',
            'Phân tích chi tiết doanh thu & đơn hàng (' . $monthStart->format('d/m/Y') . ' - ' . $anchor->format('d/m/Y') . ').',
            'day'
        );

        return [
            'anchorDate' => $anchor->format('Y-m-d'),
            'today'      => $todayData,
            'week'       => $weekData,
            'month'      => $monthData
        ];
    }

    // Lọc thống kê theo khoảng ngày tùy chỉnh từ giao diện (startDate -> endDate)
    public function buildCustomRangeData($fromInput, $toInput) {
        $from = DateTime::createFromFormat('!Y-m-d', $fromInput);
        $to   = DateTime::createFromFormat('!Y-m-d', $toInput);
        if (!$from || !$to) {
            $anchor = $this->getAnchorDate();
            $from   = (clone $anchor)->modify('-29 days');
            $to     = clone $anchor;
        }
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }
        $toExclusive = (clone $to)->modify('+1 day');
        $diffDays    = max(1, (int) $from->diff($toExclusive)->days);

        return $this->buildPeriodDataset(
            $from,
            $toExclusive,
            "Kỳ trước ({$diffDays} ngày)",
            'Thống kê từ ngày ' . $from->format('d/m/Y') . ' đến ngày ' . $to->format('d/m/Y') . " ({$diffDays} ngày).",
            'day'
        );
    }
}

