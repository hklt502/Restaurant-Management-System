<?php
$pageTitle = 'Đăng Ký Hội Viên — Nhóm 3';
$activePage = 'dangky';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

  <!-- ===================== FORM ĐĂNG KÝ ===================== -->
  <div class="auth auth-wide">
    <div class="auth-box">
      <p class="eyebrow">Thành viên Nhóm 3</p>

      <h1>Đăng Ký<br /><span class="accent">Hội Viên</span></h1>

      <p class="auth-note">Trở thành một phần của cộng đồng Nhóm 3 và nhận các ưu đãi đặc quyền.</p>

      <form>
        <div class="form-grid">

          <div class="field field-full">
            <label for="fullname">Họ và tên *</label>
            <input type="text" id="fullname" name="fullname" placeholder="Nguyễn Văn A" required />
          </div>

          <div class="field">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" placeholder="example@gmail.com" required />
          </div>

          <div class="field">
            <label for="phone">Số điện thoại *</label>
            <input type="tel" id="phone" name="phone" placeholder="0901 234 567" required />
          </div>

          <div class="field">
            <label for="birth">Ngày sinh *</label>
            <input type="date" id="birth" name="birth" required />
          </div>

          <div class="field">
            <label>Giới tính</label>
            <div class="radio-row">
              <label><input type="radio" name="gender" value="nam" checked /> Nam</label>
              <label><input type="radio" name="gender" value="nu" /> Nữ</label>
              <label><input type="radio" name="gender" value="khac" /> Khác</label>
            </div>
          </div>

          <div class="field">
            <label for="password">Mật khẩu *</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required />
          </div>

          <div class="field">
            <label for="confirm">Xác nhận mật khẩu *</label>
            <input type="password" id="confirm" name="confirm" placeholder="••••••••" required />
          </div>

          <div class="field field-full">
            <label class="check-row" for="terms">
              <input type="checkbox" id="terms" required />
              Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a> của Nhóm 3.
            </label>

            <button type="submit" class="button button-gold button-full">Tạo Tài Khoản Ngay</button>
          </div>

        </div>
      </form>

      <p class="auth-switch">
        Bạn đã có tài khoản?
        <a href="dangnhap.php">Đăng nhập tại đây</a>
      </p>
    </div>
  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>

