<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-menu');
    exit;
}

$pageTitle  = 'Quản lý Menu - Restaurant Management System';
$pageCss    = 'public/asset/css/admin/menu.css';
$activePage = 'menu';

$foods       = $foods ?? [];
$categories  = $categories ?? [];
$fetchedFood = $fetchedFood ?? null;
$message     = $message ?? '';
$messageType = $messageType ?? 'success';

include_once __DIR__ . '/../layouts/admin_header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header__left">
        <h1 class="page-header__title">Quản lý Menu</h1>
        <p class="page-header__subtitle">
            Quản lý danh sách các món ăn, giảm giá, món nổi bật và danh mục thực đơn.
        </p>
    </div>
    <div class="page-header__action">
        <button class="btn" id="btnAddCategory" type="button">
            <i class="bx bx-plus-circle"></i> Quản lý danh mục
        </button>
    </div>
</div>

<?php if (!empty($message)): ?>
    <?php
    $isOk   = ($messageType === 'success');
    $isWarn = ($messageType === 'warning');
    $isErr  = ($messageType === 'danger');
    ?>
    <div style="padding: 12px 18px; border-radius: 8px; margin-bottom: 16px; font-weight: 500; display: flex; align-items: center; gap: 8px;
        background: <?= $isOk ? '#F0FDF4' : ($isErr ? '#FEF2F2' : ($isWarn ? '#FFFBEB' : '#EFF6FF')) ?>;
        color: <?= $isOk ? '#15803D' : ($isErr ? '#B91C1C' : ($isWarn ? '#B45309' : '#1D4ED8')) ?>;
        border: 1px solid <?= $isOk ? '#BBF7D0' : ($isErr ? '#FECACA' : ($isWarn ? '#FDE68A' : '#BFDBFE')) ?>;">
        <i class="bx <?= $isOk ? 'bx-check-circle' : ($isErr || $isWarn ? 'bx-error-circle' : 'bx-info-circle') ?>" style="font-size: 1.3rem;"></i>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
<?php endif; ?>

<!-- Quick Filter Bar -->
<div class="filter-card">
    <span class="filter-card__label">LỌC NHANH:</span>
    <div class="filter-card__tags" id="categoryFilterTags">
        <button type="button" class="filter-tag filter-tag--active" data-cat="all">Tất cả (<?= count($foods) ?>)</button>
        <?php foreach ($categories as $cat): ?>
            <button type="button" class="filter-tag" data-cat="<?= $cat['categoryId'] ?>">
                <?= htmlspecialchars($cat['categoryName']) ?>
            </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Data Table Card -->
<div class="table-card">
    <table class="menu-table">
        <thead>
            <tr>
                <th>ẢNH MÓN</th>
                <th>TÊN MÓN</th>
                <th>GIÁ BÁN / GIẢM GIÁ</th>
                <th>DANH MỤC</th>
                <th>TRẠNG THÁI</th>
                <th>HÀNH ĐỘNG</th>
            </tr>
        </thead>
        <tbody id="menuTableBody">
            <?php if (!empty($foods)): ?>
                <?php foreach ($foods as $food): ?>
                    <?php
                    $imgSrc   = !empty($food['imageURL']) ? ltrim($food['imageURL'], '/') : 'public/asset/img/thitbo.png';
                    $isAvail  = ($food['foodStatus'] === 'AVAILABLE');
                    $discount = (float)($food['foodDiscount'] ?? 0);
                    $featured = !empty($food['isFeatured']);
                    ?>
                    <tr data-id="<?= $food['foodId'] ?>" data-cat-id="<?= $food['categoryId'] ?>" data-name="<?= htmlspecialchars(mb_strtolower($food['foodName'], 'UTF-8')) ?>">
                        <td>
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($food['foodName']) ?>"
                                style="width: 52px; height: 42px; object-fit: cover; border-radius: 6px;"
                                onerror="this.src='public/asset/img/thitbo.png'">
                        </td>
                        <td>
                            <div class="dish-info">
                                <p class="dish-info__name">
                                    <?= htmlspecialchars($food['foodName']) ?>
                                </p>
                                <p class="dish-info__code">Mã: <?= str_pad((string)$food['foodId'], 3, '0', STR_PAD_LEFT) ?></p>
                            </div>
                        </td>
                        <td>
                            <strong style="color: var(--primary-500);"><?= number_format((float)$food['foodPrice'], 0, ',', '.') ?>đ</strong>
                            <?php if ($discount > 0): ?>
                                <span style="display:block;font-size:0.75rem;color:var(--green);font-weight:600;">Giảm <?= rtrim(rtrim(number_format($discount, 2, '.', ''), '0'), '.') ?>%</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge--category"><?= htmlspecialchars($food['categoryName'] ?? 'Chưa phân loại') ?></span>
                        </td>
                        <td>
                            <a href="index.php?page=admin-menu&action=toggle_status&id=<?= $food['foodId'] ?>"
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
                                    onclick='fillEditFood(<?= json_encode($food, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)'>
                                    <i class="bx bx-pencil"></i>
                                </button>
                                <a href="index.php?page=admin-menu&action=delete&id=<?= $food['foodId'] ?>" class="action-btn" title="Xóa món"
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
                        Chưa có dữ liệu món ăn trong cơ sở dữ liệu.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- CRUD form kết nối Cơ sở dữ liệu -->
