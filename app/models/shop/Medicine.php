<?php
declare(strict_types=1);

class Medicine
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, category_id, vendor_name, price, availability, description, image_path
             FROM medicines
             WHERE id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function getAll(?int $categoryId, ?string $vendor): array
    {
        $sql = 'SELECT id, name, category_id, vendor_name, price, availability, description, image_path
                FROM medicines';
        $conditions = [];
        $params = [];

        if ($categoryId !== null) {
            $conditions[] = 'category_id = ?';
            $params[] = $categoryId;
        }

        if ($vendor !== null && $vendor !== '') {
            $conditions[] = 'vendor_name = ?';
            $params[] = $vendor;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getVendors(): array
    {
        $stmt = $this->db->prepare(
            'SELECT DISTINCT vendor_name
             FROM medicines
             WHERE vendor_name IS NOT NULL AND vendor_name <> ""
             ORDER BY vendor_name ASC'
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $vendors = [];
        foreach ($rows as $row) {
            $vendors[] = (string)$row['vendor_name'];
        }

        return $vendors;
    }

    public function decreaseStock(int $medicineId, int $quantity): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE medicines
             SET availability = availability - ?
             WHERE id = ? AND availability >= ?'
        );
        $stmt->execute([$quantity, $medicineId, $quantity]);

        return $stmt->rowCount() > 0;
    }
}
