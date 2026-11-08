<?php
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/app/views/admin/') !== false) {
    header('Location: ../../../index.php?page=admin-warehouse');
    exit;
}

$pageTitle  = 'Quản lý Kho nguyên liệu - Restaurant Management System';
$pageCss    = 'public/asset/css/admin/warehouse.css';
$activePage = 'warehouse';

$message     = $message ?? '';
$messageType = $messageType ?? 'success';
$ingredients = $ingredients ?? [];
$logs        = $logs ?? [];
$fetchedItem = $fetchedItem ?? null;
$outCount    = $outCount ?? 0;
$lowCount    = $lowCount ?? 0;

include_once __DIR__ . '/../layouts/admin_header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header__left">
        <h1 class="page-header__title">Quản lý kho nguyên liệu</h1>
        <p class="page-header__subtitle">Theo dõi, nhập xuất và kiểm kê thực phẩm</p>
    </div>
    <div class="page-header__actions">
        <button class="btn btn--outline" type="button" onclick="openStockModal('EXPORT')">
            <i class="bx bx-export"></i>
            <span>Xuất kho</span>
        </button>
        <button class="btn btn--primary" type="button" onclick="openStockModal('IMPORT')">
            <i class="bx bx-import"></i>
            <span>Nhập kho</span>
        </button>
    </div>
</div>

<?php if (!empty($message)): ?>
    <div style="padding: 12px 18px; border-radius: 8px; font-weight: 500; display: flex; align-items: center; gap: 8px;
                    background: <?= $messageType === 'success' ? '#F0FDF4' : ($messageType === 'danger' ? '#FEF2F2' : '#EFF6FF') ?>;
                    color: <?= $messageType === 'success' ? '#15803D' : ($messageType === 'danger' ? '#B91C1C' : '#1D4ED8') ?>;
                    border: 1px solid <?= $messageType === 'success' ? '#BBF7D0' : ($messageType === 'danger' ? '#FECACA' : '#BFDBFE') ?>;">
        <i class="bx <?= $messageType === 'success' ? 'bx-check-circle' : 'bx-info-circle' ?>" style="font-size: 1.3rem;"></i>
        <span><?= htmlspecialchars($message) ?></span>
    </div>
<?php endif; ?>

<!-- Form Nhập/Xuất Kho nhanh -->
<div id="stockIoPanel" style="display:none; background: var(--white); border: 1px solid var(--border); border-radius: var(--r-md); padding: 20px 24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <h3 id="stockIoTitle" style="font-size:1.1rem; color:var(--primary-400);">Nhập / Xuất Kho Nguyên Liệu</h3>
        <button type="button" onclick="document.getElementById('stockIoPanel').style.display='none'" style="font-size:1.3rem;color:var(--text3);">&times;</button>
    </div>
    <form action="index.php?page=admin-warehouse" method="POST" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; align-items:end;">
        <input type="hidden" name="io_type" id="io_type" value="IMPORT">
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:4px;">Nguyên liệu *</label>
            <select name="io_ingredient_id" id="io_ingredient_id" required style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--r-sm);">
                <?php foreach ($ingredients as $ing): ?>
                    <option value="<?= $ing['ingredientId'] ?>">
                        #<?= $ing['ingredientId'] ?> - <?= htmlspecialchars($ing['ingredientName']) ?> (Tồn: <?= rtrim(rtrim(number_format((float)$ing['quantityInStock'], 2, '.', ''), '0'), '.') ?> <?= htmlspecialchars($ing['unit']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:4px;">Số lượng *</label>
            <input type="number" name="io_quantity" step="0.01" min="0.01" required placeholder="VD: 10" style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--r-sm);">
        </div>
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;margin-bottom:4px;">Ghi chú phiếu</label>
            <input type="text" name="io_note" placeholder="Lý do nhập / xuất bếp..." style="width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:var(--r-sm);">
        </div>
        <div>
            <button type="submit" name="btn_stock_io" class="btn btn--primary" style="width:100%;">
                <i class="bx bx-check"></i> Xác nhận phiếu
            </button>
        </div>
    </form>
