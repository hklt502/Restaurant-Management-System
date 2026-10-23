<?php
$pageTitle = 'Trang Cá Nhân — Nhóm 3';
$activePage = 'canhan';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

  <!-- ===================== CỘT TRÁI + NỘI DUNG ===================== -->
  <div class="account">

    <!-- ---------- Cột trái: thông tin thành viên và menu dọc ---------- -->
    <div>
      <div class="user-card">
        <span class="avatar">👤</span>
        <div>
          <span class="user-name">Nguyễn Văn A</span>
          <span class="user-role">Thành viên</span>
        </div>
      </div>

      <div class="account-menu">
        <a class="active" href="canhan.php">Thông Tin Cá Nhân</a>
        <a href="doimatkhau.php">Đổi Mật Khẩu</a>
        <a href="dangnhap.php">Đăng Xuất</a>
      </div>
    </div>

    <!-- ---------- Cột phải: hồ sơ cá nhân ---------- -->
    <div class="account-content">
      <p class="eyebrow">Thành viên Nhóm 3</p>

      <h2>Hồ Sơ Cá Nhân</h2>
      <p class="account-note">Quản lý thông tin tài khoản và bảo mật của bạn.</p>

      <h3 class="block-title">Thông tin cá nhân</h3>

      <div class="profile-grid">
        <div class="profile-item">
          <label>Họ và tên</label>
          <p>Nguyễn Văn A</p>
        </div>

        <div class="profile-item">
          <label>Email</label>
          <p>nguyenvana@example.com</p>
        </div>

        <div class="profile-item">
          <label>Số điện thoại</label>
          <p>0901 234 567</p>
        </div>

        <div class="profile-item">
          <label>Ngày sinh</label>
          <p>01/06/2000</p>
        </div>

        <div class="profile-item profile-item-full">
          <label>Địa chỉ</label>
          <p>123 Đường Lê Lợi, Phường Bến Thành, Quận 1, TP. Hồ Chí Minh</p>
        </div>
      </div>

      <h3 class="block-title">Hoạt động của bạn</h3>

      <div class="mini-stats">
        <div class="mini-stat">
          <p class="mini-stat-number">24</p>
          <p class="mini-stat-title">Lượt ghé thăm</p>
        </div>

        <div class="mini-stat">
          <p class="mini-stat-number">12</p>
          <p class="mini-stat-title">Voucher khả dụng</p>
        </div>
      </div>

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

