<?php
declare(strict_types=1);

class Payment
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $orderId, float $amount, string $method, string $transactionId): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (order_id, amount, payment_method, transaction_id, payment_date)
             VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$orderId, $amount, $method, $transactionId]);
    }
}
