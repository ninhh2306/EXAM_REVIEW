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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $model = new Chapter();
        $id        = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        $name      = trim($_POST['name'] ?? '');
        $subjectId = (int)($_POST['subjectId'] ?? 0);
        $gradeId   = (int)($_POST['gradeId'] ?? 0);
        $positionValue = $_POST['positionValue'] ?? 'last';

        // =========================
        // SORT ORDER
        // =========================

        $sortOrder = 1;

        if ($positionValue === 'last') {

            $sortOrder =
                $model->getMaxSortOrder($subjectId) + 1;
        }

        elseif ($positionValue === 'first') {
            $sortOrder = 1;
            $model->increaseSortOrders($subjectId, 1, $id);
        }

        elseif (str_starts_with($positionValue, 'after-')) {

            $afterChapterId = (int)str_replace('after-', '', $positionValue);

            $afterSort = $model->getSortOrder($afterChapterId, $subjectId);

            $sortOrder = $afterSort + 1;

            // Truyền $id để loại trừ chính nó
            $model->increaseSortOrders($subjectId, $sortOrder, $id);
        }


        // ====== SLUG ========
        $slug = $model->toSlug($name);


        // =========================
        // FLASH OLD
        // =========================

        $oldData = [
            'id'        => $id,
            'name'      => $name,
            'subjectId' => $subjectId,
            'gradeId'   => $gradeId,
            'sortOrder' => $sortOrder,
            'slug'      => $slug,
        ];

        $_SESSION['flash_old'] = $oldData;

        // =========================
        // VALIDATE
        // =========================

        if (
            empty($name)
            || !$subjectId
            || !$gradeId
        ) {

            $_SESSION['flash_error'] = 'empty';

            header("Location: /admin/chapters");
            exit;
        }

        // CHECK NAME

        if ($model->existsName($name, $subjectId, $id)) {

            $_SESSION['flash_error'] = 'name_exists';

            header("Location: /admin/chapters");
            exit;
        }


        // =========================
        // UPDATE
        // =========================

        if ($id) {

            $oldChapter = $model->getById($id);

            $oldSubjectId = (int)$oldChapter['subjectId'];
            $oldSortOrder = (int)$oldChapter['sortOrder'];

            // =====================================
            // CÙNG SUBJECT
            // =====================================

            if ($oldSubjectId == $subjectId) {

                // Tạm thời đưa chapter ra khỏi danh sách
                $model->update($id, $name, $slug, $subjectId, 0);

                // Dồn các chapter phía sau lên
                $model->decreaseSortOrders(
                    $subjectId,
                    $oldSortOrder
                );

                // Chèn vào vị trí mới
                if ($positionValue === 'first') {

                    $sortOrder = 1;

                    $model->increaseSortOrders(
                        $subjectId,
                        1
                    );
                }

                elseif (str_starts_with($positionValue, 'after-')) {

                    $afterChapterId = (int)str_replace(
                        'after-',
                        '',
                        $positionValue
                    );

                    $afterSort = $model->getSortOrder(
                        $afterChapterId,
                        $subjectId
                    );

                    $sortOrder = $afterSort + 1;

                    $model->increaseSortOrders(
                        $subjectId,
                        $sortOrder
                    );
                }

                else {

                    $sortOrder =
                        $model->getMaxSortOrder($subjectId) + 1;
                }
            }

            // =====================================
            // ĐỔI SUBJECT
            // =====================================

            else {

                // Xóa vị trí cũ
                $model->decreaseSortOrders(
                    $oldSubjectId,
                    $oldSortOrder
                );

                $model->reorderSortOrders($oldSubjectId);

                // Thêm cuối subject mới
                $sortOrder =
                    $model->getMaxSortOrder($subjectId) + 1;
            }

            $model->update(
                $id,
                $name,
                $slug,
                $subjectId,
                $sortOrder
            );

            $model->reorderSortOrders($subjectId);

            header("Location: /admin/chapters?success=updated");
        }



        // =========================
        // CREATE
        // =========================

        else {
            $model->create($name, $slug, $subjectId, $sortOrder);
            $model->reorderSortOrders($subjectId);

            header("Location: /admin/chapters?success=created");
        }

        exit;
    }

    public function delete($id)
    {
        $chapterModel = new Chapter();
        $chapter = $chapterModel->getById($id);

        if ($chapter) {

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
        }

        header("Location: /admin/chapters?success=deleted");
        exit;
    }

    
}