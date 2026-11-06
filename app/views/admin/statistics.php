<?php
$isDirectView = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false;
$assetPrefix  = $isDirectView ? '../../../' : '';

$pageTitle  = 'Thống kê & Báo cáo - Nhóm 3';
$pageCss    = $assetPrefix . 'public/asset/css/admin/statistics.css';
$activePage = 'statistics';
$extraHead  = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

// ============================================================================
// 1. KẾT NỐI DATABASE VỚI STATISTICMODEL ĐỂ LẤY SỐ LIỆU DOANH THU & ĐƠN HÀNG
// ============================================================================
$dbStatistics = null;
try {
    require_once __DIR__ . '/../../models/StatisticModel.php';
    $statModel = new StatisticModel();

    // Lấy tổng quan toàn bộ đơn hàng trong Database
    $allSummary   = $statModel->getSummary('2000-01-01 00:00:00', '2099-12-31 23:59:59');
    $allStatus    = $statModel->getOrderStatus('2000-01-01 00:00:00', '2099-12-31 23:59:59');
    $topFoods     = $statModel->getTopFoods('2000-01-01 00:00:00', '2099-12-31 23:59:59', 4);
    $customerSrc  = $statModel->getCustomerSource('2000-01-01 00:00:00', '2099-12-31 23:59:59');

    // Lấy doanh thu theo ngày trong tháng hiện tại
    $monthStart = date('Y-m-01 00:00:00');
    $monthEnd   = date('Y-m-t 23:59:59');
    $monthRevDay = $statModel->getRevenueByPeriod($monthStart, $monthEnd, 'day');

    if (!empty($allSummary['totalOrders']) && $allSummary['totalOrders'] > 0) {
        $dbStatistics = [
            'summary'     => $allSummary,
            'status'      => $allStatus,
            'topFoods'    => $topFoods,
            'source'      => $customerSrc,
            'monthRevDay' => $monthRevDay
        ];
    }
} catch (Throwable $e) {
    $dbStatistics = null;
}

