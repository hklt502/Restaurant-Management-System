<?php
/**
 * Điểm vào chính của ứng dụng (Front Controller)
 * Điều hướng toàn bộ request thông qua Router
 */

require_once __DIR__ . '/app/core/Router.php';

$router = new Router();

// ======================== ROUTES CLIENT ========================
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

