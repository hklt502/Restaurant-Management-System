<?php
$pageTitle = 'Trang Cá Nhân — Nhóm 3';
$activePage = 'canhan';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';

// Tính số lượng voucher khả dụng từ danh sách $vouchers
$activeVoucherCount = 0;
if (!empty($vouchers)) {
    foreach ($vouchers as $v) {
        if ($v['status'] === 'UNUSED') {
            $activeVoucherCount++;
        }
    }
}
?>

  <!-- ===================== CỘT TRÁI + NỘI DUNG ===================== -->
  <div class="account">

    <!-- ---------- Cột trái: thông tin thành viên và menu dọc ---------- -->
    <div>
      <div class="user-card">
        <span class="avatar">👤</span>
        <div>
          <span class="user-name"><?= htmlspecialchars($customer['customerName'] ?? 'Thành viên') ?></span>
          <span class="user-role">Hội viên Nhóm 3</span>
        </div>
      </div>

      <div class="account-menu">
        <a class="active" href="index.php?page=canhan">Thông Tin Cá Nhân</a>
        <a href="index.php?page=doimatkhau">Đổi Mật Khẩu</a>
        <a href="index.php?page=dangxuat">Đăng Xuất</a>
      </div>
    </div>

    <!-- ---------- Cột phải: hồ sơ cá nhân ---------- -->
    <div class="account-content">
      <p class="eyebrow">Thành viên Nhóm 3</p>

      <h2>Hồ Sơ Cá Nhân</h2>
      <p class="account-note">Quản lý thông tin tài khoản, ví ưu đãi và lịch sử đặt bàn của bạn.</p>

      <h3 class="block-title">Thông tin cá nhân</h3>

      <div class="profile-grid">
        <div class="profile-item">
          <label>Họ và tên</label>
          <p><?= htmlspecialchars($customer['customerName'] ?? 'Chưa cập nhật') ?></p>
        </div>

        <div class="profile-item">
          <label>Email</label>
          <p><?= htmlspecialchars($customer['customerEmail'] ?? 'Chưa cập nhật') ?></p>
        </div>

        <div class="profile-item">
          <label>Số điện thoại</label>
          <p><?= htmlspecialchars($customer['customerPhone'] ?? 'Chưa cập nhật') ?></p>
        </div>

        <div class="profile-item">
          <label>Ngày sinh</label>
          <p><?= !empty($customer['birthDate']) ? date('d/m/Y', strtotime($customer['birthDate'])) : 'Chưa cập nhật' ?></p>
        </div>

        <div class="profile-item profile-item-full">
          <label>Địa chỉ</label>
          <p><?= htmlspecialchars($customer['address'] ?? 'Chưa cập nhật') ?></p>
        </div>
      </div>

      <h3 class="block-title">Hoạt động của bạn</h3>

      <div class="mini-stats">
        <div class="mini-stat">
          <p class="mini-stat-number"><?= (int)($customer['visitCount'] ?? 0) ?></p>
          <p class="mini-stat-title">Lượt ghé thăm</p>
        </div>

        <div class="mini-stat">
          <p class="mini-stat-number"><?= $activeVoucherCount ?></p>
          <p class="mini-stat-title">Voucher khả dụng</p>
        </div>
      </div>

      <!-- ---------- VÍ VOUCHER ---------- -->
      <h3 class="block-title">Ví Voucher của bạn</h3>
      <?php if (!empty($vouchers)): ?>
        <div class="voucher-list">
          <?php foreach ($vouchers as $v): ?>
            <?php $isUnused = ($v['status'] === 'UNUSED'); ?>
            <div class="voucher-card <?= $isUnused ? '' : 'voucher-card--used' ?>">
              <span class="voucher-card__status"><?= $isUnused ? 'Khả dụng' : 'Đã sử dụng' ?></span>
              <p class="voucher-card__code"><?= htmlspecialchars($v['voucherCode']) ?></p>
              <p class="voucher-card__discount">
                Giảm <?= $v['discountType'] === 'PERCENT' ? (float)$v['discountValue'] . '%' : number_format($v['discountValue'], 0, ',', '.') . '₫' ?>
              </p>
              <p class="voucher-card__condition">Đơn tối thiểu <?= number_format($v['minOrderValue'], 0, ',', '.') ?>₫</p>
              <?php if (!empty($v['expiryDate'])): ?>
                <p class="voucher-card__expiry">Hạn dùng: <?= date('d/m/Y', strtotime($v['expiryDate'])) ?></p>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="account-empty">Bạn chưa có mã ưu đãi nào trong ví.</p>
      <?php endif; ?>

      <!-- ---------- LỊCH SỬ ĐẶT BÀN ---------- -->
      <h3 class="block-title">Lịch sử đặt bàn</h3>
      <?php
      $statusLabels = ['PENDING' => 'Chờ xác nhận', 'CONFIRMED' => 'Đã xác nhận', 'CANCELLED' => 'Đã hủy'];
      ?>
      <?php if (!empty($reservations)): ?>
        <div class="reservation-list">
          <?php foreach ($reservations as $res): ?>
            <div class="reservation-card">
              <div>
                <p class="reservation-card__title">
                  <?= date('d/m/Y', strtotime($res['reservationDate'])) ?> lúc <?= substr($res['reservationTime'], 0, 5) ?> · <?= (int)$res['guestCount'] ?> khách
                </p>
                <p class="reservation-card__meta">
                  Chi nhánh: <?= htmlspecialchars($res['branch'] ?? 'Lê Lợi Q1') ?>
                  <span class="reservation-status reservation-status--<?= strtolower($res['status']) ?>"><?= $statusLabels[$res['status']] ?? $res['status'] ?></span>
                </p>
              </div>
              <?php if ($res['status'] === 'PENDING'): ?>
                <form action="index.php?page=huy-datban" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đặt bàn này không?')">
                  <input type="hidden" name="reservationId" value="<?= (int)$res['reservationId'] ?>">
                  <button type="submit" class="button button-danger">Hủy bàn</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="account-empty">Bạn chưa có đơn đặt bàn nào.</p>
      <?php endif; ?>

      <div class="notice">
        <h3>Xóa tài khoản</h3>
        <p>Khi xóa tài khoản, mọi dữ liệu sẽ không thể khôi phục. Hãy cân nhắc kỹ trước khi thực hiện.</p>
        <button type="button" class="button button-danger">Xóa Tài Khoản</button>
      </div>
    </div>

  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>
