<?php
// app/controllers/admin/StatisticController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/StatisticModel.php';

class StatisticController extends BaseController {
    private $statistics;

    public function __construct() {
        parent::__construct();
        $this->statistics = new StatisticModel();
    }

    // Trang thống kê: mặc định hiển thị "Tháng này", gom doanh thu theo ngày
    public function index() {
        $this->render('admin/statistics', ['stats' => $this->buildStatistics()]);
    }

    // Trả dữ liệu JSON cho JavaScript mỗi khi đổi bộ lọc (không cần tải lại trang)
    public function data() {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->buildStatistics(), JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Gom toàn bộ số liệu của trang theo bộ lọc trên URL: range, group, from, to
    private function buildStatistics() {
        $range = $_GET['range'] ?? 'month';
        $group = $_GET['group'] ?? 'day';
        if (!in_array($group, ['day', 'week', 'month'])) {
            $group = 'day';
        }

        [$range, $from, $to] = $this->resolveRange($range, $_GET['from'] ?? '', $_GET['to'] ?? '');

        // Kỳ trước có cùng số ngày, nằm ngay trước kỳ đang xem -> dùng để tính % tăng/giảm
        $days     = (int) $from->diff($to)->days;
        $prevTo   = clone $from;
        $prevFrom = (clone $from)->modify("-$days days");

        $fromStr     = $from->format('Y-m-d H:i:s');
        $toStr       = $to->format('Y-m-d H:i:s');
        $current     = $this->statistics->getSummary($fromStr, $toStr);
        $previous    = $this->statistics->getSummary($prevFrom->format('Y-m-d H:i:s'), $prevTo->format('Y-m-d H:i:s'));

        // Xem trong 1 ngày mà chọn "Theo ngày" thì biểu đồ chỉ có 1 điểm -> tự chuyển sang theo giờ
        $chartGroup = ($group === 'day' && $days === 1) ? 'hour' : $group;
        $revenue    = $this->statistics->getRevenueByPeriod($fromStr, $toStr, $chartGroup);

        $source = $this->statistics->getCustomerSource($fromStr, $toStr);
        $lastDay = (clone $to)->modify('-1 day');

        return [
            'range'      => $range,
            'group'      => $group,
            'chartGroup' => $chartGroup,
            'from'       => $from->format('Y-m-d'),
            'to'         => $lastDay->format('Y-m-d'),
            'rangeLabel' => $from->format('d/m/Y') . ' - ' . $lastDay->format('d/m/Y'),
            'prevLabel'  => $prevFrom->format('d/m/Y') . ' - ' . (clone $prevTo)->modify('-1 day')->format('d/m/Y'),
            'kpi' => [
                'revenue'  => $this->compare($current['revenue'], $previous['revenue']),
                'orders'   => $this->compare($current['totalOrders'], $previous['totalOrders']),
                'guests'   => $this->compare($current['totalGuests'], $previous['totalGuests']),
                'avgOrder' => $this->compare($current['avgOrder'], $previous['avgOrder']),
            ],
            'revenueChart'   => $this->buildChartSeries($from, $to, $chartGroup, $revenue),
            'orderStatus'    => array_map('intval', $this->statistics->getOrderStatus($fromStr, $toStr)),
            'topFoods'       => $this->statistics->getTopFoods($fromStr, $toStr, 4),
            'customerSource' => [
                'labels' => ['Đặt bàn trước', 'Khách thành viên', 'Khách vãng lai'],
                'data'   => [(int) $source['reservation'], (int) $source['member'], (int) $source['walkin']],
            ],
        ];
    }

    // Đổi tên khoảng thời gian thành mốc [từ, đến) cụ thể. Mốc "đến" luôn là 00:00 của ngày kế tiếp
    private function resolveRange($range, $fromInput, $toInput) {
        $today    = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');

        switch ($range) {
            case 'today':
                return ['today', $today, $tomorrow];

            case 'week':
                // Tuần bắt đầu từ thứ 2
                return ['week', new DateTime('monday this week'), $tomorrow];

            case 'custom':
                $from = DateTime::createFromFormat('!Y-m-d', $fromInput);
                $to   = DateTime::createFromFormat('!Y-m-d', $toInput);
                if ($from && $to) {
                    if ($from > $to) {
                        [$from, $to] = [$to, $from];
                    }
                    return ['custom', $from, $to->modify('+1 day')];
                }
                // Ngày nhập sai định dạng -> quay về "Tháng này"
                break;
        }
        return ['month', new DateTime('first day of this month 00:00'), $tomorrow];
    }

    // Lập đầy đủ các mốc trên trục X (mốc không có doanh thu thì = 0)
    private function buildChartSeries(DateTime $from, DateTime $to, $group, $revenue) {
        $labels = [];
        $data   = [];

        if ($group === 'hour') {
            // Giờ mở cửa 10h - 22h, mở rộng thêm nếu có đơn ngoài khung giờ
            $hours = array_map('intval', array_keys($revenue));
            $start = min(array_merge([10], $hours));
            $end   = max(array_merge([22], $hours));
            for ($h = $start; $h <= $end; $h++) {
                $key      = str_pad($h, 2, '0', STR_PAD_LEFT);
                $labels[] = $key . 'h';
                $data[]   = $revenue[$key] ?? 0;
            }
            return ['labels' => $labels, 'data' => $data];
        }

        $cursor = clone $from;
        if ($group === 'week') {
            $cursor->modify('monday this week');
        } elseif ($group === 'month') {
            $cursor->modify('first day of this month');
        }

        while ($cursor < $to) {
            if ($group === 'week') {
                $key      = $cursor->format('Y-m-d');
                $labels[] = 'Tuần ' . $cursor->format('d/m');
                $cursor->modify('+7 days');
            } elseif ($group === 'month') {
                $key      = $cursor->format('Y-m');
                $labels[] = 'T' . $cursor->format('m/Y');
                $cursor->modify('+1 month');
            } else {
                $key      = $cursor->format('Y-m-d');
                $labels[] = $cursor->format('d/m');
                $cursor->modify('+1 day');
            }
            $data[] = $revenue[$key] ?? 0;
        }
        return ['labels' => $labels, 'data' => $data];
    }

    // Tính % thay đổi so với kỳ trước
    private function compare($current, $previous) {
        $current  = (float) $current;
        $previous = (float) $previous;
        if ($previous > 0) {
            $change = round(($current - $previous) / $previous * 100, 1);
        } else {
            $change = $current > 0 ? 100 : 0;
        }
        return ['value' => $current, 'previous' => $previous, 'change' => $change];
    }
}

