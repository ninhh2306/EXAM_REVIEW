<?php

require_once ROOT . '/app/models/Grade.php';

class GradeController extends Controller
{
    // index() — thêm đọc flash session
    public function index()
    {
        $model     = new Grade();
        $allGrades = $model->getAll();

        $perTab     = 5;
        $total      = count($allGrades);
        $tabCount   = max(1, (int) ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));
        $grades     = array_slice($allGrades, ($currentTab - 1) * $perTab, $perTab);

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $this->viewAdmin("grades/index", [
            "title"      => "Quản lý Khối lớp",
            "grades"     => $grades,
            "tabCount"   => $tabCount,
            "currentTab" => $currentTab,
            "flashError" => $flashError,
            "flashOld"   => $flashOld,
        ]);
    }


    public function store()
{
    $name    = trim($_POST['name'] ?? '');
    $slugInp = trim($_POST['slug'] ?? '');

    $model = new Grade();
    $slug  = empty($slugInp) ? $model->toSlug($name) : $model->toSlug($slugInp);

    if ($model->existsFull($name, $slug)) {

        $_SESSION['flash_error'] = 'exists';
        $_SESSION['flash_old']   = [
            'name' => $name,
            'slug' => $slug,
        ];

        header("Location: /admin/grades");
        exit;
    }

    $model->create($name, $slug);

    unset($_SESSION['flash_error'], $_SESSION['flash_old']);

    header("Location: /admin/grades?success=created");
    exit;
}

    public function update()
    {
        $id      = $_POST['id']   ?? null;
        $name    = trim($_POST['name']  ?? '');
        $slugInp = trim($_POST['slug']  ?? '');

        if (!$id) {
            header("Location: /admin/grades");
            exit;
        }

        $model = new Grade();
        $slug  = empty($slugInp) ? $model->toSlug($name) : $model->toSlug($slugInp);

        if ($model->existsFullExcept($name, $slug, $id)) {

            $_SESSION['flash_error'] = 'exists';
            $_SESSION['flash_old']   = [
                'id'   => $id,
                'name' => $name,
                'slug' => $slug,
            ];

            header("Location: /admin/grades");
            exit;
        }

        $model->update($id, $name, $slug);

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        header("Location: /admin/grades?success=updated");
        exit;
    }

    
    public function delete($id)
    {
        $model = new Grade();
        $model->delete($id);

        header("Location: /admin/grades?success=deleted");
        exit;
    }
}