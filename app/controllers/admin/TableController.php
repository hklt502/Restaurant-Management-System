<?php
// app/controllers/admin/TableController.php
require_once __DIR__ . '/../../core/BaseController.php';
require_once __DIR__ . '/../../models/TableModel.php';

class TableController extends BaseController
{
    public function index()
    {
        $tables = [];
        try {
            $tableModel = new TableModel();
            $tables     = $tableModel->getAll();
        } catch (Throwable $e) {
            $tables = [];
        }

        $this->render('admin/tables', [
            'tables' => $tables
        ]);
    }
}
