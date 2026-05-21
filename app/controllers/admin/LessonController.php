<?php

require_once ROOT . '/app/models/Lesson.php';
require_once ROOT . '/app/models/Grade.php';
require_once ROOT . '/app/models/Subject.php';
require_once ROOT . '/app/models/Chapter.php';

class LessonController extends Controller
{
    public function index()
    {
        $lessonModel = new Lesson();

        $total      = $lessonModel->countAll();
        $perTab     = 5;
        $tabCount   = max(1, (int) ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));
        $offset     = ($currentTab - 1) * $perTab;

        $lessons = $lessonModel->getPaginate($perTab, $offset);

        $this->viewAdmin("lessons/index", [
            "title"      => "Quản lý Bài học",
            "lessons"    => $lessons,
            "tabCount"   => $tabCount,
            "currentTab" => $currentTab,
        ]);
    }

    public function create()
    {
        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];
        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $gradeModel = new Grade();

        $this->viewAdmin('lessons/create', [
            'title'      => 'Thêm bài học',
            'grades'     => $gradeModel->getAllAsc(),
            'flashError' => $flashError,
            'flashOld'   => $flashOld,
        ]);
    }

    public function store()
    {
        $lessonModel = new Lesson();

        $name          = trim($_POST['lessonName'] ?? '');
        $subjectId     = (int)($_POST['subjectId'] ?? 0);
        $chapterId     = (int)($_POST['chapterId'] ?? 0);
        $gradeId       = (int)($_POST['gradeId'] ?? 0);
        $positionValue = $_POST['positionValue'] ?? 'last';
        $content       = $_POST['content'] ?? '';
        $isActive      = isset($_POST['isActive']) ? 1 : 0;
        $slugInput     = trim($_POST['slug'] ?? '');

        $nameError = $lessonModel->validateLessonName($name);

        $slug = $slugInput
            ? $lessonModel->toSlug($slugInput)
            : $lessonModel->toSlug($name);

        // =========================
        // SORT ORDER
        // =========================
        $sortOrder = 1;

        if ($positionValue === 'last') {
            $sortOrder = $lessonModel->getMaxSortOrder($chapterId) + 1;
        }
        elseif ($positionValue === 'first') {
            $sortOrder = 1;
            $lessonModel->increaseSortOrders($chapterId, 1);
        }
        elseif (str_starts_with($positionValue, 'after-')) {
            $afterLessonId = (int)str_replace('after-', '', $positionValue);
            $afterSort     = $lessonModel->getSortOrderById($afterLessonId, $chapterId);
            $sortOrder     = $afterSort + 1;
            $lessonModel->increaseSortOrders($chapterId, $sortOrder);
        }

        $old = [
            'lessonName'    => $name,
            'subjectId'     => $subjectId,
            'chapterId'     => $chapterId,
            'gradeId'       => $gradeId,
            'positionValue' => $positionValue,
            'slug'          => $slugInput,
            'content'       => $content,
            'isActive'      => $isActive,
        ];

        $_SESSION['flash_old'] = $old;

        // EMPTY
        if (empty($name) || empty($subjectId) || empty($chapterId)) {
            $_SESSION['flash_error'] = 'empty_fields';
            header("Location: /admin/lessons/create");
            exit;
        }

        // NAME ERROR
        if ($nameError) {
            $_SESSION['flash_error'] = $nameError;
            header("Location: /admin/lessons/create");
            exit;
        }

        // CONTENT
        if (empty(trim(strip_tags($content)))) {
            $_SESSION['flash_error'] = 'empty_content';
            header("Location: /admin/lessons/create");
            exit;
        }

        // SLUG
        if ($lessonModel->slugExists($slug, $chapterId)) {
            $_SESSION['flash_error'] = 'slug_exists';
            header("Location: /admin/lessons/create");
            exit;
        }

        // NAME
        if ($lessonModel->nameExists($name, $chapterId)) {
            $_SESSION['flash_error'] = 'name_exists';
            header("Location: /admin/lessons/create");
            exit;
        }


        $lessonModel->create([
            'subjectId'  => $subjectId,
            'chapterId'  => $chapterId,
            'lessonName' => $name,
            'slug'       => $slug,
            'content'    => $content,
            'isActive'   => $isActive,
            'sortOrder'  => $sortOrder,
            'createdBy'  => $_SESSION['user']['userId'] ?? null,
        ]);

        $lessonModel->reorderSortOrders($chapterId);

        unset($_SESSION['flash_old']);
        header("Location: /admin/lessons?success=created");
        exit;
    }

    public function edit($id)
    {
        $lessonModel  = new Lesson();
        $gradeModel   = new Grade();
        $subjectModel = new Subject();
        $chapterModel = new Chapter();

        $lesson = $lessonModel->getById($id);

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];
        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $gradeId   = !empty($flashOld) ? ($flashOld['gradeId']   ?? $lesson['gradeId'])   : $lesson['gradeId'];
        $subjectId = !empty($flashOld) ? ($flashOld['subjectId'] ?? $lesson['subjectId']) : $lesson['subjectId'];

        $this->viewAdmin('lessons/edit', [
            'title'      => 'Cập nhật bài học',
            'lesson'     => $lesson,
            'grades'     => $gradeModel->getAll(),
            'subjects'   => $subjectModel->getByGrade($gradeId),
            'chapters'   => $chapterModel->getBySubject($subjectId),
            'flashError' => $flashError,
            'flashOld'   => $flashOld,
        ]);
    }

    public function update()
    {
        $lessonModel = new Lesson();

        $id            = (int)$_POST['id'];
        $name          = trim($_POST['lessonName'] ?? '');
        $subjectId     = (int)($_POST['subjectId'] ?? 0);
        $chapterId     = (int)($_POST['chapterId'] ?? 0);
        $gradeId       = (int)($_POST['gradeId'] ?? 0);
        $positionValue = $_POST['positionValue'] ?? 'last';
        $content       = $_POST['content'] ?? '';
        $isActive      = isset($_POST['isActive']) ? 1 : 0;
        $slugInput     = trim($_POST['slug'] ?? '');

        $nameError = $lessonModel->validateLessonName($name);

        $slug = $slugInput
            ? $lessonModel->toSlug($slugInput)
            : $lessonModel->toSlug($name);

        $old = [
            'lessonName'    => $name,
            'subjectId'     => $subjectId,
            'chapterId'     => $chapterId,
            'gradeId'       => $gradeId,
            'positionValue' => $positionValue,
            'slug'          => $slugInput,
            'content'       => $content,
            'isActive'      => $isActive,
        ];

        $_SESSION['flash_old'] = $old;

        // EMPTY
        if (empty($name) || empty($subjectId) || empty($chapterId)) {
            $_SESSION['flash_error'] = 'empty_fields';
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // CONTENT
        if (empty(trim(strip_tags($content)))) {
            $_SESSION['flash_error'] = 'empty_content';
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // NAME ERROR
        if ($nameError) {
            $_SESSION['flash_error'] = $nameError;
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // SLUG
        if ($lessonModel->slugExists($slug, $chapterId, $id)) {
            $_SESSION['flash_error'] = 'slug_exists';
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // NAME
        if ($lessonModel->nameExists($name, $chapterId, $id)) {
            $_SESSION['flash_error'] = 'name_exists';
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // =========================
        // SORT ORDER
        // =========================
        $oldLesson    = $lessonModel->getById($id);
        $oldChapterId = (int)$oldLesson['chapterId'];
        $oldSortOrder = (int)$oldLesson['sortOrder'];

        if ($oldChapterId !== $chapterId) {

            // Đổi chapter → xóa bài khỏi chapter cũ, đưa xuống cuối chapter mới
            $lessonModel->decreaseSortOrders($oldChapterId, $oldSortOrder);
            $lessonModel->reorderSortOrders($oldChapterId);
            $sortOrder = $lessonModel->getMaxSortOrder($chapterId) + 1;

        } else {

            // Cùng chapter — tạm thời đặt sortOrder = 0 để tránh conflict
            $lessonModel->updateSortOrderOnly($id, 0);

            // Decrease các bài phía sau vị trí cũ
            $lessonModel->decreaseSortOrders($chapterId, $oldSortOrder);

            // Tính sortOrder mới theo positionValue
            if ($positionValue === 'last') {
                $sortOrder = $lessonModel->getMaxSortOrder($chapterId) + 1;

            } elseif ($positionValue === 'first') {
                $lessonModel->increaseSortOrders($chapterId, 1);
                $sortOrder = 1;

            } elseif (str_starts_with($positionValue, 'after-')) {
                $afterLessonId = (int)str_replace('after-', '', $positionValue);
                $afterSort     = $lessonModel->getSortOrderById($afterLessonId, $chapterId);
                $sortOrder     = $afterSort + 1;
                $lessonModel->increaseSortOrders($chapterId, $sortOrder);

            } else {
                $sortOrder = $lessonModel->getMaxSortOrder($chapterId) + 1;
            }
        }

        $lessonModel->updateLesson($id, [
            'subjectId'  => $subjectId,
            'chapterId'  => $chapterId,
            'lessonName' => $name,
            'slug'       => $slug,
            'content'    => $content,
            'isActive'   => $isActive,
            'sortOrder'  => $sortOrder,
            'createdBy'  => $_SESSION['user']['userId'] ?? null,
        ]);

        $lessonModel->reorderSortOrders($chapterId);

        unset($_SESSION['flash_old']);
        header("Location: /admin/lessons?success=updated");
        exit;
    }


    public function delete($id)
    {
        $lessonModel = new Lesson();
        $lesson = $lessonModel->getById($id);

        if ($lesson) {

            if ($lessonModel->hasExams($id)) {
                header("Location: /admin/lessons?error=has_exams");
                exit;
            }

            $chapterId = $lesson['chapterId'];
            $lessonModel->delete($id);
            $lessonModel->reorderSortOrders($chapterId);
        }

        header("Location: /admin/lessons?success=deleted");
        exit;
    }

    
    public function getChaptersBySubject()
    {
        $subjectId    = $_GET['subject_id'];
        $chapterModel = new Chapter();

        header('Content-Type: application/json');
        echo json_encode($chapterModel->getBySubject($subjectId));
    }

    public function getByChapterAjax()
    {
        $chapterId   = $_GET['chapter_id'] ?? 0;
        $lessonModel = new Lesson();
        $data        = $lessonModel->getByChapter($chapterId);
        header('Content-Type: application/json');
        echo json_encode($data);
    }


    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $lessonModel = new Lesson();

        // Nếu rỗng -> trả full danh sách
        if ($keyword === '') {

            $lessons = $lessonModel->getPaginate(999, 0);
        } else {
            $lessons = $lessonModel->searchLessons($keyword);
        }

        // Không có dữ liệu
        if (empty($lessons)) {
            echo '
            <tr>
                <td colspan="7" class="text-center">
                    Không có bài học nào
                </td>
            </tr>
            ';

            return;
        }

        // Render rows
        foreach ($lessons as $l) {
            ?>

            <tr>
                <td><?= $l['lessonId'] ?></td>
                <td><?= htmlspecialchars($l['gradeName']) ?></td>
                <td><?= htmlspecialchars($l['subjectName']) ?></td>
                <td><?= htmlspecialchars($l['chapterName']) ?></td>
                <td><?= htmlspecialchars($l['lessonName']) ?></td>
                <td><?= htmlspecialchars($l['slug']) ?></td>
                <td>
                    <div class="admin-actions">
                        <a href="/admin/lessons/edit/<?= $l['lessonId'] ?>"
                        class="action-btn btn-edit">
                            ✏
                        </a>

                        <button class="action-btn btn-delete"
                                onclick="openDeleteLesson(<?= $l['lessonId'] ?>)">
                            🗑
                        </button>
                    </div>
                </td>
            </tr>

            <?php
        }
    }

}