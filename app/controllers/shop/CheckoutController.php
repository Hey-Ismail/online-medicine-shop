<?php
declare(strict_types=1);

class CheckoutController extends Controller
{
    public function addressForm(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        $userId = Auth::userId();
        $cartModel = new Cart($this->db);
        $items = $cartModel->getItemsByUser($userId);

        if (empty($items)) {
            $this->redirect('/cart');
        }

        $userModel = new User($this->db);
        $user = $userModel->getById($userId);

        $address = $_SESSION['checkout_address'] ?? ($user['address'] ?? '');
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $data = $this->baseViewData();
        $data['address'] = $address;
        $data['errors'] = $errors;
        $data['pageScripts'] = ['checkout'];

        $this->render('checkout/address', $data);
    }

    public function saveAddress(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            $_SESSION['errors'] = ['shipping_address' => 'Invalid session. Please try again.'];
            $this->redirect('/checkout');
        }

        $address = trim((string)($_POST['shipping_address'] ?? ''));
        $errors = [];

        if ($address === '') {
            $errors['shipping_address'] = 'Shipping address is required.';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['checkout_address'] = $address;
            $this->redirect('/checkout');
        }

        $_SESSION['checkout_address'] = $address;
        unset($_SESSION['invoice_confirmed']);

        $this->redirect('/checkout/invoice');
    }

    public function invoice(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        $address = trim((string)($_SESSION['checkout_address'] ?? ''));

        if ($address === '') {
            $this->redirect('/checkout');
        }

        $userId = Auth::userId();
        $cartModel = new Cart($this->db);
        $items = $cartModel->getItemsByUser($userId);

        if (empty($items)) {
            $this->redirect('/cart');
        }

        $totals = $cartModel->getTotals($userId);

        $data = $this->baseViewData();
        $data['address'] = $address;
        $data['items'] = $items;
        $data['totals'] = $totals;

        $this->render('checkout/invoice', $data);
    }

    public function confirmInvoice(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            $_SESSION['errors'] = ['invoice' => 'Invalid session. Please try again.'];
            $this->redirect('/checkout/invoice');
        }

        $_SESSION['invoice_confirmed'] = true;

        $this->redirect('/checkout/payment');
    }

    public function paymentForm(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        $address = trim((string)($_SESSION['checkout_address'] ?? ''));

        if (empty($_SESSION['invoice_confirmed']) || $address === '') {
            $this->redirect('/checkout');
        }

        $userId = Auth::userId();
        $cartModel = new Cart($this->db);
        $items = $cartModel->getItemsByUser($userId);

        if (empty($items)) {
            $this->redirect('/cart');
        }

        $totals = $cartModel->getTotals($userId);
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);

        $data = $this->baseViewData();
        $data['items'] = $items;
        $data['totals'] = $totals;
        $data['errors'] = $errors;
        $data['pageScripts'] = ['checkout'];

        $this->render('checkout/payment', $data);
    }

    public function processPayment(): void
    {
        if (!Auth::isCustomer()) {
            $this->redirect('/login');
        }

        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            $_SESSION['errors'] = ['payment' => 'Invalid session. Please try again.'];
            $this->redirect('/checkout/payment');
        }

        $method = trim((string)($_POST['payment_method'] ?? ''));
        $allowedMethods = [
            'Credit Card',
            'bKash',
            'Nagad',
            'Bank Transfer',
            'Cash on Delivery',
        ];

        if (!in_array($method, $allowedMethods, true)) {
            $_SESSION['errors'] = ['payment' => 'Please select a payment method.'];
            $this->redirect('/checkout/payment');
        }

        $address = trim((string)($_SESSION['checkout_address'] ?? ''));

        if ($address === '') {
            $_SESSION['errors'] = ['payment' => 'Shipping address is required.'];
            $this->redirect('/checkout');
        }

        $userId = Auth::userId();
        $cartModel = new Cart($this->db);
        $items = $cartModel->getItemsByUser($userId);

        if (empty($items)) {
            $this->redirect('/cart');
        }

        $totalAmount = 0.0;
        foreach ($items as $item) {
            $totalAmount += ((float)$item['price'] * (int)$item['quantity']);
        }

        try {
            $this->db->beginTransaction();

            $orderModel = new Order($this->db);
            $orderId = $orderModel->create(
                $userId,
                $totalAmount,
                $address,
                'pending',
                $method
            );

            $orderItemModel = new OrderItem($this->db);
            $paymentModel = new Payment($this->db);
            $medicineModel = new Medicine($this->db);

            foreach ($items as $item) {
                $medicineId = (int)$item['medicine_id'];
                $quantity = (int)$item['quantity'];

                if (!$medicineModel->decreaseStock($medicineId, $quantity)) {
                    throw new RuntimeException('Insufficient stock.');
                }

                $orderItemModel->create(
                    $orderId,
                    $medicineId,
                    $quantity,
                    (float)$item['price']
                );
            }

            $paymentModel->create(
                $orderId,
                $totalAmount,
                $method,
                'txn_' . bin2hex(random_bytes(6))
            );

            $cartModel->clearByUser($userId);

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $_SESSION['errors'] = ['payment' => 'Unable to process the order. Please try again.'];
            $this->redirect('/checkout/payment');
        }

        unset($_SESSION['checkout_address'], $_SESSION['invoice_confirmed']);

        $this->redirect('/orders/success?order_id=' . $orderId);
    }
}
