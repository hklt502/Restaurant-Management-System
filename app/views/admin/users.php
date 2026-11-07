<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-users');
    exit;
}

$pageTitle  = "Quản lý người dùng - Nhóm 3";
$pageCss    = "public/asset/css/admin/users.css";
$activePage = "users";

$users         = $users ?? [];
$statTotal     = $statTotal ?? 0;
$statActive    = $statActive ?? 0;
$statLocked    = $statLocked ?? 0;
$statNewMonth  = $statNewMonth ?? 0;
$currentPage   = $currentPage ?? 1;
$totalPages    = $totalPages ?? 1;
$startItem     = $startItem ?? 0;
$endItem       = $endItem ?? 0;
$totalUsers    = $totalUsers ?? 0;
$prevPageUrl   = $prevPageUrl ?? null;
$nextPageUrl   = $nextPageUrl ?? null;
$searchKeyword = $searchKeyword ?? '';
$filterRole    = $filterRole ?? '';
$filterStatus  = $filterStatus ?? '';
$message       = $message ?? '';
$messageType   = $messageType ?? 'success';

include_once __DIR__ . '/../layouts/admin_header.php';
?>

<!-- PAGE TITLE -->
<div class="page-title">
    <div>
        <h1 class="page-title__heading">Quản lý người dùng</h1>
        <p class="page-title__desc">Quản lý tài khoản quản trị, khách hàng và phân quyền hệ thống.</p>
    </div>
    <button class="btn btn--primary" id="btnAddUser" type="button">
        <i class="bx bx-plus"></i> Thêm người dùng
    </button>
</div>

<!-- THÔNG BÁO HOẠT ĐỘNG (NẾU CÓ) -->
<?php if (!empty($message)): ?>
    <div style="padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-weight: 500; display: flex; align-items: center; gap: 8px;
        background: <?= $messageType === 'success' ? '#F0FDF4' : ($messageType === 'danger' ? '#FEF2F2' : '#EFF6FF') ?>;
        color: <?= $messageType === 'success' ? '#15803D' : ($messageType === 'danger' ? '#B91C1C' : '#1D4ED8') ?>;
        border: 1px solid <?= $messageType === 'success' ? '#BBF7D0' : ($messageType === 'danger' ? '#FECACA' : '#BFDBFE') ?>;">
        <i class="bx <?= $messageType === 'success' ? 'bx-check-circle' : ($messageType === 'danger' ? 'bx-error-circle' : 'bx-info-circle') ?>" style="font-size: 1.3rem;"></i>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
<?php endif; ?>

<!-- STAT CARDS (DỮ LIỆU THỰC TẾ THEO BẢNG) -->
<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-card__header">
            <div class="stat-card__icon stat-card__icon--blue"><i class="bx bx-group"></i></div>
            <span class="stat-card__badge stat-card__badge--up">Tổng</span>
        </div>
        <p class="stat-card__label">Tổng người dùng</p>
        <p class="stat-card__value"><?= $statTotal ?></p>
    </div>
    <div class="stat-card">
        <div class="stat-card__header">
            <div class="stat-card__icon stat-card__icon--green"><i class="bx bx-check-circle"></i></div>
        </div>
        <p class="stat-card__label">Đang hoạt động</p>
        <p class="stat-card__value"><?= $statActive ?></p>
    </div>
    <div class="stat-card">
        <div class="stat-card__header">
            <div class="stat-card__icon stat-card__icon--orange"><i class="bx bx-user-plus"></i></div>
            <span class="stat-card__badge stat-card__badge--up">+<?= $statNewMonth ?></span>
        </div>
        <p class="stat-card__label">Mới trong tháng</p>
        <p class="stat-card__value"><?= $statNewMonth ?></p>
    </div>
    <div class="stat-card">
        <div class="stat-card__header">
            <div class="stat-card__icon stat-card__icon--red"><i class="bx bx-block"></i></div>
        </div>
        <p class="stat-card__label">Bị khóa</p>
        <p class="stat-card__value"><?= $statLocked ?></p>
    </div>
