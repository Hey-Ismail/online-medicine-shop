<?php
declare(strict_types=1);

class Order
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(
        int $userId,
        float $totalAmount,
        string $shippingAddress,
        string $status,
        string $paymentMethod
    ): int {
        $stmt = $this->db->prepare(
            'INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_method, order_date)
             VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$userId, $totalAmount, $shippingAddress, $status, $paymentMethod]);

        return (int)$this->db->lastInsertId();
    }

    public function getByIdForUser(int $orderId, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, user_id, total_amount, shipping_address, status, payment_method, order_date
             FROM orders
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$orderId, $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
