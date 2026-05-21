<?php
declare(strict_types=1);

class Cart
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getItemsByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.medicine_id, c.quantity, m.name, m.vendor_name, m.price, m.availability, m.image_path
             FROM cart c
             INNER JOIN medicines m ON m.id = c.medicine_id
             WHERE c.user_id = ?
             ORDER BY c.added_at DESC'
        );
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function getCountByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(quantity), 0) AS item_count FROM cart WHERE user_id = ?'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        return $row ? (int)$row['item_count'] : 0;
    }

    public function getTotals(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(c.quantity), 0) AS item_count,
                    COALESCE(SUM(c.quantity * m.price), 0) AS total_amount
             FROM cart c
             INNER JOIN medicines m ON m.id = c.medicine_id
             WHERE c.user_id = ?'
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch();

        return [
            'item_count' => $row ? (int)$row['item_count'] : 0,
            'total_amount' => $row ? (float)$row['total_amount'] : 0.0,
        ];
    }

    public function getQuantity(int $userId, int $medicineId): int
    {
        $stmt = $this->db->prepare(
            'SELECT quantity FROM cart WHERE user_id = ? AND medicine_id = ?'
        );
        $stmt->execute([$userId, $medicineId]);
        $row = $stmt->fetch();

        return $row ? (int)$row['quantity'] : 0;
    }

    public function setQuantity(int $userId, int $medicineId, int $quantity): void
    {
        $existing = $this->getQuantity($userId, $medicineId);

        if ($existing === 0) {
            $stmt = $this->db->prepare(
                'INSERT INTO cart (user_id, medicine_id, quantity, added_at)
                 VALUES (?, ?, ?, NOW())'
            );
            $stmt->execute([$userId, $medicineId, $quantity]);
        } else {
            $stmt = $this->db->prepare(
                'UPDATE cart SET quantity = ? WHERE user_id = ? AND medicine_id = ?'
            );
            $stmt->execute([$quantity, $userId, $medicineId]);
        }
    }

    public function removeItem(int $userId, int $medicineId): void
    {
        $stmt = $this->db->prepare(
            'DELETE FROM cart WHERE user_id = ? AND medicine_id = ?'
        );
        $stmt->execute([$userId, $medicineId]);
    }

    public function clearByUser(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM cart WHERE user_id = ?');
        $stmt->execute([$userId]);
    }
}
