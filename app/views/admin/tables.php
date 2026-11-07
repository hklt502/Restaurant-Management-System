<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-tables');
    exit;
}

$pageTitle = "Quản lý bàn - Nhóm 3";
$pageCss = "../../../public/asset/css/admin/tables.css";
$activePage = "tables";

// Nạp cấu hình database của dự án
$configCandidates = [
    __DIR__ . '/../../config/database.php',
    __DIR__ . '/../../../app/config/database.php',
    __DIR__ . '/../config/database.php'
];
foreach ($configCandidates as $configFile) {
    if (is_file($configFile)) { require_once $configFile; break; }
}

try {
    $pdo = new PDO(
        'mysql:host=' . (defined('DB_HOST') ? DB_HOST : 'localhost') . ';dbname=' . (defined('DB_NAME') ? DB_NAME : 'restaurant') . ';charset=utf8mb4',
        defined('DB_USER') ? DB_USER : 'root',
        defined('DB_PASS') ? DB_PASS : '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die('Không thể kết nối database: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

// AJAX: cập nhật/thêm/xóa bàn trực tiếp trong service_table
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    try {
        $action = $_POST['action'];
        if ($action === 'update_status') {
            $status = strtoupper(trim($_POST['status'] ?? ''));
            if (!in_array($status, ['AVAILABLE', 'OCCUPIED', 'RESERVED'], true)) throw new Exception('Trạng thái không hợp lệ.');
            $stmt = $pdo->prepare('UPDATE service_table SET status = ? WHERE tableId = ? AND isDeleted = 0');
            $stmt->execute([$status, (int)($_POST['id'] ?? 0)]);
            if (!$stmt->rowCount()) {
                $check = $pdo->prepare('SELECT tableId FROM service_table WHERE tableId = ? AND isDeleted = 0');
                $check->execute([(int)($_POST['id'] ?? 0)]);
                if (!$check->fetch()) throw new Exception('Không tìm thấy bàn.');
            }
            echo json_encode(['success' => true]);
        } elseif ($action === 'save_table') {
            $id = (int)($_POST['id'] ?? 0);
            $number = trim($_POST['tableNumber'] ?? '');
            $area = trim($_POST['area'] ?? '');
            $capacity = (int)($_POST['capacity'] ?? 0);
            $status = strtoupper(trim($_POST['status'] ?? 'AVAILABLE'));
            if ($number === '' || $area === '' || $capacity < 1) throw new Exception('Vui lòng nhập đầy đủ thông tin bàn.');
            if (!in_array($status, ['AVAILABLE', 'OCCUPIED', 'RESERVED'], true)) throw new Exception('Trạng thái không hợp lệ.');
            if ($id > 0) {
                $stmt = $pdo->prepare('UPDATE service_table SET tableNumber=?, area=?, capacity=?, status=? WHERE tableId=? AND isDeleted=0');
                $stmt->execute([$number, $area, $capacity, $status, $id]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO service_table (tableNumber, area, capacity, status, isDeleted) VALUES (?, ?, ?, ?, 0)');
                $stmt->execute([$number, $area, $capacity, $status]);
            }
            echo json_encode(['success' => true]);
        } elseif ($action === 'delete_table') {
            $stmt = $pdo->prepare('UPDATE service_table SET isDeleted = 1 WHERE tableId = ? AND isDeleted = 0');
            $stmt->execute([(int)($_POST['id'] ?? 0)]);
            if (!$stmt->rowCount()) throw new Exception('Không tìm thấy bàn để xóa.');
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Yêu cầu không hợp lệ.');
        }
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$stmt = $pdo->query('SELECT tableId, tableNumber, area, capacity, status FROM service_table WHERE isDeleted = 0 ORDER BY tableId ASC');
$tables = $stmt->fetchAll();
$totalTables = count($tables);
$statusCounts = ['AVAILABLE' => 0, 'OCCUPIED' => 0, 'RESERVED' => 0];
foreach ($tables as $row) if (isset($statusCounts[$row['status']])) $statusCounts[$row['status']]++;

$areaMap = [
    'Trong nhà' => ['slug' => 'tang1-trong', 'label' => 'Tầng 1 - Trong nhà', 'icon' => 'bx-building-house'],
    'Ngoài trời' => ['slug' => 'tang1-ngoai', 'label' => 'Tầng 1 - Ngoài trời', 'icon' => 'bx-sun'],
    'VIP' => ['slug' => 'tang2-vip', 'label' => 'Tầng 2 - VIP', 'icon' => 'bx-crown'],
    'Phòng riêng' => ['slug' => 'tang2-rieng', 'label' => 'Tầng 2 - Phòng riêng', 'icon' => 'bx-door-open']
];
$areaGroups = [];
foreach ($tables as $table) $areaGroups[$table['area'] ?: 'Chưa phân khu'][] = $table;

function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function statusText($status) {
    return ['AVAILABLE' => 'Trống', 'OCCUPIED' => 'Đang phục vụ', 'RESERVED' => 'Đã đặt trước'][$status] ?? $status;
}
function statusClass($status) { return strtolower((string)$status); }

include_once __DIR__ . '/../layouts/admin_header.php';
?>
<!-- PAGE TITLE -->
            <div class="page-title">
                <div>
                    <h1 class="page-title__heading">Quản lý bàn</h1>
                    <p class="page-title__desc">Quản lý sơ đồ bàn, trạng thái và xếp khách theo thời gian thực.</p>
                </div>
                <div class="page-title__actions">
                    <div class="view-toggle">
                        <button class="view-toggle__btn active" data-view="grid" title="Xem dạng lưới">
                            <i class="bx bx-grid-alt"></i>
                        </button>
                        <button class="view-toggle__btn" data-view="list" title="Xem dạng danh sách">
                            <i class="bx bx-list-ul"></i>
                        </button>
                    </div>
                    <button class="btn btn--primary" id="btnAddTable" type="button">
                        <i class="bx bx-plus"></i> Thêm bàn mới
                    </button>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="stat-cards">
                <div class="stat-card stat-card--total">
                    <div class="stat-card__icon"><i class="bx bx-table"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statTotal"><?= $totalTables ?></p>
                        <p class="stat-card__label">Tổng số bàn</p>
                    </div>
                </div>
                <div class="stat-card stat-card--available">
                    <div class="stat-card__icon"><i class="bx bx-check-circle"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statAvailable"><?= $statusCounts['AVAILABLE'] ?></p>
                        <p class="stat-card__label">Bàn trống</p>
                    </div>
                </div>
                <div class="stat-card stat-card--occupied">
                    <div class="stat-card__icon"><i class="bx bx-user-check"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statOccupied"><?= $statusCounts['OCCUPIED'] ?></p>
                        <p class="stat-card__label">Đang phục vụ</p>
                    </div>
                </div>
                <div class="stat-card stat-card--reserved">
                    <div class="stat-card__icon"><i class="bx bx-calendar-check"></i></div>
                    <div class="stat-card__info">
                        <p class="stat-card__value" id="statReserved"><?= $statusCounts['RESERVED'] ?></p>
                        <p class="stat-card__label">Đã đặt trước</p>
                    </div>
                </div>

            </div>

            <!-- TOOLBAR -->
            <div class="toolbar">
                <div class="toolbar__left">
                    <div class="toolbar__filter-group">
                        <label class="toolbar__label">Khu vực:</label>
                        <select class="toolbar__select" id="filterZone">
                            <option value="">Tất cả</option>
                            <?php foreach (array_keys($areaGroups) as $areaOption): ?>
                                <option value="<?= h($areaMap[$areaOption]['slug'] ?? 'khac') ?>"><?= h($areaMap[$areaOption]['label'] ?? $areaOption) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="toolbar__filter-group">
                        <label class="toolbar__label">Trạng thái:</label>
                        <select class="toolbar__select" id="filterStatus">
                            <option value="">Tất cả</option>
                            <option value="available">Trống</option>
                            <option value="occupied">Đang phục vụ</option>
                            <option value="reserved">Đã đặt trước</option>

                        </select>
                    </div>
                    <div class="toolbar__filter-group">
                        <label class="toolbar__label">Sức chứa:</label>
                        <select class="toolbar__select" id="filterCapacity">
                            <option value="">Tất cả</option>
                            <option value="2">2 người</option>
                            <option value="4">4 người</option>
                            <option value="6">6 người</option>
                            <option value="8">8 người</option>
                            <option value="10">10+ người</option>
                        </select>
                    </div>
                </div>
                <div class="toolbar__right">
                    <!-- STATUS LEGEND -->
                    <div class="status-legend">
                        <span class="status-legend__item"><span class="status-dot status-dot--available"></span>
                            Trống</span>
                        <span class="status-legend__item"><span class="status-dot status-dot--occupied"></span> Đang
                            phục vụ</span>
                        <span class="status-legend__item"><span class="status-dot status-dot--reserved"></span> Đã
                            đặt</span>
                    </div>
                </div>
            </div>

            <!-- ===========================
                 GRID VIEW - SƠ ĐỒ BÀN 
            ============================ -->
            <div class="tables-view tables-view--grid active" id="gridView">
                <?php if (!$tables): ?>
                    <p class="quick-available__empty">Chưa có bàn nào trong database.</p>
                <?php endif; ?>
                <?php foreach ($areaGroups as $areaName => $areaTables):
                    $areaInfo = $areaMap[$areaName] ?? ['slug' => 'khac', 'label' => $areaName, 'icon' => 'bx-map']; ?>
                    <div class="zone-section">
                        <div class="zone-section__header"><div class="zone-section__title">
                            <i class="bx <?= h($areaInfo['icon']) ?>"></i>
                            <h2><?= h($areaInfo['label']) ?></h2>
                            <span class="zone-section__count"><?= count($areaTables) ?> bàn</span>
                        </div></div>
                        <div class="tables-grid">
                            <?php foreach ($areaTables as $table):
                                $id = (int)$table['tableId'];
                                $status = $table['status'];
                                $statusLower = statusClass($status);
                                $zone = $areaInfo['slug']; ?>
                                <div class="table-card table-card--<?= h($statusLower) ?>" data-id="<?= $id ?>" data-zone="<?= h($zone) ?>" data-area="<?= h($table['area']) ?>" data-status="<?= h($statusLower) ?>" data-capacity="<?= (int)$table['capacity'] ?>">
                                    <div class="table-card__header"><span class="table-card__number"><?= h($table['tableNumber']) ?></span><div class="table-card__status-dot"></div></div>
                                    <div class="table-card__icon"><i class="bx bx-chair"></i></div>
                                    <div class="table-card__info">
                                        <span class="table-card__capacity"><i class="bx bx-user"></i> <?= (int)$table['capacity'] ?> người</span>
                                        <span class="table-card__status-text"><?= h(statusText($status)) ?></span>
                                    </div>
                                    <div class="table-card__actions">
                                        <?php if ($status === 'AVAILABLE'): ?>
                                            <button class="table-card__btn table-card__btn--seat" onclick="seatGuest(<?= $id ?>)"><i class="bx bx-user-plus"></i> Xếp khách</button>
                                        <?php elseif ($status === 'OCCUPIED'): ?>
                                            <button class="table-card__btn table-card__btn--bill" onclick="billTable(<?= $id ?>)"><i class="bx bx-receipt"></i> Thanh toán</button>
                                        <?php else: ?>
                                            <button class="table-card__btn table-card__btn--checkin" onclick="changeStatus(<?= $id ?>, 'OCCUPIED')"><i class="bx bx-log-in-circle"></i> Check-in</button>
                                        <?php endif; ?>
                                        <button class="table-card__btn table-card__btn--more" onclick="toggleMenu(<?= $id ?>)"><i class="bx bx-dots-vertical-rounded"></i></button>
                                    </div>
                                    <div class="table-card__menu" id="menu-<?= $id ?>">
                                        <button onclick="editTable(<?= $id ?>)"><i class="bx bx-edit-alt"></i> Chỉnh sửa</button>
                                        <?php if ($status === 'AVAILABLE'): ?>
                                            <button onclick="changeStatus(<?= $id ?>, 'RESERVED')"><i class="bx bx-calendar-check"></i> Đặt trước</button>
                                        <?php elseif ($status === 'OCCUPIED'): ?>
                                            <button onclick="changeStatus(<?= $id ?>, 'AVAILABLE')"><i class="bx bx-check-circle"></i> Trả bàn</button>
                                        <?php else: ?>
                                            <button onclick="changeStatus(<?= $id ?>, 'AVAILABLE')"><i class="bx bx-x-circle"></i> Hủy đặt</button>
                                        <?php endif; ?>
                                        <button class="table-card__menu-danger" onclick="deleteTable(<?= $id ?>)"><i class="bx bx-trash"></i> Xóa bàn</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ===========================
                 LIST VIEW - BẢNG DANH SÁCH 
            ============================ -->
            <div class="tables-view tables-view--list" id="listView">
                <div class="list-card">
                    <div class="table-wrapper">
                        <table class="tables-table">
                            <thead>
                                <tr>
                                    <th>
                                        <label class="custom-checkbox">
                                            <input type="checkbox" id="checkAllTables">
                                            <span class="custom-checkbox__mark"></span>
                                        </label>
                                    </th>
                                    <th>SỐ BÀN</th>
                                    <th>KHU VỰC</th>
                                    <th>SỨC CHỨA</th>
                                    <th>TRẠNG THÁI</th>
                                    <th>THỜI GIAN SỬ DỤNG</th>
                                    <th>GHI CHÚ</th>
                                    <th>HÀNH ĐỘNG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tables as $table): ?>
                                    <tr data-id="<?= (int)$table['tableId'] ?>">
                                        <td><input type="checkbox"></td>
                                        <td><strong><?= h($table['tableNumber']) ?></strong></td>
                                        <td><span class="zone-tag"><?= h($table['area']) ?></span></td>
                                        <td><?= (int)$table['capacity'] ?> người</td>
                                        <td><span class="status-badge status-badge--<?= h(statusClass($table['status'])) ?>"><?= h(statusText($table['status'])) ?></span></td>
                                        <td>—</td><td>—</td>
                                        <td><button type="button" class="table-card__btn" onclick="editTable(<?= (int)$table['tableId'] ?>)">Chỉnh sửa</button></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- XEM NHANH BÀN TRỐNG -->
            <div class="quick-available" id="quickAvailable">
                <div class="quick-available__header">
                    <h3><i class="bx bx-check-circle"></i> Xem nhanh bàn trống — Sẵn sàng xếp khách</h3>
                    <button class="quick-available__toggle" id="toggleQuickView" type="button">
                        <i class="bx bx-chevron-up"></i>
                    </button>
                </div>
                <div class="quick-available__body" id="quickAvailableBody">
                    <div class="quick-available__grid" id="quickGrid">
                        <!-- Sẽ được JS tạo tự động từ các bàn trống -->
                    </div>
                </div>
            </div>

<!-- MODAL: THÊM / SỬA BÀN -->
    <div class="modal-overlay" id="tableModal">
        <div class="modal">
            <div class="modal__header">
                <h2 class="modal__title" id="modalTitle">Thêm bàn mới</h2>
                <button class="modal__close" id="btnCloseModal" type="button"><i class="bx bx-x"></i></button>
            </div>
            <div class="modal__body">
                <form class="modal-form" id="tableForm">
                    <input type="hidden" id="inputTableId" value="">
                    <div class="modal-form__row">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Tên / Số bàn <span>*</span></label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-hash"></i>
                                <input type="text" class="modal-form__input" id="inputTableName"
                                    placeholder="VD: Bàn 21, VIP 05..." required>
                            </div>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Sức chứa (người) <span>*</span></label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-group"></i>
                                <input type="number" class="modal-form__input" id="inputCapacity" placeholder="VD: 4"
                                    min="1" max="50" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form__row">
                        <div class="modal-form__group">
                            <label class="modal-form__label">Khu vực <span>*</span></label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-map"></i>
                                <select class="modal-form__input" id="inputZone" required>
                                    <option value="">Chọn khu vực</option>
                                    <?php foreach (array_keys($areaGroups) as $areaOption): ?>
                                        <option value="<?= h($areaOption) ?>"><?= h($areaOption) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-form__group">
                            <label class="modal-form__label">Trạng thái</label>
                            <div class="modal-form__input-wrapper">
                                <i class="bx bx-info-circle"></i>
                                <select class="modal-form__input" id="inputStatus">
                                    <option value="available">Trống</option>
                                    <option value="occupied">Đang phục vụ</option>
                                    <option value="reserved">Đã đặt trước</option>
        
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Ghi chú</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-note"></i>
                            <input type="text" class="modal-form__input" id="inputNote"
                                placeholder="Ghi chú thêm (nếu có)">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnCancelTable" type="button">Hủy bỏ</button>
                <button class="btn btn--primary" type="submit" form="tableForm">
                    <i class="bx bx-save"></i> Lưu bàn
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL: XẾP KHÁCH -->
    <div class="modal-overlay" id="seatModal">
        <div class="modal modal--sm">
            <div class="modal__header">
                <h2 class="modal__title"><i class="bx bx-user-plus"></i> Xếp khách vào bàn</h2>
                <button class="modal__close" id="btnCloseSeat" type="button"><i class="bx bx-x"></i></button>
            </div>
            <div class="modal__body">
                <div class="seat-info" id="seatInfo">
                    <!-- Thông tin bàn sẽ hiện ở đây -->
                </div>
                <form class="modal-form" id="seatForm">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Số khách</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-group"></i>
                            <input type="number" class="modal-form__input" id="inputGuestCount"
                                placeholder="Số lượng khách" min="1" required>
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Tên khách (tùy chọn)</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-user"></i>
                            <input type="text" class="modal-form__input" id="inputGuestName"
                                placeholder="Nhập tên khách hàng">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal__footer">
                <button class="btn btn--ghost" id="btnCancelSeat" type="button">Hủy</button>
                <button class="btn btn--primary" type="submit" form="seatForm">
                    <i class="bx bx-check"></i> Xác nhận xếp khách
                </button>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
        // ============================
        //  ĐỒNG HỒ THỜI GIAN THỰC
        // ============================
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clockTime').textContent = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ============================
        //  CHUYỂN GRID / LIST VIEW
        // ============================
        const viewBtns = document.querySelectorAll('.view-toggle__btn');
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (btn.dataset.view === 'grid') {
                    gridView.classList.add('active');
                    listView.classList.remove('active');
                } else {
                    listView.classList.add('active');
                    gridView.classList.remove('active');
                }
            });
        });

        // ============================
        //  LỌC BÀN (FILTER)
        // ============================
        const filterZone = document.getElementById('filterZone');
        const filterStatus = document.getElementById('filterStatus');
        const filterCapacity = document.getElementById('filterCapacity');

        function applyFilters() {
            const zone = filterZone.value;
            const status = filterStatus.value;
            const capacity = filterCapacity.value;
            const cards = document.querySelectorAll('.table-card');

            cards.forEach(card => {
                let show = true;
                if (zone && card.dataset.zone !== zone) show = false;
                if (status && card.dataset.status !== status) show = false;
                if (capacity) {
                    const cap = parseInt(card.dataset.capacity);
                    if (capacity === '10' && cap < 10) show = false;
                    else if (capacity !== '10' && cap !== parseInt(capacity)) show = false;
                }
                card.style.display = show ? '' : 'none';
            });

            // Ẩn zone nếu không có bàn nào hiện
            document.querySelectorAll('.zone-section').forEach(zone => {
                const visibleCards = zone.querySelectorAll('.table-card:not([style*="display: none"])');
                zone.style.display = visibleCards.length > 0 ? '' : 'none';
            });
        }

        filterZone.addEventListener('change', applyFilters);
        filterStatus.addEventListener('change', applyFilters);
        filterCapacity.addEventListener('change', applyFilters);

        // ============================
        //  DROPDOWN MENU
        // ============================
        function toggleMenu(id) {
            // Đóng tất cả menu khác
            document.querySelectorAll('.table-card__menu.active').forEach(m => {
                if (m.id !== `menu-${id}`) m.classList.remove('active');
            });
            document.getElementById(`menu-${id}`).classList.toggle('active');
        }

        // Đóng menu khi click ngoài
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.table-card__btn--more') && !e.target.closest('.table-card__menu')) {
                document.querySelectorAll('.table-card__menu.active').forEach(m => m.classList.remove('active'));
            }
        });

        // ============================
        //  ĐỔI TRẠNG THÁI BÀN
        // ============================
        const statusConfig = {
            AVAILABLE: { class: 'table-card--available', text: 'Trống' },
            OCCUPIED: { class: 'table-card--occupied', text: 'Đang phục vụ' },
            RESERVED: { class: 'table-card--reserved', text: 'Đã đặt trước' }
        };

        async function postTableData(data) {
            const body = new URLSearchParams(data);
            const response = await fetch(window.location.href, { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Không thể lưu dữ liệu.');
            return result;
        }

        async function changeStatus(id, newStatus) {
            const normalized = String(newStatus).toUpperCase();
            const config = statusConfig[normalized];
            const card = document.querySelector(`.table-card[data-id="${id}"]`);
            if (!card || !config) return;
            try {
                await postTableData({action:'update_status', id, status:normalized});
                Object.values(statusConfig).forEach(item => card.classList.remove(item.class));
                card.classList.add(config.class);
                card.dataset.status = normalized.toLowerCase();
                const statusText = card.querySelector('.table-card__status-text');
                if (statusText) statusText.textContent = config.text;
                const row = document.querySelector(`.tables-table tbody tr[data-id="${id}"]`);
                if (row) row.querySelector('.status-badge').textContent = config.text;
                updateCardActions(card, normalized.toLowerCase());
                const menu = document.getElementById(`menu-${id}`);
                if (menu) menu.classList.remove('active');
                updateStats(); updateQuickAvailable();
                showToast('Đã cập nhật trạng thái bàn.', 'success');
            } catch (error) { showToast(error.message, 'error'); }
        }

        function updateCardActions(card, status) {
            const actionsDiv = card.querySelector('.table-card__actions');
            const id = card.dataset.id;
            let html = '';

            if (status === 'available') {
                html = `<button class="table-card__btn table-card__btn--seat" onclick="seatGuest(${id})"><i class="bx bx-user-plus"></i> Xếp khách</button>`;
                // Xóa time nếu có
                const timeEl = card.querySelector('.table-card__time');
                if (timeEl) timeEl.remove();
                const resEl = card.querySelector('.table-card__reserved-info');
                if (resEl) resEl.remove();
            } else if (status === 'occupied') {
                html = `<button class="table-card__btn table-card__btn--bill" onclick="billTable(${id})"><i class="bx bx-receipt"></i> Thanh toán</button>`;
            } else if (status === 'reserved') {
                html = `<button class="table-card__btn table-card__btn--checkin" onclick="changeStatus(${id}, 'occupied')"><i class="bx bx-log-in-circle"></i> Check-in</button>`;
            } else if (status === 'maintenance') {
                html = `<button class="table-card__btn table-card__btn--restore" onclick="changeStatus(${id}, 'available')"><i class="bx bx-check"></i> Hoàn tất</button>`;
                const timeEl = card.querySelector('.table-card__time');
                if (timeEl) timeEl.remove();
            }

            html += `<button class="table-card__btn table-card__btn--more" onclick="toggleMenu(${id})"><i class="bx bx-dots-vertical-rounded"></i></button>`;
            actionsDiv.innerHTML = html;
        }

        // ============================
        //  CẬP NHẬT THỐNG KÊ
        // ============================
        function updateStats() {
            const cards = document.querySelectorAll('.table-card');
            let total = 0, available = 0, occupied = 0, reserved = 0;
            cards.forEach(card => {
                total++;
                switch (card.dataset.status) {
                    case 'available': available++; break;
                    case 'occupied': occupied++; break;
                    case 'reserved': reserved++; break;

                }
            });
            document.getElementById('statTotal').textContent = total;
            document.getElementById('statAvailable').textContent = available;
            document.getElementById('statOccupied').textContent = occupied;
            document.getElementById('statReserved').textContent = reserved;

        }

        // ============================
        //  XEM NHANH BÀN TRỐNG
        // ============================
        function updateQuickAvailable() {
            const grid = document.getElementById('quickGrid');
            const available = document.querySelectorAll('.table-card[data-status="available"]');

            if (available.length === 0) {
                grid.innerHTML = '<p class="quick-available__empty"><i class="bx bx-info-circle"></i> Không có bàn trống nào lúc này.</p>';
                return;
            }

            let html = '';
            available.forEach(card => {
                const name = card.querySelector('.table-card__number').textContent;
                const cap = card.dataset.capacity;
                const zone = card.dataset.zone;
                const zoneLabels = {
                    'tang1-trong': 'T1 - Trong nhà',
                    'tang1-ngoai': 'T1 - Ngoài trời',
                    'tang2-vip': 'T2 - VIP',
                    'tang2-rieng': 'T2 - P.Riêng'
                };
                html += `
                    <div class="quick-item" onclick="seatGuest(${card.dataset.id})">
                        <div class="quick-item__name">${name}</div>
                        <div class="quick-item__meta">
                            <span><i class="bx bx-user"></i> ${cap}</span>
                            <span><i class="bx bx-map"></i> ${zoneLabels[zone] || zone}</span>
                        </div>
                        <button class="quick-item__btn"><i class="bx bx-user-plus"></i></button>
                    </div>`;
            });
            grid.innerHTML = html;
        }
        updateQuickAvailable();

        // Toggle quick view
        document.getElementById('toggleQuickView').addEventListener('click', () => {
            const body = document.getElementById('quickAvailableBody');
            const icon = document.querySelector('#toggleQuickView i');
            body.classList.toggle('collapsed');
            icon.classList.toggle('bx-chevron-up');
            icon.classList.toggle('bx-chevron-down');
        });

        // ============================
        //  MODAL: THÊM / SỬA BÀN
        // ============================
        const tableModal = document.getElementById('tableModal');
        const btnAddTable = document.getElementById('btnAddTable');
        const btnCloseModal = document.getElementById('btnCloseModal');
        const btnCancelTable = document.getElementById('btnCancelTable');

        function openTableModal(title = 'Thêm bàn mới') {
            document.getElementById('modalTitle').textContent = title;
            tableModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeTableModal() {
            tableModal.classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('tableForm').reset();
        }

        btnAddTable.addEventListener('click', () => { document.getElementById('inputTableId').value = ''; openTableModal(); });
        btnCloseModal.addEventListener('click', closeTableModal);
        btnCancelTable.addEventListener('click', closeTableModal);
        tableModal.addEventListener('click', (e) => { if (e.target === tableModal) closeTableModal(); });

        function editTable(id) {
            const card = document.querySelector(`.table-card[data-id="${id}"]`);
            if (!card) return;
            document.getElementById('inputTableId').value = id;
            document.getElementById('inputTableName').value = card.querySelector('.table-card__number').textContent;
            document.getElementById('inputCapacity').value = card.dataset.capacity;
            document.getElementById('inputZone').value = card.dataset.area;
            document.getElementById('inputStatus').value = card.dataset.status.toUpperCase();
            openTableModal('Chỉnh sửa bàn');
            // Đóng dropdown
            document.querySelectorAll('.table-card__menu.active').forEach(m => m.classList.remove('active'));
        }

        // ============================
        //  MODAL: XẾP KHÁCH
        // ============================
        const seatModal = document.getElementById('seatModal');
        const btnCloseSeat = document.getElementById('btnCloseSeat');
        const btnCancelSeat = document.getElementById('btnCancelSeat');

        function seatGuest(id) {
            const card = document.querySelector(`.table-card[data-id="${id}"]`);
            if (!card) return;
            const name = card.querySelector('.table-card__number').textContent;
            const cap = card.dataset.capacity;
            document.getElementById('seatInfo').innerHTML = `
                <div class="seat-info__card">
                    <i class="bx bx-chair"></i>
                    <div>
                        <strong>${name}</strong>
                        <span>Sức chứa: ${cap} người</span>
                    </div>
                </div>`;
            document.getElementById('inputGuestCount').max = cap;
            seatModal.dataset.tableId = id;
            seatModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeSeatModal() {
            seatModal.classList.remove('active');
            document.body.style.overflow = '';
            document.getElementById('seatForm').reset();
        }

        btnCloseSeat.addEventListener('click', closeSeatModal);
        btnCancelSeat.addEventListener('click', closeSeatModal);
        seatModal.addEventListener('click', (e) => { if (e.target === seatModal) closeSeatModal(); });

        document.getElementById('seatForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const id = seatModal.dataset.tableId;
            changeStatus(id, 'OCCUPIED');
            closeSeatModal();
            showToast('Đã xếp khách thành công!', 'success');
        });

        // ============================
        //  XÓA BÀN
        // ============================
        async function deleteTable(id) {
            if (!confirm('Bạn có chắc muốn xóa bàn này?')) return;
            try {
                await postTableData({action:'delete_table', id});
                const card = document.querySelector(`.table-card[data-id="${id}"]`);
                const row = document.querySelector(`.tables-table tbody tr[data-id="${id}"]`);
                if (card) card.remove(); if (row) row.remove();
                updateStats(); updateQuickAvailable();
                showToast('Đã xóa bàn khỏi danh sách.', 'success');
            } catch (error) { showToast(error.message, 'error'); }
            document.querySelectorAll('.table-card__menu.active').forEach(m => m.classList.remove('active'));
        }

        // ============================
        //  THANH TOÁN (placeholder)
        // ============================
        function billTable(id) {
            showToast(`Chuyển đến trang thanh toán cho bàn #${id}`, 'info');
        }

        // ============================
        //  TOAST NOTIFICATIONS
        // ============================
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast--${type}`;
            const icons = { success: 'bx-check-circle', error: 'bx-error-circle', info: 'bx-info-circle' };
            toast.innerHTML = `<i class="bx ${icons[type] || icons.info}"></i><span>${message}</span>`;
            container.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ============================
        //  CHECK ALL (List View)
        // ============================
        const checkAllTables = document.getElementById('checkAllTables');
        if (checkAllTables) {
            checkAllTables.addEventListener('change', () => {
                document.querySelectorAll('.tables-table tbody input[type="checkbox"]').forEach(cb => {
                    cb.checked = checkAllTables.checked;
                });
            });
        }

        // ============================
        //  FORM SUBMIT: THÊM BÀN
        // ============================
        document.getElementById('tableForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                await postTableData({
                    action:'save_table', id:document.getElementById('inputTableId').value,
                    tableNumber:document.getElementById('inputTableName').value.trim(),
                    capacity:document.getElementById('inputCapacity').value,
                    area:document.getElementById('inputZone').value,
                    status:document.getElementById('inputStatus').value
                });
                showToast('Đã lưu bàn vào database.', 'success');
                window.location.reload();
            } catch (error) { showToast(error.message, 'error'); }
        });
    </script>

    <!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
    </main>
  </div>

</body>

</html>