<?php
/**
 * Điểm vào chính của ứng dụng (Front Controller)
 * Điều hướng toàn bộ request thông qua Router (Mô hình MVC)
 */

require_once __DIR__ . '/app/core/Router.php';

$router = new Router();

// ======================== ROUTES ADMIN (QUẢN TRỊ VIÊN) ========================
$router->get('admin-login', 'AdminAuthController', 'login');
$router->post('admin-login', 'AdminAuthController', 'login');
$router->get('admin-logout', 'AdminAuthController', 'logout');

$router->get('admin-overview', 'OverviewController', 'index');

$router->get('admin-menu', 'AdminMenuController', 'index');
$router->post('admin-menu', 'AdminMenuController', 'index');

$router->get('admin-tables', 'TableController', 'index');
$router->post('admin-tables', 'TableController', 'index');

$router->get('admin-orders', 'OrderController', 'index');
$router->post('admin-orders', 'OrderController', 'index');

$router->get('admin-vouchers', 'VoucherController', 'index');
$router->post('admin-vouchers', 'VoucherController', 'index');

$router->get('admin-warehouse', 'WarehouseController', 'index');
$router->post('admin-warehouse', 'WarehouseController', 'index');

$router->get('admin-users', 'UserController', 'index');
$router->post('admin-users', 'UserController', 'index');

$router->get('admin-statistics', 'StatisticController', 'index');
$router->get('admin-statistics-data', 'StatisticController', 'data');

// ======================== ROUTES CLIENT (KHÁCH HÀNG) ========================
// 1. Trang chủ
$router->get('trangchu', 'HomeController', 'index');

// 2. Thực đơn
$router->get('thucdon', 'MenuController', 'index');

// 3. Khuyến mãi
$router->get('khuyenmai', 'PromotionController', 'index');
$router->post('nhan-voucher', 'PromotionController', 'claimVoucher');

// 4. Đặt bàn
$router->get('datban', 'BookingController', 'index');
$router->post('datban', 'BookingController', 'store');
$router->post('huy-datban', 'BookingController', 'cancel');

// 5. Xác thực tài khoản (Auth)
$router->get('dangnhap', 'AuthController', 'loginForm');
$router->post('dangnhap', 'AuthController', 'login');
$router->get('dangxuat', 'AuthController', 'logout');

$router->get('dangky', 'AuthController', 'registerForm');
$router->post('dangky', 'AuthController', 'register');

// 6. Trang cá nhân & Đổi mật khẩu
$router->get('canhan', 'AuthController', 'profile');
$router->get('doimatkhau', 'AuthController', 'changePasswordForm');
$router->post('doimatkhau', 'AuthController', 'changePassword');

// Kích hoạt điều hướng request
$router->dispatch();
