<?php
// app/controllers/admin/OrderController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/FoodModel.php';

class OrderController extends BaseController
{
    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            header('Content-Type: application/json; charset=utf-8');

            // 1. Xử lý AJAX cập nhật trạng thái đơn hàng xuống Database
            if ($_POST['action'] === 'update_status') {
                $rawId     = $_POST['orderId'] ?? '';
                $newStatus = $_POST['status'] ?? '';
                $numId     = (int) preg_replace('/\D/', '', $rawId);
                $ok = false;
                try {
                    $orderModel = new OrderModel();
                    if ($numId > 0) {
                        $ok = $orderModel->updateStatus($numId, $newStatus);
                    }
                } catch (Throwable $e) {
                    $ok = false;
                }
                echo json_encode(['success' => $ok, 'orderId' => $rawId, 'status' => $newStatus]);
                exit;
            }

            // 2. Xử lý AJAX tạo đơn hàng mới từ POS và INSERT vào Database
            if ($_POST['action'] === 'create_order') {
                try {
                    $orderModel = new OrderModel();
                    $newOrder   = $orderModel->createOrderFromPos($_POST);
                    echo json_encode(['success' => true, 'order' => $newOrder], JSON_UNESCAPED_UNICODE);
                } catch (Throwable $e) {
                    echo json_encode(['success' => false, 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
                }
                exit;
            }
        }

        $dbOrders   = [];
        $dbFoodsPos = [];
        try {
            $orderModel = new OrderModel();
            $dbOrders   = $orderModel->getAllOrders();

            $foodModel = new FoodModel();
            $rawFoods  = $foodModel->getAllAdmin();
            foreach ($rawFoods as $f) {
                $catId  = (int)($f['categoryId'] ?? 1);
                $catKey = 'beef';
                if ($catId === 2) $catKey = 'seafood';
                elseif ($catId === 3 || $catId === 4) $catKey = 'appetizer';
                elseif ($catId === 5) $catKey = 'drink';
                $dbFoodsPos[] = [
                    'id'    => (int)$f['foodId'],
                    'name'  => $f['foodName'] ?? $f['name'] ?? 'Món ăn',
                    'cat'   => $catKey,
                    'price' => (float)($f['foodPrice'] ?? $f['price'] ?? 0),
                    'unit'  => 'Phần'
                ];
            }
        } catch (Throwable $e) {
            $dbOrders   = [];
            $dbFoodsPos = [];
        }

        $this->render('admin/orders', [
            'dbOrders'   => $dbOrders,
            'dbFoodsPos' => $dbFoodsPos
        ]);
    }
}