<div class="CRUD">
    <div id="title">
        <p style="padding-left: 20px;">CRUD Quản lý Món ăn</p>
    </div>

    <form action="index.php?page=admin-menu" method="POST" enctype="multipart/form-data" id="foodCrudForm">
        <div>
            <p><label for="crud_id">ID Món ăn (Dùng khi Cập nhật / Xóa / Lấy thông tin):</label></p>
            <p><input type="number" name="id" id="crud_id" value="<?= htmlspecialchars((string)($fetchedFood['foodId'] ?? '')) ?>" placeholder="Nhập ID món ăn..."></p>

            <p><label for="crud_food">Tên món ăn <span>*</span>:</label></p>
            <p><input type="text" name="food" id="crud_food" value="<?= htmlspecialchars($fetchedFood['foodName'] ?? '') ?>" placeholder="Nhập tên món ăn..." required></p>

            <p><label for="crud_price">Giá bán (VNĐ) <span>*</span>:</label></p>
            <p><input type="number" name="price" id="crud_price" value="<?= isset($fetchedFood['foodPrice']) ? (float)$fetchedFood['foodPrice'] : '' ?>" placeholder="VD: 150000" min="0" required></p>

            <p><label for="crud_discount">Giảm giá (%):</label></p>
            <p><input type="number" name="discount" id="crud_discount" step="0.5" min="0" max="100" value="<?= isset($fetchedFood['foodDiscount']) ? (float)$fetchedFood['foodDiscount'] : 0 ?>" placeholder="VD: 10"></p>

            <p><label for="crud_list">Danh mục:</label></p>
            <p>
                <?php $curCat = (int)($fetchedFood['categoryId'] ?? 1); ?>
                <select name="list" id="crud_list">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['categoryId'] ?>" <?= $curCat === (int)$cat['categoryId'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['categoryName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p><label for="crud_status">Trạng thái bán:</label></p>
            <p>
                <?php $curSt = $fetchedFood['foodStatus'] ?? 'AVAILABLE'; ?>
                <select name="status" id="crud_status">
                    <option value="AVAILABLE" <?= $curSt === 'AVAILABLE' ? 'selected' : '' ?>>Đang bán</option>
                    <option value="UNAVAILABLE" <?= $curSt === 'UNAVAILABLE' ? 'selected' : '' ?>>Ngưng bán</option>
                </select>
            </p>

            <p><label for="crud_featured">Món nổi bật (Hiển thị Trang chủ):</label></p>
            <p>
                <?php $curFeat = (int)($fetchedFood['isFeatured'] ?? 0); ?>
                <select name="is_featured" id="crud_featured">
                    <option value="0" <?= $curFeat === 0 ? 'selected' : '' ?>>Bình thường</option>
                    <option value="1" <?= $curFeat === 1 ? 'selected' : '' ?>>★ Món nổi bật</option>
                </select>
            </p>

            <p><label for="crud_desc">Mô tả món ăn:</label></p>
            <p><input type="text" name="desc" id="crud_desc" value="<?= htmlspecialchars($fetchedFood['foodDescription'] ?? '') ?>" placeholder="Mô tả thành phần, cách nấu..."></p>

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
            <button type="submit" name="btn_delete" id="btn_delete" formnovalidate title="Xóa món theo ID"
                onclick="return confirm('Bạn có chắc muốn xóa món này theo ID?')">
                <i class="bx bx-trash"></i> Xoá
            </button>
            <button type="submit" name="btn_get" id="btn_get" formnovalidate title="Lấy thông tin món theo ID">
                <i class="bx bx-search"></i> Lấy theo ID
            </button>
        </div>
    </form>
</div>

