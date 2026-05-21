<?php
class Medicine {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all medicines with category info
     */
    public function getAll() {
        $stmt = $this->pdo->prepare(
            "SELECT m.*, c.name as category_name, c.category_type 
             FROM medicines m 
             LEFT JOIN categories c ON m.category_id = c.id 
             ORDER BY m.name ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get medicine by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT m.*, c.name as category_name 
             FROM medicines m 
             LEFT JOIN categories c ON m.category_id = c.id 
             WHERE m.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Create a new medicine
     */
    public function create($name, $category_id, $vendor_name, $price, $availability, $description, $image_path) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO medicines (name, category_id, vendor_name, price, availability, description, image_path) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$name, $category_id, $vendor_name, $price, $availability, $description, $image_path]);
    }

    /**
     * Update medicine
     */
    public function update($id, $name, $category_id, $vendor_name, $price, $availability, $description, $image_path = null) {
        if ($image_path) {
            $stmt = $this->pdo->prepare(
                "UPDATE medicines SET name = ?, category_id = ?, vendor_name = ?, price = ?, availability = ?, description = ?, image_path = ? WHERE id = ?"
            );
            return $stmt->execute([$name, $category_id, $vendor_name, $price, $availability, $description, $image_path, $id]);
        } else {
            $stmt = $this->pdo->prepare(
                "UPDATE medicines SET name = ?, category_id = ?, vendor_name = ?, price = ?, availability = ?, description = ? WHERE id = ?"
            );
            return $stmt->execute([$name, $category_id, $vendor_name, $price, $availability, $description, $id]);
        }
    }

    /**
     * Delete medicine (only if not in pending orders)
     */
    public function delete($id) {
        // Check if medicine is in any pending order
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) as count FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE oi.medicine_id = ? AND o.status = 'pending'"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            throw new Exception("Cannot delete medicine: Item exists in pending orders");
        }

        // Get image path and delete image
        $medicine = $this->getById($id);
        if ($medicine && $medicine['image_path']) {
            $filePath = ROOT_DIR . '/public/' . $medicine['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $stmt = $this->pdo->prepare("DELETE FROM medicines WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get medicines count
     */
    public function getCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM medicines");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
