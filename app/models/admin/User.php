<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Get all customers
     */
    public function getAllCustomers() {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, email, phone, address, created_at FROM users WHERE role = 'customer' ORDER BY created_at DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT id, name, email, role, profile_picture, address, phone FROM users WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get customer count
     */
    public function getCustomerCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }

    /**
     * Delete customer (cascade delete cart and orders)
     */
    public function deleteCustomer($id) {
        try {
            $this->pdo->beginTransaction();

            // Delete cart items
            $stmt = $this->pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$id]);

            // Delete order items
            $stmt = $this->pdo->prepare(
                "DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)"
            );
            $stmt->execute([$id]);

            // Delete orders
            $stmt = $this->pdo->prepare("DELETE FROM orders WHERE user_id = ?");
            $stmt->execute([$id]);

            // Delete payments
            $stmt = $this->pdo->prepare(
                "DELETE FROM payments WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)"
            );
            $stmt->execute([$id]);

            // Delete user
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
            $result = $stmt->execute([$id]);

            $this->pdo->commit();
            return $result;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
?>