</div>

<!-- TOOLBAR VÀ DANH SÁCH BẢNG -->
<div class="users-card">
    <form class="toolbar" method="GET" action="index.php" id="filterForm">
        <input type="hidden" name="page" value="admin-users">
        <div class="toolbar__left">
            <div class="toolbar__search">
                <i class="bx bx-search"></i>
                <input type="text" name="search" id="searchInput" placeholder="Tìm theo tên, email, SĐT, mã..." value="<?= htmlspecialchars($searchKeyword ?? '') ?>">
            </div>
            <!-- Lọc theo 2 vai trò tương ứng 2 bảng dữ liệu: Quản trị và Khách hàng -->
            <div class="toolbar__filter">
                <select class="toolbar__select" name="role" id="roleFilter" onchange="this.form.submit()">
                    <option value="">Tất cả vai trò</option>
                    <option value="Quản trị" <?= ($filterRole === 'Quản trị') ? 'selected' : '' ?>>Quản trị</option>
                    <option value="Khách hàng" <?= ($filterRole === 'Khách hàng') ? 'selected' : '' ?>>Khách hàng</option>
                </select>
            </div>
            <!-- Lọc theo trạng thái -->
            <div class="toolbar__filter">
                <select class="toolbar__select" name="status" id="statusFilter" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= ($filterStatus === 'active') ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="locked" <?= ($filterStatus === 'locked') ? 'selected' : '' ?>>Bị khóa</option>
                </select>
            </div>
        </div>
        <div class="toolbar__right">
            <button class="toolbar__btn" type="submit" title="Áp dụng bộ lọc">
                <i class="bx bx-filter-alt"></i> Lọc
            </button>
            <?php if (!empty($searchKeyword) || !empty($filterRole) || !empty($filterStatus)): ?>
                <a href="index.php?page=admin-users" class="toolbar__btn" style="text-decoration: none; color: var(--text2);" title="Đặt lại bộ lọc">
                    <i class="bx bx-reset"></i> Đặt lại
                </a>
            <?php endif; ?>
        </div>
    </form>

    <!-- TABLE -->
    <div class="table-wrapper">
        <table class="users-table">
            <thead>
                <tr>
                    <th>
                        <label class="custom-checkbox">
                            <input type="checkbox" id="checkAll">
                            <span class="custom-checkbox__mark"></span>
                        </label>
                    </th>
                    <th>NGƯỜI DÙNG</th>
                    <th>EMAIL / TÊN ĐĂNG NHẬP</th>
                    <th>SỐ ĐIỆN THOẠI</th>
                    <th>VAI TRÒ</th>
                    <th>TRẠNG THÁI</th>
                    <th>NGÀY TẠO</th>
                    <th style="text-align: right;">HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text3);">
                            <i class="bx bx-user-x" style="font-size: 3rem; display: block; margin-bottom: 8px; color: var(--border);"></i>
                            Không tìm thấy người dùng nào phù hợp với bộ lọc!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php
                    $avatarColors = ['blue', 'green', 'purple', 'orange', 'red'];
                    foreach ($users as $u):
                        $cleanName = trim($u['name']);
                        $parts = preg_split('/\s+/', $cleanName);
                        if (count($parts) > 1) {
                            $initials = mb_substr($parts[0], 0, 1, 'UTF-8') . mb_substr(end($parts), 0, 1, 'UTF-8');
                        } else {
                            $initials = mb_substr($cleanName, 0, 2, 'UTF-8');
                        }
                        $initials = mb_strtoupper($initials, 'UTF-8');
                        $colorClass = 'user-info__avatar--' . $avatarColors[$u['id'] % count($avatarColors)];
                    ?>
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox" class="user-row-check" value="<?= $u['id'] ?>">
                                    <span class="custom-checkbox__mark"></span>
                                </label>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-info__avatar <?= $colorClass ?>"><?= $initials ?></div>
                                    <div>
                                        <p class="user-info__name"><?= htmlspecialchars($u['name']) ?></p>
                                        <p class="user-info__id"><?= htmlspecialchars($u['code']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['phone']) ?></td>
                            <td><span class="role-badge role-badge--<?= $u['type'] ?>"><?= htmlspecialchars($u['role']) ?></span></td>
                            <td><span class="status-badge status-badge--<?= $u['status'] ?>"><?= $u['status'] === 'locked' ? 'Bị khóa' : 'Hoạt động' ?></span></td>
                            <td><?= htmlspecialchars($u['createdAt']) ?></td>
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end;">
                                    <button class="action-btn" type="button" title="Xem chi tiết" onclick='viewUserDetail(<?= json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <i class="bx bx-show"></i>
                                    </button>
                                    <button class="action-btn" type="button" title="Chỉnh sửa" onclick='editUser(<?= json_encode($u, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <i class="bx bx-edit-alt"></i>
                                    </button>
                                    <?php if ($u['status'] === 'active'): ?>
                                        <a href="index.php?page=admin-users&action=toggle&type=<?= $u['type'] ?>&id=<?= $u['id'] ?>" class="action-btn action-btn--danger" title="Khóa tài khoản" onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">
                                            <i class="bx bx-lock-alt"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?page=admin-users&action=toggle&type=<?= $u['type'] ?>&id=<?= $u['id'] ?>" class="action-btn action-btn--success" title="Mở khóa tài khoản" onclick="return confirm('Mở khóa tài khoản này?')">
                                            <i class="bx bx-lock-open-alt"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="index.php?page=admin-users&action=delete&type=<?= $u['type'] ?>&id=<?= $u['id'] ?>" class="action-btn" style="color: var(--red);" title="Xóa người dùng" onclick="return confirm('Bạn có chắc muốn xóa người dùng <?= htmlspecialchars($u['name']) ?>?')">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG (PAGINATION) - CHỈ CÓ NEXT / PREV THEO YÊU CẦU -->
    <div class="pagination">
        <div class="pagination__info">
            Hiển thị <strong><?= $startItem ?> - <?= $endItem ?></strong> trong <strong><?= $totalUsers ?></strong> người dùng
            <?php if ($totalPages > 1): ?>
                (Trang <strong><?= $currentPage ?></strong> / <strong><?= $totalPages ?></strong>)
            <?php endif; ?>
        </div>
        <div class="pagination__controls">
            <!-- NÚT PREV (TRANG TRƯỚC) -->
            <button class="pagination__btn" <?= $prevPageUrl ? "onclick=\"location.href='{$prevPageUrl}'\"" : "disabled" ?> id="btnPrevPage" type="button" title="Trang trước">
                <i class="bx bx-chevron-left"></i>
            </button>

            <!-- NHÃN TRANG HIỆN TẠI -->
            <span class="pagination__current-label" style="font-size: 0.88rem; font-weight: 600; padding: 0 12px; color: var(--text);">
                Trang <?= $currentPage ?> / <?= $totalPages ?>
            </span>

            <!-- NÚT NEXT (TRANG SAU) -->
            <button class="pagination__btn" <?= $nextPageUrl ? "onclick=\"location.href='{$nextPageUrl}'\"" : "disabled" ?> id="btnNextPage" type="button" title="Trang kế tiếp">
                <i class="bx bx-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 1: THÊM NGƯỜI DÙNG MỚI (VAI TRÒ: QUẢN TRỊ HOẶC KHÁCH HÀNG)
     ========================================================================== -->
