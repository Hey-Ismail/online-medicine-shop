<?php
declare(strict_types=1);

class OrderController extends Controller
{
    public function success(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

        if ($orderId <= 0) {
            $this->redirect('/cart');
        }

        $orderModel = new Order($this->db);
        $order = $orderModel->getByIdForUser($orderId, Auth::userId());

        if (!$order) {
            http_response_code(404);
            echo 'Order not found';
            exit;
        }

        $orderItemModel = new OrderItem($this->db);
        $items = $orderItemModel->getByOrderId($orderId);

        $data = $this->baseViewData();
        $data['order'] = $order;
        $data['items'] = $items;

        $this->render('orders/success', $data);
    }
}
