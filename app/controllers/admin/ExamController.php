<?php

require_once ROOT . "/app/core/Controller.php";

require_once ROOT . "/app/models/Exam.php";
require_once ROOT . "/app/models/Grade.php";
require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Chapter.php";
require_once ROOT . "/app/models/Lesson.php";
require_once ROOT . "/app/models/Question.php";
require_once ROOT . "/app/models/ExamQuestion.php";
require_once ROOT . "/app/models/Result.php";

class ExamController extends Controller
{

    // =====================================================
    // INDEX
    // =====================================================
    public function index()
    {
        $examModel = new Exam();

        $examModel->deleteExpiredTemporaryExams();

        $keyword = trim($_GET['keyword'] ?? '');

        // =====================================
        // SEARCH
        // =====================================

        if ($keyword !== '') {

            $exams = $examModel->searchAdminExams($keyword);

            $tabCount   = 1;
            $currentTab = 1;

        } else {

            // =====================================
            // PAGINATION
            // =====================================

            $limit = 25;

            $currentTab = max(
                1,
                (int)($_GET['tab'] ?? 1)
            );

            $offset = ($currentTab - 1) * $limit;

            $totalExams = $examModel->countAllExams();

            $tabCount = (int)ceil(
                $totalExams / $limit
            );

            $exams = $examModel->getAdminExamsPaginated(
                $limit,
                $offset
            );
        }

        $this->viewAdmin(
            'exams/index',
            compact(
                'exams',
                'tabCount',
                'currentTab'
            )
        );
    }
    // =====================================================
    // CREATE
    // =====================================================
    public function create()
    {
        $gradeModel = new Grade();
        $grades     = $gradeModel->all();

        // Lấy flash error + old rồi xóa session
        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];
        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $this->viewAdmin('exams/create', compact('grades', 'flashError', 'flashOld'));
    }

    // =====================================================
    // STORE
    // =====================================================
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/exams');
            exit;
        }

        $examModel         = new Exam();
        $examQuestionModel = new ExamQuestion();

        $title          = trim($_POST['title'] ?? '');
        $slug           = trim($_POST['slug'] ?? '');
        $gradeId        = (int)($_POST['gradeId'] ?? 0);
        $subjectId      = (int)($_POST['subjectId'] ?? 0);
        $chapterId      = !empty($_POST['chapterId']) ? (int)$_POST['chapterId'] : null;
        $lessonId       = !empty($_POST['lessonId'])  ? (int)$_POST['lessonId']  : null;
        $examType       = $_POST['examType']       ?? 'lesson';
        $generationType = $_POST['generationType'] ?? 'manual';
        $duration       = (int)($_POST['duration']       ?? 0);
        $totalQuestions = (int)($_POST['totalQuestions'] ?? 0);
        $questionOrder  = $_POST['questionOrder'] ?? 'manual';
        $isActive       = isset($_POST['isActive']) ? 1 : 0;
        $questionIds    = $_POST['questionIds']   ?? [];
        $positionValue  = $_POST['positionValue'] ?? 'last';
        $afterExamId    = null;

        if (str_starts_with($positionValue, 'after-')) {
            $afterExamId = (int) str_replace('after-', '', $positionValue);
        }

        // ==========================
        // SORT ORDER
        // ==========================
        $sortOrder = 1;

        if ($examType === 'lesson') {

            if ($positionValue === 'last') {
                $sortOrder = $examModel->getMaxSortOrderByLesson($lessonId) + 1;

            } elseif ($positionValue === 'first') {
                $sortOrder = 1;
                $examModel->increaseSortOrders($lessonId, 1);

            } elseif ($afterExamId) {
                $afterSort = $examModel->getSortOrder($afterExamId, $lessonId);
                $sortOrder = $afterSort + 1;
                $examModel->increaseSortOrders($lessonId, $sortOrder);
            }
        }

        // Lưu lại old để restore khi lỗi
        $_SESSION['flash_old'] = $_POST;

        // ==========================
        // VALIDATE CƠ BẢN
        // ==========================
        if (empty($title) || empty($slug) || !$gradeId || !$subjectId || !$duration) {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
            header('Location: /admin/exams/create');
            exit;
        }

        // ==========================
        // VALIDATE TRÙNG TÊN
        // ==========================
        if ($examModel->titleExistsInScope($title, $examType, $lessonId, $subjectId)) {

            $_SESSION['flash_error'] = $examType === 'lesson'
                ? 'Tên đề thi đã tồn tại trong chương học này!'
                : 'Tên đề thi đã tồn tại trong môn học này!';

            header('Location: /admin/exams/create');
            exit;
        }

        if ($totalQuestions <= 0) {
            $_SESSION['flash_error'] = 'Tổng số câu phải lớn hơn 0';
            header('Location: /admin/exams/create');
            exit;
        }

        if ($totalQuestions > 200) {
            $_SESSION['flash_error'] = 'Không được vượt quá 200 câu hỏi';
            header('Location: /admin/exams/create');
            exit;
        }

        // ==========================
        // AUTO: tự pick câu hỏi
        // ==========================
        if ($generationType === 'auto') {

            $questionModel = new Question();

            $pool = ($examType === 'thpt')
                ? $questionModel->getThptQuestions($gradeId, $subjectId)
                : $questionModel->getByLesson($lessonId);

            if (count($pool) < $totalQuestions) {
                $_SESSION['flash_error'] =
                    "Ngân hàng câu hỏi chỉ có "
                    . count($pool)
                    . "/{$totalQuestions} câu.";
                header('Location: /admin/exams/create');
                exit;
            }

            $groups = ['knowledge' => [], 'comprehension' => [], 'application' => []];

            foreach ($pool as $q) {
                $lvl = strtolower($q['level'] ?? '');
                if (str_contains($lvl, 'comprehension')) {
                    $groups['comprehension'][] = $q;
                } elseif (str_contains($lvl, 'application')) {
                    $groups['application'][] = $q;
                } else {
                    $groups['knowledge'][] = $q;
                }
            }

            shuffle($groups['knowledge']);
            shuffle($groups['comprehension']);
            shuffle($groups['application']);

            $kPercent = (int)($_POST['knowledgePercent']     ?? 50);
            $cPercent = (int)($_POST['comprehensionPercent'] ?? 30);

            $nK = (int)round($totalQuestions * $kPercent / 100);
            $nC = (int)round($totalQuestions * $cPercent / 100);
            $nA = $totalQuestions - $nK - $nC;

            $errors = [];

            if (count($groups['knowledge']) < $nK) {
                $errors[] = "Ngân hàng câu hỏi mức Nhận biết chỉ có " . count($groups['knowledge']) . "/{$nK} câu.";
            }
            if (count($groups['comprehension']) < $nC) {
                $errors[] = "Ngân hàng câu hỏi mức Thông hiểu chỉ có " . count($groups['comprehension']) . "/{$nC} câu.";
            }
            if (count($groups['application']) < $nA) {
                $errors[] = "Ngân hàng câu hỏi mức Vận dụng chỉ có " . count($groups['application']) . "/{$nA} câu.";
            }

            if (!empty($errors)) {
                $_SESSION['flash_error'] = implode('<br>', $errors);
                header('Location: /admin/exams/create');
                exit;
            }

            $picked = array_merge(
                array_slice($groups['knowledge'],     0, $nK),
                array_slice($groups['comprehension'], 0, $nC),
                array_slice($groups['application'],   0, $nA)
            );

            if ($questionOrder === 'random') shuffle($picked);

            $questionIds = array_column($picked, 'questionId');
        }

        // ==========================
        // CREATE EXAM
        // ==========================
        $examId = $examModel->createExam([
            'gradeId'        => $gradeId,
            'subjectId'      => $subjectId,
            'chapterId'      => $chapterId,
            'lessonId'       => $lessonId,
            'sortOrder'      => $sortOrder,
            'title'          => $title,
            'slug'           => $slug,
            'examType'       => $examType,
            'generationType' => $generationType,
            'totalQuestions' => $totalQuestions,
            'duration'       => $duration,
            'questionOrder'  => $questionOrder,
            'isActive'       => $isActive,
            'createdBy'      => $_SESSION['user_id'] ?? null,  // ← FIX: dùng user_id
        ]);

        // ==========================
        // RANDOM đề thủ công
        // ==========================
        if ($generationType === 'manual' && $questionOrder === 'random') {
            $questionIds = array_values($questionIds);
            shuffle($questionIds);
        }

        // ==========================
        // SAVE QUESTIONS
        // ==========================
        foreach ($questionIds as $index => $questionId) {
            $examQuestionModel->create([
                'examId'        => $examId,
                'questionId'    => $questionId,
                'questionOrder' => $index + 1,
            ]);
        }

        unset($_SESSION['flash_old']);

        $_SESSION['success'] = 'Thêm đề thi thành công!';
        header('Location: /admin/exams');
        exit;
    }




    // =========================================================
    // AJAX SUBJECTS (theo grade)
    // =========================================================
    public function ajaxSubjects()
    {
        $gradeId = (int)($_GET['grade_id'] ?? 0);
    
        $subjectModel = new Subject();
    
        $subjects = $subjectModel->getByGrade($gradeId);
    
        header('Content-Type: application/json');
        echo json_encode($subjects);
    }
    
    
    // =========================================================
    // AJAX CHAPTERS (theo subject)
    // =========================================================
    public function ajaxChapters()
    {
        $subjectId = (int)($_GET['subject_id'] ?? 0);
    
        $chapterModel = new Chapter();
    
        $chapters = $chapterModel->getBySubject($subjectId);
    
        header('Content-Type: application/json');
        echo json_encode($chapters);
    }
    
    
    // =========================================================
    // AJAX LESSONS (theo chapter)
    // =========================================================
    public function ajaxLessons()
    {
        $chapterId = (int)($_GET['chapter_id'] ?? 0);
    
        $lessonModel = new Lesson();
    
        $lessons = $lessonModel->getByChapter($chapterId);
    
        header('Content-Type: application/json');
        echo json_encode($lessons);
    }


    // =====================================================
    // AJAX EXAMS BY LESSON
    // =====================================================
    public function ajaxExamsByLesson()
    {
        $lessonId = (int)($_GET['lesson_id'] ?? 0);

        $examModel = new Exam();

        $exams = $examModel->getByLesson($lessonId);

        header('Content-Type: application/json');

        echo json_encode($exams);
    }


    // =========================================================
    // AJAX QUESTIONS
    // =========================================================
    public function ajaxQuestions()
    {
        $lessonId = (int)($_GET['lesson_id'] ?? 0);

        $questionModel = new Question();

        $questions =
            $questionModel->getByLesson($lessonId);

        header('Content-Type: application/json');

        echo json_encode($questions);
    }


    // =========================================================
    // AJAX THPT QUESTIONS
    // =========================================================
    public function ajaxThptQuestions()
    {
        $gradeId   = (int)($_GET['grade_id'] ?? 0);
        $subjectId = (int)($_GET['subject_id'] ?? 0);

        $questionModel = new Question();

        $questions =
            $questionModel->getThptQuestions(
                $gradeId,
                $subjectId
            );

        header('Content-Type: application/json');

        echo json_encode($questions);
    }



    // =====================================================
    // EDIT
    // =====================================================
    public function edit($id)
    {
        $examModel         = new Exam();
        $examQuestionModel = new ExamQuestion();
        $gradeModel        = new Grade();

        $exam = $examModel->getById($id);

        if (!$exam) {
            header('Location: /admin/exams');
            exit;
        }

        // Lấy question ids
        $questionIds = $examQuestionModel->getQuestionIdsByExam($id);
        $exam['questionIds'] = $questionIds;

        // Tính positionValue thực tế để restore dropdown đúng
        if ($exam['examType'] === 'lesson' && !empty($exam['lessonId'])) {

            if ((int)$exam['sortOrder'] === 1) {
                $exam['positionValue'] = 'first';
            } else {
                $prevExam = $examModel->getPrevExam(
                    $exam['lessonId'],
                    $exam['sortOrder']
                );
                $exam['positionValue'] = $prevExam
                    ? 'after-' . $prevExam['examId']
                    : 'last';
            }

        } else {
            // THPT hoặc không có lesson thì không dùng position
            $exam['positionValue'] = 'last';
        }

        $grades = $gradeModel->all();

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $this->viewAdmin(
            'exams/edit',
            compact('exam', 'grades', 'flashError', 'flashOld')
        );
    }

    // =====================================================
    // UPDATE
    // =====================================================
   
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/exams');
            exit;
        }

        $id = (int)($_POST['examId'] ?? 0);

        $examModel         = new Exam();
        $examQuestionModel = new ExamQuestion();

        $oldExam = $examModel->getById($id);

        if (!$oldExam) {
            header('Location: /admin/exams');
            exit;
        }

        $title          = trim($_POST['title'] ?? '');
        $slug           = trim($_POST['slug'] ?? '');
        $gradeId        = (int)($_POST['gradeId'] ?? 0);
        $subjectId      = (int)($_POST['subjectId'] ?? 0);
        $chapterId      = !empty($_POST['chapterId']) ? (int)$_POST['chapterId'] : null;
        $lessonId       = !empty($_POST['lessonId'])  ? (int)$_POST['lessonId']  : null;
        $examType       = $_POST['examType']       ?? 'lesson';
        $generationType = $_POST['generationType'] ?? 'manual';
        $duration       = (int)($_POST['duration']       ?? 0);
        $totalQuestions = (int)($_POST['totalQuestions'] ?? 0);
        $questionOrder  = $_POST['questionOrder'] ?? 'manual';
        $isActive       = isset($_POST['isActive']) ? 1 : 0;
        $questionIds    = $_POST['questionIds'] ?? [];
        $positionValue  = $_POST['positionValue'] ?? 'last';

        $_SESSION['flash_old'] = $_POST;

        // ==========================
        // VALIDATE CƠ BẢN
        // ==========================
        if (empty($title) || empty($slug) || !$gradeId || !$subjectId || !$duration) {
            $_SESSION['flash_error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
            header('Location: /admin/exams/edit/' . $id);
            exit;
        }

        if ($examType === 'lesson' && !$lessonId) {
            $_SESSION['flash_error'] = 'Vui lòng chọn bài học';
            header('Location: /admin/exams/edit/' . $id);
            exit;
        }

        // ==========================
        // VALIDATE TRÙNG TÊN
        // ==========================
        if ($examModel->titleExistsInScope($title, $examType, $lessonId, $subjectId, $id)) {

            $_SESSION['flash_error'] = $examType === 'lesson'
                ? 'Tên đề thi đã tồn tại trong chương học này!'
                : 'Tên đề thi đã tồn tại trong môn học này!';

            header('Location: /admin/exams/edit/' . $id);
            exit;
        }

        if ($totalQuestions <= 0) {
            $_SESSION['flash_error'] = 'Tổng số câu phải lớn hơn 0';
            header('Location: /admin/exams/edit/' . $id);
            exit;
        }

        if ($totalQuestions > 200) {
            $_SESSION['flash_error'] = 'Không được vượt quá 200 câu hỏi';
            header('Location: /admin/exams/edit/' . $id);
            exit;
        }

        // ==========================
        // AUTO GENERATE
        // ==========================
        if ($generationType === 'auto' && empty($questionIds)) {

            $questionModel = new Question();

            $pool = ($examType === 'thpt')
                ? $questionModel->getThptQuestions($gradeId, $subjectId)
                : $questionModel->getByLesson($lessonId);

            if (count($pool) < $totalQuestions) {
                $_SESSION['flash_error'] = 'Ngân hàng câu hỏi không đủ số lượng';
                header('Location: /admin/exams/edit/' . $id);
                exit;
            }

            $groups = [
                'knowledge'     => [],
                'comprehension' => [],
                'application'   => [],
            ];

            foreach ($pool as $q) {
                $lvl = strtolower($q['level'] ?? '');
                if (str_contains($lvl, 'comprehension')) {
                    $groups['comprehension'][] = $q;
                } elseif (str_contains($lvl, 'application')) {
                    $groups['application'][] = $q;
                } else {
                    $groups['knowledge'][] = $q;
                }
            }

            shuffle($groups['knowledge']);
            shuffle($groups['comprehension']);
            shuffle($groups['application']);

            $kPercent = (int)($_POST['knowledgePercent'] ?? 50);
            $cPercent = (int)($_POST['comprehensionPercent'] ?? 30);
            $nK = (int)round($totalQuestions * $kPercent / 100);
            $nC = (int)round($totalQuestions * $cPercent / 100);
            $nA = $totalQuestions - $nK - $nC;

            if (
                count($groups['knowledge'])     < $nK
                || count($groups['comprehension']) < $nC
                || count($groups['application'])   < $nA
            ) {
                $_SESSION['flash_error'] = 'Không đủ câu hỏi theo mức độ yêu cầu';
                header('Location: /admin/exams/edit/' . $id);
                exit;
            }

            $picked = array_merge(
                array_slice($groups['knowledge'],     0, $nK),
                array_slice($groups['comprehension'], 0, $nC),
                array_slice($groups['application'],   0, $nA)
            );

            if ($questionOrder === 'random') shuffle($picked);

            $questionIds = array_column($picked, 'questionId');
        }

        // ==========================
        // MANUAL VALIDATE
        // ==========================
        if ($generationType === 'manual' && count($questionIds) != $totalQuestions) {
            $_SESSION['flash_error'] = 'Bạn phải chọn đúng số lượng câu hỏi';
            header('Location: /admin/exams/edit/' . $id);
            exit;
        }

        // ==========================
        // RANDOM MANUAL
        // ==========================
        if ($generationType === 'manual' && $questionOrder === 'random') {
            $questionIds = array_values($questionIds);
            shuffle($questionIds);
        }

        // ==========================
        // SORT ORDER
        // ==========================
        $sortOrder = $oldExam['sortOrder'];

        if ($examType === 'lesson') {

            $lessonChanged = ($lessonId != $oldExam['lessonId']);

            if ($positionValue === 'last') {
                $sortOrder = $lessonChanged
                    ? $examModel->getMaxSortOrderByLesson($lessonId) + 1
                    : $oldExam['sortOrder'];

            } elseif ($positionValue === 'first') {
                $sortOrder = 1;
                $examModel->increaseSortOrders($lessonId, 1);

            } elseif (str_starts_with($positionValue, 'after-')) {
                $afterExamId = (int)str_replace('after-', '', $positionValue);
                $sortOrder   = $examModel->getSortOrder($afterExamId, $lessonId) + 1;
                $examModel->increaseSortOrders($lessonId, $sortOrder);
            }
        }

        // ==========================
        // UPDATE EXAM
        // ==========================
        $examModel->updateExam($id, [
            'gradeId'        => $gradeId,
            'subjectId'      => $subjectId,
            'chapterId'      => $chapterId,
            'lessonId'       => $lessonId,
            'sortOrder'      => $sortOrder,
            'title'          => $title,
            'slug'           => $slug,
            'examType'       => $examType,
            'generationType' => $generationType,
            'totalQuestions' => $totalQuestions,
            'duration'       => $duration,
            'questionOrder'  => $questionOrder,
            'isActive'       => $isActive,
        ]);

        // ==========================
        // RESET QUESTIONS
        // ==========================
        $examQuestionModel->deleteByExam($id);

        foreach ($questionIds as $index => $questionId) {
            $examQuestionModel->create([
                'examId'        => $id,
                'questionId'    => $questionId,
                'questionOrder' => $index + 1,
            ]);
        }

        unset($_SESSION['flash_old']);

        $_SESSION['success'] = 'Cập nhật đề ôn luyện thành công!';
        header('Location: /admin/exams');
        exit;
    }



    // =====================================================
    // DELETE
    // =====================================================
    public function delete($id)
    {
        $examModel         = new Exam();
        $examQuestionModel = new ExamQuestion();
        $resultModel       = new Result();

        // Kiểm tra có kết quả liên quan không
        $hasResults = $resultModel->countByExam($id);

        if ($hasResults > 0) {
            $_SESSION['error'] = "Không thể xóa đề thi này vì đã có {$hasResults} kết quả làm bài. Hãy ẩn đề (tắt trạng thái) thay vì xóa.";
            header('Location: /admin/exams');
            exit;
        }

        $examQuestionModel->deleteByExam($id);
        $deleted = $examModel->deleteExam($id);

        if ($deleted) {
            $_SESSION['success'] = 'Xóa đề ôn luyện thành công!';
        } else {
            $_SESSION['error'] = 'Xóa đề ôn luyện thất bại';
        }

        header('Location: /admin/exams');
        exit;
    }

}