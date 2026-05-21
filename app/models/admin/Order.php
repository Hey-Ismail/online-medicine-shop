<?php
class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all orders with customer info
     */
    public function getAll($status = null) {
        $query = "SELECT o.*, u.name as customer_name, u.email as customer_email 
                  FROM orders o 
                  JOIN users u ON o.user_id = u.id";

        if ($status) {
            $query .= " WHERE o.status = ?";
            $stmt = $this->pdo->prepare($query . " ORDER BY o.order_date DESC");
            $stmt->execute([$status]);
        } else {
            $stmt = $this->pdo->prepare($query . " ORDER BY o.order_date DESC");
            $stmt->execute();
        }

        return $stmt->fetchAll();
    }

    /**
     * Get order by ID with items
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT o.*, u.name as customer_name, u.email as customer_email 
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             WHERE o.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get order items
     */
    public function getOrderItems($order_id) {
        $stmt = $this->pdo->prepare(
            "SELECT oi.*, m.name as medicine_name, m.image_path 
             FROM order_items oi 
             JOIN medicines m ON oi.medicine_id = m.id 
             WHERE oi.order_id = ?"
        );
        $stmt->execute([$order_id]);
        return $stmt->fetchAll();
    }

    /**
     * Update order status
     */
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    /**
     * Get pending orders count
     */
    public function getPendingCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Get all accepted orders (purchase history)
     */
    public function getAcceptedOrders() {
        $stmt = $this->pdo->prepare(
            "SELECT o.*, u.name as customer_name, u.email as customer_email 
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             WHERE o.status = 'accepted' 
             ORDER BY o.order_date DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
