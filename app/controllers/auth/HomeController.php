<?php
/**
 * HomeController – Home page, medicine listing, category browsing
 * Online Medicine Shop – Task 1 (23-50009-1)
 */

require_once dirname(__DIR__, 3) . '/config/config.php';
require_once dirname(__DIR__, 3) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/models/auth/Category.php';
require_once dirname(__DIR__, 2) . '/models/auth/Medicine.php';

class HomeController
{
    private Category $catModel;
    private Medicine $medModel;

    public function __construct()
    {
        $this->catModel = new Category();
        $this->medModel = new Medicine();
    }

    // ── Home page ─────────────────────────────────────────────────────────────

    /**
     * Shows:
     *  • Category listing grouped by liquid / solid
     *  • Featured medicine cards (latest 8)
     */
    public function index(): void
    {
        $groupedCategories = $this->catModel->getAllGrouped();
        $featuredMedicines = $this->medModel->getFeatured(8);
        $vendors           = $this->medModel->getVendors();
        $allCategories     = $this->catModel->getAll();

        $this->render('home/index', [
            'groupedCategories' => $groupedCategories,
            'featuredMedicines' => $featuredMedicines,
            'vendors'           => $vendors,
            'allCategories'     => $allCategories,
            'pageTitle'         => 'Home – MediShop',
        ]);
    }

    // ── All medicines with search bar & filters ───────────────────────────────

    public function medicines(): void
    {
        $medicines     = $this->medModel->getAll();
        $allCategories = $this->catModel->getAll();
        $vendors       = $this->medModel->getVendors();

        $this->render('medicines/browse', [
            'medicines'     => $medicines,
            'allCategories' => $allCategories,
            'vendors'       => $vendors,
            'activeCategory'=> null,
            'pageTitle'     => 'All Medicines – MediShop',
        ]);
    }

    // ── Medicines filtered by category ───────────────────────────────────────

    public function category(int $id): void
    {
        $category = $this->catModel->findById($id);
        if (!$category) {
            set_flash('danger', 'Category not found.');
            redirect('medicines');
        }

        // Optional liquid/solid sub-filter from query string
        $typeFilter = $_GET['type'] ?? '';

        if ($typeFilter === 'liquid' || $typeFilter === 'solid') {
            // Honour sub-type filter: only show if category_type matches
            if ($category['category_type'] !== $typeFilter) {
                $medicines = [];
            } else {
                $medicines = $this->medModel->getByCategory($id);
            }
        } else {
            $medicines = $this->medModel->getByCategory($id);
        }

        $allCategories = $this->catModel->getAll();
        $vendors       = $this->medModel->getVendors();

        $this->render('medicines/browse', [
            'medicines'      => $medicines,
            'allCategories'  => $allCategories,
            'vendors'        => $vendors,
            'activeCategory' => $category,
            'pageTitle'      => e($category['name']) . ' – MediShop',
        ]);
    }

    // ── Private helper ────────────────────────────────────────────────────────

    private function render(string $view, array $data = []): void
    {
        extract($data);
        include dirname(__DIR__, 2) . '/views/auth/' . $view . '.php';
    }
}