</div>

<!-- Alert Cards -->
<div class="alert-cards">
    <div class="alert-card alert-card--danger">
        <p class="alert-card__label">CẢNH BÁO HẾT KHO</p>
        <p class="alert-card__value"><?= $outCount ?> <span class="alert-card__unit">nguyên liệu đã hết</span></p>
        <a href="#inventoryTable" onclick="filterStock('out'); return false;" class="alert-card__action alert-card__action--danger">
            <i class="bx bx-error-circle"></i> Cần nhập ngay
        </a>
    </div>
    <div class="alert-card alert-card--warning">
        <p class="alert-card__label">CẢNH BÁO TỒN KHO</p>
        <p class="alert-card__value"><?= $lowCount ?> <span class="alert-card__unit">nguyên liệu sắp hết</span></p>
        <a href="#inventoryTable" onclick="filterStock('low'); return false;" class="alert-card__action alert-card__action--warning">
            <i class="bx bx-error"></i> Xem xét nhập hàng
        </a>
    </div>
</div>

<!-- Quick Filter Bar -->
<div class="filter-card">
    <span class="filter-card__label">LỌC NHANH:</span>
    <div class="filter-card__tags" id="stockFilterTags">
        <button type="button" class="filter-tag filter-tag--active" data-filter="all">Tất cả (<?= count($ingredients) ?>)</button>
        <button type="button" class="filter-tag" data-filter="ok">Còn hàng (<?= max(0, count($ingredients) - $outCount - $lowCount) ?>)</button>
        <button type="button" class="filter-tag" data-filter="low">Sắp hết (<?= $lowCount ?>)</button>
        <button type="button" class="filter-tag" data-filter="out">Hết hàng (<?= $outCount ?>)</button>
    </div>
</div>

<!-- Table -->
<div class="table-card" id="inventoryTable">
    <table class="inventory-table">
        <thead>
            <tr>
                <th>NGUYÊN LIỆU</th>
                <th>ĐƠN VỊ</th>
                <th>SỐ LƯỢNG TỒN KHO</th>
                <th>THAO TÁC</th>
            </tr>
        </thead>
        <tbody id="warehouseTableBody">
            <?php if (!empty($ingredients)): ?>
                <?php foreach ($ingredients as $ing): ?>
                    <?php
                    $qty      = (float)$ing['quantityInStock'];
                    $min      = (float)$ing['minQuantity'];
                    $unit     = $ing['unit'];
                    $state    = ($qty <= 0) ? 'out' : (($qty <= $min) ? 'low' : 'ok');
                    $qtyClass = ($state === 'out') ? 'alert-card__action--danger' : (($state === 'low') ? 'alert-card__action--warning' : '');
                    ?>
                    <tr data-id="<?= $ing['ingredientId'] ?>"
                        data-state="<?= $state ?>"
                        data-name="<?= htmlspecialchars(mb_strtolower($ing['ingredientName'], 'UTF-8')) ?>">
                        <td>
                            <div class="food-info">
                                <p class="food-info__name"><?= htmlspecialchars($ing['ingredientName']) ?></p>
                                <p class="food-info__code">Mã: <?= str_pad((string)$ing['ingredientId'], 3, '0', STR_PAD_LEFT) ?></p>
                            </div>
                        </td>
                        <td>
                            <span class="food--category"><?= htmlspecialchars($unit) ?></span>
                        </td>
                        <td>
                            <p class="food-info__quantity <?= $qtyClass ?>">
                                <?= rtrim(rtrim(number_format($qty, 2, '.', ''), '0'), '.') ?> <?= htmlspecialchars($unit) ?>
                            </p>
                            <span class="min-quantity"><span>min:</span> <?= rtrim(rtrim(number_format($min, 2, '.', ''), '0'), '.') ?> <?= htmlspecialchars($unit) ?></span>
                        </td>
                        <td>
                            <div style="display:flex;gap:10px;align-items:center;">
                                <button type="button" title="Chọn sửa nguyên liệu này"
                                    onclick='fillIngredientForm(<?= htmlspecialchars(json_encode($ing, JSON_UNESCAPED_UNICODE)) ?>)'
                                    style="color:var(--primary-400);font-size:1.2rem;">
                                    <i class="bx bx-edit"></i>
                                </button>
                                <a href="index.php?page=admin-warehouse&action=delete&id=<?= $ing['ingredientId'] ?>"
                                    onclick="return confirm('Xóa nguyên liệu <?= htmlspecialchars(addslashes($ing['ingredientName'])) ?>?')"
                                    title="Xóa nguyên liệu" style="color:var(--red);font-size:1.2rem;">
                                    <i class="bx bx-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;padding:30px;color:var(--text3);">Chưa có dữ liệu nguyên liệu trong kho.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- CRUD Form -->
