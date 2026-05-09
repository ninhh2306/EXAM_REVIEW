<?php

require_once ROOT . '/app/models/Category.php';

class CategoryController extends Controller
{
    public function index()
    {
        $categoryModel = new Category();

        $allCategories = $categoryModel->getAll();

        $perTab     = 5;
        $total      = count($allCategories);
        $tabCount   = max(1, (int) ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));

        $categories = array_slice(
            $allCategories,
            ($currentTab - 1) * $perTab,
            $perTab
        );

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_old']);

        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_old']);

        $this->viewAdmin('categories/index', [
            'title'       => 'Quản lý Danh mục',
            'categories'  => $categories,
            'tabCount'    => $tabCount,
            'currentTab'  => $currentTab,
            'flashError'  => $flashError,
            'flashOld'    => $flashOld,
        ]);
    }

    // ================= STORE =================
    public function store()
    {
        $categoryModel = new Category();

        $name        = trim($_POST['name']);
        $description = trim($_POST['description']);
        $slug        = trim($_POST['slug']);

        if ($categoryModel->existsFull($name, $slug)) {

            $_SESSION['flash_error'] = 'exists';

            $_SESSION['flash_old'] = [
                'name'        => $name,
                'description' => $description,
                'slug'        => $slug
            ];

            header("Location: /admin/categories");
            exit;
        }

        $categoryModel->create($name, $slug, $description);

        // CLEAR FLASH
        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_old']);

        header("Location: /admin/categories?success=created");
        exit;
    }

    // ================= UPDATE =================
    public function update()
    {
        $categoryModel = new Category();

        $id          = $_POST['id'];
        $name        = trim($_POST['name']);
        $description = trim($_POST['description']);
        $slug        = trim($_POST['slug']);

        if ($categoryModel->existsFullExcept($name, $slug, $id)) {

            $_SESSION['flash_error'] = 'exists';

            $_SESSION['flash_old'] = [
                'id'          => $id,
                'name'        => $name,
                'description' => $description,
                'slug'        => $slug
            ];

            header("Location: /admin/categories");
            exit;
        }

        $categoryModel->update($id, $name, $slug, $description);

        // CLEAR FLASH
        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_old']);

        header("Location: /admin/categories?success=updated");
        exit;
    }

    // ================= DELETE =================
    public function delete($id)
    {
        $categoryModel = new Category();

        $categoryModel->delete($id);

        header("Location: /admin/categories?success=deleted");
        exit;
    }
}