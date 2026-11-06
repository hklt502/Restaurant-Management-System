<?php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/AdminModel.php';
require_once __DIR__ . '/../../models/CustomerModel.php';

class AdminController extends BaseController
{
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $adminModel = new AdminModel();
            $admin = $adminModel->login($username, $password);

            if ($admin) {
                $_SESSION['admin'] = $admin;
                $this->redirect('index.php?page=admin-users');
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng";
                $this->render('admin/login', ['error' => $error]);
            }
        } else {
            $this->render('admin/login');
        }
    }
public function users()
{
    $adminModel = new AdminModel();
    $customerModel = new CustomerModel();

    $admins = $adminModel->getAll();
    $customers = $customerModel->getAll();

    $this->render('admin/users', [
        'admins' => $admins,
        'customers' => $customers
    ]);
}
public function tables()
{
    require_once __DIR__ . '/../../models/TableModel.php';

    $tableModel = new TableModel();
    $tables = $tableModel->getAll();

    $this->render('admin/tables', [
        'tables' => $tables
    ]);
}
}



