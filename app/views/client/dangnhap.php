<?php
$pageTitle = 'Đăng Nhập — Nhóm 3';
$activePage = 'dangnhap';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

  <!-- ===================== FORM ĐĂNG NHẬP ===================== -->
  <div class="auth">
    <div class="auth-box">
      <p class="eyebrow">Thành viên Nhóm 3</p>

      <h1>Chào Mừng<br /><span class="accent">Trở Lại</span></h1>

      <p class="auth-note">Đăng nhập để nhận ưu đãi độc quyền dành riêng cho thành viên.</p>

      <?php if (!empty($error)): ?>
        <div style="background: #fce8e6; color: #c5221f; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: 500;">
          ⚠️ <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <form class="form-vertical" action="index.php?page=dangnhap" method="POST">
        <div class="field">
          <label for="phone">Số điện thoại</label>
          <input type="text" id="phone" name="phone" placeholder="0901 234 567" required />
        </div>

        <div class="field">
          <div class="label-row">
            <label for="password">Mật khẩu</label>
            <a href="index.php?page=doimatkhau">Quên mật khẩu?</a>
          </div>
          <input type="password" id="password" name="password" placeholder="••••••••" required />
        </div>

        <button type="submit" class="button button-gold button-full">Đăng Nhập Ngay</button>
      </form>

      <p class="auth-switch">
        Chưa có tài khoản thành viên?
        <a href="index.php?page=dangky">Đăng ký thành viên</a>
      </p>
    </div>
  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>
