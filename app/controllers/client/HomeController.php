<?php
// app/controllers/client/HomeController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/FoodModel.php';

class HomeController extends BaseController {
    // Trang chủ: hiển thị món nổi bật
    public function index() {
        $featuredFoods = (new FoodModel())->getFeatured(3);
        $this->render('client/trangchu', ['featuredFoods' => $featuredFoods]);
    }
}

