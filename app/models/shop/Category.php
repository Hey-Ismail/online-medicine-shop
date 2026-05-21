<?php
declare(strict_types=1);

class Category
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, category_type
             FROM categories
             ORDER BY name ASC'
        );
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
