<?php
/**
 * User Model – CRUD operations on the `users` table.
 * Online Medicine Shop – Task 1 (23-50009-1)
 * All queries use PDO prepared statements (SQL-injection safe).
 */

require_once dirname(__DIR__, 3) . '/config/database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Finders ──────────────────────────────────────────────────────────────

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findByRememberToken(string $token): array|false
    {
        if (empty($token)) return false;
        $stmt = $this->db->prepare('SELECT * FROM users WHERE remember_token = ? LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function emailExists(string $email, int $excludeId = 0): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM users WHERE email = ? AND id != ?'
        );
        $stmt->execute([$email, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAll(): array
    {
        return $this->db->query(
            'SELECT id, name, email, role, phone, address, created_at FROM users ORDER BY created_at DESC'
        )->fetchAll();
    }

    // ── Writes ───────────────────────────────────────────────────────────────

    /**
     * Insert a new user. Returns the new user's ID.
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash, role, address, phone)
             VALUES (:name, :email, :password_hash, :role, :address, :phone)'
        );
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':role'          => $data['role'],
            ':address'       => $data['address'] ?? null,
            ':phone'         => $data['phone']   ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Update arbitrary user fields by ID.
     * Only columns present in $data are updated (whitelist enforced).
     */
    public function update(int $id, array $data): bool
    {
        $allowed = [
            'name', 'email', 'password_hash', 'role',
            'profile_picture', 'address', 'phone', 'remember_token',
        ];
        $sets   = [];
        $params = [];
        foreach ($data as $col => $val) {
            if (in_array($col, $allowed, true)) {
                $sets[]            = "`$col` = ?";
                $params[]          = $val;
            }
        }
        if (empty($sets)) return false;
        $params[] = $id;
        $sql  = 'UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?';
        return $this->db->prepare($sql)->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function setRememberToken(int $id, ?string $token): bool
    {
        return $this->update($id, ['remember_token' => $token]);
    }
}
