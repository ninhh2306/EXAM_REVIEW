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

        $grade    = $gradeModel->getBySlug($gradeSlug);
        $subject  = $subjectModel->getBySlug($subjectSlug);
        $exams    = $examModel->getBySubjectSlug($subjectSlug);
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
        if (!$exam) { $this->view('errors/404'); return; }

        $_SESSION['exam_start_' . $exam['examId']] = date('Y-m-d H:i:s');

        $questions = $questionModel->getByExamWithAnswers($exam['examId']);

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

        $examModel = new Exam();
        $exam      = $examModel->getById($examId);

        if (!$exam) {
            die("Exam không tồn tại");
        }

        $subjectId = $exam['subjectId'];

        // Chấm điểm
        $answerModel   = new Answer();
        $questionModel = new Question();

        $totalQuestions = (int)$exam['totalQuestions'];
        $totalCorrect   = 0;
        $details        = [];

        $allQuestions = $questionModel->getByExam($examId);

        foreach ($allQuestions as $q) {
            $questionId       = (int)$q['questionId'];
            $selectedAnswerId = isset($answers[$questionId]) ? (int)$answers[$questionId] : null;

            $isCorrect = false;
            if ($selectedAnswerId) {
                $correctAnswer = $answerModel->getCorrectByQuestion($questionId);
                $isCorrect = $correctAnswer
                    && ($selectedAnswerId === (int)$correctAnswer['answerId']);
            }

            if ($isCorrect) $totalCorrect++;

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
        $resultId    = $resultModel->create([
            'examId'         => $examId,
            'subjectId'      => $subjectId,
            'userId'         => $userId,
            'score'          => $score,
            'totalCorrect'   => $totalCorrect,
            'totalQuestions' => $totalQuestions,
            'startTime'      => $startTime,
            'endTime'        => $endTime,
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

        $examModel->increaseViewCount($examId);
        unset($_SESSION['exam_start_' . $examId]);

        header("Location: " . APP_URL . "/ket-qua/{$exam['slug']}-{$resultId}");
        exit;
    }
}