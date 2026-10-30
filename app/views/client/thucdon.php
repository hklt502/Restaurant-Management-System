<?php
$pageTitle = 'Thực Đơn — Nhóm 3';
$activePage = 'thucdon';
include_once __DIR__ . '/../layouts/client_head.php';
include_once __DIR__ . '/../layouts/client_header.php';
?>

<!-- ===================== PHẦN MỞ ĐẦU ===================== -->
<div class="page-hero">
  <div class="page-hero-inner">
    <p class="eyebrow">Nhà hàng Nhóm 3</p>
    <h1>Thực Đơn<br /><span class="accent">Mùa Này</span></h1>
    <p>Hơn 120 món ăn Việt Nam được chăm chút mỗi ngày. Giá đã bao gồm VAT.</p>
  </div>
</div>

<!-- ===================== THANH DANH MỤC ===================== -->
<div class="tabs">
  <?php if (!empty($categories)): ?>
    <?php foreach ($categories as $cat): ?>
      <a href="#cat-<?= $cat['categoryId'] ?>"><?= htmlspecialchars($cat['categoryName']) ?></a>
    <?php endforeach; ?>
  <?php else: ?>
    <a href="#mon-nuoc">Món Nước</a>
    <a href="#mon-chinh">Món Chính</a>
    <a href="#khai-vi">Khai Vị</a>
    <a href="#do-uong">Đồ Uống</a>
  <?php endif; ?>
</div>

<!-- ===================== DANH SÁCH MÓN ===================== -->
<div class="menu-list">
  <?php if (!empty($categories) && !empty($foodsByCategory)): ?>
    <?php foreach ($categories as $cat): ?>
      <?php if (!empty($foodsByCategory[$cat['categoryId']])): ?>
        <div class="menu-section" id="cat-<?= $cat['categoryId'] ?>">
          <h2><?= htmlspecialchars($cat['categoryName']) ?></h2>
          <div class="dish-grid">
            <?php foreach ($foodsByCategory[$cat['categoryId']] as $food): ?>
              <div class="dish-card">
                <img src="<?= htmlspecialchars(!empty($food['imageURL']) ? ltrim($food['imageURL'], '/') : 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&h=400&fit=crop&auto=format') ?>"
                  alt="<?= htmlspecialchars($food['foodName']) ?>" />
                <div class="dish-card-body">
                  <div class="dish-card-head">
                    <h3><?= htmlspecialchars($food['foodName']) ?></h3>
                    <span class="price"><?= number_format($food['foodPrice'], 0, ',', '.') ?>₫</span>
                  </div>
                  <p><?= htmlspecialchars($food['foodDescription'] ?? 'Nguyên liệu tươi ngon mỗi ngày.') ?></p>
                  <div class="dish-card-foot">
                    <span><?= htmlspecialchars($food['categoryName']) ?></span>
                    <a href="index.php?page=datban">Gọi món +</a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <!-- Mặc định khi chưa có dữ liệu DB -->
  <?php endif; ?>
</div>

<!-- ===================== KÊU GỌI ĐẶT BÀN ===================== -->
<div class="cta">
  <p class="eyebrow">Thưởng thức trọn vẹn hương vị</p>
  <h2>Bạn Đã Chọn Được<br /><span class="accent">Món Ưng Ý?</span></h2>
  <div class="cta-buttons">
    <a class="button button-gold" href="index.php?page=datban">Đặt Bàn Ngay</a>
    <a class="button button-outline" href="tel:02838245678">Gọi 028 3824 5678</a>
  </div>
</div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>