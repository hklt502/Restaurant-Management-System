<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-vouchers');
    exit;
}

$isDirectView      = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false;
$assetPrefix       = $isDirectView ? '../../../' : '';
$currentVoucherUrl = $isDirectView ? 'vouchers.php' : 'index.php?page=admin-vouchers';
$querySep          = $isDirectView ? '?' : '&';

$pageTitle  = 'Quản lý Voucher & Khuyến mãi - Restaurant Management System';
$pageCss    = $assetPrefix . 'public/asset/css/admin/vouchers.css';
$activePage = 'vouchers';

// ============================================================================
// 1. KẾT NỐI DATABASE VỚI VOUCHERMODEL (Hỗ trợ cả MVC Controller & Direct View)
// ============================================================================
if (!isset($vouchers) || !isset($promotions)) {
    $message     = $message ?? '';
    $messageType = $messageType ?? 'success';
    $vouchers    = [];
    $promotions  = [];

    try {
        require_once __DIR__ . '/../../models/VoucherModel.php';
        $voucherModel = new VoucherModel();

        // 1.1. Xử lý Thêm mới hoặc Cập nhật Voucher từ Modal Form
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_voucher'])) {
            $voucherId     = (int)($_POST['voucherId'] ?? 0);
            $code          = strtoupper(trim($_POST['voucherCode'] ?? ''));
            $discountType  = strtoupper(trim($_POST['discountType'] ?? 'PERCENT'));
            $discountValue = (float)($_POST['discountValue'] ?? 0);
            $minOrderValue = (float)($_POST['minOrderValue'] ?? 0);
            $expiryDate    = !empty($_POST['expiryDate']) ? $_POST['expiryDate'] : null;
            $usageLimit    = max(1, (int)($_POST['usageLimit'] ?? 100));
            $promotionId   = !empty($_POST['promotionId']) ? (int)$_POST['promotionId'] : null;
            $isActive      = isset($_POST['isActive']) ? 1 : 0;

            if (!empty($code) && $discountValue > 0) {
                if ($voucherId > 0) {
                    $ok = $voucherModel->update($voucherId, $code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId, $isActive);
                    $message     = $ok ? "Đã cập nhật thành công Voucher \"$code\"!" : "Không thể cập nhật Voucher \"$code\"!";
                    $messageType = $ok ? 'success' : 'danger';
                } else {
                    $newId = $voucherModel->create($code, $discountType, $discountValue, $minOrderValue, $expiryDate, $usageLimit, $promotionId, $isActive);
                    $message     = $newId ? "Đã tạo mới thành công Voucher \"$code\" (ID: $newId) vào Database!" : "Mã Voucher \"$code\" đã tồn tại hoặc dữ liệu không hợp lệ!";
                    $messageType = $newId ? 'success' : 'danger';
                }
            } else {
                $message     = 'Vui lòng nhập đầy đủ Mã Voucher và Mức giảm lớn hơn 0!';
                $messageType = 'danger';
            }
        }

        // 1.2. Xử lý Tạm ngưng / Kích hoạt lại Voucher
        if (isset($_GET['action']) && $_GET['action'] === 'toggle') {
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $newSt = $voucherModel->toggleActive($id);
                if ($newSt !== false) {
                    $stLabel     = ($newSt === 1) ? 'Đang hoạt động' : 'Tạm ngưng';
                    $message     = "Đã chuyển trạng thái Voucher #$id sang \"$stLabel\"!";
                    $messageType = 'info';
                }
            }
        }

        // 1.3. Xử lý Xóa mềm Voucher
        if (isset($_GET['action']) && $_GET['action'] === 'delete') {
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0 && $voucherModel->delete($id)) {
                $message     = "Đã xóa Voucher #$id khỏi danh sách!";
                $messageType = 'success';
            }
        }

        $vouchers   = $voucherModel->getAll();
        $promotions = $voucherModel->getPromotions();
    } catch (Throwable $e) {
        $vouchers   = [];
        $promotions = [];
    }
}

// ============================================================================
// 2. TÍNH TOÁN THỐNG KÊ TỪ DỮ LIỆU DATABASE THỰC TẾ
// ============================================================================
$totalVouchers  = count($vouchers);
$activeVouchers = 0;
$totalUsedCount = 0;
$expiredOrOff   = 0;
$todayDate      = date('Y-m-d');

