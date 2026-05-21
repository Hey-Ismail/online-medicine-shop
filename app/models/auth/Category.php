<?php
/**
 * Category Model
 * Online Medicine Shop – Task 1 (23-50009-1)
 */

require_once dirname(__DIR__, 3) . '/config/database.php';

class Category
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /** All categories ordered by type then name */
    public function getAll(): array
    {
        return $this->db->query(
            'SELECT * FROM categories ORDER BY category_type, name'
        )->fetchAll();
    }

    /** Categories grouped by type: ['solid' => [...], 'liquid' => [...]] */
    public function getAllGrouped(): array
    {
        $rows   = $this->getAll();
        $groups = ['solid' => [], 'liquid' => []];
        foreach ($rows as $row) {
            $groups[$row['category_type']][] = $row;
        }
        return $groups;
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM categories WHERE category_type = ? ORDER BY name'
        );
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    // ── For Task 2 (admin category management) ────────────────────────────────
    public function create(string $name, string $type): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, category_type) VALUES (?, ?)'
        );
        $stmt->execute([$name, $type]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $type): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name = ?, category_type = ? WHERE id = ?'
        );
        return $stmt->execute([$name, $type, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
