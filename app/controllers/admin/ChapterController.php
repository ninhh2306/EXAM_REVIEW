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
        $grades      = $gradeModel->all();

        $perTab     = 5;
        $total      = count($allChapters);
        $tabCount   = max(1, (int) ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));
        $chapters   = array_slice($allChapters, ($currentTab - 1) * $perTab, $perTab);

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
        $subjectId = (int)($_GET['subject_id'] ?? 0);
        $model = new Chapter();
        $data  = $model->getBySubject($subjectId);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $model = new Chapter();

        $id            = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name          = trim($_POST['name'] ?? '');
        $subjectId     = (int)($_POST['subjectId'] ?? 0);
        $gradeId       = (int)($_POST['gradeId']   ?? 0);
        $positionValue = $_POST['positionValue'] ?? 'last';
        $slug          = $model->toSlug($name);

        // =========================
        // CHỈ TÍNH sortOrder — CHƯA ĐỤNG DB
        // =========================

        $sortOrder = 1;

        if ($positionValue === 'last') {
            $sortOrder = $model->getMaxSortOrder($subjectId) + 1;

        } elseif ($positionValue === 'first') {
            $sortOrder = 1;

        } elseif (str_starts_with($positionValue, 'after-')) {
            $afterChapterId = (int)str_replace('after-', '', $positionValue);
            $afterSort      = $model->getSortOrder($afterChapterId, $subjectId);
            $sortOrder      = $afterSort + 1;
        }

        // =========================
        // FLASH OLD
        // =========================

        $_SESSION['flash_old'] = [
            'id'        => $id,
            'name'      => $name,
            'subjectId' => $subjectId,
            'gradeId'   => $gradeId,
            'sortOrder' => $sortOrder,
            'slug'      => $slug,
        ];

        // =========================
        // VALIDATE — trước khi đụng DB
        // =========================

        if (empty($name) || !$subjectId || !$gradeId) {
            $_SESSION['flash_error'] = 'empty';
            header("Location: /admin/chapters");
            exit;
        }

        if ($model->existsName($name, $subjectId, $id)) {
            $_SESSION['flash_error'] = 'name_exists';
            header("Location: /admin/chapters");
            exit;
        }

        // =========================
        // UPDATE
        // =========================

        if ($id) {

            $oldChapter   = $model->getById($id);
            $oldSubjectId = (int)$oldChapter['subjectId'];
            $oldSortOrder = (int)$oldChapter['sortOrder'];

            // ---------------------------
            // CÙNG SUBJECT
            // ---------------------------

            if ($oldSubjectId === $subjectId) {

                // FIX BUG 2: Lấy afterSort TRƯỚC khi decreaseSortOrders
                // vì decreaseSortOrders sẽ thay đổi sortOrder của các chapter phía sau
                $precomputedAfterSort = null;
                if (str_starts_with($positionValue, 'after-')) {
                    $afterChapterId       = (int)str_replace('after-', '', $positionValue);
                    $precomputedAfterSort = $model->getSortOrder($afterChapterId, $subjectId);
                }

                // Đưa chapter ra khỏi danh sách (sortOrder = 0 là giá trị tạm)
                $model->update($id, $name, $slug, $subjectId, 0);

                // Dồn các chapter phía sau vị trí cũ lên 1
                $model->decreaseSortOrders($subjectId, $oldSortOrder);

                // Tính sortOrder mới và shift DB để nhường chỗ
                if ($positionValue === 'first') {

                    $sortOrder = 1;
                    $model->increaseSortOrders($subjectId, 1);

                } elseif ($precomputedAfterSort !== null) {

                    // Nếu afterChapter nằm sau vị trí cũ, sortOrder của nó
                    // đã bị giảm 1 bởi decreaseSortOrders → điều chỉnh lại
                    $adjustedAfterSort = ($precomputedAfterSort > $oldSortOrder)
                        ? $precomputedAfterSort - 1
                        : $precomputedAfterSort;

                    $sortOrder = $adjustedAfterSort + 1;
                    $model->increaseSortOrders($subjectId, $sortOrder);

                } else {
                    // positionValue === 'last'
                    $sortOrder = $model->getMaxSortOrder($subjectId) + 1;
                }

            // ---------------------------
            // ĐỔI SUBJECT
            // ---------------------------

            } else {

                // Dọn vị trí cũ
                $model->decreaseSortOrders($oldSubjectId, $oldSortOrder);
                $model->reorderSortOrders($oldSubjectId);

                // Luôn thêm vào cuối subject mới khi đổi subject
                // (positionValue trong trường hợp đổi subject thường không áp dụng)
                $sortOrder = $model->getMaxSortOrder($subjectId) + 1;
            }

            $model->update($id, $name, $slug, $subjectId, $sortOrder);
            $model->reorderSortOrders($subjectId);

            header("Location: /admin/chapters?success=updated");
            exit;
        }

        // =========================
        // CREATE
        // =========================

        // FIX BUG 1: Shift DB sau validate, ngay trước khi create
        if ($positionValue === 'first') {
            $model->increaseSortOrders($subjectId, 1);

        } elseif (str_starts_with($positionValue, 'after-')) {
            $model->increaseSortOrders($subjectId, $sortOrder);
        }

        $model->create($name, $slug, $subjectId, $sortOrder);
        $model->reorderSortOrders($subjectId);

        header("Location: /admin/chapters?success=created");
        exit;
    }

    public function delete($id)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $chapterModel = new Chapter();
        $chapter      = $chapterModel->getById($id);

        if (!$chapter) {
            header("Location: /admin/chapters?error=not_found");
            exit;
        }

        if ($chapterModel->hasLessons($id)) {
            header("Location: /admin/chapters?error=has_lessons");
            exit;
        }

        $chapterModel->decreaseSortOrders(
            $chapter['subjectId'],
            $chapter['sortOrder']
        );

        $chapterModel->delete($id);
        $chapterModel->reorderSortOrders($chapter['subjectId']);

        header("Location: /admin/chapters?success=deleted");
        exit;
    }
}