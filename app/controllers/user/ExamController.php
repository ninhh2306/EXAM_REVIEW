<?php

require_once ROOT . "/app/core/Controller.php";
require_once ROOT . "/app/models/Grade.php";
require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Exam.php";
require_once ROOT . "/app/models/Question.php";
require_once ROOT . "/app/models/Chapter.php";
require_once ROOT . "/app/models/Answer.php";
require_once ROOT . "/app/models/Result.php";
require_once ROOT . "/app/models/ResultDetail.php";

class ExamController extends Controller
{
    // Xem tất cả đề thi theo môn
    public function index($gradeSlug, $subjectSlug)
    {
        $gradeModel   = new Grade();
        $subjectModel = new Subject();
        $examModel    = new Exam();
        $chapterModel = new Chapter();

        $grade   = $gradeModel->getBySlug($gradeSlug);
        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']); // fix: dùng getBySlugAndGrade

        // fix: truyền thêm gradeId để lọc đúng môn + loại trừ thpt
        $exams    = $examModel->getBySubjectSlug($subjectSlug, $grade['gradeId']);
        $chapters = $chapterModel->getBySubject($subject['subjectId']);

        $this->view('exams/index', compact(
            'grade', 'subject', 'exams', 'chapters'
        ));
    }


    // Chi tiết 1 đề thi theo slug
    public function show($gradeSlug, $subjectSlug, $examSlug)
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $gradeModel    = new Grade();
        $subjectModel  = new Subject();
        $examModel     = new Exam();
        $questionModel = new Question();

        $grade = $gradeModel->getBySlug($gradeSlug);
        if (!$grade) { $this->view('errors/404'); return; }

        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']);
        if (!$subject) { $this->view('errors/404'); return; }

        $exam = $examModel->getBySlug($examSlug, $subject['subjectId']);

        if (!$exam) {$this->view('errors/404');
            return;
        }

        $_SESSION['exam_start_' . $exam['examId']] = date('Y-m-d H:i:s');

        $questions = $questionModel->getByExamWithAnswers($exam['examId']);
        $exam['realTotalQuestions'] = count($questions);