<div class="CRUD" id="crudSection">
    <div id="title">
        <p>CRUD Nguyên liệu</p>
    </div>

    <form action="index.php?page=admin-warehouse" method="POST" enctype="multipart/form-data" id="ingredientCrudForm">
        <div>
            <p><label for="crud_id">ID Nguyên liệu (Dùng khi Cập nhật / Xóa / Lấy)</label></p>
            <p><input type="number" name="id" id="crud_id" value="<?= htmlspecialchars((string)($fetchedItem['ingredientId'] ?? '')) ?>" placeholder="Nhập ID nguyên liệu..."></p>

            <p><label for="crud_ingredient">Tên nguyên liệu *</label></p>
            <p><input type="text" name="ingredient" id="crud_ingredient" value="<?= htmlspecialchars($fetchedItem['ingredientName'] ?? '') ?>" placeholder="VD: Thịt Bò Wagyu..."></p>

            <p><label for="crud_quantity">Số lượng tồn kho</label></p>
            <p><input type="number" name="quantity" id="crud_quantity" step="0.01" min="0" value="<?= isset($fetchedItem['quantityInStock']) ? (float)$fetchedItem['quantityInStock'] : '' ?>" placeholder="VD: 50"></p>

            <p><label for="crud_min_stock">Định mức tối thiểu</label></p>
            <p><input type="number" name="min_stock" id="crud_min_stock" step="0.01" min="0" value="<?= isset($fetchedItem['minQuantity']) ? (float)$fetchedItem['minQuantity'] : '' ?>" placeholder="VD: 5"></p>

            <p><label for="crud_unit">Đơn vị tính</label></p>
            <p>
                <?php $curUnit = $fetchedItem['unit'] ?? 'kg'; ?>
                <select name="unit" id="crud_unit">
                    <option value="kg" <?= $curUnit === 'kg' ? 'selected' : '' ?>>Kg</option>
                    <option value="gram" <?= $curUnit === 'gram' ? 'selected' : '' ?>>Gram</option>
                    <option value="lít" <?= $curUnit === 'lít' ? 'selected' : '' ?>>Lít</option>
                    <option value="cái" <?= $curUnit === 'cái' ? 'selected' : '' ?>>Cái/Miếng</option>
                    <option value="gói" <?= $curUnit === 'gói' ? 'selected' : '' ?>>Gói</option>
                </select>
            </p>
        </div>
        <div class="buttonForm">
            <button type="submit" name="btn_insert" id="btn_insert">Thêm</button>
            <button type="submit" name="btn_update" id="btn_update">Cập nhật</button>
            <button type="submit" name="btn_delete" id="btn_delete" onclick="return confirm('Xác nhận xóa nguyên liệu theo ID?')">Xoá</button>
            <button type="submit" name="btn_get" id="btn_get">Lấy</button>
        </div>
    </form>
</div>

