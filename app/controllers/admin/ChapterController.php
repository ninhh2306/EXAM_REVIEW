<?php

require_once ROOT . '/app/models/Chapter.php';
require_once ROOT . '/app/models/Subject.php';
require_once ROOT . '/app/models/Grade.php';

class ChapterController extends Controller
{
    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $chapterModel = new Chapter();
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $allChapters = $chapterModel->getAll();
        $subjects    = $subjectModel->getAll();
        $grades      = $gradeModel->getAll();

        $perTab     = 5;
        $total      = count($allChapters);
        $tabCount   = max(1, (int) ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));
        $chapters   = array_slice($allChapters, ($currentTab - 1) * $perTab, $perTab);

        // Lấy flash data từ session (chỉ dùng 1 lần rồi xóa)
        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];
        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $this->viewAdmin("chapters/index", [
            "title"      => "Quản lý Chương học",
            "chapters"   => $chapters,
            "subjects"   => $subjects,
            "grades"     => $grades,
            "tabCount"   => $tabCount,
            "currentTab" => $currentTab,
            "flashError" => $flashError,
            "flashOld"   => $flashOld,
        ]);
    }

    public function getBySubject()
    {
        $subjectId = $_GET['subject_id'] ?? 0;
        $model = new Chapter();
        $data  = $model->getBySubject($subjectId);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id        = $_POST['id']        ?? null;
        $name      = trim($_POST['name'] ?? '');
        $subjectId = (int)($_POST['subjectId'] ?? 0);
        $gradeId   = (int)($_POST['gradeId']   ?? 0);
        $sortOrder = (int)($_POST['sortOrder']  ?? 0);
        $slug      = 'chuong-' . $sortOrder;

        $model = new Chapter();

        $oldData = [
            'id'        => $id,
            'name'      => $name,
            'subjectId' => $subjectId,
            'gradeId'   => $gradeId,
            'sortOrder' => $sortOrder,
            'slug'      => $slug,
        ];

        // CHECK TRÙNG SORT ORDER
        if ($model->existsSortOrder($subjectId, $sortOrder, $id ?: null)) {
            $_SESSION['flash_error'] = 'sort_exists';
            $_SESSION['flash_old']   = $oldData;
            header("Location: /admin/chapters");
            exit;
        }

        // CHECK TRÙNG TÊN / SLUG
        if (!$id && $model->exists($name, $slug, $subjectId)) {
            $_SESSION['flash_error'] = 'exists';
            $_SESSION['flash_old']   = $oldData;
            header("Location: /admin/chapters");
            exit;
        }

        if ($id) {
            $model->update($id, $name, $slug, $subjectId, $sortOrder);
            header("Location: /admin/chapters?success=updated");
        } else {
            $model->create($name, $slug, $subjectId, $sortOrder);
            header("Location: /admin/chapters?success=created");
        }
        exit;
    }

    public function delete($id)
    {
        $id = $_GET['id'];
        $chapterModel = new Chapter();
        $chapterModel->delete($id);
        header("Location: /admin/chapters?success=deleted");
    }
}