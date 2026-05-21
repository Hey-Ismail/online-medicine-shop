<?php
/**
 * Medicine Model
 * Online Medicine Shop – Task 1 (23-50009-1)
 */

require_once dirname(__DIR__, 3) . '/config/database.php';

class Medicine
{
    private PDO $db;

    private const BASE_SELECT = '
        SELECT  m.*,
                c.name          AS category_name,
                c.category_type AS category_type
        FROM    medicines m
        LEFT JOIN categories c ON c.id = m.category_id
    ';

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // ── Read ─────────────────────────────────────────────────────────────────

    public function getAll(?int $limit = null): array
    {
        $sql  = self::BASE_SELECT . ' ORDER BY m.created_at DESC';
        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int)$limit;
        }
        return $this->db->query($sql)->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(self::BASE_SELECT . ' WHERE m.id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByCategory(int $categoryId): array
    {
        $stmt = $this->db->prepare(
            self::BASE_SELECT . ' WHERE m.category_id = ? ORDER BY m.name'
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * Full-text style search used by the AJAX endpoint.
     * Filters: q (name LIKE), vendor, genre/category name.
     * All parameters are bound – no string interpolation.
     */
    public function search(string $q = '', string $vendor = '', string $genre = ''): array
    {
        $where  = ['1=1'];
        $params = [];

        if ($q !== '') {
            $where[]  = 'm.name LIKE ?';
            $params[] = '%' . $q . '%';
        }
        if ($vendor !== '') {
            $where[]  = 'm.vendor_name LIKE ?';
            $params[] = '%' . $vendor . '%';
        }
        if ($genre !== '') {
            $where[]  = 'c.name LIKE ?';
            $params[] = '%' . $genre . '%';
        }

        $sql  = self::BASE_SELECT
              . ' WHERE ' . implode(' AND ', $where)
              . ' ORDER BY m.name';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Distinct vendor list for the filter dropdown */
    public function getVendors(): array
    {
        return $this->db->query(
            'SELECT DISTINCT vendor_name
             FROM   medicines
             WHERE  vendor_name IS NOT NULL AND vendor_name != ""
             ORDER  BY vendor_name'
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Featured / recently added medicines */
    public function getFeatured(int $limit = 8): array
    {
        return $this->getAll($limit);
    }

    // ── Write (used by Task 2 admin, kept here to share the model) ───────────

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO medicines
                 (name, category_id, vendor_name, price, availability, description, image_path)
             VALUES
                 (:name, :category_id, :vendor_name, :price, :availability, :description, :image_path)'
        );
        $stmt->execute([
            ':name'         => $data['name'],
            ':category_id'  => $data['category_id']  ?? null,
            ':vendor_name'  => $data['vendor_name']  ?? null,
            ':price'        => $data['price'],
            ':availability' => $data['availability'] ?? 0,
            ':description'  => $data['description']  ?? null,
            ':image_path'   => $data['image_path']   ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $allowed = ['name','category_id','vendor_name','price','availability','description','image_path'];
        $sets    = [];
        $params  = [];
        foreach ($data as $col => $val) {
            if (in_array($col, $allowed, true)) {
                $sets[]   = "`$col` = ?";
                $params[] = $val;
            }
        }
        if (empty($sets)) return false;
        $params[] = $id;
        return $this->db->prepare(
            'UPDATE medicines SET ' . implode(', ', $sets) . ' WHERE id = ?'
        )->execute($params);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM medicines WHERE id = ?')->execute([$id]);
    }
}
