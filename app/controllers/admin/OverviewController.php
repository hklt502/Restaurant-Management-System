<?php
// app/controllers/admin/OverviewController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/OrderModel.php';
require_once __DIR__ . '/../../models/TableModel.php';
require_once __DIR__ . '/../../models/CustomerModel.php';
require_once __DIR__ . '/../../models/FoodModel.php';
require_once __DIR__ . '/../../models/IngredientModel.php';

class OverviewController extends BaseController
{
    public function index()
    {
        $selectedDate  = $_GET['date'] ?? '';
        $revenue       = 0;
        $totalOrders   = 0;
        $occupiedCount = 0;
        $totalTables   = 0;
        $customerCount = 0;
        $hourlyHeights = array_fill(10, 13, 10);
        $hourlyVal     = array_fill(10, 13, 0);
        $recentOrders  = [];
        $featuredFoods = [];
        $stockAlerts   = [];

        try {
            $orderModel      = new OrderModel();
            $tableModel      = new TableModel();
            $customerModel   = new CustomerModel();
            $foodModel       = new FoodModel();
            $ingredientModel = new IngredientModel();

            $revenue       = $orderModel->grossIncome();
            $totalOrders   = $orderModel->countOrder();
            $customerCount = $customerModel->countCustomer();

            $tables      = $tableModel->getAll();
            $totalTables = count($tables);
            foreach ($tables as $t) {
                if (in_array(strtoupper($t['status'] ?? ''), ['OCCUPIED', 'RESERVED'], true)) {
                    $occupiedCount++;
                }
            }

            if (!empty($selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
                $begin = $selectedDate . ' 00:00:00';
                $end   = $selectedDate . ' 23:59:59';
            } else {
                $begin = '2000-01-01 00:00:00';
                $end   = '2099-12-31 23:59:59';
            }

            foreach ($orderModel->getOrders($begin, $end) as $ord) {
                if (strtoupper($ord['status'] ?? '') === 'CANCELLED') {
                    continue;
                }
                $hour = (int)date('G', strtotime($ord['orderDate']));
                if ($hour >= 10 && $hour <= 22) {
                    $hourlyVal[$hour] += (float)($ord['totalAmount'] ?? 0);
                }
            }

            $maxRev = !empty($hourlyVal) ? max($hourlyVal) : 0;
            for ($h = 10; $h <= 22; $h++) {
                $hourlyHeights[$h] = $maxRev > 0 ? max(8, (int)round(($hourlyVal[$h] / $maxRev) * 95)) : 8;
            }

            $recentOrders  = $orderModel->getOrders(0, 5);
            $featuredFoods = $foodModel->getFeatured(3);
            $stockAlerts   = $ingredientModel->getAlerts();
        } catch (Throwable $e) {
        }

        $tablePercent = $totalTables > 0 ? round(($occupiedCount / $totalTables) * 100) : 0;

        $this->render('admin/overview', [
            'selectedDate'  => $selectedDate,
            'revenue'       => $revenue,
            'totalOrders'   => $totalOrders,
            'occupiedCount' => $occupiedCount,
            'totalTables'   => $totalTables,
            'tablePercent'  => $tablePercent,
            'customerCount' => $customerCount,
            'hourlyHeights' => $hourlyHeights,
            'hourlyVal'     => $hourlyVal,
            'recentOrders'  => $recentOrders,
            'featuredFoods' => $featuredFoods,
            'stockAlerts'   => $stockAlerts,
        ]);
    }
}