<!-- Lịch sử Nhập / Xuất Kho (Bảng inventory_log) -->
<div class="table-card" style="margin-top: 8px;">
    <div style="padding: 16px 24px; border-bottom: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--primary-400);">Lịch sử Nhập / Xuất Kho gần đây</h3>
        <span style="font-size:0.8rem; color:var(--text3);"><?= count($logs) ?> phiếu gần nhất</span>
    </div>
    <table class="inventory-table">
        <thead>
            <tr>
                <th>MÃ PHIẾU</th>
                <th>NGUYÊN LIỆU</th>
                <th>LOẠI PHIẾU</th>
                <th>SỐ LƯỢNG</th>
                <th>NGƯỜI THỰC HIỆN</th>
                <th>GHI CHÚ & THỜI GIAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                    <?php $isImport = (strtoupper($log['logType']) === 'IMPORT'); ?>
                    <tr>
                        <td><strong>#<?= $log['logId'] ?></strong></td>
                        <td><?= htmlspecialchars($log['ingredientName']) ?></td>
                        <td>
                            <span style="padding:4px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;
                                            background:<?= $isImport ? 'var(--green-bg)' : 'var(--orange-bg)' ?>;
                                            color:<?= $isImport ? 'var(--green)' : 'var(--orange)' ?>;">
                                <?= $isImport ? 'NHẬP KHO' : 'XUẤT KHO' ?>
                            </span>
                        </td>
                        <td>
                            <strong><?= $isImport ? '+' : '-' ?><?= rtrim(rtrim(number_format((float)$log['quantity'], 2, '.', ''), '0'), '.') ?> <?= htmlspecialchars($log['unit'] ?? '') ?></strong>
                        </td>
                        <td><?= htmlspecialchars($log['adminName'] ?? 'Quản trị viên') ?></td>
                        <td>
                            <div><?= htmlspecialchars($log['note'] ?? '') ?></div>
                            <small style="color:var(--text3);"><?= htmlspecialchars($log['createdAt'] ?? '') ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;color:var(--text3);">Chưa có lịch sử nhập/xuất kho.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    function openStockModal(type) {
        const panel = document.getElementById('stockIoPanel');
        const title = document.getElementById('stockIoTitle');
        const input = document.getElementById('io_type');
        panel.style.display = 'block';
        input.value = type;
        title.textContent = (type === 'EXPORT') ? 'Phiếu Xuất Kho Nguyên Liệu' : 'Phiếu Nhập Kho Nguyên Liệu';
        panel.scrollIntoView({
            behavior: 'smooth'
        });
    }

    function fillIngredientForm(ing) {
        document.getElementById('crud_id').value = ing.ingredientId || '';
        document.getElementById('crud_ingredient').value = ing.ingredientName || '';
        document.getElementById('crud_quantity').value = parseFloat(ing.quantityInStock || 0);
        document.getElementById('crud_min_stock').value = parseFloat(ing.minQuantity || 0);
        const unitSelect = document.getElementById('crud_unit');
        if (unitSelect && ing.unit) {
            unitSelect.value = ing.unit;
        }
        document.getElementById('crudSection').scrollIntoView({
            behavior: 'smooth'
        });
    }

    const filterTags = document.querySelectorAll('#stockFilterTags .filter-tag');
    const rows = document.querySelectorAll('#warehouseTableBody tr[data-id]');

    function filterStock(filterValue) {
        filterTags.forEach(t => {
            t.classList.toggle('filter-tag--active', t.dataset.filter === filterValue);
        });
        rows.forEach(r => {
            r.style.display = (filterValue === 'all' || r.dataset.state === filterValue) ? '' : 'none';
        });
    }

    filterTags.forEach(tag => {
        tag.addEventListener('click', () => filterStock(tag.dataset.filter));
    });

    const headerSearch = document.querySelector('.header__search input');
    if (headerSearch) {
        headerSearch.addEventListener('input', function() {
            const kw = this.value.trim().toLowerCase();
            rows.forEach(r => {
                const match = !kw || (r.dataset.name && r.dataset.name.includes(kw)) || (r.dataset.id && r.dataset.id.includes(kw));
                r.style.display = match ? '' : 'none';
            });
        });
    }
</script>

<!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
</main>
</div>

</body>

</html>