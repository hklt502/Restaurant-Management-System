<?php
// app/controllers/client/MenuController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/FoodModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';

class MenuController extends BaseController {
    // Trang thực đơn: nhóm món theo danh mục
    public function index() {
        $categories = (new CategoryModel())->getAll();
        $foods      = (new FoodModel())->getAvailable();

        // Gom món theo categoryId => [món, món, ...]
        $foodsByCategory = [];
        foreach ($foods as $food) {
            $foodsByCategory[$food['categoryId']][] = $food;
        }

        $this->render('client/thucdon', [
            'categories'      => $categories,
            'foodsByCategory' => $foodsByCategory,
        ]);
    }
}

