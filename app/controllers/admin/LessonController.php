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
            'grades'     => $gradeModel->getAll(),
            'flashError' => $flashError,
            'flashOld'   => $flashOld,
        ]);
    }

    public function store()
    {
        $lessonModel = new Lesson();

        $name      = trim($_POST['lessonName'] ?? '');
        $subjectId = (int)($_POST['subjectId'] ?? 0);
        $chapterId = (int)($_POST['chapterId'] ?? 0);
        $gradeId   = (int)($_POST['gradeId']   ?? 0);
        $sortOrder = (int)($_POST['sortOrder']  ?? 0);
        $content   = $_POST['content'] ?? '';

        $slugInput = trim($_POST['slug'] ?? '');
        $slug      = $slugInput ?: $lessonModel->toSlug($name);

        // key đồng nhất với view
        $old = [
            'lessonName' => $name,
            'subjectId'  => $subjectId,
            'chapterId'  => $chapterId,
            'gradeId'    => $gradeId,
            'sortOrder'  => $sortOrder,
            'slug'       => $slug,
            'content'    => $content,
        ];

        // CHECK TRÙNG SORT ORDER trong cùng chapter
        if ($lessonModel->existsSortOrder($chapterId, $sortOrder)) {
            $_SESSION['flash_error'] = 'sort_exists';
            $_SESSION['flash_old']   = $old;
            header("Location: /admin/lessons/create");
            exit;
        }

        // CHECK TRÙNG SLUG trong cùng chapter
        if ($lessonModel->slugExists($slug, $chapterId)) {
            $_SESSION['flash_error'] = 'slug_exists';
            $_SESSION['flash_old']   = $old;
            header("Location: /admin/lessons/create");
            exit;
        }

        // CHECK TRÙNG TÊN trong cùng chapter (không phải subject)
        if ($lessonModel->nameExists($name, $chapterId)) {
            $_SESSION['flash_error'] = 'name_exists';
            $_SESSION['flash_old']   = $old;
            header("Location: /admin/lessons/create");
            exit;
        }

        $lessonModel->create([
            'subjectId'  => $subjectId,
            'chapterId'  => $chapterId,
            'lessonName' => $name,
            'slug'       => $slug,
            'content'    => $content,
            'sortOrder'  => $sortOrder,
            'createdBy'  => $_SESSION['user_id'] ?? null,
        ]);

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

        $id        = $_POST['id'];
        $name      = trim($_POST['lessonName'] ?? '');
        $subjectId = (int)($_POST['subjectId'] ?? 0);
        $chapterId = (int)($_POST['chapterId'] ?? 0);
        $gradeId   = (int)($_POST['gradeId']   ?? 0);
        $sortOrder = (int)($_POST['sortOrder']  ?? 0);
        $content   = $_POST['content'] ?? '';

        $slugInput = trim($_POST['slug'] ?? '');
        $slug      = $slugInput ?: $lessonModel->toSlug($name);

        $old = [
            'id'         => $id,
            'lessonName' => $name,
            'subjectId'  => $subjectId,
            'chapterId'  => $chapterId,
            'gradeId'    => $gradeId,
            'sortOrder'  => $sortOrder,
            'slug'       => $slug,
            'content'    => $content,
        ];

        // CHECK TRÙNG SORT ORDER trong cùng chapter (bỏ qua chính nó)
        if ($lessonModel->existsSortOrder($chapterId, $sortOrder, $id)) {
            $_SESSION['flash_error'] = 'sort_exists';
            $_SESSION['flash_old']   = $old;
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // CHECK TRÙNG SLUG trong cùng chapter (bỏ qua chính nó)
        if ($lessonModel->slugExists($slug, $chapterId, $id)) {
            $_SESSION['flash_error'] = 'slug_exists';
            $_SESSION['flash_old']   = $old;
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        // CHECK TRÙNG TÊN trong cùng chapter (bỏ qua chính nó)
        if ($lessonModel->nameExists($name, $chapterId, $id)) {
            $_SESSION['flash_error'] = 'name_exists';
            $_SESSION['flash_old']   = $old;
            header("Location: /admin/lessons/edit/$id");
            exit;
        }

        $lessonModel->updateLesson($id, [
            'subjectId'  => $subjectId,
            'chapterId'  => $chapterId,
            'lessonName' => $name,
            'slug'       => $slug,
            'content'    => $content,
            'sortOrder'  => $sortOrder,
            'createdBy'  => $_SESSION['user_id'] ?? null,
        ]);

        header("Location: /admin/lessons?success=updated");
        exit;
    }

    public function delete($id)
    {
        $lessonModel = new Lesson();
        $lessonModel->delete($id);

        // Không cần reorderChapter nữa — sortOrder chỉ để sắp xếp
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
}