<?php
$isDirectView   = strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false;
$assetPrefix    = $isDirectView ? '../../../' : '';
$currentMenuUrl = $isDirectView ? 'menu.php' : 'index.php?page=admin-menu';
$querySep       = $isDirectView ? '?' : '&';

$pageTitle = 'Quản lý Menu - Restaurant Management System';
$pageCss = $assetPrefix . 'public/asset/css/admin/menu.css';
$activePage = 'menu';

// ============================================================================
// 1. KẾT NỐI DATABASE VỚI FOOD VÀ CATEGORY (Hỗ trợ cả MVC Controller & Direct View)
// ============================================================================
if (!isset($foods) || !isset($categories)) {
    $message = $message ?? '';
    $messageType = $messageType ?? 'success';
    $foods = [];
    $categories = [];

    try {
        require_once __DIR__ . '/../../models/FoodModel.php';
        require_once __DIR__ . '/../../models/CategoryModel.php';

        $foodModel = new FoodModel();
        $categoryModel = new CategoryModel();

        // 1.1. Xử lý Thêm Món mới
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_insert'])) {
            $name        = trim($_POST['food'] ?? '');
            $categoryId  = (int)($_POST['list'] ?? 1);
            $price       = (float)($_POST['price'] ?? 0);
            $desc        = trim($_POST['desc'] ?? '');
            $status      = trim($_POST['status'] ?? 'AVAILABLE');
            $imageURL    = '/public/asset/img/thitbo.png';

            if (!empty($_FILES['photos']['name']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $newFileName = 'dish_' . time() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../../public/asset/img/';
                    if (move_uploaded_file($_FILES['photos']['tmp_name'], $uploadDir . $newFileName)) {
                        $imageURL = '/public/asset/img/' . $newFileName;
                    }
                }
            }

            if (!empty($name) && $price >= 0) {
                $insertedId = $foodModel->create($categoryId, $name, $desc, $price, 0, $status, $imageURL, 0);
                if ($insertedId) {
                    $message = "Đã thêm món mới: \"$name\" (Mã: $insertedId) vào cơ sở dữ liệu!";
                    $messageType = "success";
                } else {
                    $message = "Thêm món thất bại, vui lòng thử lại!";
                    $messageType = "danger";
                }
            } else {
                $message = "Vui lòng nhập đầy đủ tên món và giá hợp lệ!";
                $messageType = "warning";
            }
        }

        // 1.2. Xử lý Cập nhật Món
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update'])) {
            $id          = (int)($_POST['id'] ?? 0);
            $name        = trim($_POST['food'] ?? '');
            $categoryId  = (int)($_POST['list'] ?? 1);
            $price       = (float)($_POST['price'] ?? 0);
            $desc        = trim($_POST['desc'] ?? '');
            $status      = trim($_POST['status'] ?? 'AVAILABLE');
            $imageURL    = null;

            if (!empty($_FILES['photos']['name']) && $_FILES['photos']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photos']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                    $newFileName = 'dish_' . time() . '.' . $ext;
                    $uploadDir = __DIR__ . '/../../../public/asset/img/';
                    if (move_uploaded_file($_FILES['photos']['tmp_name'], $uploadDir . $newFileName)) {
                        $imageURL = '/public/asset/img/' . $newFileName;
                    }
                }
            }

            if ($id > 0 && !empty($name)) {
                $updated = $foodModel->update($id, $categoryId, $name, $desc, $price, 0, $status, $imageURL, 0);
                if ($updated) {
                    $message = "Đã cập nhật thành công món \"$name\" (Mã: $id)!";
                    $messageType = "success";
                } else {
                    $message = "Cập nhật món thất bại!";
                    $messageType = "danger";
                }
            } else {
                $message = "Vui lòng nhập ID hợp lệ và tên món cần cập nhật!";
                $messageType = "warning";
            }
        }

        // 1.3. Xử lý Xóa Món (từ Form hoặc Query Param)
        if (($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_delete'])) || (isset($_GET['action']) && $_GET['action'] === 'delete')) {
            $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
            if ($id > 0) {
                $deleted = $foodModel->delete($id);
                if ($deleted) {
                    $message = "Đã xóa món ăn có Mã: $id khỏi thực đơn!";
                    $messageType = "success";
                } else {
                    $message = "Xóa món thất bại!";
                    $messageType = "danger";
                }
            }
        }

        // 1.4. Xử lý Đổi trạng thái Đang bán <-> Ngưng bán
        if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $newSt = $foodModel->toggleStatus($id);
                if ($newSt) {
                    $stName = ($newSt === 'AVAILABLE') ? 'Đang bán' : 'Ngưng bán';
                    $message = "Đã đổi trạng thái món (Mã: $id) sang \"$stName\"!";
                    $messageType = "info";
                }
            }
        }

        // 1.5. Xử lý Tạo danh mục mới trong Modal
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_save_category'])) {
            $catName = trim($_POST['categoryName'] ?? '');
            $catDesc = trim($_POST['categoryDesc'] ?? '');
            if (!empty($catName)) {
                $catId = $categoryModel->create($catName, $catDesc);
                if ($catId) {
                    $message = "Đã tạo mới danh mục: \"$catName\"!";
                    $messageType = "success";
                }
            }
        }

        // Tải toàn bộ danh mục và danh sách món từ cơ sở dữ liệu
        $categories = $categoryModel->getAll();
        $foods = $foodModel->getAllAdmin();

    } catch (Throwable $e) {
        $categories = [];
        $foods = [];
    }
}

