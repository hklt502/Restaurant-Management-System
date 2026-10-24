<?php
$pageTitle = 'Quản lý Menu - Restaurant Management System';
$pageCss = '../../../public/asset/css/admin/menu.css';
$activePage = 'menu';
include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header__left">
                    <h1 class="page-header__title">Quản lý Menu</h1>
                    <p class="page-header__subtitle">Quản lý danh sách các món ăn, trạng thái món và giá cả thực đơn.
                    </p>
                </div>
                <div class="page-header__action">
                    <button class="btn" id="btnAddCategory" type="button">
                        <i class="bx bx-plus-circle"></i> Tạo danh mục mới
                    </button>
                </div>
            </div>

            <!-- Quick Filter Bar -->
            <div class="filter-card">
                <span class="filter-card__label">LỌC NHANH:</span>
                <div class="filter-card__tags">
                    <button type="button" class="filter-tag filter-tag--active">Tất cả</button>
                    <button type="button" class="filter-tag">Thịt</button>
                    <button type="button" class="filter-tag">Viên thả lẩu</button>
                    <button type="button" class="filter-tag">Hải sản</button>
                    <button type="button" class="filter-tag">Nội tạng</button>
                    <button type="button" class="filter-tag">Rau củ</button>
                </div>
            </div>

            <!-- Data Table Card -->
            <div class="table-card">
                <table class="menu-table">
                    <thead>
                        <tr>
                            <th>ẢNH MÓN</th>
                            <th>TÊN MÓN</th>
                            <th>DANH MỤC</th>
                            <th>TRẠNG THÁI</th>
                            <th>HÀNH ĐỘNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Row 1: Active -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png">
                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 001</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="active-text">Đang bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 2: Active -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 002</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="active-text">Đang bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 3: Inactive -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 003</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="inactive-text">Ngưng bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 4: Active -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 004</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="active-text">Đang bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 5: Active -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">

                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 005</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="active-text">Đang bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 6: Inactive -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 006</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="inactive-text">Ngưng bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <!-- Row 7: Active -->
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="dish-info">
                                    <p class="dish-info__name">Thịt bò Wagyu thượng hạng</p>
                                    <p class="dish-info__code">Mã: 007</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge--category">Thịt</span>
                            </td>
                            <td>
                                <span class="active-text">Đang bán</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button type="button" class="action-btn" title="Sửa"><i
                                            class="bx bx-pencil"></i></button>
                                    <button type="button" class="action-btn" title="Xóa"><i
                                            class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- CRUD form -->
            <div class="CRUD">
                <div id="title">
                    <p>CRUD</p>
                </div>

                <form action="#" method="">
                    <div>
                        <p><label for="id">ID</label></p>
                        <p><input type="number" name="id"></p>
                        <p><label for="food">Tên món</label></p>
                        <p><input type="text" name="food"></p>
                        <p><label for="list">Danh mục</label></p>
                        <p><select name="list" id="list">
                                <option value="orther">Khác</option>
                                <option value="meats">Thịt</option>
                                <option value="toppings">Viên thả lẩu</option>
                                <option value="seafoods">Hải sản</option>
                                <option value="Offal">Nội tại</option>
                                <option value="vegetables">Rau củ</option>
                            </select></p>
                        <p><label for="desc">Mô tả</label></p>
                        <p><input type="text"></p>
                        <p><label for="photos">Ảnh</label></p>
                        <p><input type="file" name="photos"></p>
                    </div>
                    <div class="buttonForm">
                        <button type="submit" id="btn_insert">Thêm</button>
                        <button type="submit" id="btn_update">Cập nhật</button>
                        <button type="submit" id="btn_delete">Xoá</button>
                        <button type="submit" id="btn_get">Lấy</button>
                    </div>
                </form>
            </div>

            <!-- MODAL: TẠO DANH MỤC MỚI -->
            <div class="modal-overlay" id="categoryModal">
                <div class="modal">
                    <div class="modal__header">
                        <h2 class="modal__title">Tạo danh mục mới</h2>
                        <button class="modal__close" id="btnCloseCategoryModal" type="button"><i class="bx bx-x"></i></button>
                    </div>
                    <div class="modal__body">
                        <form class="modal-form" id="categoryForm">
                            <div class="modal-form__group">
                                <label class="modal-form__label">Mã danh mục <span>*</span></label>
                                <div class="modal-form__input-wrapper">
                                    <i class="bx bx-barcode"></i>
                                    <input type="text" class="modal-form__input" placeholder="VD: CAT_01" required>
                                </div>
                            </div>
                            <div class="modal-form__group">
                                <label class="modal-form__label">Tên danh mục <span>*</span></label>
                                <div class="modal-form__input-wrapper">
                                    <i class="bx bx-text"></i>
                                    <input type="text" class="modal-form__input" placeholder="VD: Khai vị, Đồ uống..." required>
                                </div>
                            </div>
                            <div class="modal-form__group">
                                <label class="modal-form__label">Mô tả</label>
                                <div class="modal-form__input-wrapper">
                                    <i class="bx bx-detail"></i>
                                    <input type="text" class="modal-form__input" placeholder="Mô tả ngắn về danh mục">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal__footer">
                        <button class="btn btn--ghost" id="btnCancelCategory" type="button">Hủy bỏ</button>
                        <button class="btn" type="button" style="background: var(--red-bg); color: var(--red);">
                            <i class="bx bx-trash"></i> Xoá
                        </button>
                        <button class="btn" type="button" style="background: var(--orange-bg); color: var(--orange);">
                            <i class="bx bx-edit"></i> Cập nhật
                        </button>
                        <button class="btn btn--primary" type="button" style="background: linear-gradient(135deg, var(--primary-400), var(--primary-500)); color: var(--white);">
                            <i class="bx bx-save"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>

            <script>
                // Modal Logic
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

                btnAddCategory.addEventListener('click', openCategoryModal);
                btnCloseCategoryModal.addEventListener('click', closeCategoryModal);
                btnCancelCategory.addEventListener('click', closeCategoryModal);
                categoryModal.addEventListener('click', (e) => { 
                    if (e.target === categoryModal) closeCategoryModal(); 
                });
            </script>

<?php
include_once __DIR__ . '/../layouts/admin_footer.php';
?>