<div class="modal-overlay" id="userModal">
    <div class="modal">
        <div class="modal__header">
            <h2 class="modal__title">Thêm người dùng mới</h2>
            <button class="modal__close" id="btnCloseModal" type="button"><i class="bx bx-x"></i></button>
        </div>
        <div class="modal__body">
            <form class="modal-form" id="userForm" method="POST" action="index.php?page=admin-users">
                <input type="hidden" name="btn_add_user" value="1">
                <div class="modal-form__row">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Họ và tên <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-user"></i>
                            <input type="text" name="fullname" class="modal-form__input" placeholder="Nhập họ và tên" required>
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Vai trò <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-shield-quarter"></i>
                            <select name="role" id="modalRoleSelect" class="modal-form__input" required onchange="handleRoleChange(this.value)">
                                <option value="Khách hàng" selected>Khách hàng</option>
                                <option value="Quản trị">Quản trị</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-form__row">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Số điện thoại <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-phone"></i>
                            <input type="tel" name="phone" class="modal-form__input" placeholder="Nhập số điện thoại" required>
                        </div>
                    </div>
                    <div class="modal-form__group" id="groupUsername" style="display: none;">
                        <label class="modal-form__label">Tên đăng nhập (Username) <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-id-card"></i>
                            <input type="text" name="username" id="inputUsername" class="modal-form__input" placeholder="VD: admin02">
                        </div>
                    </div>
                    <div class="modal-form__group" id="groupEmail">
                        <label class="modal-form__label">Email</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-envelope"></i>
                            <input type="email" name="email" class="modal-form__input" placeholder="Nhập địa chỉ email">
                        </div>
                    </div>
                </div>
                <div class="modal-form__row">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Mật khẩu <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-lock-alt"></i>
                            <input type="password" name="password" id="inputPassword" class="modal-form__input" placeholder="Nhập mật khẩu" required minlength="4">
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Xác nhận mật khẩu <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-lock"></i>
                            <input type="password" name="confirm_password" id="inputConfirmPassword" class="modal-form__input" placeholder="Nhập lại mật khẩu" required minlength="4">
                        </div>
                    </div>
                </div>
                <div class="modal-form__group">
                    <label class="modal-form__label">Trạng thái ban đầu</label>
                    <div class="toggle-group">
                        <label class="toggle">
                            <input type="checkbox" name="status" checked>
                            <span class="toggle__slider"></span>
                        </label>
                        <span class="toggle-group__text">Kích hoạt tài khoản ngay</span>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--ghost" id="btnCancel" type="button">Hủy bỏ</button>
            <button class="btn btn--primary" type="submit" form="userForm">
                <i class="bx bx-save"></i> Lưu người dùng
            </button>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 2: CHỈNH SỬA THÔNG TIN NGƯỜI DÙNG
     ========================================================================== -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal">
        <div class="modal__header">
            <h2 class="modal__title">Chỉnh sửa thông tin người dùng</h2>
            <button class="modal__close" id="btnCloseEditModal" type="button"><i class="bx bx-x"></i></button>
        </div>
        <div class="modal__body">
            <form class="modal-form" id="editUserForm" method="POST" action="index.php?page=admin-users">
                <input type="hidden" name="btn_edit_user" value="1">
                <input type="hidden" name="userId" id="editUserId">
                <input type="hidden" name="userType" id="editUserType">
                <div class="modal-form__row">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Mã tài khoản</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-hash"></i>
                            <input type="text" id="editUserCode" class="modal-form__input" readonly style="background: #f1f5f9;">
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Vai trò</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-shield-quarter"></i>
                            <input type="text" id="editUserRoleName" class="modal-form__input" readonly style="background: #f1f5f9;">
                        </div>
                    </div>
                </div>
                <div class="modal-form__row">
                    <div class="modal-form__group">
                        <label class="modal-form__label">Họ và tên <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-user"></i>
                            <input type="text" name="fullname" id="editFullname" class="modal-form__input" required>
                        </div>
                    </div>
                    <div class="modal-form__group">
                        <label class="modal-form__label">Số điện thoại <span>*</span></label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-phone"></i>
                            <input type="tel" name="phone" id="editPhone" class="modal-form__input" required>
                        </div>
                    </div>
                </div>
                <div class="modal-form__row" id="editRowCredentials">
                    <div class="modal-form__group" id="editGroupUsername">
                        <label class="modal-form__label">Tên đăng nhập</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-id-card"></i>
                            <input type="text" name="username" id="editUsername" class="modal-form__input">
                        </div>
                    </div>
                    <div class="modal-form__group" id="editGroupEmail">
                        <label class="modal-form__label">Email</label>
                        <div class="modal-form__input-wrapper">
                            <i class="bx bx-envelope"></i>
                            <input type="email" name="email" id="editEmail" class="modal-form__input">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal__footer">
            <button class="btn btn--ghost" id="btnCancelEdit" type="button">Hủy bỏ</button>
            <button class="btn btn--primary" type="submit" form="editUserForm">
                <i class="bx bx-check"></i> Cập nhật thông tin
            </button>
        </div>
    </div>
