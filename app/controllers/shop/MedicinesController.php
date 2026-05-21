<?php
declare(strict_types=1);

class MedicinesController extends Controller
{
    public function index(): void
    {
        $vendor = isset($_GET['vendor']) ? trim((string)$_GET['vendor']) : '';
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

        $medicineModel = new Medicine($this->db);
        $categoryModel = new Category($this->db);

        $medicines = $medicineModel->getAll(
            $categoryId > 0 ? $categoryId : null,
            $vendor !== '' ? $vendor : null
        );
        $categories = $categoryModel->getAll();
        $vendors = $medicineModel->getVendors();

        $data = $this->baseViewData();
        $data['medicines'] = $medicines;
        $data['categories'] = $categories;
        $data['vendors'] = $vendors;
        $data['filters'] = [
            'vendor' => $vendor,
            'category_id' => $categoryId,
        ];
        $data['pageScripts'] = ['cart'];

        $this->render('medicines/index', $data);
    }
}
