<?php
$pageTitle = 'Trang Chủ — Nhóm 3';
$activePage = 'trangchu';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

  <!-- ===================== PHẦN MỞ ĐẦU ===================== -->
  <div class="hero">
    <div class="hero-inner">
      <p class="eyebrow">— Ẩm Thực Truyền Thống Việt Nam</p>

      <h1>Hương Vị Trọn Vẹn<br /><span class="accent">Từ Bếp Lửa Nhà Nhóm 3</span></h1>

      <p>Hơn 120 món ăn Việt Nam, nguyên liệu tươi mỗi ngày và công thức
        gia truyền được giữ nguyên từ năm 1987.</p>

      <div class="hero-buttons">
        <a class="button button-gold" href="index.php?page=thucdon">Xem Thực Đơn</a>
        <a class="button button-outline" href="index.php?page=datban">Đặt Bàn Ngay</a>
      </div>
    </div>
  </div>

  <!-- ===================== DẢI SỐ LIỆU ===================== -->
  <div class="stats">
    <div class="stats-inner">

      <div class="stat">
        <p class="stat-number">1987</p>
        <p class="stat-title">Năm thành lập</p>
        <p class="stat-note">Hơn 35 năm giữ lửa</p>
      </div>

      <div class="stat">
        <p class="stat-number">120+</p>
        <p class="stat-title">Món ăn đặc sắc</p>
        <p class="stat-note">Từ Bắc vào Nam</p>
      </div>

      <div class="stat">
        <p class="stat-number">4.9★</p>
        <p class="stat-title">Đánh giá khách hàng</p>
        <p class="stat-note">Trên Google &amp; Foody</p>
      </div>

    </div>
  </div>

  <!-- ===================== CÂU CHUYỆN ===================== -->
  <div class="story">

    <div>
      <p class="eyebrow">Câu chuyện của chúng tôi</p>

      <h2>Bếp lửa truyền qua<br /><span class="accent">ba thế hệ</span></h2>

      <p>Khởi đầu từ gánh hàng ăn nhỏ của bà nội tại chợ Bến Thành năm 1987,
        Nhóm 3 được xây dựng trên triết lý đơn giản: nguyên liệu tươi mỗi ngày,
        công thức gia truyền không thay đổi.</p>

      <p>Đến nay, thế hệ thứ ba vẫn giữ nguyên linh hồn của những món ăn đó —
        chỉ thêm không gian đẹp hơn cho thực khách thưởng thức.</p>

      <a class="button button-line" href="index.php?page=thucdon">Khám phá thực đơn →</a>
    </div>

    <div class="story-image">
      <img src="https://images.unsplash.com/photo-1641440615059-42c2be0b4fa1?w=800&h=600&fit=crop&auto=format"
        alt="Các món ăn đặc sắc của nhà hàng Nhóm 3" />

      <div class="quote">
        <p>"Ăn là để nhớ, không phải chỉ để no."</p>
        <span>— Bà Nguyễn Thị Hoa, người sáng lập</span>
      </div>
    </div>

  </div>

  <!-- ===================== MÓN NỔI BẬT ===================== -->
  <div class="featured">
    <div class="featured-inner">

      <div class="featured-head">
        <div>
          <p class="eyebrow">Được yêu thích nhất</p>
          <h2>Món Nổi Bật</h2>
        </div>
        <a class="button button-line" href="index.php?page=thucdon">Xem tất cả →</a>
      </div>

      <div class="dish-grid">
        <?php if (!empty($featuredFoods)): ?>
          <?php foreach ($featuredFoods as $food): ?>
            <a class="dish" href="index.php?page=thucdon">
              <img src="<?= htmlspecialchars(!empty($food['imageURL']) ? $food['imageURL'] : 'https://images.unsplash.com/photo-1701480253822-1842236c9a97?w=600&h=400&fit=crop&auto=format') ?>"
                alt="<?= htmlspecialchars($food['foodName']) ?>" />
              <div class="dish-body">
                <h3><?= htmlspecialchars($food['foodName']) ?></h3>
                <p class="price"><?= number_format($food['foodPrice'], 0, ',', '.') ?>₫</p>
                <p class="dish-note"><?= htmlspecialchars($food['foodDescription'] ?? 'Món ngon thượng hạng chuẩn vị truyền thống.') ?></p>
              </div>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <a class="dish" href="index.php?page=thucdon">
            <img src="https://images.unsplash.com/photo-1701480253822-1842236c9a97?w=600&h=400&fit=crop&auto=format" alt="Phở Bò Đặc Biệt" />
            <div class="dish-body">
              <h3>Phở Bò Đặc Biệt</h3>
              <p class="price">85.000₫</p>
              <p class="dish-note">Nước dùng hầm xương 12 tiếng, thịt bò tái, gân, gầu, kèm rau thơm.</p>
            </div>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ===================== KÊU GỌI ĐẶT BÀN ===================== -->
  <div class="cta">
    <p class="eyebrow">Đặt chỗ trước để có trải nghiệm tốt nhất</p>

    <h2>Dành Một Buổi Tối<br /><span class="accent">Thật Đặc Biệt</span></h2>

    <div class="cta-buttons">
      <a class="button button-gold" href="index.php?page=datban">Đặt Bàn Ngay</a>
      <a class="button button-outline" href="tel:02838245678">Gọi 028 3824 5678</a>
    </div>

    <p>Hoặc gọi trực tiếp: <span>028 3824 5678</span></p>
  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>
