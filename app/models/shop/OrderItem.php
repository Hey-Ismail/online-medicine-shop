<?php
declare(strict_types=1);

class OrderItem
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $orderId, int $medicineId, int $quantity, float $unitPrice): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO order_items (order_id, medicine_id, quantity, unit_price)
             VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$orderId, $medicineId, $quantity, $unitPrice]);
    }

    public function getByOrderId(int $orderId): array
    {
        $stmt = $this->db->prepare(
            'SELECT oi.medicine_id, oi.quantity, oi.unit_price, m.name, m.vendor_name
             FROM order_items oi
             INNER JOIN medicines m ON m.id = oi.medicine_id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }
}
