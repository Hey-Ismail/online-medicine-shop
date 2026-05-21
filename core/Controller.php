<?php
declare(strict_types=1);

class Controller
{
    protected array $config;
    protected PDO $db;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db = Database::connection($config['db']);
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $baseUrl = $this->config['base_url'];
        $csrfToken = Csrf::token();

        require dirname(__DIR__) . '/app/views/shop/layouts/header.php';
        require dirname(__DIR__) . '/app/views/shop/' . $view . '.php';
        require dirname(__DIR__) . '/app/views/shop/layouts/footer.php';
    }

    protected function json(array $data, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): void
    {
        $baseUrl = $this->config['base_url'];
        if ($path === '/login') {
            $baseUrl = defined('ROOT_BASE_PATH') ? ROOT_BASE_PATH : BASE_PATH;
        }
        header('Location: ' . $baseUrl . $path);
        exit;
    }

    protected function baseViewData(): array
    {
        $count = 0;

        if (Auth::check()) {
            $cartModel = new Cart($this->db);
            $count = $cartModel->getCountByUser(Auth::userId());
        }

        return ['cartCount' => $count];
    }
}
