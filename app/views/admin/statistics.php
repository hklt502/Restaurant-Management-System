<?php
$pageTitle = "Thống kê & Báo cáo - Nhóm 3";
$pageCss = "../../../public/asset/css/admin/statistics.css";
$activePage = "statistics";
$extraHead = '    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
include_once __DIR__ . '/../layouts/admin_header.php';
?>
<!-- PAGE TITLE & DATE FILTER -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Thống kê & Báo cáo</h1>
                    <p class="page-desc">Phân tích chi tiết về doanh thu, lượng khách và hiệu suất kinh doanh.</p>
                </div>
                <div class="date-filter">
                    <button class="btn btn--outline"><i class="bx bx-calendar"></i> Hôm nay</button>
                    <button class="btn btn--outline"><i class="bx bx-calendar"></i> Tuần này</button>
                    <button class="btn btn--primary"><i class="bx bx-calendar"></i> Tháng này</button>
                    <div class="custom-date">
                        <input type="date" class="date-input">
                        <span>-</span>
                        <input type="date" class="date-input">
                        <button class="btn btn--icon" title="Lọc"><i class="bx bx-filter-alt"></i></button>
                    </div>
                    <button class="btn btn--ghost" title="Xuất báo cáo PDF"><i class="bx bx-download"></i></button>
                </div>
            </div>

            <!-- KPI CARDS -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--blue"><i class="bx bx-money"></i></div>
                        <span class="badge badge--success"><i class="bx bx-up-arrow-alt"></i> 12.5%</span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Tổng doanh thu</p>
                        <h3 class="kpi-card__value">124.500.000đ</h3>
                        <p class="kpi-card__compare">So với tháng trước: 110.650.000đ</p>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--green"><i class="bx bx-receipt"></i></div>
                        <span class="badge badge--success"><i class="bx bx-up-arrow-alt"></i> 8.2%</span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Tổng đơn hàng</p>
                        <h3 class="kpi-card__value">1,452</h3>
                        <p class="kpi-card__compare">So với tháng trước: 1,342</p>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--orange"><i class="bx bx-group"></i></div>
                        <span class="badge badge--danger"><i class="bx bx-down-arrow-alt"></i> 2.1%</span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Khách phục vụ</p>
                        <h3 class="kpi-card__value">3,840</h3>
                        <p class="kpi-card__compare">So với tháng trước: 3,922</p>
                    </div>
                </div>

                <div class="kpi-card">
                    <div class="kpi-card__header">
                        <div class="kpi-card__icon kpi-card__icon--purple"><i class="bx bx-wallet"></i></div>
                        <span class="badge badge--success"><i class="bx bx-up-arrow-alt"></i> 4.3%</span>
                    </div>
                    <div class="kpi-card__body">
                        <p class="kpi-card__title">Giá trị đơn trung bình</p>
                        <h3 class="kpi-card__value">85.700đ</h3>
                        <p class="kpi-card__compare">So với tháng trước: 82.200đ</p>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 1 -->
            <div class="charts-row">
                <!-- Revenue Chart -->
                <div class="chart-container chart-container--large">
                    <div class="chart-header">
                        <h3 class="chart-title">Biểu đồ doanh thu</h3>
                        <select class="chart-select">
                            <option>Theo ngày</option>
                            <option>Theo tuần</option>
                            <option>Theo tháng</option>
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
                        <button class="btn btn--icon"><i class="bx bx-dots-vertical-rounded"></i></button>
                    </div>
                    <div class="chart-body">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="chart-legend-custom">
                        <div class="legend-item"><span class="dot dot--success"></span> Hoàn thành (65%)</div>
                        <div class="legend-item"><span class="dot dot--warning"></span> Đang xử lý (20%)</div>
                        <div class="legend-item"><span class="dot dot--danger"></span> Đã hủy (15%)</div>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 2 -->
            <div class="charts-row">
                <!-- Top Products -->
                <div class="chart-container chart-container--half">
                    <div class="chart-header">
                        <h3 class="chart-title">Món ăn bán chạy nhất</h3>
                        <a href="menu.php" class="chart-link">Xem tất cả</a>
                    </div>
                    <div class="top-products">
                        <div class="top-product-item">
                            <div class="product-rank">1</div>
                            <div class="product-img"><i class="bx bx-bowl-hot"></i></div>
                            <div class="product-info">
                                <h4>Phở Bò Đặc Biệt</h4>
                                <p>Món nước</p>
                            </div>
                            <div class="product-stats">
                                <div class="stat-sold">450 p</div>
                                <div class="stat-revenue">22.5M</div>
                            </div>
                        </div>
                        <div class="top-product-item">
                            <div class="product-rank">2</div>
                            <div class="product-img"><i class="bx bx-baguette"></i></div>
                            <div class="product-info">
                                <h4>Bánh Mì Thịt Nướng</h4>
                                <p>Ăn nhẹ</p>
                            </div>
                            <div class="product-stats">
                                <div class="stat-sold">380 p</div>
                                <div class="stat-revenue">11.4M</div>
                            </div>
                        </div>
                        <div class="top-product-item">
                            <div class="product-rank">3</div>
                            <div class="product-img"><i class="bx bx-drink"></i></div>
                            <div class="product-info">
                                <h4>Trà Đào Cam Sả</h4>
                                <p>Đồ uống</p>
                            </div>
                            <div class="product-stats">
                                <div class="stat-sold">320 ly</div>
                                <div class="stat-revenue">14.4M</div>
                            </div>
                        </div>
                        <div class="top-product-item">
                            <div class="product-rank">4</div>
                            <div class="product-img"><i class="bx bx-dish"></i></div>
                            <div class="product-info">
                                <h4>Cơm Tấm Sườn Bì</h4>
                                <p>Món chính</p>
                            </div>
                            <div class="product-stats">
                                <div class="stat-sold">290 d</div>
                                <div class="stat-revenue">15.9M</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Demographics / Source -->
                <div class="chart-container chart-container--half">
                    <div class="chart-header">
                        <h3 class="chart-title">Nguồn khách hàng</h3>
                        <button class="btn btn--icon"><i class="bx bx-dots-vertical-rounded"></i></button>
                    </div>
                    <div class="chart-body chart-body--bar">
                        <canvas id="customerSourceChart"></canvas>
                    </div>
                </div>
            </div>

