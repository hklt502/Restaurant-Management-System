<?php
// app/controllers/admin/AdminAuthController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/AdminModel.php';

class AdminAuthController extends BaseController
{
    public function login()
    {
        if ($this->adminId()) {
            $this->redirect('index.php?page=admin-overview');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $adminModel = new AdminModel();
            $admin = $adminModel->login($username, $password);

            if ($admin) {
                unset($admin['passwordHash']);
                session_regenerate_id(true);
                $_SESSION['admin'] = $admin;
                $this->redirect('index.php?page=admin-overview');
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng";
                $this->render('admin/login', ['error' => $error, 'oldUsername' => $username]);
            }
        } else {
            $this->render('admin/login');
        }
    }

    public function logout()
    {
        unset($_SESSION['admin']);
        $this->redirect('index.php?page=admin-login');
    }
}
