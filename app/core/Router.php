<?php
// app/core/Router.php

class Router {
    private $routes = [];

    /**
     * Đăng ký Route cho phương thức GET
     */
    public function get($page, $controller, $action) {
        $this->routes['GET'][$page] = [
            'controller' => $controller,
            'action'     => $action
        ];
    }

    /**
     * Đăng ký Route cho phương thức POST
     */
    public function post($page, $controller, $action) {
        $this->routes['POST'][$page] = [
            'controller' => $controller,
            'action'     => $action
        ];
    }

    /**
     * Điều hướng và thực thi Controller tương ứng
     */
    public function dispatch() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            if (!empty($_SESSION['customer']['customerId'])) {
                require_once __DIR__ . '/../models/CustomerModel.php';
                $customer = (new CustomerModel())->getById((int)$_SESSION['customer']['customerId']);
                if ($customer) {
                    $_SESSION['customer'] = [
                        'customerId'    => $customer['customerId'],
                        'customerName'  => $customer['customerName'],
                        'customerPhone' => $customer['customerPhone'],
                        'customerEmail' => $customer['customerEmail'],
                    ];
                } else {
                    unset($_SESSION['customer']);
                }
            }
            if (!empty($_SESSION['admin']['adminId'])) {
                require_once __DIR__ . '/../models/AdminModel.php';
                $admin = (new AdminModel())->getById((int)$_SESSION['admin']['adminId']);
                if ($admin && empty($admin['isDeleted'])) {
                    unset($admin['passwordHash']);
                    $_SESSION['admin'] = $admin;
                } else {
                    unset($_SESSION['admin']);
                }
            }
        } catch (Throwable $e) {
        }

        $page   = $_GET['page'] ?? 'trangchu';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'HEAD') {
            $method = 'GET';
        }

        if (isset($this->routes[$method][$page])) {
            $route           = $this->routes[$method][$page];
            $controllerClass = $route['controller'];
            $action          = $route['action'];

            // Kiểm tra controller ở phân hệ client
            $clientPath = __DIR__ . '/../controllers/client/' . $controllerClass . '.php';
            // Kiểm tra controller ở phân hệ admin
            $adminPath  = __DIR__ . '/../controllers/admin/' . $controllerClass . '.php';

            if (file_exists($clientPath)) {
                if (in_array($page, ['canhan', 'doimatkhau', 'huy-datban', 'nhan-voucher'], true) && empty($_SESSION['customer']['customerId'])) {
                    header('Location: index.php?page=dangnhap');
                    exit;
                }
                if (in_array($page, ['dangnhap', 'dangky'], true) && !empty($_SESSION['customer']['customerId'])) {
                    header('Location: index.php?page=trangchu');
                    exit;
                }
                require_once $clientPath;
            } elseif (file_exists($adminPath)) {
                if ($page !== 'admin-login' && empty($_SESSION['admin']['adminId'])) {
                    header('Location: index.php?page=admin-login');
                    exit;
                }
                if ($page === 'admin-login' && !empty($_SESSION['admin']['adminId'])) {
                    header('Location: index.php?page=admin-overview');
                    exit;
                }
                require_once $adminPath;
            } else {
                die("Không tìm thấy Controller: $controllerClass");
            }

            $controller = new $controllerClass();
            $controller->$action();
            return;
        }

        // Không tìm thấy trang (404)
        http_response_code(404);
        echo "<div style='font-family:sans-serif; text-align:center; padding-top:80px;'>";
        echo "<h1>404 - Không tìm thấy trang</h1>";
        echo "<p>Đường dẫn không hợp lệ hoặc trang không tồn tại.</p>";
        echo "<p><a href='index.php?page=trangchu' style='color:#c9a84c;'>Quay về Trang chủ</a></p>";
        echo "</div>";
    }
}