include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header__left">
                    <h1 class="page-header__title">Quản lý Menu</h1>
                    <p class="page-header__subtitle">
                        Quản lý danh sách các món ăn, trạng thái món và giá cả thực đơn từ cơ sở dữ liệu.
                    </p>
                </div>
                <div class="page-header__action">
                    <button class="btn" id="btnAddCategory" type="button">
                        <i class="bx bx-plus-circle"></i> Tạo danh mục mới
                    </button>
                </div>
            </div>

            <!-- THÔNG BÁO HOÀN TẤT THAO TÁC (NẾU CÓ) -->
            <?php if (!empty($message)): ?>
                <div style="padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-weight: 500; display: flex; align-items: center; gap: 8px;
                    background: <?= $messageType === 'success' ? '#F0FDF4' : ($messageType === 'danger' ? '#FEF2F2' : '#EFF6FF') ?>;
                    color: <?= $messageType === 'success' ? '#15803D' : ($messageType === 'danger' ? '#B91C1C' : '#1D4ED8') ?>;
                    border: 1px solid <?= $messageType === 'success' ? '#BBF7D0' : ($messageType === 'danger' ? '#FECACA' : '#BFDBFE') ?>;">
                    <i class="bx <?= $messageType === 'success' ? 'bx-check-circle' : 'bx-info-circle' ?>" style="font-size: 1.3rem;"></i>
                    <span><?= htmlspecialchars($message) ?></span>
                </div>
            <?php endif; ?>

            <!-- Quick Filter Bar -->
            <div class="filter-card">
                <span class="filter-card__label">LỌC NHANH:</span>
                <div class="filter-card__tags" id="categoryFilterTags">
                    <button type="button" class="filter-tag filter-tag--active" data-cat="all">Tất cả (<?= count($foods) ?>)</button>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <button type="button" class="filter-tag" data-cat="<?= $cat['categoryId'] ?>">
                                <?= htmlspecialchars($cat['categoryName']) ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <button type="button" class="filter-tag" data-cat="Thịt">Thịt</button>
                        <button type="button" class="filter-tag" data-cat="Hải sản">Hải sản</button>
                        <button type="button" class="filter-tag" data-cat="Viên thả lẩu">Viên thả lẩu</button>
                        <button type="button" class="filter-tag" data-cat="Rau củ">Rau củ</button>
                        <button type="button" class="filter-tag" data-cat="Đồ uống">Đồ uống</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="table-card">
                <table class="menu-table">
                    <thead>
                        <tr>
                            <th>ẢNH MÓN</th>
                            <th>TÊN MÓN</th>
                            <th>GIÁ BÁN</th>
                            <th>DANH MỤC</th>
                            <th>TRẠNG THÁI</th>
                            <th>HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody id="menuTableBody">
                        <?php if (!empty($foods)): ?>
                            <?php foreach ($foods as $food): ?>
                                <?php 
                                    $imgSrc = !empty($food['imageURL']) ? $food['imageURL'] : '/public/asset/img/thitbo.png';
                                    if (strpos($imgSrc, '/') === 0) {
                                        $imgSrc = $assetPrefix . ltrim($imgSrc, '/');
                                    }
                                    $isAvail = ($food['foodStatus'] === 'AVAILABLE');
                                ?>
                                <tr data-id="<?= $food['foodId'] ?>" data-cat-id="<?= $food['categoryId'] ?>" data-name="<?= htmlspecialchars(strtolower($food['foodName'])) ?>">
                                    <td>
                                        <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($food['foodName']) ?>"
                                             style="width: 52px; height: 42px; object-fit: cover; border-radius: 6px;"
                                             onerror="this.src='<?= $assetPrefix ?>public/asset/img/thitbo.png'">
                                    </td>
                                    <td>
                                        <div class="dish-info">
                                            <p class="dish-info__name"><?= htmlspecialchars($food['foodName']) ?></p>
                                            <p class="dish-info__code">Mã: <?= str_pad($food['foodId'], 3, '0', STR_PAD_LEFT) ?></p>
                                        </div>
                                    </td>
                                    <td>
                                        <strong style="color: var(--primary-500);"><?= number_format($food['foodPrice'], 0, ',', '.') ?>đ</strong>
                                    </td>
                                    <td>
                                        <span class="badge badge--category"><?= htmlspecialchars($food['categoryName'] ?? 'Chưa phân loại') ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= $currentMenuUrl . $querySep ?>action=toggle_status&id=<?= $food['foodId'] ?>"
                                           title="Nhấp để đổi trạng thái"
                                           class="<?= $isAvail ? 'active-text' : 'inactive-text' ?>"
                                           style="text-decoration: none; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="bx <?= $isAvail ? 'bx-check-circle' : 'bx-x-circle' ?>"></i>
                                            <?= $isAvail ? 'Đang bán' : 'Ngưng bán' ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button type="button" class="action-btn" title="Điền thông tin để sửa"
                                                onclick="fillEditFood(<?= htmlspecialchars(json_encode($food, JSON_UNESCAPED_UNICODE)) ?>)">
                                                <i class="bx bx-pencil"></i>
                                            </button>
                                            <a href="<?= $currentMenuUrl . $querySep ?>action=delete&id=<?= $food['foodId'] ?>" class="action-btn" title="Xóa món"
                                               onclick="return confirm('Bạn có chắc muốn xóa món <?= htmlspecialchars(addslashes($food['foodName'])) ?>?')">
                                                <i class="bx bx-trash" style="color: var(--red);"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 36px; color: var(--text3);">
                                    <i class="bx bx-food-menu" style="font-size: 2.5rem; display: block; margin-bottom: 8px;"></i>
                                    Chưa có dữ liệu món ăn trong cơ sở dữ liệu. Vui lòng thêm món bằng form bên dưới!
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- CRUD form kết nối Cơ sở dữ liệu -->
            <div class="CRUD">
                <div id="title">
                    <p style="padding-left: 20px;">CRUD Quản lý Món ăn (Kết nối Database)</p>
                </div>

                <form action="<?= $currentMenuUrl ?>" method="POST" enctype="multipart/form-data" id="foodCrudForm">
                    <div>
                        <p><label for="crud_id">ID Món ăn (Dùng khi Cập nhật / Xóa / Lấy thông tin):</label></p>
                        <p><input type="number" name="id" id="crud_id" placeholder="Nhập ID món ăn..."></p>

                        <p><label for="crud_food">Tên món ăn <span>*</span>:</label></p>
                        <p><input type="text" name="food" id="crud_food" placeholder="Nhập tên món ăn..." required></p>

                        <p><label for="crud_price">Giá bán (VNĐ) <span>*</span>:</label></p>
                        <p><input type="number" name="price" id="crud_price" placeholder="VD: 150000" min="0" required></p>

                        <p><label for="crud_list">Danh mục:</label></p>
                        <p>
                            <select name="list" id="crud_list">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['categoryId'] ?>"><?= htmlspecialchars($cat['categoryName']) ?></option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="1">Thịt</option>
                                    <option value="2">Hải sản</option>
                                    <option value="3">Viên thả lẩu</option>
                                    <option value="4">Rau củ</option>
                                    <option value="5">Đồ uống</option>
                                    <option value="6">Nội tạng</option>
                                <?php endif; ?>
                            </select>
                        </p>

                        <p><label for="crud_status">Trạng thái bán:</label></p>
                        <p>
                            <select name="status" id="crud_status">
                                <option value="AVAILABLE">Đang bán</option>
                                <option value="UNAVAILABLE">Ngưng bán</option>
                            </select>
                        </p>

                        <p><label for="crud_desc">Mô tả món ăn:</label></p>
                        <p><input type="text" name="desc" id="crud_desc" placeholder="Mô tả thành phần, cách nấu..."></p>

                        <p><label for="crud_photos">Ảnh món ăn (JPG, PNG, WebP):</label></p>
                        <p><input type="file" name="photos" id="crud_photos" accept="image/*"></p>
                    </div>

                    <div class="buttonForm">
                        <button type="submit" name="btn_insert" id="btn_insert" title="Thêm món mới vào DB">
                            <i class="bx bx-plus"></i> Thêm món
                        </button>
                        <button type="submit" name="btn_update" id="btn_update" title="Cập nhật theo ID">
                            <i class="bx bx-save"></i> Cập nhật
                        </button>
                        <button type="submit" name="btn_delete" id="btn_delete" title="Xóa món theo ID"
                            onclick="return confirm('Bạn có chắc muốn xóa món này theo ID?')">
                            <i class="bx bx-trash"></i> Xoá
                        </button>
                        <button type="button" id="btn_get" title="Làm mới hoặc xóa trắng form">
                            <i class="bx bx-refresh"></i> Làm mới form
                        </button>
                    </div>
                </form>
            </div>

            <!-- MODAL: TẠO DANH MỤC MỚI VÀO DATABASE -->
            <div class="modal-overlay" id="categoryModal">
                <div class="modal">
                    <div class="modal__header">
                        <h2 class="modal__title">Tạo danh mục món ăn mới</h2>
                        <button class="modal__close" id="btnCloseCategoryModal" type="button"><i class="bx bx-x"></i></button>
                    </div>
                    <form action="<?= $currentMenuUrl ?>" method="POST" id="categoryForm">
                        <div class="modal__body">
                            <div class="modal-form__group" style="margin-bottom: 16px;">
                                <label class="modal-form__label" style="display: block; font-weight: 600; margin-bottom: 6px;">
                                    Tên danh mục món <span>*</span>
                                </label>
                                <div class="modal-form__input-wrapper">
                                    <input type="text" name="categoryName" class="modal-form__input" placeholder="VD: Khai vị, Đồ uống, Tráng miệng..." required
                                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--r-sm);">
                                </div>
                            </div>
                            <div class="modal-form__group">
                                <label class="modal-form__label" style="display: block; font-weight: 600; margin-bottom: 6px;">
                                    Mô tả danh mục
                                </label>
                                <div class="modal-form__input-wrapper">
                                    <input type="text" name="categoryDesc" class="modal-form__input" placeholder="Mô tả ngắn gọn về nhóm món ăn này"
                                           style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--r-sm);">
                                </div>
                            </div>
                        </div>
                        <div class="modal__footer" style="display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border);">
                            <button class="btn btn--ghost" id="btnCancelCategory" type="button">Hủy bỏ</button>
                            <button class="btn btn--primary" type="submit" name="btn_save_category"
                                    style="background: linear-gradient(135deg, var(--primary-400), var(--primary-500)); color: var(--white); padding: 9px 20px; border-radius: var(--r-sm); cursor: pointer;">
                                <i class="bx bx-save"></i> Lưu danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                // ==========================================================
                // 1. MODAL TẠO DANH MỤC MỚI
                // ==========================================================
                const categoryModal = document.getElementById('categoryModal');
                const btnAddCategory = document.getElementById('btnAddCategory');
                const btnCloseCategoryModal = document.getElementById('btnCloseCategoryModal');
                const btnCancelCategory = document.getElementById('btnCancelCategory');

                function openCategoryModal() { 
                    categoryModal.classList.add('active'); 
                    document.body.style.overflow = 'hidden'; 
                }
                function closeCategoryModal() { 
                    categoryModal.classList.remove('active'); 
                    document.body.style.overflow = ''; 
                }

                if (btnAddCategory) btnAddCategory.addEventListener('click', openCategoryModal);
                if (btnCloseCategoryModal) btnCloseCategoryModal.addEventListener('click', closeCategoryModal);
                if (btnCancelCategory) btnCancelCategory.addEventListener('click', closeCategoryModal);
                if (categoryModal) {
                    categoryModal.addEventListener('click', (e) => { 
                        if (e.target === categoryModal) closeCategoryModal(); 
                    });
                }

                // ==========================================================
                // 2. ĐIỀN THÔNG TIN MÓN VÀO FORM KHI BẤM SỬA
                // ==========================================================
                function fillEditFood(food) {
                    document.getElementById('crud_id').value    = food.foodId || '';
                    document.getElementById('crud_food').value  = food.foodName || '';
                    document.getElementById('crud_price').value = food.foodPrice || 0;
                    document.getElementById('crud_list').value  = food.categoryId || 1;
                    document.getElementById('crud_desc').value  = food.foodDescription || '';
                    document.getElementById('crud_status').value= food.foodStatus || 'AVAILABLE';

                    // Cuộn trang mượt mà tới form CRUD
                    document.querySelector('.CRUD').scrollIntoView({ behavior: 'smooth' });
                }

                // Nút Làm mới form
                document.getElementById('btn_get').addEventListener('click', function () {
                    document.getElementById('foodCrudForm').reset();
                    document.getElementById('crud_id').value = '';
                });

                // ==========================================================
                // 3. LỌC NHANH MÓN ĂN THEO DANH MỤC & TÌM KIẾM
                // ==========================================================
                const filterTags = document.querySelectorAll('#categoryFilterTags .filter-tag');
                const tableRows  = document.querySelectorAll('#menuTableBody tr[data-id]');

                filterTags.forEach(tag => {
                    tag.addEventListener('click', function () {
                        filterTags.forEach(t => t.classList.remove('filter-tag--active'));
                        this.classList.add('filter-tag--active');

                        const selectedCat = this.dataset.cat;
                        tableRows.forEach(row => {
                            if (selectedCat === 'all' || row.dataset.catId === selectedCat) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                });

                // Tìm kiếm bằng ô input trên Header
                const headerSearch = document.querySelector('.header__search input');
                if (headerSearch) {
                    headerSearch.addEventListener('input', function () {
                        const kw = this.value.trim().toLowerCase();
                        tableRows.forEach(row => {
                            const name = row.dataset.name || '';
                            const id = row.dataset.id || '';
                            if (!kw || name.includes(kw) || id.includes(kw)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    });
                }
            </script>

    <!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
    </main>
  </div>

</body>

</html>
