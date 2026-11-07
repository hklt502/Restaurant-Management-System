<?php
// Nếu truy cập trực tiếp file View trên trình duyệt -> chuyển hướng về Front Controller (StatisticController::index)
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-statistics');
    exit;
}

$pageTitle  = 'Thống kê & Báo cáo - Nhóm 3';
$pageCss    = 'public/asset/css/admin/statistics.css';
$activePage = 'statistics';
$extraHead  = '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';

$dbStatistics = $dbStatistics ?? null;
$initKpi      = $dbStatistics['month']['kpi'] ?? null;
include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- PAGE TITLE & DATE FILTER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Thống kê & Báo cáo</h1>
                    <p class="page-desc" id="periodSubtitle">
                        <?= htmlspecialchars($dbStatistics['month']['subtitle'] ?? 'Phân tích chi tiết về doanh thu, lượng khách và hiệu suất kinh doanh kết nối trực tiếp Cơ sở dữ liệu.') ?>
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
                        <span class="badge <?= ($initKpi['revenue']['isUp'] ?? true) ? 'badge--success' : 'badge--danger' ?>" id="kpiRevenueBadge">
                            <i class="bx <?= ($initKpi['revenue']['isUp'] ?? true) ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>"></i> <?= $initKpi['revenue']['change'] ?? 0 ?>%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Tổng doanh thu</p>
                        <h3 class="kpi-card__value" id="kpiRevenueValue"><?= htmlspecialchars($initKpi['revenue']['value'] ?? '0đ') ?></h3>
                        <p class="kpi-card__compare" id="kpiRevenueCompare"><?= htmlspecialchars($initKpi['revenue']['compare'] ?? '') ?></p>
                    </div>
                </div>

                <!-- 2. Tổng đơn hàng -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--green">
                            <i class="bx bx-receipt"></i>
                        </div>
                        <span class="badge <?= ($initKpi['orders']['isUp'] ?? true) ? 'badge--success' : 'badge--danger' ?>" id="kpiOrdersBadge">
                            <i class="bx <?= ($initKpi['orders']['isUp'] ?? true) ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>"></i> <?= $initKpi['orders']['change'] ?? 0 ?>%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Tổng đơn hàng</p>
                        <h3 class="kpi-card__value" id="kpiOrdersValue"><?= htmlspecialchars($initKpi['orders']['value'] ?? '0') ?></h3>
                        <p class="kpi-card__compare" id="kpiOrdersCompare"><?= htmlspecialchars($initKpi['orders']['compare'] ?? '') ?></p>
                    </div>
                </div>

                <!-- 3. Khách phục vụ -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--orange">
                            <i class="bx bx-group"></i>
                        </div>
                        <span class="badge <?= ($initKpi['guests']['isUp'] ?? true) ? 'badge--success' : 'badge--danger' ?>" id="kpiGuestsBadge">
                            <i class="bx <?= ($initKpi['guests']['isUp'] ?? true) ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>"></i> <?= $initKpi['guests']['change'] ?? 0 ?>%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Khách phục vụ</p>
                        <h3 class="kpi-card__value" id="kpiGuestsValue"><?= htmlspecialchars($initKpi['guests']['value'] ?? '0') ?></h3>
                        <p class="kpi-card__compare" id="kpiGuestsCompare"><?= htmlspecialchars($initKpi['guests']['compare'] ?? '') ?></p>
                    </div>
                </div>

                <!-- 4. Giá trị đơn trung bình -->
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--purple">
                            <i class="bx bx-wallet"></i>
                        </div>
                        <span class="badge <?= ($initKpi['avgOrder']['isUp'] ?? true) ? 'badge--success' : 'badge--danger' ?>" id="kpiAvgBadge">
                            <i class="bx <?= ($initKpi['avgOrder']['isUp'] ?? true) ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' ?>"></i> <?= $initKpi['avgOrder']['change'] ?? 0 ?>%
                        </span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Giá trị đơn trung bình</p>
                        <h3 class="kpi-card__value" id="kpiAvgValue"><?= htmlspecialchars($initKpi['avgOrder']['value'] ?? '0đ') ?></h3>
                        <p class="kpi-card__compare" id="kpiAvgCompare"><?= htmlspecialchars($initKpi['avgOrder']['compare'] ?? '') ?></p>
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
            </div>

    <!-- Khởi tạo Chart.js & Xử lý tương tác thống kê -->
    <script>
        // ---------------------------------------------------------
        // 1. DỮ LIỆU TỪ DATABASE PHP NẠP TRỰC TIẾP VÀO JAVASCRIPT
        // ---------------------------------------------------------
        const serverDbStats = <?= json_encode($dbStatistics, JSON_UNESCAPED_UNICODE) ?>;

        const emptyPeriodFallback = {
            subtitle: "Chưa có dữ liệu đơn hàng trong kỳ này.",
            defaultChartGroup: "day",
            fromDate: "<?= date('Y-m-01') ?>",
            toDate: "<?= date('Y-m-d') ?>",
            kpi: {
                revenue:  { value: "0đ", change: 0, isUp: true, compare: "Kỳ trước: 0đ" },
                orders:   { value: "0",  change: 0, isUp: true, compare: "Kỳ trước: 0 đơn" },
                guests:   { value: "0",  change: 0, isUp: true, compare: "Kỳ trước: 0 khách" },
                avgOrder: { value: "0đ", change: 0, isUp: true, compare: "Kỳ trước: 0đ" }
            },
            revenueSeries: {
                day:   { labels: [], data: [] },
                week:  { labels: [], data: [] },
                month: { labels: [], data: [] }
            },
            orderStatus: [0, 0, 0],
            orderStatusCounts: [0, 0, 0],
            topProducts: []
        };

        const statisticsData = {
            today: (serverDbStats && serverDbStats.today) ? serverDbStats.today : emptyPeriodFallback,
            week:  (serverDbStats && serverDbStats.week)  ? serverDbStats.week  : emptyPeriodFallback,
            month: (serverDbStats && serverDbStats.month) ? serverDbStats.month : emptyPeriodFallback
        };

        // ---------------------------------------------------------
        // 2. KHỞI TẠO CÁC BIỂU ĐỒ CHART.JS TỪ DỮ LIỆU DATABASE
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

        // ---------------------------------------------------------
        // 3. CÁC HÀM CẬP NHẬT GIAO DIỆN & ĐỒ THỊ ĐỘNG
        // ---------------------------------------------------------
        let currentPeriodKey = "month";
        let currentPeriodData = statisticsData.month;

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
                listEl.innerHTML = `<p style="color: var(--text3); font-size: 0.9rem; padding: 12px 0;">Không có dữ liệu món ăn trong kỳ này.</p>`;
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

            const [completedPct, processingPct, cancelledPct] = periodData.orderStatus || [0, 0, 0];
            const [cComp, cProc, cCanc] = periodData.orderStatusCounts || [0, 0, 0];
            orderStatusChart.data.datasets[0].data = [completedPct, processingPct, cancelledPct];
            orderStatusChart.update();

            document.getElementById("legendCompleted").textContent  = `Hoàn thành: ${cComp} đơn (${completedPct}%)`;
            document.getElementById("legendProcessing").textContent = `Đang xử lý: ${cProc} đơn (${processingPct}%)`;
            document.getElementById("legendCancelled").textContent  = `Đã hủy: ${cCanc} đơn (${cancelledPct}%)`;

            const searchInput = document.querySelector(".header__search input");
            renderTopProducts(periodData.topProducts || [], searchInput ? searchInput.value : "");
        }

        function syncDateInputs(periodKey) {
            const startInput = document.getElementById("startDate");
            const endInput   = document.getElementById("endDate");
            if (!startInput || !endInput) return;

            const pData = statisticsData[periodKey];
            if (pData && pData.fromDate && pData.toDate) {
                startInput.value = pData.fromDate;
                endInput.value   = pData.toDate;
            }
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

        // ---------------------------------------------------------
        // 4. GẮN SỰ KIỆN TƯƠNG TÁC CHO CÁC NÚT & BỘ LỌC (TRUY VẤN DB THẬT)
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
                const sep = window.location.href.includes('?') ? '&' : '?';
                const ajaxUrl = `${window.location.href}${sep}ajax_custom=1&from=${encodeURIComponent(startVal)}&to=${encodeURIComponent(endVal)}`;

                fetch(ajaxUrl)
                    .then(res => res.json())
                    .then(res => {
                        if (res && res.success && res.data) {
                            applyStatisticsView(res.data, true);
                        }
                    })
                    .catch(err => console.error("Lỗi tải thống kê tùy chỉnh:", err));
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
