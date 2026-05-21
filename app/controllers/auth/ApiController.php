<?php
/**
 * ApiController – JSON endpoints (AJAX)
 * Online Medicine Shop – Task 1 (23-50009-1)
 *
 * Route:  GET /api/medicines/search?q=&vendor=&genre=
 * Returns: application/json  { "medicines": [...] }
 */

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/auth/Medicine.php';

class ApiController
{
    private Medicine $medModel;

    public function __construct()
    {
        $this->medModel = new Medicine();
    }

    /**
     * GET /api/medicines/search
     *
     * Query params (all optional, all sanitised before use):
     *   q      – medicine name search string
     *   vendor – vendor name filter
     *   genre  – category/genre name filter
     *
     * Response: { "medicines": [ { id, name, vendor_name, price,
     *                               availability, category_name,
     *                               category_type, image_path } ] }
     */
    public function search(): void
    {
        // Always return JSON
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');

        // Only GET is supported
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        // Sanitise inputs (strip_tags + trim)
        $q      = trim(strip_tags($_GET['q']      ?? ''));
        $vendor = trim(strip_tags($_GET['vendor']  ?? ''));
        $genre  = trim(strip_tags($_GET['genre']   ?? ''));

        try {
            $rows = $this->medModel->search($q, $vendor, $genre);
        } catch (Throwable $e) {
            error_log('ApiController::search error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Server error. Please try again.']);
            return;
        }

        // Return only the columns the front-end needs (no full row dump)
        $medicines = array_map(static function (array $m): array {
            return [
                'id'            => (int)$m['id'],
                'name'          => $m['name'],
                'vendor_name'   => $m['vendor_name']   ?? '',
                'price'         => number_format((float)$m['price'], 2, '.', ''),
                'availability'  => (int)$m['availability'],
                'category_name' => $m['category_name'] ?? '',
                'category_type' => $m['category_type'] ?? '',
                'image_path'    => $m['image_path']    ?? '',
                'description'   => $m['description']   ?? '',
            ];
        }, $rows);

        echo json_encode(['medicines' => $medicines], JSON_UNESCAPED_UNICODE);
    }
}
