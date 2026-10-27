<?php
class BaseController
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    // extract đưa key biến mảng thành biến và value thành gián trị biến đó
    // Nạp view và truyền dữ liệu: $this->render('client/thucdon', ['foods' => $foods])
    protected function render($view, $data = [])
    {
        extract($data);
        include __DIR__ . '/../views/' . $view . '.php';
    }

    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    // ID khách đang đăng nhập (null nếu chưa)
    protected function customerId()
    {
        return $_SESSION['customer']['customerId'] ?? null;
    }

    // Bắt buộc đăng nhập, nếu chưa thì chuyển về trang đăng nhập
    protected function requireLogin()
    {
        if (!$this->customerId()) {
            $this->redirect('index.php?page=dangnhap');
        }
    }
}
