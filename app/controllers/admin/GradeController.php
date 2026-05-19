<?php

require_once ROOT . '/app/models/Grade.php';

class GradeController extends Controller
{
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


    public function checkName()
    {
        $name = trim($_GET['name'] ?? '');
        $id   = $_GET['id'] ?? null;

        $model = new Grade();

        if ($id) {
            $exists = $model->existsNameExcept($name, $id);
        } else {
            $exists = $model->existsName($name);
        }

        echo json_encode([
            'exists' => $exists
        ]);
    }


    public function store()
    {
        $name    = trim($_POST['name'] ?? '');
        $slugInp = trim($_POST['slug'] ?? '');

        $model = new Grade();

        $slug = empty($slugInp)
            ? $model->toSlug($name)
            : $model->toSlug($slugInp);

        // ===== CHECK NAME =====
        if ($model->existsName($name)) {

            $_SESSION['flash_error'] = 'name_exists';

            $_SESSION['flash_old'] = [
                'name' => $name,
                'slug' => $slug,
            ];

            header("Location: /admin/grades");
            exit;
        }

        // ===== CHECK SLUG =====
        if ($model->existsSlug($slug)) {

            $_SESSION['flash_error'] = 'slug_exists';

            $_SESSION['flash_old'] = [
                'name' => $name,
                'slug' => $slug,
            ];

            header("Location: /admin/grades");
            exit;
        }

        // ===== CREATE =====
        $model->create($name, $slug);

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        header("Location: /admin/grades?success=created");
        exit;
    }

    public function update()
    {
        $id      = $_POST['id'] ?? null;
        $name    = trim($_POST['name'] ?? '');
        $slugInp = trim($_POST['slug'] ?? '');

        if (!$id) {
            header("Location: /admin/grades");
            exit;
        }

        $model = new Grade();

        $slug = empty($slugInp)
            ? $model->toSlug($name)
            : $model->toSlug($slugInp);

        // ===== CHECK NAME =====
        if ($model->existsNameExcept($name, $id)) {

            $_SESSION['flash_error'] = 'name_exists';

            $_SESSION['flash_old'] = [
                'id'   => $id,
                'name' => $name,
                'slug' => $slug,
            ];

            header("Location: /admin/grades");
            exit;
        }

        // ===== CHECK SLUG =====
        if ($model->existsSlugExcept($slug, $id)) {

            $_SESSION['flash_error'] = 'slug_exists';

            $_SESSION['flash_old'] = [
                'id'   => $id,
                'name' => $name,
                'slug' => $slug,
            ];

            header("Location: /admin/grades");
            exit;
        }

        // ===== UPDATE =====
        $model->update($id, $name, $slug);

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        header("Location: /admin/grades?success=updated");
        exit;
    }

    public function delete($id)
    {
        $gradeModel = new Grade();

        if ($gradeModel->hasSubjects($id)) {
            header("Location: /admin/grades?error=has_subjects");
            exit;
        }

        if ($gradeModel->hasExams($id)) {
            header("Location: /admin/grades?error=has_exams");
            exit;
        }

        $gradeModel->delete($id);

        header("Location: /admin/grades?success=deleted");
        exit;
    }


    public function ajaxSearch()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $model = new Grade();

        if ($keyword === '') {
            echo json_encode($model->getAll());
            return;
        }

        echo json_encode(
            $model->search($keyword)
        );
    }

}