</div>

<!-- ==========================================================================
     MODAL 3: XEM CHI TIẾT NGƯỜI DÙNG
     ========================================================================== -->
<div class="modal-overlay" id="detailUserModal">
    <div class="modal">
        <div class="modal__header">
            <h2 class="modal__title">Chi tiết người dùng</h2>
            <button class="modal__close" id="btnCloseDetailModal" type="button"><i class="bx bx-x"></i></button>
        </div>
        <div class="modal__body" style="padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <div id="detailAvatar" style="width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; font-weight: 700; color: #fff; background: var(--primary-400);">TN</div>
                <div>
                    <h3 id="detailName" style="font-size: 1.2rem; font-weight: 700; color: var(--text); margin-bottom: 4px;">Nguyễn Văn A</h3>
                    <p id="detailCode" style="color: var(--text3); font-size: 0.85rem;">#ADM001</p>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; background: var(--bg); padding: 16px; border-radius: var(--r-sm);">
                <div>
                    <span style="font-size: 0.8rem; color: var(--text3);">Vai trò:</span>
                    <p id="detailRole" style="font-weight: 600; margin-top: 2px;">Quản trị</p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text3);">Trạng thái:</span>
                    <p id="detailStatus" style="font-weight: 600; margin-top: 2px;">Hoạt động</p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text3);">Số điện thoại:</span>
                    <p id="detailPhone" style="font-weight: 600; margin-top: 2px;">0901 234 567</p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text3);">Email / Username:</span>
                    <p id="detailEmail" style="font-weight: 600; margin-top: 2px;">admin01</p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text3);">Ngày tạo tài khoản:</span>
                    <p id="detailCreatedAt" style="font-weight: 600; margin-top: 2px;">15/01/2026</p>
                </div>
                <div>
                    <span style="font-size: 0.8rem; color: var(--text3);">Bảng dữ liệu:</span>
                    <p id="detailType" style="font-weight: 600; margin-top: 2px;">admin</p>
                </div>
            </div>
        </div>
        <div class="modal__footer">
            <button class="btn btn--primary" id="btnCloseDetailModal2" type="button">Đóng</button>
        </div>
    </div>