        $this->view('exams/detail', compact('grade', 'subject', 'exam', 'questions'));
    }


    // POST /submit-exam
    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/');
            exit;
        }

        $examId  = (int)($_POST['examId'] ?? 0);
        $answers = $_POST['answer'] ?? [];
        $userId  = (int)($_SESSION['user_id'] ?? 0);

        $examModel     = new Exam();
        $questionModel = new Question();  
        $answerModel   = new Answer();

        $exam = $examModel->getById($examId);

        if (!$exam) {
            $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
            if (str_contains($referer, 'error=')) {
                header('Location: ' . APP_URL . '/?error=exam_deleted');
            } else {
                $sep = str_contains($referer, '?') ? '&' : '?';
                header('Location: ' . $referer . $sep . 'error=exam_deleted');
            }
            exit;
        }

        // Kiểm tra câu hỏi còn không
        $allQuestions = $questionModel->getByExam($examId);
        if (empty($allQuestions)) {
            $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL . '/';
            $sep = str_contains($referer, '?') ? '&' : '?';
            header('Location: ' . $referer . $sep . 'error=exam_deleted');
            exit;
        }

        $subjectId      = $exam['subjectId'];
        $totalQuestions = count($allQuestions);

        // Chấm điểm
        $answerModel   = new Answer();
        $questionModel = new Question();

        $totalQuestions = count($allQuestions);

        $totalCorrect         = 0;
        $correctKnowledge     = 0;
        $correctComprehension = 0;
        $correctApplication   = 0;

        $totalKnowledge       = 0;
        $totalComprehension   = 0;
        $totalApplication     = 0;

        $details = [];

        foreach ($allQuestions as $q) {
            $questionId       = (int) $q['questionId'];
            $selectedAnswerId = isset($answers[$questionId]) ? (int)$answers[$questionId] : null;

            // Đếm tổng câu theo level
            switch ($q['level']) {
                case 'knowledge':     $totalKnowledge++;     break;
                case 'comprehension': $totalComprehension++; break;
                case 'application':   $totalApplication++;   break;
            }

            $isCorrect = false;
            if ($selectedAnswerId) {
                $correctAnswer = $answerModel->getCorrectByQuestion($questionId);
                $isCorrect = $correctAnswer
                    && ($selectedAnswerId === (int)$correctAnswer['answerId']);
            }

            if ($isCorrect) {
                $totalCorrect++;
                // Đếm câu đúng theo level
                switch ($q['level']) {
                    case 'knowledge':     $correctKnowledge++;     break;
                    case 'comprehension': $correctComprehension++; break;
                    case 'application':   $correctApplication++;   break;
                }
            }

            $details[] = [
                'questionId'       => $questionId,
                'selectedAnswerId' => $selectedAnswerId,
                'isCorrect'        => (int)$isCorrect,
            ];
        }
        

        $score = $totalQuestions > 0
            ? round(($totalCorrect / $totalQuestions) * 10, 2)
            : 0;

        $startTime = $_SESSION['exam_start_' . $examId] ?? date('Y-m-d H:i:s');

        if (!empty($_POST['expiredAt'])) {
            $endTime = date('Y-m-d H:i:s', strtotime($_POST['expiredAt']));
        } else {
            $endTime = date('Y-m-d H:i:s');
        }

        // Lưu kết quả
        $resultModel = new Result();
        $resultId = $resultModel->create([
            'examId'               => $examId,
            'subjectId'            => $subjectId,
            'userId'               => $userId,
            'score'                => $score,
            'totalCorrect'         => $totalCorrect,
            'totalQuestions'       => $totalQuestions,
            'totalKnowledge'       => $totalKnowledge,
            'totalComprehension'   => $totalComprehension,
            'totalApplication'     => $totalApplication,
            'correctKnowledge'     => $correctKnowledge,
            'correctComprehension' => $correctComprehension,
            'correctApplication'   => $correctApplication,
            'startTime'            => $startTime,
            'endTime'              => $endTime,
        ]);

        // Lưu chi tiết
        $resultDetailModel = new ResultDetail();
        foreach ($details as $d) {
            $resultDetailModel->create([
                'resultId'         => $resultId,
                'questionId'       => $d['questionId'],
                'selectedAnswerId' => $d['selectedAnswerId'],
                'isCorrect'        => $d['isCorrect'],
            ]);
        }

        if ($exam['examType'] === 'random') {
            $examModel->increasePlayCount($examId);
            $examModel->activateExam($examId);
        } else {
            $examModel->increaseViewCount($examId);
        }

        unset($_SESSION['exam_start_' . $examId]);

        header("Location: " . APP_URL . "/ket-qua/{$exam['slug']}-{$resultId}");
        exit;
    }


    // =====================================================
    // THPT QUỐC GIA - TẤT CẢ ĐỀ
    // =====================================================

    public function thpt()
    {
        $examModel    = new Exam();
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $grade = $gradeModel->getBySlug('lop-12');

        $subjects = $subjectModel->getByGrade($grade['gradeId']);

        $exams = $examModel->getThptExams();

        $currentSubject = null;

        $thptSubjects = $subjects;

        $this->view('exams/thpt', compact(
            'grade',
            'subjects',
            'exams',
            'currentSubject',
            'thptSubjects'       
        ));
    }


    // =====================================================
    // THPT QUỐC GIA - THEO MÔN
    // =====================================================

    public function thptBySubject($subjectSlug)
    {
        $examModel    = new Exam();
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $grade = $gradeModel->getBySlug('lop-12');

        if (!$grade) {
            $this->view('errors/404');
            return;
        }

        $subjects = $subjectModel->getByGrade($grade['gradeId']);

        $currentSubject = $subjectModel->getBySlugAndGrade(
            $subjectSlug,
            $grade['gradeId']   // gradeId = 3
        );

        if (!$currentSubject) {
            $this->view('errors/404');
            return;
        }

        $exams = $examModel->getThptExamsBySubject(
            $currentSubject['subjectId']
        );

        $thptSubjects = $subjects;

        $this->view('exams/thpt', compact(
            'grade',
            'subjects',
            'exams',
            'currentSubject',
            'thptSubjects'
        ));
    }


    public function generateQuickExam()
    {
        $jsonError = function (string $msg, int $code = 400) {
            http_response_code($code);
            header('Content-Type: application/json');
            echo json_encode(['error' => $msg]);
            exit;
        };

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $jsonError('Method not allowed', 405);
        }

        // Chưa login → trả JSON để JS mở modal
        if (empty($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['requireLogin' => true]);
            exit;
        }

        $subjectId      = (int)($_POST['subjectId'] ?? 0);
        $totalQuestions = (int)($_POST['totalQuestions'] ?? 20);
        $template       = $_POST['template'] ?? 'basic';

        $subjectModel  = new Subject();
        $questionModel = new Question();
        $examModel     = new Exam();
        $gradeModel    = new Grade();

        $grade12 = $gradeModel->getBySlug('lop-12');
        $subject = $subjectModel->getById($subjectId);

        if (!$subject) {
            $jsonError('Môn học không tồn tại');
        }

        if ((int)$subject['gradeId'] !== (int)$grade12['gradeId']) {
            $jsonError('Chỉ được tạo đề cho khối lớp 12');
        }

        $configs = [
            'basic'    => ['knowledge' => 50, 'comprehension' => 30, 'application' => 20],
            'standard' => ['knowledge' => 40, 'comprehension' => 30, 'application' => 30],
            'advanced' => ['knowledge' => 30, 'comprehension' => 40, 'application' => 30],
        ];

        $config = $configs[$template] ?? $configs['basic'];

        $knowledgeCount     = round($totalQuestions * $config['knowledge'] / 100);
        $comprehensionCount = round($totalQuestions * $config['comprehension'] / 100);
        $applicationCount   = $totalQuestions - $knowledgeCount - $comprehensionCount;

        $kAvail = $questionModel->countByLevel($subjectId, 'knowledge');
        $cAvail = $questionModel->countByLevel($subjectId, 'comprehension');
        $aAvail = $questionModel->countByLevel($subjectId, 'application');

        if (
            $kAvail < $knowledgeCount
            || $cAvail < $comprehensionCount
            || $aAvail < $applicationCount
        ) {
            $jsonError('Không đủ câu hỏi để tạo đề. Vui lòng chọn môn khác hoặc giảm số câu!');
        }

        $questions = array_merge(
            $questionModel->getRandomQuestionsByLevel($grade12['gradeId'], $subjectId, 'knowledge',     $knowledgeCount),
            $questionModel->getRandomQuestionsByLevel($grade12['gradeId'], $subjectId, 'comprehension', $comprehensionCount),
            $questionModel->getRandomQuestionsByLevel($grade12['gradeId'], $subjectId, 'application',   $applicationCount)
        );

        shuffle($questions);

        if (empty($questions)) {
            $jsonError('Không đủ câu hỏi để tạo đề');
        }

        $slug     = 'de-random-' . uniqid();
        $duration = match ($totalQuestions) {
            20      => 30,
            40      => 50,
            default => 60,
        };

        $examId = $examModel->create([
            'gradeId'              => $subject['gradeId'],
            'subjectId'            => $subjectId,
            'chapterId'            => null,
            'lessonId'             => null,
            'title'                => 'Đề thi tạo nhanh ' . date('d/m/Y H:i'),
            'slug'                 => $slug,
            'examType'             => 'random',
            'generationType'       => 'auto',
            'totalQuestions'       => $totalQuestions,
            'duration'             => $duration,
            'knowledgePercent'     => $config['knowledge'],
            'comprehensionPercent' => $config['comprehension'],
            'applicationPercent'   => $config['application'],
            'questionOrder'        => 'random',
            'isPublic'             => 0,
            'isTemporary'          => 1,
            'isActive'             => 0,   // ← thêm dòng này
            'createdBy'            => $_SESSION['user_id'],
        ]);

        foreach ($questions as $index => $q) {
            $examModel->insertExamQuestion($examId, $q['questionId'], $index + 1);
        }

        $examModel->deleteExpiredTemporaryExams();

        header('Content-Type: application/json');
        echo json_encode(['redirect' => APP_URL . '/de-thi/' . $slug]);
        exit;
    }


    public function showRandom($slug)
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $examModel     = new Exam();
        $questionModel = new Question();
        $subjectModel  = new Subject();
        $gradeModel    = new Grade();

        $exam = $examModel->getRandomExamBySlug(
            $slug,
            $_SESSION['user_id']
        );

        if (
            !$exam
            || $exam['examType'] !== 'random'
        ) {
            $this->view('errors/404');
            return;
        }

        // owner only
        if ((int)$exam['createdBy'] !== (int)$_SESSION['user_id']) {
            $this->view('errors/403');
            return;
        }

        $subject = $subjectModel->getById(
            $exam['subjectId']
        );

        $grade = $gradeModel->getById(
            $exam['gradeId']
        );

        if (!$subject || !$grade) {
            $this->view('errors/404');
            return;
        }

        $_SESSION['exam_start_' . $exam['examId']]
            = date('Y-m-d H:i:s');

        $questions =
            $questionModel->getByExamWithAnswers(
                $exam['examId']
            );

        $exam['realTotalQuestions']
            = count($questions);

        $this->view(
            'exams/detail',
            compact(
                'grade',
                'subject',
                'exam',
                'questions'
            )
        );
    }


    public function apiCheckExam()
    {
        $examId    = (int)($_GET['examId'] ?? 0);
        $examModel = new Exam();
        $exam      = $examModel->getById($examId);

        header('Content-Type: application/json');
        echo json_encode([
            'exists' => !empty($exam) && (
                $exam['examType'] === 'random'
                    ? true                            
                    : (int)($exam['isActive'] ?? 0) === 1  
            )
        ]);
        exit;
    }


}