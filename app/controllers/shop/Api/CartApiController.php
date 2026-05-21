<?php
declare(strict_types=1);

class CartApiController extends Controller
{
    public function add(): void
    {
        if (!Auth::isCustomer()) {
            $this->json(['success' => false, 'error' => 'Login required.'], 401);
        }

        if (!Csrf::validate(Csrf::fromRequest())) {
            $this->json(['success' => false, 'error' => 'Invalid session.'], 400);
        }

        $medicineId = (int)($_POST['medicine_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($medicineId <= 0 || $quantity <= 0) {
            $this->json(['success' => false, 'error' => 'Invalid medicine or quantity.'], 422);
        }

        $medicineModel = new Medicine($this->db);
        $medicine = $medicineModel->getById($medicineId);

        if (!$medicine) {
            $this->json(['success' => false, 'error' => 'Medicine not found.'], 404);
        }

        $available = (int)$medicine['availability'];

        if ($quantity > $available) {
            $this->json(['success' => false, 'error' => 'Requested quantity exceeds stock.'], 422);
        }

        $cartModel = new Cart($this->db);
        $userId = Auth::userId();
        $existing = $cartModel->getQuantity($userId, $medicineId);
        $newQty = $existing + $quantity;

        if ($newQty > $available) {
            $this->json(['success' => false, 'error' => 'Requested quantity exceeds stock.'], 422);
        }

        $cartModel->setQuantity($userId, $medicineId, $newQty);
        $cartCount = $cartModel->getCountByUser($userId);

        $this->json(['success' => true, 'cartCount' => $cartCount]);
    }

    public function update(): void
    {
        if (!Auth::isCustomer()) {
            $this->json(['success' => false, 'error' => 'Login required.'], 401);
        }

        if (!Csrf::validate(Csrf::fromRequest())) {
            $this->json(['success' => false, 'error' => 'Invalid session.'], 400);
        }

        $medicineId = (int)($_POST['medicine_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($medicineId <= 0 || $quantity <= 0) {
            $this->json(['success' => false, 'error' => 'Invalid medicine or quantity.'], 422);
        }

        $medicineModel = new Medicine($this->db);
        $medicine = $medicineModel->getById($medicineId);

        if (!$medicine) {
            $this->json(['success' => false, 'error' => 'Medicine not found.'], 404);
        }

        $available = (int)$medicine['availability'];

        if ($quantity > $available) {
            $this->json(['success' => false, 'error' => 'Requested quantity exceeds stock.'], 422);
        }

        $cartModel = new Cart($this->db);
        $userId = Auth::userId();
        $cartModel->setQuantity($userId, $medicineId, $quantity);

        $totals = $cartModel->getTotals($userId);
        $itemSubtotal = (float)$medicine['price'] * $quantity;

        $this->json([
            'success' => true,
            'cartCount' => $totals['item_count'],
            'cartTotal' => $totals['total_amount'],
            'itemSubtotal' => $itemSubtotal,
            'quantity' => $quantity,
        ]);
    }

    public function remove(): void
    {
        if (!Auth::isCustomer()) {
            $this->json(['success' => false, 'error' => 'Login required.'], 401);
        }

        $input = $this->readInput();
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['_csrf'] ?? null);

        if (!Csrf::validate(is_string($token) ? $token : null)) {
            $this->json(['success' => false, 'error' => 'Invalid session.'], 400);
        }

        $medicineId = isset($input['medicine_id']) ? (int)$input['medicine_id'] : 0;

        if ($medicineId <= 0) {
            $this->json(['success' => false, 'error' => 'Invalid medicine.'], 422);
        }

        $cartModel = new Cart($this->db);
        $userId = Auth::userId();
        $cartModel->removeItem($userId, $medicineId);

        $totals = $cartModel->getTotals($userId);

        $this->json([
            'success' => true,
            'cartCount' => $totals['item_count'],
            'cartTotal' => $totals['total_amount'],
        ]);
    }

    private function readInput(): array
    {
        $raw = file_get_contents('php://input') ?: '';
        $data = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (stripos($contentType, 'application/json') !== false) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        } else {
            parse_str($raw, $data);
        }

        return $data;
    }
}
