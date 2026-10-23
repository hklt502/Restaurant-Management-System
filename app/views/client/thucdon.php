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
    <a href="#mon-nuoc">Món Nước</a>
    <a href="#mon-chinh">Món Chính</a>
    <a href="#khai-vi">Khai Vị</a>
    <a href="#do-uong">Đồ Uống</a>
  </div>

  <!-- ===================== DANH SÁCH MÓN ===================== -->
  <div class="menu-list">

    <!-- ---------- Món nước ---------- -->
    <div class="menu-section" id="mon-nuoc">
      <h2>Món Nước</h2>

      <div class="dish-grid">

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1701480253822-1842236c9a97?w=600&h=400&fit=crop&auto=format"
            alt="Phở Bò Đặc Biệt" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Phở Bò Đặc Biệt</h3>
              <span class="price">85.000₫</span>
            </div>
            <p>Nước dùng hầm xương 12 tiếng, thịt bò tái, gân, gầu, kèm rau thơm.</p>
            <div class="dish-card-foot">
              <span>Món Nước</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1555126634-323283e090fa?w=600&h=400&fit=crop&auto=format"
            alt="Bún Bò Huế" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Bún Bò Huế</h3>
              <span class="price">75.000₫</span>
            </div>
            <p>Nước dùng sả cay đặc trưng, bò &amp; chả, bún sợi to, huyết luộc.</p>
            <div class="dish-card-foot">
              <span>Món Nước</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1533787761082-492a5b83e614?w=600&h=400&fit=crop&auto=format"
            alt="Cháo Gà Ta" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Cháo Gà Ta</h3>
              <span class="price">65.000₫</span>
            </div>
            <p>Cháo gạo lứt nấu từ gà ta thả vườn, hành lá, gừng tươi.</p>
            <div class="dish-card-foot">
              <span>Món Nước</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ---------- Món chính ---------- -->
    <div class="menu-section" id="mon-chinh">
      <h2>Món Chính</h2>

      <div class="dish-grid">

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1701480254009-53c2bf8a82ee?w=600&h=400&fit=crop&auto=format"
            alt="Cơm Tấm Sườn Bì" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Cơm Tấm Sườn Bì</h3>
              <span class="price">70.000₫</span>
            </div>
            <p>Sườn nướng than hoa, bì trộn, trứng ốp la, mỡ hành, đồ chua.</p>
            <div class="dish-card-foot">
              <span>Món Chính</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1672858502748-fb7dc81ef830?w=600&h=400&fit=crop&auto=format"
            alt="Bò Lúc Lắc" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Bò Lúc Lắc</h3>
              <span class="price">120.000₫</span>
            </div>
            <p>Thịt bò Mỹ xào lửa to với tỏi bơ, ăn kèm cơm trắng và salad cà chua.</p>
            <div class="dish-card-foot">
              <span>Món Chính</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1511910849309-0dffb8785146?w=600&h=400&fit=crop&auto=format"
            alt="Cá Kho Tộ" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Cá Kho Tộ</h3>
              <span class="price">95.000₫</span>
            </div>
            <p>Cá basa kho nước dừa, tiêu xanh, gừng — đậm đà theo kiểu miền Tây.</p>
            <div class="dish-card-foot">
              <span>Món Chính</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ---------- Khai vị ---------- -->
    <div class="menu-section" id="khai-vi">
      <h2>Khai Vị</h2>

      <div class="dish-grid">

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1641440615059-42c8ed3af8c8?w=600&h=400&fit=crop&auto=format"
            alt="Gỏi Cuốn Tôm Thịt" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Gỏi Cuốn Tôm Thịt</h3>
              <span class="price">45.000₫</span>
            </div>
            <p>Bánh tráng cuốn tươi, tôm sú, thịt heo, bún, rau sống &amp; tương hoisin.</p>
            <div class="dish-card-foot">
              <span>Khai Vị</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1615444814488-f0b1952b2f27?w=600&h=400&fit=crop&auto=format"
            alt="Chả Giò Cua Biển" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Chả Giò Cua Biển</h3>
              <span class="price">60.000₫</span>
            </div>
            <p>Chả giò giòn rụm nhân cua biển, nấm mèo, miến — chiên vàng tươi.</p>
            <div class="dish-card-foot">
              <span>Khai Vị</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ---------- Đồ uống ---------- -->
    <div class="menu-section" id="do-uong">
      <h2>Đồ Uống</h2>

      <div class="dish-grid">

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1712630514718-3830cc6c0d0a?w=600&h=400&fit=crop&auto=format"
            alt="Cà Phê Sữa Đá" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Cà Phê Sữa Đá</h3>
              <span class="price">35.000₫</span>
            </div>
            <p>Robusta pha phin truyền thống, sữa đặc Ông Thọ, đá viên to.</p>
            <div class="dish-card-foot">
              <span>Đồ Uống</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

        <div class="dish-card">
          <img src="https://images.unsplash.com/photo-1650490632010-e18cbb694dd1?w=600&h=400&fit=crop&auto=format"
            alt="Nước Chanh Muối" />
          <div class="dish-card-body">
            <div class="dish-card-head">
              <h3>Nước Chanh Muối</h3>
              <span class="price">25.000₫</span>
            </div>
            <p>Chanh tươi vắt, muối hạt, soda — thanh mát đặc trưng miền Nam.</p>
            <div class="dish-card-foot">
              <span>Đồ Uống</span>
              <a href="datban.php">Gọi món +</a>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>

<?php
include_once __DIR__ . '/../layouts/client_footer.php';
?>