<!-- MODAL: QUẢN LÝ DANH MỤC (THÊM / SỬA / XÓA) -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal" style="max-width: 580px;">
        <div class="modal__header">
            <h2 class="modal__title">Quản lý danh mục món ăn</h2>
            <button class="modal__close" id="btnCloseCategoryModal" type="button"><i class="bx bx-x"></i></button>
        </div>
        <form action="index.php?page=admin-menu" method="POST" id="categoryForm">
            <div class="modal__body">
                <input type="hidden" name="categoryId" id="cat_id" value="0">
                <div class="modal-form__group" style="margin-bottom: 12px;">
                    <label class="modal-form__label" style="display: block; font-weight: 600; margin-bottom: 6px;">
                        Tên danh mục món <span>*</span>
                    </label>
                    <input type="text" name="categoryName" id="cat_name" class="modal-form__input" placeholder="VD: Khai vị, Đồ uống, Tráng miệng..." required
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--r-sm);">
                </div>
                <div class="modal-form__group" style="margin-bottom: 16px;">
                    <label class="modal-form__label" style="display: block; font-weight: 600; margin-bottom: 6px;">
                        Mô tả danh mục
                    </label>
                    <input type="text" name="categoryDesc" id="cat_desc" class="modal-form__input" placeholder="Mô tả ngắn gọn về nhóm món ăn này"
                        style="width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--r-sm);">
                </div>

                <div style="border-top: 1px solid var(--border); padding-top: 12px; max-height: 220px; overflow-y: auto;">
                    <p style="font-size: 0.82rem; font-weight: 700; color: var(--text2); margin-bottom: 8px;">DANH MỤC HIỆN CÓ:</p>
                    <?php foreach ($categories as $cat): ?>
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:6px 8px; border-bottom:1px solid var(--border);">
                            <div>
                                <strong>#<?= $cat['categoryId'] ?> - <?= htmlspecialchars($cat['categoryName']) ?></strong>
                                <div style="font-size:0.78rem; color:var(--text3);"><?= htmlspecialchars($cat['description'] ?? '') ?></div>
                            </div>
                            <div style="display:flex; gap:8px;">
                                <button type="button" onclick='fillEditCategory(<?= json_encode($cat, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>)' style="color:var(--primary-400); font-weight:600; font-size:0.82rem;">Sửa</button>
                                <a href="index.php?page=admin-menu&action=delete_category&cat_id=<?= $cat['categoryId'] ?>"
                                    onclick="return confirm('Xóa danh mục <?= htmlspecialchars(addslashes($cat['categoryName'])) ?>?')"
                                    style="color:var(--red); font-weight:600; font-size:0.82rem;">Xóa</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal__footer" style="display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid var(--border);">
                <button class="btn btn--ghost" id="btnCancelCategory" type="button">Đóng</button>
                <button class="btn btn--primary" type="submit" name="btn_save_category"
                    style="background: linear-gradient(135deg, var(--primary-400), var(--primary-500)); color: var(--white); padding: 9px 20px; border-radius: var(--r-sm); cursor: pointer;">
                    <i class="bx bx-save"></i> Lưu danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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
        document.getElementById('cat_id').value = '0';
        document.getElementById('cat_name').value = '';
        document.getElementById('cat_desc').value = '';
    }

    function fillEditCategory(cat) {
        document.getElementById('cat_id').value = cat.categoryId || 0;
        document.getElementById('cat_name').value = cat.categoryName || '';
        document.getElementById('cat_desc').value = cat.description || '';
    }

    if (btnAddCategory) btnAddCategory.addEventListener('click', openCategoryModal);
    if (btnCloseCategoryModal) btnCloseCategoryModal.addEventListener('click', closeCategoryModal);
    if (btnCancelCategory) btnCancelCategory.addEventListener('click', closeCategoryModal);
    if (categoryModal) {
        categoryModal.addEventListener('click', (e) => {
            if (e.target === categoryModal) closeCategoryModal();
        });
    }

    function fillEditFood(food) {
        document.getElementById('crud_id').value = food.foodId || '';
        document.getElementById('crud_food').value = food.foodName || '';
        document.getElementById('crud_price').value = food.foodPrice || 0;
        document.getElementById('crud_discount').value = food.foodDiscount || 0;
        document.getElementById('crud_list').value = food.categoryId || 1;
        document.getElementById('crud_desc').value = food.foodDescription || '';
        document.getElementById('crud_status').value = food.foodStatus || 'AVAILABLE';
        document.getElementById('crud_featured').value = food.isFeatured ? '1' : '0';

        document.querySelector('.CRUD').scrollIntoView({
            behavior: 'smooth'
        });
    }

    const filterTags = document.querySelectorAll('#categoryFilterTags .filter-tag');
    const tableRows = document.querySelectorAll('#menuTableBody tr[data-id]');

    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            filterTags.forEach(t => t.classList.remove('filter-tag--active'));
            this.classList.add('filter-tag--active');

            const selectedCat = this.dataset.cat;
            tableRows.forEach(row => {
                row.style.display = (selectedCat === 'all' || row.dataset.catId === selectedCat) ? '' : 'none';
            });
        });
    });

    const headerSearch = document.querySelector('.header__search input');
    if (headerSearch) {
        headerSearch.addEventListener('input', function() {
            const kw = this.value.trim().toLowerCase();
            tableRows.forEach(row => {
                const name = row.dataset.name || '';
                const id = row.dataset.id || '';
                row.style.display = (!kw || name.includes(kw) || id.includes(kw)) ? '' : 'none';
            });
        });
    }
</script>

<!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
</main>
</div>

</body>

</html>