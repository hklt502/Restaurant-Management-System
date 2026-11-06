<?php
$pageTitle = 'Quản lý Kho nguyên liệu - Restaurant Management System';
$pageCss = '../../../public/asset/css/admin/warehouse.css';
$activePage = 'warehouse';
include_once __DIR__ . '/../layouts/admin_header.php';
?>

            <!-- Page Header -->
            <div class="page-header">
                <div class="page-header__left">
                    <h1 class="page-header__title">Quản lý kho nguyên liệu</h1>
                    <p class="page-header__subtitle">Theo dõi, nhập xuất và kiểm kê thực phẩm</p>
                </div>
                <div class="page-header__actions">
                    <button class="btn btn--outline" type="button">
                        <i class="bx bx-export"></i>
                        <span>Xuất kho</span>
                    </button>
                    <button class="btn btn--primary" type="button">
                        <i class="bx bx-import"></i>
                        <span>Nhập kho</span>
                    </button>
                </div>
            </div>

            <!-- Alert Cards -->
            <div class="alert-cards">
                <div class="alert-card alert-card--danger">
                    <p class="alert-card__label">CẢNH BÁO HẾT KHO</p>
                    <p class="alert-card__value">1 <span class="alert-card__unit">nguyên liệu đã hết</span></p>
                    <a href="#" class="alert-card__action alert-card__action--danger">
                        <i class="bx bx-error-circle"></i> Cần nhập ngay
                    </a>
                </div>
                <div class="alert-card alert-card--warning">
                    <p class="alert-card__label">CẢNH BÁO TỒN KHO</p>
                    <p class="alert-card__value">2 <span class="alert-card__unit">nguyên liệu sắp hết</span></p>
                    <a href="#" class="alert-card__action alert-card__action--warning">
                        <i class="bx bx-error"></i> Xem xét nhập hàng
                    </a>
                </div>
            </div>

            <!-- Quick Filter Bar -->
            <div class="filter-card">
                <span class="filter-card__label">LỌC NHANH:</span>
                <div class="filter-card__tags">
                    <button type="button" class="filter-tag filter-tag--active">Tất cả</button>
                    <button type="button" class="filter-tag">Còn hàng</button>
                    <button type="button" class="filter-tag">Sắp hết</button>
                    <button type="button" class="filter-tag">Hết hàng</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-card">
                <table class="inventory-table">
                    <thead>
                        <tr>
                            <th>ẢNH MÓN</th>
                            <th>NGUYÊN LIỆU</th>
                            <th>DANH MỤC</th>
                            <th>SỐ LƯỢNG</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png">
                            </td>
                            <td>
                                <div class="food-info">
                                    <p class="food-info__name"> Thịt Wagyu thượng hạng</p>
                                    <p class="food-info__code">Mã: 001</p>
                                </div>
                            </td>
                            <td>
                                <span class="food--category"> Thịt</span>
                            </td>
                            <td>
                                <p class=" food-info__quantity ">100 kg</p>
                                <span class="min-quantity"> <span>min:</span> 5 kg</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="food-info">
                                    <p class="food-info__name"> Thịt cừu</p>
                                    <p class="food-info__code">Mã: 002</p>
                                </div>
                            </td>
                            <td>
                                <span class=" food--category"> Thịt</span>
                            </td>
                            <td>
                                <p class=" food-info__quantity alert-card__action--warning">1 kg</p>
                                <span class="min-quantity"><span>min:</span>5 kg</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="food-info">
                                    <p class="food-info__name"> Thịt lợn</p>
                                    <p class="food-info__code">Mã: 003</p>
                                </div>
                            </td>
                            <td>
                                <span class=" food--category"> Thịt</span>
                            </td>
                            <td>
                                <p class=" food-info__quantity alert-card__action--danger">0 kg</p>
                                <span class="min-quantity"><span>min:</span>5 kg</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="../../../public/asset/img/thitbo.png" alt="">
                            </td>
                            <td>
                                <div class="food-info">
                                    <p class="food-info__name">Xúc xích </p>
                                    <p class="food-info__code">Mã: 004</p>
                                </div>
                            </td>
                            <td>
                                <span class=" food--category"> viên</span>
                            </td>
                            <td>
                                <p class=" food-info__quantity">1000 cái</p>
                                <span class="min-quantity"><span>min:</span>10 cái</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- CRUD Form -->
            <div class="CRUD">
                <div id="title">
                    <p>CRUD Nguyên liệu</p>
                </div>

                <form action="#" method="">
                    <div>
                        <p><label for="id">ID</label></p>
                        <p><input type="number" name="id"></p>
                        <p><label for="ingredient">Tên nguyên liệu</label></p>
                        <p><input type="text" name="ingredient"></p>
                        <p><label for="category">Loại</label></p>
                        <p> <select name="category" id="category">
                                <option value="other">Khác</option>
                                <option value="meat">Thịt</option>
                                <option value="sea">Hải sản</option>
                                <option value="vegetable">Rau củ</option>
                                <option value="spice">Gia vị</option>
                                <option value="drink">Đồ uống</option>
                            </select></p>
                        <p><label for="quantity">Số lượng tồn kho</label></p>
                        <p><input type="number" name="quantity" step="0.1"></p>
                        <p><label for="min_stock">Định mức tối thiểu</label></p>
                        <p><input type="number" name="min_stock" step="0.1"></p>
                        <p><label for="unit">Đơn vị</label></p>
                        <p> <select name="unit" id="unit">
                                <option value="kg">Kg</option>
                                <option value="gram">Gram</option>
                                <option value="lit">Lít</option>
                                <option value="piece">Cái/Miếng</option>
                                <option value="pack">Gói</option>
                            </select></p>
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

    <!-- KẾT THÚC VÙNG NỘI DUNG CHÍNH -->
    </main>
  </div>

</body>

</html>