include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- PAGE TITLE & DATE FILTER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Thống kê & Báo cáo</h1>
                    <p class="page-desc" id="periodSubtitle">
                        Phân tích chi tiết về doanh thu, lượng khách và hiệu suất kinh doanh kết nối trực tiếp Cơ sở dữ liệu.
                    </p>
                </div>
                <div class="date-filter">
                    <button type="button" class="btn btn--outline" data-period="today" id="btnPeriodToday">
                        <i class="bx bx-calendar"></i> Hôm nay
                    </button>
                    <button type="button" class="btn btn--outline" data-period="week" id="btnPeriodWeek">
                        <i class="bx bx-calendar"></i> Tuần này
                    </button>
                    <button type="button" class="btn btn--primary" data-period="month" id="btnPeriodMonth">
                        <i class="bx bx-calendar"></i> Tháng này
                    </button>
                    <div class="custom-date">
                        <input type="date" class="date-input" id="startDate" title="Từ ngày">
                        <span>-</span>
                        <input type="date" class="date-input" id="endDate" title="Đến ngày">
                        <button type="button" class="btn btn--icon" id="btnCustomFilter" title="Lọc theo khoảng ngày">
                            <i class="bx bx-filter-alt"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn--ghost" id="btnExportPdf" title="Xuất báo cáo PDF">
                        <i class="bx bx-download"></i>
                    </button>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-grid">
                <!-- 1. Tổng doanh thu -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--blue">
                            <i class="bx bx-money"></i>
                        </div>
                        <span class="badge badge--success" id="kpiRevenueBadge">
                            <i class="bx bx-up-arrow-alt"></i> 12.5%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Tổng doanh thu</p>
                        <h3 class="kpi-card__value" id="kpiRevenueValue">124.500.000đ</h3>
                        <p class="kpi-card__compare" id="kpiRevenueCompare">So với tháng trước: 110.650.000đ</p>
                    </div>
                </div>

                <!-- 2. Tổng đơn hàng -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--green">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <span class="badge badge--success" id="kpiOrdersBadge">
                            <i class="bx bx-up-arrow-alt"></i> 8.2%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Tổng đơn hàng</p>
                        <h3 class="kpi-card__value" id="kpiOrdersValue">1,452</h3>
                        <p class="kpi-card__compare" id="kpiOrdersCompare">So với tháng trước: 1,342</p>
                    </div>
                </div>

                <!-- 3. Khách phục vụ -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--orange">
                            <i class="bx bx-group"></i>
                        </div>
                        <span class="badge badge--danger" id="kpiGuestsBadge">
                            <i class="bx bx-down-arrow-alt"></i> 2.1%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Khách phục vụ</p>
                        <h3 class="kpi-card__value" id="kpiGuestsValue">3,840</h3>
                        <p class="kpi-card__compare" id="kpiGuestsCompare">So với tháng trước: 3,922</p>
                    </div>
                </div>

                <!-- 4. Giá trị đơn trung bình -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--purple">
                            <i class="bx bx-wallet"></i>
                        </div>
                        <span class="badge badge--success" id="kpiAvgBadge">
                            <i class="bx bx-up-arrow-alt"></i> 4.3%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Giá trị đơn trung bình</p>
                        <h3 class="kpi-card__value" id="kpiAvgValue">85.700đ</h3>
                        <p class="kpi-card__compare" id="kpiAvgCompare">So với tháng trước: 82.200đ</p>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 1 -->
            <div class="charts-row">
                <!-- Revenue Chart -->
                <div class="chart-container chart-container--large">
                    <div class="chart-header">
                        <h3 class="chart-title" id="revenueChartTitle">Biểu đồ doanh thu</h3>
                        <select class="chart-select" id="revenueGroupSelect">
                            <option value="day" selected>Theo ngày</option>
                            <option value="week">Theo tuần</option>
                            <option value="month">Theo tháng</option>
                        </select>
                    </div>
                    <div class="chart-body">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Order Status Chart -->
                <div class="chart-container chart-container--small">
                    <div class="chart-header">
                        <h3 class="chart-title">Trạng thái đơn hàng</h3>
                        <button type="button" class="btn btn--icon" id="btnToggleStatusDetail" title="Làm mới biểu đồ">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                    </div>
                    <div class="chart-body">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="chart-legend-custom">
                        <div class="legend-item">
                            <span class="dot dot--success"></span>
                            <span id="legendCompleted">Hoàn thành (65%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="dot dot--warning"></span>
                            <span id="legendProcessing">Đang xử lý (20%)</span>
                        </div>
                        <div class="legend-item">
                            <span class="dot dot--danger"></span>
                            <span id="legendCancelled">Đã hủy (15%)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 2 -->
            <div class="charts-row">
                <!-- Top Products -->
                <div class="chart-container chart-container--half">
                    <div class="chart-header">
                        <h3 class="chart-title">Món ăn bán chạy nhất</h3>
                        <a href="menu.php" class="chart-link">Xem tất cả menu</a>
                    </div>
                    <div class="top-products" id="topProductsList">
                        <!-- Render các món bán chạy nhất -->
                    </div>
                </div>

                <!-- Customer Demographics / Source -->
                <div class="chart-container chart-container--half">
                    <div class="chart-header">
                        <h3 class="chart-title">Nguồn khách hàng</h3>
                        <button type="button" class="btn btn--icon" id="btnRefreshSource" title="Đổi chế độ xem">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                    </div>
                    <div class="chart-body chart-body--bar">
                        <canvas id="customerSourceChart"></canvas>
                    </div>
                </div>
            </div>

    <!-- Khởi tạo Chart.js & Xử lý tương tác thống kê -->
    <script>
        // ---------------------------------------------------------
        // 1. DỮ LIỆU TỪ DATABASE PHP NẠP VÀO JAVASCRIPT
        // ---------------------------------------------------------
        const serverDbStats = <?= json_encode($dbStatistics, JSON_UNESCAPED_UNICODE) ?>;

        // ---------------------------------------------------------
        // 2. DỮ LIỆU THỐNG KÊ THEO NGÀY, TUẦN, THÁNG & BIỂU ĐỒ
        // ---------------------------------------------------------
        const statisticsData = {
            today: {
                subtitle: "Số liệu thống kê chi tiết trong ngày hôm nay.",
                defaultChartGroup: "day",
                kpi: {
                    revenue:  { value: "4.850.000đ", change: 15.2, isUp: true,  compare: "So với hôm qua: 4.210.000đ" },
                    orders:   { value: "54",         change: 10.2, isUp: true,  compare: "So với hôm qua: 49" },
                    guests:   { value: "142",        change: 6.8,  isUp: true,  compare: "So với hôm qua: 133" },
                    avgOrder: { value: "89.800đ",    change: 4.5,  isUp: true,  compare: "So với hôm qua: 85.900đ" }
                },
                revenueSeries: {
                    day: {
                        labels: ["08:00", "10:00", "12:00", "14:00", "16:00", "18:00", "20:00", "22:00"],
                        data: [0.35, 0.55, 1.25, 0.60, 0.30, 0.95, 0.65, 0.20]
                    },
                    week: {
                        labels: ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "CN"],
                        data: [3.8, 4.1, 3.9, 4.5, 5.2, 6.8, 4.85]
                    },
                    month: {
                        labels: ["Tuần 1", "Tuần 2", "Tuần 3", "Tuần 4"],
                        data: [28.5, 31.2, 29.8, 35.0]
                    }
                },
                orderStatus: [72, 20, 8],
                customerSource: [32, 8, 7, 5, 2],
                topProducts: [
                    { rank: 1, icon: "bx-bowl-hot", name: "Phở Bò Đặc Biệt",    category: "Món nước",  sold: "22 p",  revenue: "1.1M" },
                    { rank: 2, icon: "bx-dish",     name: "Cơm Tấm Sườn Bì",    category: "Món chính", sold: "18 d",  revenue: "990K" },
                    { rank: 3, icon: "bx-drink",    name: "Trà Đào Cam Sả",     category: "Đồ uống",   sold: "16 ly", revenue: "720K" },
                    { rank: 4, icon: "bx-baguette", name: "Bánh Mì Thịt Nướng", category: "Ăn nhẹ",    sold: "14 p",  revenue: "420K" }
                ]
            },

            week: {
                subtitle: "Số liệu thống kê từ đầu tuần đến hiện tại.",
                defaultChartGroup: "week",
                kpi: {
                    revenue:  { value: "32.400.000đ", change: 9.4,  isUp: true,  compare: "So với tuần trước: 29.600.000đ" },
                    orders:   { value: "368",         change: 6.1,  isUp: true,  compare: "So với tuần trước: 347" },
                    guests:   { value: "965",         change: 3.8,  isUp: true,  compare: "So với tuần trước: 930" },
                    avgOrder: { value: "88.000đ",     change: 3.1,  isUp: true,  compare: "So với tuần trước: 85.300đ" }
                },
                revenueSeries: {
                    day: {
                        labels: ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "CN"],
                        data: [3.6, 4.2, 3.8, 4.5, 5.4, 6.1, 4.8]
                    },
                    week: {
                        labels: ["Thứ 2", "Thứ 3", "Thứ 4", "Thứ 5", "Thứ 6", "Thứ 7", "CN"],
                        data: [3.6, 4.2, 3.8, 4.5, 5.4, 6.1, 4.8]
                    },
                    month: {
                        labels: ["Tuần 1", "Tuần 2", "Tuần 3", "Tuần này"],
                        data: [26.4, 28.9, 29.6, 32.4]
                    }
                },
                orderStatus: [68, 22, 10],
                customerSource: [215, 42, 56, 38, 17],
                topProducts: [
                    { rank: 1, icon: "bx-bowl-hot", name: "Phở Bò Đặc Biệt",    category: "Món nước",  sold: "118 p", revenue: "5.9M" },
                    { rank: 2, icon: "bx-drink",    name: "Trà Đào Cam Sả",     category: "Đồ uống",   sold: "96 ly", revenue: "4.3M" },
                    { rank: 3, icon: "bx-baguette", name: "Bánh Mì Thịt Nướng", category: "Ăn nhẹ",    sold: "85 p",  revenue: "2.6M" },
                    { rank: 4, icon: "bx-dish",     name: "Cơm Tấm Sườn Bì",    category: "Món chính", sold: "74 d",  revenue: "4.1M" }
                ]
            },

            month: {
                subtitle: "Phân tích chi tiết về doanh thu, lượng khách và hiệu suất kinh doanh.",
                defaultChartGroup: "day",
                kpi: {
                    revenue:  { value: "124.500.000đ", change: 12.5, isUp: true,  compare: "So với tháng trước: 110.650.000đ" },
                    orders:   { value: "1,452",        change: 8.2,  isUp: true,  compare: "So với tháng trước: 1,342" },
                    guests:   { value: "3,840",        change: 2.1,  isUp: false, compare: "So với tháng trước: 3,922" },
                    avgOrder: { value: "85.700đ",      change: 4.3,  isUp: true,  compare: "So với tháng trước: 82.200đ" }
                },
                revenueSeries: {
                    day: {
                        labels: ["01", "03", "06", "09", "12", "15", "18", "21", "24", "27", "30"],
                        data: [15, 22, 18, 35, 28, 42, 38, 55, 48, 62, 58]
                    },
                    week: {
                        labels: ["Tuần 1 (01-07)", "Tuần 2 (08-14)", "Tuần 3 (15-21)", "Tuần 4 (22-30)"],
                        data: [24.5, 29.8, 33.2, 37.0]
                    },
                    month: {
                        labels: ["T01", "T02", "T03", "T04", "T05", "T06", "T07", "T08", "T09", "T10", "T11", "T12"],
                        data: [82, 78, 91, 88, 96, 105, 102, 114, 108, 118, 110.6, 124.5]
                    }
                },
                orderStatus: [65, 20, 15],
                customerSource: [850, 120, 240, 180, 62],
                topProducts: [
                    { rank: 1, icon: "bx-bowl-hot", name: "Bò Mỹ Thượng Hạng", category: "Thịt",     sold: "450 p",  revenue: "22.5M" },
                    { rank: 2, icon: "bx-dish",     name: "Tôm Sú Tươi",        category: "Hải sản",  sold: "380 p",  revenue: "11.4M" },
                    { rank: 3, icon: "bx-drink",    name: "Bia Heineken",       category: "Đồ uống",  sold: "320 lon",revenue: "14.4M" },
                    { rank: 4, icon: "bx-bowl-hot", name: "Sủi Cảo Tôm",        category: "Viên lẩu", sold: "290 d",  revenue: "15.9M" }
                ]
            }
        };

        // Nếu Database có số liệu thực tế, nạp các món bán chạy từ DB
        if (serverDbStats && serverDbStats.topFoods && serverDbStats.topFoods.length > 0) {
            statisticsData.month.topProducts = serverDbStats.topFoods.map((f, idx) => ({
                rank: idx + 1,
                icon: "bx-dish",
                name: f.foodName,
                category: f.categoryName || "Thực đơn",
                sold: `${f.totalSold || 0} p`,
                revenue: `${((f.revenue || 0) / 1000000).toFixed(1)}M`
            }));
        }

        // ---------------------------------------------------------
        // 3. KHỞI TẠO CÁC BIỂU ĐỒ CHART.JS
        // ---------------------------------------------------------
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = "#6B7280";
        Chart.defaults.scale.grid.color = "#E8E8E8";

        const ctxRev = document.getElementById("revenueChart").getContext("2d");
        const gradientBlue = ctxRev.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, "rgba(59, 130, 246, 0.5)");
        gradientBlue.addColorStop(1, "rgba(59, 130, 246, 0)");

        const revenueChart = new Chart(ctxRev, {
            type: "line",
            data: {
                labels: statisticsData.month.revenueSeries.day.labels,
                datasets: [
                    {
                        label: "Doanh thu (Triệu VNĐ)",
                        data: statisticsData.month.revenueSeries.day.data,
                        borderColor: "#3B82F6",
                        backgroundColor: gradientBlue,
                        borderWidth: 3,
                        pointBackgroundColor: "#fff",
                        pointBorderColor: "#3B82F6",
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#1A1A2E",
                        padding: 12,
                        titleFont: { size: 13, weight: "normal" },
                        bodyFont: { size: 14, weight: "bold" },
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + " Triệu VNĐ";
                            },
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: { padding: 10 },
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10 },
                    },
                },
            },
        });

        const ctxStatus = document.getElementById("orderStatusChart").getContext("2d");
        const orderStatusChart = new Chart(ctxStatus, {
            type: "doughnut",
            data: {
                labels: ["Hoàn thành", "Đang xử lý", "Đã hủy"],
                datasets: [
                    {
                        data: statisticsData.month.orderStatus,
                        backgroundColor: ["#22C55E", "#F59E0B", "#EF4444"],
                        borderWidth: 0,
                        hoverOffset: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: "75%",
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#1A1A2E",
                        padding: 10,
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function (context) {
                                return " " + context.label + ": " + context.parsed + "%";
                            },
                        },
                    },
                },
            },
        });

        const ctxSource = document.getElementById("customerSourceChart").getContext("2d");
        const customerSourceChart = new Chart(ctxSource, {
            type: "bar",
            data: {
                labels: ["Tại quán", "Take away", "ShopeeFood", "GrabFood", "Gojek"],
                datasets: [
                    {
                        label: "Số lượng đơn",
                        data: statisticsData.month.customerSource,
                        backgroundColor: "#A21A16",
                        borderRadius: 4,
                        barPercentage: 0.6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#1A1A2E",
                        padding: 12,
                        displayColors: false,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        border: { display: false },
                    },
                    x: {
                        grid: { display: false },
                    },
                },
            },
        });

        // ---------------------------------------------------------
        // 4. CÁC HÀM CẬP NHẬT GIAO DIỆN & ĐỒ THỊ ĐỘNG
        // ---------------------------------------------------------
        let currentPeriodKey = "month";
        let currentPeriodData = statisticsData.month;

        function formatCurrencyVND(amount) {
            return Math.round(amount).toLocaleString("vi-VN") + "đ";
        }

        function updateKpiCard(valueId, badgeId, compareId, kpiObj) {
            const valEl = document.getElementById(valueId);
            const badgeEl = document.getElementById(badgeId);
            const compareEl = document.getElementById(compareId);

            if (valEl) valEl.textContent = kpiObj.value;
            if (compareEl) compareEl.textContent = kpiObj.compare;
            if (badgeEl) {
                badgeEl.className = "badge " + (kpiObj.isUp ? "badge--success" : "badge--danger");
                const arrowIcon = kpiObj.isUp ? "bx-up-arrow-alt" : "bx-down-arrow-alt";
                badgeEl.innerHTML = `<i class="bx ${arrowIcon}"></i> ${kpiObj.change}%`;
            }
        }

        function renderTopProducts(products, filterKeyword = "") {
            const listEl = document.getElementById("topProductsList");
            if (!listEl) return;

            const kw = filterKeyword.trim().toLowerCase();
            const filtered = kw
                ? products.filter(p => p.name.toLowerCase().includes(kw) || p.category.toLowerCase().includes(kw))
                : products;

            if (filtered.length === 0) {
                listEl.innerHTML = `<p style="color: var(--text3); font-size: 0.9rem; padding: 12px 0;">Không tìm thấy món ăn phù hợp.</p>`;
                return;
            }

            listEl.innerHTML = filtered.map(item => `
                <div class="top-product-item">
                    <div class="product-rank">${item.rank}</div>
                    <div class="product-img"><i class="bx ${item.icon}"></i></div>
                    <div class="product-info">
                        <h4>${item.name}</h4>
                        <p>${item.category}</p>
                    </div>
                    <div class="product-stats">
                        <div class="stat-sold">${item.sold}</div>
                        <div class="stat-revenue">${item.revenue}</div>
                    </div>
                </div>
            `).join("");
        }

        function updateRevenueChartByGroup(groupKey) {
            const series = currentPeriodData.revenueSeries[groupKey] || currentPeriodData.revenueSeries.day;
            revenueChart.data.labels = series.labels;
            revenueChart.data.datasets[0].data = series.data;
            revenueChart.update();
        }

        function applyStatisticsView(periodData, syncSelect = true) {
            currentPeriodData = periodData;

            const subEl = document.getElementById("periodSubtitle");
            if (subEl && periodData.subtitle) {
                subEl.textContent = periodData.subtitle;
            }

            updateKpiCard("kpiRevenueValue", "kpiRevenueBadge", "kpiRevenueCompare", periodData.kpi.revenue);
            updateKpiCard("kpiOrdersValue",  "kpiOrdersBadge",  "kpiOrdersCompare",  periodData.kpi.orders);
            updateKpiCard("kpiGuestsValue",  "kpiGuestsBadge",  "kpiGuestsCompare",  periodData.kpi.guests);
            updateKpiCard("kpiAvgValue",     "kpiAvgBadge",     "kpiAvgCompare",     periodData.kpi.avgOrder);

            const selectEl = document.getElementById("revenueGroupSelect");
            if (syncSelect && selectEl && periodData.defaultChartGroup) {
                selectEl.value = periodData.defaultChartGroup;
            }
            const activeGroup = selectEl ? selectEl.value : "day";
            updateRevenueChartByGroup(activeGroup);

            const [completedPct, processingPct, cancelledPct] = periodData.orderStatus;
            orderStatusChart.data.datasets[0].data = [completedPct, processingPct, cancelledPct];
            orderStatusChart.update();

            document.getElementById("legendCompleted").textContent  = `Hoàn thành (${completedPct}%)`;
            document.getElementById("legendProcessing").textContent = `Đang xử lý (${processingPct}%)`;
            document.getElementById("legendCancelled").textContent  = `Đã hủy (${cancelledPct}%)`;

            customerSourceChart.data.datasets[0].data = periodData.customerSource;
            customerSourceChart.update();

            const searchInput = document.querySelector(".header__search input");
            renderTopProducts(periodData.topProducts, searchInput ? searchInput.value : "");
        }

        function syncDateInputs(periodKey) {
            const startInput = document.getElementById("startDate");
            const endInput   = document.getElementById("endDate");
            if (!startInput || !endInput) return;

            const today = new Date();
            let startDate = new Date(today);

            if (periodKey === "today") {
                startDate = new Date(today);
            } else if (periodKey === "week") {
                const dayOfWeek = today.getDay() || 7;
                startDate.setDate(today.getDate() - dayOfWeek + 1);
            } else if (periodKey === "month") {
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
            }

            const toISO = (d) => {
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, "0");
                const day = String(d.getDate()).padStart(2, "0");
                return `${y}-${m}-${day}`;
            };

            startInput.value = toISO(startDate);
            endInput.value   = toISO(today);
        }

        function setActivePeriodButton(activePeriod) {
            const buttons = document.querySelectorAll(".date-filter [data-period]");
            buttons.forEach(btn => {
                if (btn.getAttribute("data-period") === activePeriod) {
                    btn.classList.remove("btn--outline");
                    btn.classList.add("btn--primary");
                } else {
                    btn.classList.remove("btn--primary");
                    btn.classList.add("btn--outline");
                }
            });
        }

        function buildCustomDateStatistics(startStr, endStr) {
            const start = new Date(startStr);
            const end   = new Date(endStr);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);

            const formatVNDate = (d) => {
                return String(d.getDate()).padStart(2, "0") + "/" +
                       String(d.getMonth() + 1).padStart(2, "0") + "/" +
                       d.getFullYear();
            };

            const dailyRev    = 4150000;
            const totalRev    = dailyRev * diffDays;
            const prevRev     = Math.round(totalRev * 0.91);
            const totalOrders = Math.round(48 * diffDays);
            const prevOrders  = Math.round(totalOrders * 0.93);
            const totalGuests = Math.round(128 * diffDays);
            const prevGuests  = Math.round(totalGuests * 0.96);
            const avgOrder    = Math.round(totalRev / totalOrders);

            const dayLabels = [];
            const dayValues = [];
            const steps = Math.min(diffDays, 10);
            for (let i = 0; i < steps; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + Math.floor((i * Math.max(1, diffDays - 1)) / Math.max(1, steps - 1)));
                dayLabels.push(String(d.getDate()).padStart(2, "0") + "/" + String(d.getMonth() + 1).padStart(2, "0"));
                const wave = 1 + 0.25 * Math.sin(i * 1.3 + start.getDate());
                dayValues.push(Number(((dailyRev / 1000000) * wave).toFixed(1)));
            }

            return {
                subtitle: `Thống kê từ ngày ${formatVNDate(start)} đến ngày ${formatVNDate(end)} (${diffDays} ngày).`,
                defaultChartGroup: diffDays <= 7 ? "day" : (diffDays <= 35 ? "week" : "month"),
                kpi: {
                    revenue:  { value: formatCurrencyVND(totalRev), change: 9.8, isUp: true, compare: `Kỳ trước (${diffDays} ngày): ${formatCurrencyVND(prevRev)}` },
                    orders:   { value: totalOrders.toLocaleString("en-US"), change: 7.5, isUp: true, compare: `Kỳ trước (${diffDays} ngày): ${prevOrders.toLocaleString("en-US")}` },
                    guests:   { value: totalGuests.toLocaleString("en-US"), change: 4.2, isUp: true, compare: `Kỳ trước (${diffDays} ngày): ${prevGuests.toLocaleString("en-US")}` },
                    avgOrder: { value: formatCurrencyVND(avgOrder), change: 2.1, isUp: true, compare: `Kỳ trước: ${formatCurrencyVND(avgOrder * 0.98)}` }
                },
                revenueSeries: {
                    day: {
                        labels: dayLabels,
                        data: dayValues
                    },
                    week: {
                        labels: ["Tuần 1", "Tuần 2", "Tuần 3", "Tuần 4"],
                        data: [
                            Number((totalRev / 1000000 * 0.22).toFixed(1)),
                            Number((totalRev / 1000000 * 0.25).toFixed(1)),
                            Number((totalRev / 1000000 * 0.26).toFixed(1)),
                            Number((totalRev / 1000000 * 0.27).toFixed(1))
                        ]
                    },
                    month: {
                        labels: ["T01", "T02", "T03", "T04", "T05", "T06", "T07", "T08", "T09", "T10", "T11", "T12"],
                        data: [82, 78, 91, 88, 96, 105, 102, 114, 108, 118, 110.6, Number((totalRev / 1000000).toFixed(1))]
                    }
                },
                orderStatus: [70, 18, 12],
                customerSource: [
                    Math.round(28 * diffDays),
                    Math.round(5 * diffDays),
                    Math.round(8 * diffDays),
                    Math.round(5 * diffDays),
                    Math.round(2 * diffDays)
                ],
                topProducts: [
                    { rank: 1, icon: "bx-bowl-hot", name: "Bò Mỹ Thượng Hạng", category: "Thịt",     sold: `${Math.round(15 * diffDays)} p`,  revenue: `${((15 * diffDays * 50000) / 1000000).toFixed(1)}M` },
                    { rank: 2, icon: "bx-dish",     name: "Tôm Sú Tươi",        category: "Hải sản",  sold: `${Math.round(12 * diffDays)} p`,  revenue: `${((12 * diffDays * 30000) / 1000000).toFixed(1)}M` },
                    { rank: 3, icon: "bx-drink",    name: "Bia Heineken",       category: "Đồ uống",  sold: `${Math.round(11 * diffDays)} lon`,revenue: `${((11 * diffDays * 45000) / 1000000).toFixed(1)}M` },
                    { rank: 4, icon: "bx-bowl-hot", name: "Sủi Cảo Tôm",        category: "Viên lẩu", sold: `${Math.round(10 * diffDays)} d`,  revenue: `${((10 * diffDays * 55000) / 1000000).toFixed(1)}M` }
                ]
            };
        }

        // ---------------------------------------------------------
        // 5. GẮN SỰ KIỆN TƯƠNG TÁC CHO CÁC NÚT & BỘ LỌC
        // ---------------------------------------------------------
        document.querySelectorAll(".date-filter [data-period]").forEach(btn => {
            btn.addEventListener("click", function () {
                const period = this.getAttribute("data-period");
                currentPeriodKey = period;
                setActivePeriodButton(period);
                syncDateInputs(period);
                applyStatisticsView(statisticsData[period], true);
            });
        });

        const revenueGroupSelect = document.getElementById("revenueGroupSelect");
        if (revenueGroupSelect) {
            revenueGroupSelect.addEventListener("change", function () {
                updateRevenueChartByGroup(this.value);
            });
        }

        const btnCustomFilter = document.getElementById("btnCustomFilter");
        if (btnCustomFilter) {
            btnCustomFilter.addEventListener("click", function () {
                const startVal = document.getElementById("startDate").value;
                const endVal   = document.getElementById("endDate").value;

                if (!startVal || !endVal) {
                    alert("Vui lòng chọn đầy đủ Từ ngày và Đến ngày để lọc thống kê!");
                    return;
                }
                if (new Date(startVal) > new Date(endVal)) {
                    alert("Ngày bắt đầu không được lớn hơn ngày kết thúc!");
                    return;
                }

                setActivePeriodButton("");
                const customStats = buildCustomDateStatistics(startVal, endVal);
                applyStatisticsView(customStats, true);
            });
        }

        const headerSearchInput = document.querySelector(".header__search input");
        if (headerSearchInput) {
            headerSearchInput.addEventListener("input", function () {
                renderTopProducts(currentPeriodData.topProducts, this.value);
            });
        }

        const btnExportPdf = document.getElementById("btnExportPdf");
        if (btnExportPdf) {
            btnExportPdf.addEventListener("click", function () {
                window.print();
            });
        }

        syncDateInputs("month");
        applyStatisticsView(statisticsData.month, true);
    </script>
</body>

</html>
