<?php

require_once ROOT . '/app/models/Category.php';

class CategoryController extends Controller
{
    public function index()
    {
        $categoryModel = new Category();
        $perTab = 5;
        $total = $categoryModel->countAllAdmin();

        $tabCount = max(1, ceil($total / $perTab));

        $currentTab = max(
            1,
            min($tabCount, (int)($_GET['tab'] ?? 1))
        );

        $offset = ($currentTab - 1) * $perTab;

        $categories = $categoryModel->getPaginate(
            $perTab,
            $offset
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

        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $slugInput   = trim($_POST['slug'] ?? '');

        $nameError = $categoryModel->validateName($name);

        $slug = $slugInput
            ? $categoryModel->toSlug($slugInput)
            : $categoryModel->toSlug($name);

        $_SESSION['flash_old'] = [
            'name'        => $name,
            'description' => $description,
            'slug'        => $slugInput
        ];

        if ($nameError) {

            $_SESSION['flash_error'] = $nameError;

            header("Location: /admin/categories");
            exit;
        }

        if ($categoryModel->existsFull($name, $slug)) {

            $_SESSION['flash_error'] = 'exists';

            header("Location: /admin/categories");
            exit;
        }

        $categoryModel->create($name, $slug, $description);

        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_old']);

        header("Location: /admin/categories?success=created");
        exit;
    }


    // ================= UPDATE =================
    public function update()
    {
        $categoryModel = new Category();

        $id          = (int)($_POST['id'] ?? 0);
        $name        = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $slugInput   = trim($_POST['slug'] ?? '');

        $nameError = $categoryModel->validateName($name);

        $slug = $slugInput
            ? $categoryModel->toSlug($slugInput)
            : $categoryModel->toSlug($name);

        $_SESSION['flash_old'] = [
            'id'          => $id,
            'name'        => $name,
            'description' => $description,
            'slug'        => $slugInput
        ];

        if ($nameError) {

            $_SESSION['flash_error'] = $nameError;

            header("Location: /admin/categories");
            exit;
        }

        if ($categoryModel->existsFullExcept($name, $slug, $id)) {

            $_SESSION['flash_error'] = 'exists';

            header("Location: /admin/categories");
            exit;
        }

        $categoryModel->update($id, $name, $slug, $description);

        unset($_SESSION['flash_error']);
        unset($_SESSION['flash_old']);

        header("Location: /admin/categories?success=updated");
        exit;
    }

    // ================= DELETE =================
    public function delete($id)
    {
        $categoryModel = new Category();

        if ($categoryModel->hasPosts($id)) {
            header("Location: /admin/categories?error=has_posts");
            exit;
        }

        $categoryModel->delete($id);

        header("Location: /admin/categories?success=deleted");
        exit;
    }


    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $categoryModel = new Category();

        if ($keyword === '') {

            $categories = $categoryModel->getPaginate(5, 0);

        } else {

            $categories = $categoryModel->search($keyword);
        }

        if (empty($categories)) {

            echo '
            <tr>
                <td colspan="5" class="text-center">
                    Không có danh mục nào
                </td>
            </tr>
            ';

            return;
        }

        foreach ($categories as $c) {
            ?>

            <tr>
                <td><?= $c['categoryId'] ?></td>
                <td><?= htmlspecialchars($c['categoryName']) ?></td>

                <td class="category-desc">
                    <?= htmlspecialchars($c['description'] ?? '') ?>
                </td>

                <td><?= htmlspecialchars($c['slug']) ?></td>

                <td>
                    <div class="admin-actions">
                        <button class="action-btn btn-edit"
                            data-id="<?= $c['categoryId'] ?>"
                            data-name="<?= htmlspecialchars($c['categoryName'], ENT_QUOTES) ?>"
                            data-description="<?= htmlspecialchars($c['description'] ?? '', ENT_QUOTES) ?>"
                            data-slug="<?= htmlspecialchars($c['slug'], ENT_QUOTES) ?>"
                            onclick="openEditCategory(this)">
                            ✏
                        </button>

                        <button class="action-btn btn-delete"
                            onclick="openDeleteCategory(<?= $c['categoryId'] ?>)">
                            🗑
                        </button>

                    </div>
                </td>
            </tr>

            <?php
        }
    }

}