</div>

<script>
    // ==========================================
    // 1. QUẢN LÝ MODAL THÊM NGƯỜI DÙNG
    // ==========================================
    const modalAdd = document.getElementById('userModal');
    const btnAdd = document.getElementById('btnAddUser');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCancel = document.getElementById('btnCancel');

    function openAddModal() {
        modalAdd.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeAddModal() {
        modalAdd.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (btnAdd) btnAdd.addEventListener('click', openAddModal);
    if (btnCloseModal) btnCloseModal.addEventListener('click', closeAddModal);
    if (btnCancel) btnCancel.addEventListener('click', closeAddModal);
    if (modalAdd) {
        modalAdd.addEventListener('click', (e) => {
            if (e.target === modalAdd) closeAddModal();
        });
    }

    // Điều khiển hiển thị trường Username tùy theo Vai trò (Quản trị hoặc Khách hàng)
    function handleRoleChange(role) {
        const groupUsername = document.getElementById('groupUsername');
        const inputUsername = document.getElementById('inputUsername');
        if (role === 'Quản trị' || role === 'admin') {
            groupUsername.style.display = 'block';
            inputUsername.required = true;
        } else {
            groupUsername.style.display = 'none';
            inputUsername.required = false;
        }
    }

    // Kiểm tra khớp mật khẩu khi thêm mới
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            const p1 = document.getElementById('inputPassword').value;
            const p2 = document.getElementById('inputConfirmPassword').value;
            if (p1 !== p2) {
                e.preventDefault();
                alert('Mật khẩu và xác nhận mật khẩu không khớp!');
            }
        });
    }

    // ==========================================
    // 2. MODAL CHỈNH SỬA NGƯỜI DÙNG
    // ==========================================
    const editModal = document.getElementById('editUserModal');
    const btnCloseEditModal = document.getElementById('btnCloseEditModal');
    const btnCancelEdit = document.getElementById('btnCancelEdit');

    function closeEditModal() {
        editModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    if (btnCloseEditModal) btnCloseEditModal.addEventListener('click', closeEditModal);
    if (btnCancelEdit) btnCancelEdit.addEventListener('click', closeEditModal);
    if (editModal) {
        editModal.addEventListener('click', (e) => {
            if (e.target === editModal) closeEditModal();
        });
    }

    function editUser(u) {
        document.getElementById('editUserId').value = u.id;
        document.getElementById('editUserType').value = u.type;
        document.getElementById('editUserCode').value = u.code;
        document.getElementById('editUserRoleName').value = u.role;
        document.getElementById('editFullname').value = u.name;
        document.getElementById('editPhone').value = (u.phone && u.phone !== 'Chưa có SĐT') ? u.phone : '';
        document.getElementById('editUsername').value = u.type === 'admin' ? u.email : '';
        document.getElementById('editEmail').value = (u.type === 'customer' && u.email !== 'Chưa cập nhật') ? u.email : '';

        document.getElementById('editGroupUsername').style.display = (u.type === 'admin') ? 'block' : 'none';
        document.getElementById('editGroupEmail').style.display = (u.type === 'admin') ? 'none' : 'block';

        editModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // ==========================================
    // 3. MODAL XEM CHI TIẾT NGƯỜI DÙNG
    // ==========================================
    const detailModal = document.getElementById('detailUserModal');
    const btnCloseDetail1 = document.getElementById('btnCloseDetailModal');
    const btnCloseDetail2 = document.getElementById('btnCloseDetailModal2');

    function closeDetailModal() {
        detailModal.classList.remove('active');
        document.body.style.overflow = '';
    }
    if (btnCloseDetail1) btnCloseDetail1.addEventListener('click', closeDetailModal);
    if (btnCloseDetail2) btnCloseDetail2.addEventListener('click', closeDetailModal);
    if (detailModal) {
        detailModal.addEventListener('click', (e) => {
            if (e.target === detailModal) closeDetailModal();
        });
    }

    function viewUserDetail(u) {
        const parts = u.name.trim().split(/\s+/);
        let initials = parts.length > 1 ? (parts[0][0] + parts[parts.length - 1][0]) : u.name.slice(0, 2);
        initials = initials.toUpperCase();

        document.getElementById('detailAvatar').textContent = initials;
        document.getElementById('detailName').textContent = u.name;
        document.getElementById('detailCode').textContent = u.code;
        document.getElementById('detailRole').textContent = u.role;
        document.getElementById('detailStatus').textContent = (u.status === 'locked') ? 'Bị khóa' : 'Hoạt động';
        document.getElementById('detailPhone').textContent = u.phone;
        document.getElementById('detailEmail').textContent = u.email;
        document.getElementById('detailCreatedAt').textContent = u.createdAt;
        document.getElementById('detailType').textContent = (u.type === 'admin') ? 'Bảng admin (Quản trị)' : 'Bảng customer (Khách hàng)';

        detailModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // ==========================================
    // 4. CHECKBOX CHỌN TẤT CẢ
    // ==========================================
    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.user-row-check');
    if (checkAll) {
        checkAll.addEventListener('change', () => {
            rowChecks.forEach(cb => cb.checked = checkAll.checked);
        });
    }
</script>

<!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
</main>
</div>

</body>

</html>