<?php
declare(strict_types=1);

class CartController extends Controller
{
    public function index(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        $userId = Auth::userId();
        $cartModel = new Cart($this->db);
        $items = $cartModel->getItemsByUser($userId);
        $totals = $cartModel->getTotals($userId);

        $data = $this->baseViewData();
        $data['items'] = $items;
        $data['totals'] = $totals;
        $data['pageScripts'] = ['cart'];

        $this->render('cart/index', $data);
    }
}