foreach ($vouchers as $idx => $v) {
    $used  = (int)($v['usedCount'] ?? 0);
    $limit = max(1, (int)($v['usageLimit'] ?? 1));
    $exp   = $v['expiryDate'] ?? null;
    $isAct = (int)($v['isActive'] ?? 0) === 1;

    $totalUsedCount += $used;

    // Xác định trạng thái thực tế của từng voucher
    if (!$isAct) {
        $vouchers[$idx]['computedStatus'] = 'disabled';
        $vouchers[$idx]['statusLabel']    = 'Tạm ngưng';
        $vouchers[$idx]['badgeClass']     = 'status-badge--disabled';
        $expiredOrOff++;
    } elseif (($exp && $exp < $todayDate) || $used >= $limit) {
        $vouchers[$idx]['computedStatus'] = 'expired';
        $vouchers[$idx]['statusLabel']    = 'Đã hết hạn';
        $vouchers[$idx]['badgeClass']     = 'status-badge--expired';
        $expiredOrOff++;
    } else {
        $vouchers[$idx]['computedStatus'] = 'active';
        $vouchers[$idx]['statusLabel']    = 'Hoạt động';
        $vouchers[$idx]['badgeClass']     = 'status-badge--active';
        $activeVouchers++;
    }
}

include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- PAGE TITLE -->
            <div class="page-title">
                <div>
                    <h1 class="page-title__heading">Quản lý Voucher</h1>
                    <p class="page-title__desc">Tạo và quản lý các mã giảm giá, chương trình khuyến mãi cho khách hàng kết nối trực tiếp Database.
                    </p>
                </div>
                <button class="btn btn--primary" id="btnAddVoucher" type="button">
                    <i class="bx bx-plus"></i> Tạo Voucher mới
                </button>
            </div>

            <!-- THÔNG BÁO THAO TÁC DATABASE -->
            <?php if (!empty($message)): ?>
                <div style="padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-weight: 500; display: flex; align-items: center; gap: 8px;
                    background: <?= $messageType === 'success' ? '#F0FDF4' : ($messageType === 'danger' ? '#FEF2F2' : '#EFF6FF') ?>;
                    color: <?= $messageType === 'success' ? '#15803D' : ($messageType === 'danger' ? '#B91C1C' : '#1D4ED8') ?>;
                    border: 1px solid <?= $messageType === 'success' ? '#BBF7D0' : ($messageType === 'danger' ? '#FECACA' : '#BFDBFE') ?>;">
                    <i class="bx <?= $messageType === 'success' ? 'bx-check-circle' : 'bx-info-circle' ?>" style="font-size: 1.3rem;"></i>
                    <span><?= htmlspecialchars($message) ?></span>
                </div>
            <?php endif; ?>

            <!-- STAT CARDS (Dữ liệu thực từ bảng voucher) -->
            <div class="stat-cards">
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--blue"><i class="bx bx-purchase-tag"></i></div>
                    </div>
                    <p class="stat-card__label">Tổng số Voucher</p>
                    <p class="stat-card__value" id="statTotalVouchers"><?= $totalVouchers ?></p>
                </div>
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--green"><i class="bx bx-check-shield"></i></div>
                        <span class="stat-card__badge stat-card__badge--up">Active</span>
                    </div>
                    <p class="stat-card__label">Đang hoạt động</p>
                    <p class="stat-card__value" id="statActiveVouchers"><?= $activeVouchers ?></p>
                </div>
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--orange"><i class="bx bx-share-alt"></i></div>
                    </div>
                    <p class="stat-card__label">Tổng lượt đã sử dụng</p>
                    <p class="stat-card__value" id="statUsedCount"><?= $totalUsedCount ?></p>
                </div>
                <div class="stat-card">
                    <div class="stat-card__header">
                        <div class="stat-card__icon stat-card__icon--red"><i class="bx bx-time-five"></i></div>
                    </div>
                    <p class="stat-card__label">Hết hạn / Tạm ngưng</p>
                    <p class="stat-card__value" id="statExpiredVouchers"><?= $expiredOrOff ?></p>
                </div>
            </div>

            <!-- VOUCHERS CARD -->
            <div class="vouchers-card">
                <!-- TOOLBAR -->
                <div class="toolbar">
                    <div class="toolbar__left">
                        <div class="toolbar__search">
                            <i class="bx bx-search"></i>
                            <input type="text" id="searchVoucherInput" placeholder="Tìm mã voucher, tên chương trình...">
                        </div>
                        <div class="toolbar__filter">
                            <select class="toolbar__select" id="filterDiscountType">
                                <option value="all">Tất cả loại</option>
                                <option value="PERCENT">Giảm theo %</option>
                                <option value="FIXED">Giảm tiền mặt</option>
                            </select>
                        </div>
                        <div class="toolbar__filter">
                            <select class="toolbar__select" id="filterVoucherStatus">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="disabled">Tạm ngưng</option>
                                <option value="expired">Đã hết hạn</option>
                            </select>
                        </div>
                    </div>
                    <div class="toolbar__right">
                        <button class="toolbar__btn" id="btnExportVoucherCsv" type="button" title="Xuất file CSV">
                            <i class="bx bx-export"></i> Xuất dữ liệu
                        </button>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="table-wrapper">
                    <table class="vouchers-table">
                        <thead>
                            <tr>
                                <th>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" id="checkAll">
                                        <span class="custom-checkbox__mark"></span>
                                    </label>
                                </th>
                                <th>MÃ VOUCHER</th>
                                <th>LOẠI GIẢM GIÁ</th>
                                <th>ĐIỀU KIỆN</th>
                                <th>ĐÃ DÙNG</th>
                                <th>HẠN SỬ DỤNG</th>
                                <th>TRẠNG THÁI</th>
                                <th>HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody id="voucherTableBody">
                            <?php if (!empty($vouchers)): ?>
                                <?php foreach ($vouchers as $v): ?>
                                    <?php
                                        $typeUpper = strtoupper($v['discountType'] ?? 'PERCENT');
                                        $isPercent = ($typeUpper === 'PERCENT');
                                        $valNum    = (float)$v['discountValue'];
                                        $minOrd    = (float)$v['minOrderValue'];
                                        $used      = (int)$v['usedCount'];
                                        $limit     = max(1, (int)$v['usageLimit']);
                                        $pctUsed   = min(100, round(($used / $limit) * 100));
                                        $expText   = !empty($v['expiryDate']) ? date('d/m/Y', strtotime($v['expiryDate'])) : 'Không giới hạn';
                                        $promoName = !empty($v['promotionTitle']) ? $v['promotionTitle'] : 'Ưu đãi chung toàn nhà hàng';
                                        $isActive  = (int)$v['isActive'] === 1;
                                    ?>
                                    <tr class="voucher-row"
                                        data-code="<?= htmlspecialchars(strtolower($v['voucherCode'] . ' ' . $promoName)) ?>"
                                        data-type="<?= htmlspecialchars($typeUpper) ?>"
                                        data-status="<?= htmlspecialchars($v['computedStatus']) ?>">
                                        <td>
                                            <label class="custom-checkbox">
                                                <input type="checkbox" class="row-checkbox" value="<?= $v['voucherId'] ?>">
                                                <span class="custom-checkbox__mark"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="voucher-info">
                                                <div class="voucher-info__code"><?= htmlspecialchars($v['voucherCode']) ?></div>
                                                <p class="voucher-info__name"><?= htmlspecialchars($promoName) ?></p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isPercent): ?>
                                                <span class="type-tag type-tag--percent">Giảm <?= rtrim(rtrim(number_format($valNum, 2, '.', ''), '0'), '.') ?>%</span>
                                                <div class="voucher-subtext">Theo phần trăm hóa đơn</div>
                                            <?php else: ?>
                                                <span class="type-tag type-tag--fixed">Giảm <?= number_format($valNum, 0, ',', '.') ?>đ</span>
                                                <div class="voucher-subtext">Trừ trực tiếp hóa đơn</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $minOrd > 0 ? ('Đơn từ ' . number_format($minOrd, 0, ',', '.') . 'đ') : 'Không giới hạn' ?>
                                        </td>
                                        <td>
                                            <div class="usage-bar">
                                                <div class="usage-bar__text"><?= $used ?> / <?= $limit ?></div>
                                                <div class="usage-bar__track">
                                                    <div class="usage-bar__fill" style="width: <?= $pctUsed ?>%; <?= $pctUsed >= 100 ? 'background: var(--text3);' : '' ?>"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="voucher-date"><?= $expText ?></div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $v['badgeClass'] ?>"><?= $v['statusLabel'] ?></span>
                                        </td>
                                        <td>
                                            <div class="action-btns">
                                                <button type="button" class="action-btn" title="Chỉnh sửa Voucher"
                                                    onclick='openEditVoucherModal(<?= htmlspecialchars(json_encode($v, JSON_UNESCAPED_UNICODE), ENT_QUOTES, "UTF-8") ?>)'>
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                                <a href="<?= $currentVoucherUrl . $querySep ?>action=toggle&id=<?= $v['voucherId'] ?>"
                                                   class="action-btn action-btn--warning"
                                                   title="<?= $isActive ? 'Tạm ngưng Voucher' : 'Kích hoạt lại Voucher' ?>">
                                                    <i class="bx <?= $isActive ? 'bx-pause-circle' : 'bx-play-circle' ?>"></i>
                                                </a>
                                                <a href="<?= $currentVoucherUrl . $querySep ?>action=delete&id=<?= $v['voucherId'] ?>"
                                                   class="action-btn action-btn--danger"
                                                   title="Xóa Voucher"
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa mã Voucher <?= htmlspecialchars(addslashes($v['voucherCode'])) ?>?')">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 36px; color: var(--text3);">
                                        <i class="bx bx-purchase-tag" style="font-size: 2.5rem; display: block; margin-bottom: 8px;"></i>
                                        Chưa có Voucher nào trong cơ sở dữ liệu. Hãy nhấn "Tạo Voucher mới" để thêm!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div class="pagination">
                    <div class="pagination__info" id="voucherPaginationInfo">
                        Hiển thị <strong>1 - <?= $totalVouchers ?></strong> trong <strong><?= $totalVouchers ?></strong> voucher từ Database
                    </div>
                    <div class="pagination__controls">
                        <button class="pagination__btn" disabled><i class="bx bx-chevron-left"></i></button>
                        <button class="pagination__btn pagination__btn--active">1</button>
                        <button class="pagination__btn" disabled><i class="bx bx-chevron-right"></i></button>
                    </div>
                </div>
            </div>

            <!-- MODAL: THÊM / SỬA VOUCHER (Ghi trực tiếp xuống bảng voucher) -->
            <div class="modal-overlay" id="voucherModal">
                <div class="modal">
                    <div class="modal__header">
                        <h2 class="modal__title" id="voucherModalTitle">Tạo Voucher Mới</h2>
                        <button class="modal__close" id="btnCloseModal" type="button"><i class="bx bx-x"></i></button>
                    </div>
                    <form class="modal-form" id="voucherForm" action="<?= $currentVoucherUrl ?>" method="POST">
                        <input type="hidden" name="voucherId" id="formVoucherId" value="0">
                        <div class="modal__body">
                            <!-- General Info -->
                            <h3 class="modal-form__section-title">Thông tin cơ bản</h3>
                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Mã Voucher (Code) <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-barcode-reader"></i>
                                        <input type="text" name="voucherCode" id="formVoucherCode" class="modal-form__input"
                                            placeholder="VD: WELCOME10, VIP20..." style="text-transform: uppercase" required>
                                    </div>
                                </div>
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Thuộc chương trình khuyến mãi</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-gift"></i>
                                        <select name="promotionId" id="formPromotionId" class="modal-form__input">
                                            <option value="">-- Khuyến mãi chung --</option>
                                            <?php foreach ($promotions as $p): ?>
                                                <option value="<?= $p['promotionId'] ?>"><?= htmlspecialchars($p['title']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Discount Type -->
                            <h3 class="modal-form__section-title">Thiết lập giảm giá</h3>
                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Loại giảm giá <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-purchase-tag-alt"></i>
                                        <select name="discountType" class="modal-form__input" id="discountType" required>
                                            <option value="PERCENT">Giảm theo % (PERCENT)</option>
                                            <option value="FIXED">Giảm tiền mặt (FIXED)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-form__group" id="discountValueGroup">
                                    <label class="modal-form__label" id="discountLabel">Mức giảm (%) <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-dollar-circle"></i>
                                        <input type="number" step="any" min="1" name="discountValue" id="formDiscountValue"
                                            class="modal-form__input" placeholder="VD: 15 hoặc 50000" required>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Đơn tối thiểu (VNĐ)</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-up-arrow-circle"></i>
                                        <input type="number" min="0" name="minOrderValue" id="formMinOrderValue"
                                            class="modal-form__input" value="0" placeholder="0 = Không yêu cầu">
                                    </div>
                                </div>
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Số lượng giới hạn (Lượt dùng) <span>*</span></label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-group"></i>
                                        <input type="number" min="1" name="usageLimit" id="formUsageLimit"
                                            class="modal-form__input" value="100" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Condition -->
                            <h3 class="modal-form__section-title">Thời hạn & Trạng thái</h3>
                            <div class="modal-form__row">
                                <div class="modal-form__group">
                                    <label class="modal-form__label">Ngày hết hạn (Expiry Date)</label>
                                    <div class="modal-form__input-wrapper">
                                        <i class="bx bx-calendar-event"></i>
                                        <input type="date" name="expiryDate" id="formExpiryDate" class="modal-form__input">
                                    </div>
                                </div>
                                <div class="modal-form__group" style="display: flex; flex-direction: column; justify-content: flex-end;">
                                    <label class="modal-form__label">Trạng thái kích hoạt</label>
                                    <div class="toggle-group" style="padding: 8px 0;">
                                        <label class="toggle">
                                            <input type="checkbox" name="isActive" id="formIsActive" value="1" checked>
                                            <span class="toggle__slider"></span>
                                        </label>
                                        <span class="toggle-group__text">Kích hoạt sử dụng ngay</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal__footer">
                            <button class="btn btn--ghost" id="btnCancel" type="button">Hủy bỏ</button>
                            <button class="btn btn--primary" type="submit" name="btn_save_voucher">
                                <i class="bx bx-save"></i> Lưu Voucher
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // ---------------------------------------------------------
                // 1. ĐÓNG / MỞ MODAL TẠO MỚI & CHỈNH SỬA VOUCHER
                // ---------------------------------------------------------
                const modal          = document.getElementById('voucherModal');
                const btnAdd         = document.getElementById('btnAddVoucher');
                const btnClose       = document.getElementById('btnCloseModal');
                const btnCancel      = document.getElementById('btnCancel');
                const modalTitle     = document.getElementById('voucherModalTitle');
                const discountTypeEl = document.getElementById('discountType');
                const discountLabel  = document.getElementById('discountLabel');

                function updateDiscountLabel() {
                    if (discountTypeEl.value === 'PERCENT') {
                        discountLabel.innerHTML = 'Mức giảm (%) <span>*</span>';
                    } else {
                        discountLabel.innerHTML = 'Số tiền giảm (VNĐ) <span>*</span>';
                    }
                }
                discountTypeEl.addEventListener('change', updateDiscountLabel);

                function openCreateModal() {
                    modalTitle.textContent = 'Tạo Voucher Mới';
                    document.getElementById('formVoucherId').value     = '0';
                    document.getElementById('formVoucherCode').value   = '';
                    document.getElementById('formPromotionId').value   = '';
                    discountTypeEl.value                               = 'PERCENT';
                    document.getElementById('formDiscountValue').value = '';
                    document.getElementById('formMinOrderValue').value = '0';
                    document.getElementById('formUsageLimit').value    = '100';
                    document.getElementById('formExpiryDate').value    = '2026-12-31';
                    document.getElementById('formIsActive').checked    = true;
                    updateDiscountLabel();

                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                function openEditVoucherModal(voucher) {
                    modalTitle.textContent = 'Chỉnh sửa Voucher: ' + voucher.voucherCode;
                    document.getElementById('formVoucherId').value     = voucher.voucherId;
                    document.getElementById('formVoucherCode').value   = voucher.voucherCode || '';
                    document.getElementById('formPromotionId').value   = voucher.promotionId || '';
                    discountTypeEl.value                               = (voucher.discountType || 'PERCENT').toUpperCase();
                    document.getElementById('formDiscountValue').value = parseFloat(voucher.discountValue || 0);
                    document.getElementById('formMinOrderValue').value = parseFloat(voucher.minOrderValue || 0);
                    document.getElementById('formUsageLimit').value    = parseInt(voucher.usageLimit || 100);
                    document.getElementById('formExpiryDate').value    = voucher.expiryDate || '';
                    document.getElementById('formIsActive').checked    = parseInt(voucher.isActive) === 1;
                    updateDiscountLabel();

                    modal.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }

                btnAdd.addEventListener('click', openCreateModal);
                btnClose.addEventListener('click', closeModal);
                btnCancel.addEventListener('click', closeModal);
                modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

                // ---------------------------------------------------------
                // 2. BỘ LỌC TÌM KIẾM, LOẠI GIẢM GIÁ & TRẠNG THÁI THỜI GIAN THỰC
                // ---------------------------------------------------------
                const searchInput   = document.getElementById('searchVoucherInput');
                const headerSearch  = document.getElementById('headerGlobalSearch');
                const filterType    = document.getElementById('filterDiscountType');
                const filterStatus  = document.getElementById('filterVoucherStatus');
                const voucherRows   = document.querySelectorAll('#voucherTableBody .voucher-row');
                const pageInfo      = document.getElementById('voucherPaginationInfo');

                function filterVoucherTable() {
                    const kw     = searchInput.value.toLowerCase().trim();
                    const type   = filterType.value;
                    const status = filterStatus.value;
                    let visible  = 0;

                    voucherRows.forEach(row => {
                        const matchKw     = !kw || row.dataset.code.includes(kw);
                        const matchType   = (type === 'all') || (row.dataset.type === type);
                        const matchStatus = (status === 'all') || (row.dataset.status === status);

                        if (matchKw && matchType && matchStatus) {
                            row.style.display = '';
                            visible++;
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    if (pageInfo) {
                        pageInfo.innerHTML = `Hiển thị <strong>${visible}</strong> trong tổng số <strong>${voucherRows.length}</strong> voucher từ Database`;
                    }
                }

                searchInput.addEventListener('input', filterVoucherTable);
                filterType.addEventListener('change', filterVoucherTable);
                filterStatus.addEventListener('change', filterVoucherTable);

                if (headerSearch) {
                    headerSearch.addEventListener('input', function () {
                        searchInput.value = this.value;
                        filterVoucherTable();
                    });
                }

                // ---------------------------------------------------------
                // 3. CHỌN TẤT CẢ & XUẤT DỮ LIỆU CSV
                // ---------------------------------------------------------
                const checkAll = document.getElementById('checkAll');
                const rowChecks = document.querySelectorAll('.row-checkbox');
                if (checkAll) {
                    checkAll.addEventListener('change', () => {
                        rowChecks.forEach(cb => cb.checked = checkAll.checked);
                    });
                }

                document.getElementById('btnExportVoucherCsv').addEventListener('click', function () {
                    let csv = "data:text/csv;charset=utf-8,\uFEFF";
                    csv += "Mã Voucher,Chương trình,Loại giảm,Điều kiện,Đã dùng,Hạn sử dụng,Trạng thái\n";

                    voucherRows.forEach(row => {
                        if (row.style.display === 'none') return;
                        const code  = row.querySelector('.voucher-info__code')?.innerText.trim() || '';
                        const name  = row.querySelector('.voucher-info__name')?.innerText.trim() || '';
                        const type  = row.querySelector('.type-tag')?.innerText.trim() || '';
                        const cond  = row.children[3]?.innerText.trim() || '';
                        const used  = row.querySelector('.usage-bar__text')?.innerText.trim() || '';
                        const exp   = row.querySelector('.voucher-date')?.innerText.trim() || '';
                        const st    = row.querySelector('.status-badge')?.innerText.trim() || '';
                        csv += `"${code}","${name}","${type}","${cond}","${used}","${exp}","${st}"\n`;
                    });

                    const link = document.createElement('a');
                    link.setAttribute('href', encodeURI(csv));
                    link.setAttribute('download', `Danh_sach_voucher_${new Date().toISOString().slice(0, 10)}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    link.remove();
                });

                // Đồng hồ trên thanh Header
                function updateClock() {
                    const now = new Date();
                    const el = document.getElementById('clockTime');
                    if (el) el.textContent = now.toTimeString().slice(0, 8);
                }
                setInterval(updateClock, 1000);
                updateClock();
            </script>

    <!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
    </main>
  </div>

</body>

</html>