<!-- Khởi tạo Chart.js -->
    <script>
        // Update Clock
        function updateClock() {
            const clockEl = document.getElementById('clockTime');
            if (clockEl) {
                const now = new Date();
                const h = String(now.getHours()).padStart(2, '0');
                const m = String(now.getMinutes()).padStart(2, '0');
                const s = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${h}:${m}:${s}`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Chart defaults
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#6B7280';
        Chart.defaults.scale.grid.color = '#E8E8E8';

        // 1. Revenue Line Chart
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        const gradientBlue = ctxRev.createLinearGradient(0, 0, 0, 400);
        gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
        gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0)');

        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: ['01', '03', '06', '09', '12', '15', '18', '21', '24', '27', '30'],
                datasets: [{
                    label: 'Doanh thu (Triệu VNĐ)',
                    data: [15, 22, 18, 35, 28, 42, 38, 55, 48, 62, 58],
                    borderColor: '#3B82F6',
                    backgroundColor: gradientBlue,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3B82F6',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        padding: 12,
                        titleFont: { size: 13, weight: 'normal' },
                        bodyFont: { size: 14, weight: 'bold' },
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return context.parsed.y + ' Triệu VNĐ';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: { padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { padding: 10 }
                    }
                }
            }
        });

        // 2. Order Status Doughnut Chart
        const ctxStatus = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Hoàn thành', 'Đang xử lý', 'Đã hủy'],
                datasets: [{
                    data: [65, 20, 15],
                    backgroundColor: ['#22C55E', '#F59E0B', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        padding: 10,
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });

        // 3. Customer Source Bar Chart
        const ctxSource = document.getElementById('customerSourceChart').getContext('2d');
        new Chart(ctxSource, {
            type: 'bar',
            data: {
                labels: ['Tại quán', 'Take away', 'ShopeeFood', 'GrabFood', 'Gojek'],
                datasets: [{
                    label: 'Số lượng đơn',
                    data: [850, 120, 240, 180, 62],
                    backgroundColor: '#A21A16', // primary-500
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1A1A2E',
                        padding: 12,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5] },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    </script>

<?php
include_once __DIR__ . '/../layouts/admin_footer.php';
?>
