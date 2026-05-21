<?php
class Category {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all categories
     */
    public function getAll() {
        $stmt = $this->pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get category by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Create a new category
     */
    public function create($name, $category_type) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO categories (name, category_type) VALUES (?, ?)"
        );
        return $stmt->execute([$name, $category_type]);
    }

    /**
     * Update category
     */
    public function update($id, $name, $category_type) {
        $stmt = $this->pdo->prepare(
            "UPDATE categories SET name = ?, category_type = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $category_type, $id]);
    }

    /**
     * Delete category (only if no medicines exist)
     */
    public function delete($id) {
        // Check if medicines exist
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM medicines WHERE category_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            throw new Exception("Cannot delete category: Medicines exist in this category");
        }

        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Check if category name exists
     */
    public function exists($name, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM categories WHERE name = ?";
        $params = [$name];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}
?>
