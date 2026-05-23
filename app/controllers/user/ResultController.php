<?php

require_once ROOT . '/app/core/Controller.php';

require_once ROOT . '/app/models/Exam.php';
require_once ROOT . '/app/models/Question.php';
require_once ROOT . '/app/models/Subject.php';
require_once ROOT . '/app/models/Grade.php';
require_once ROOT . '/app/models/Result.php';
require_once ROOT . '/app/models/ResultDetail.php';

class ResultController extends Controller
{
    
    // GET /ket-qua/{resultId}
    // Hiển thị trang kết quả → views/results/index.php
    public function show($slug, $resultId)
    {
        $resultModel  = new Result();
        $examModel    = new Exam();
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        // 1. Lấy kết quả
        $result = $resultModel->getById($resultId);
        if (!$result) {
            $this->view('errors/404'); 
            return;
        }

        // 2. Lấy đề thi từ result
        $exam = $examModel->getById($result['examId']);

        if (!$exam || !$exam['isActive']) {
            $_SESSION['flash_error'] = 'exam_deleted';  // ← thêm dòng này
            $this->view('errors/404');                  // ← giữ nguyên, KHÔNG redirect
            return;
        }

        // CHECK slug đúng không (best practice)
        if ($exam['slug'] !== $slug) {
            header("Location: /ket-qua/{$exam['slug']}-{$resultId}");
            exit;
        }

        // 3. Lấy subject từ exam (ĐÚNG THEO DB)
        $subject = null;
        if (!empty($exam['subjectId'])) {
            $subject = $subjectModel->getById($exam['subjectId']);
        }

        // 4. Lấy grade từ subject
        $grade = null;
        if ($subject && !empty($subject['gradeId'])) {
            $grade = $gradeModel->getById($subject['gradeId']);
        }

        // 5. Đề gợi ý (cùng môn)
        $suggested = [];
        if (!empty($exam['subjectId'])) {
            $suggested = $examModel->getSuggested(
                $exam['subjectId'],
                $exam['examId'],
                $exam['examType'],  
                5                   
            );
        }

        $this->view('results/index', compact(
            'result', 
            'exam', 
            'subject', 
            'grade', 
            'suggested'
        ));

    }


    // GET /ket-qua/{resultId}/chi-tiet
    // Xem đáp án chi tiết → views/results/detail.php
    public function detail($slug, $resultId)
    {
        $resultModel       = new Result();
        $resultDetailModel = new ResultDetail();
        $examModel         = new Exam();
        $questionModel     = new Question();

        // 1. Lấy kết quả
        $result = $resultModel->getById($resultId);
        if (!$result) {
            $this->view('errors/404');
            return;
        }

        // 2. Lấy đề thi
        $exam = $examModel->getById($result['examId']);
        
        if (!$exam || !$exam['isActive']) {
            $_SESSION['flash_error'] = 'exam_deleted';  // ← thêm dòng này
            $this->view('errors/404');                  // ← giữ nguyên, KHÔNG redirect
            return;
        }

        // 3. Lấy toàn bộ câu hỏi + đáp án
        $questions = $questionModel->getByExamWithAnswers($result['examId']);
        $exam['realTotalQuestions'] = count($questions);

        // 4. Lấy chi tiết bài làm
        $details = $resultDetailModel->getByResult($resultId);

        // 5. Map user answers
        $userAnswers = [];
        foreach ($details as $d) {
            $userAnswers[$d['questionId']] = $d['selectedAnswerId'];
        }

        // 6. Map correct answers
        $correctAnswers = [];
        foreach ($questions as $q) {
            foreach ($q['answers'] as $a) {
                if ($a['isCorrect']) {
                    $correctAnswers[$q['questionId']] = $a['answerId'];
                    break;
                }
            }
        }

        // 7. Thống kê số câu Tổng và số câu Đúng theo mức độ
        $totalKnowledge = 0; $correctKnowledge = 0;
        $totalComprehension = 0; $correctComprehension = 0;
        $totalApplication = 0; $correctApplication = 0;

        foreach ($questions as $q) {
            $qId = $q['questionId'];
            
            // Kiểm tra xem câu này người dùng làm đúng hay sai
            $isRight = (isset($userAnswers[$qId]) && $userAnswers[$qId] == ($correctAnswers[$qId] ?? null));

            switch ($q['level']) {
                case 'knowledge':
                    $totalKnowledge++;
                    if ($isRight) $correctKnowledge++;
                    break;
                case 'comprehension':
                    $totalComprehension++;
                    if ($isRight) $correctComprehension++;
                    break;
                case 'application':
                    $totalApplication++;
                    if ($isRight) $correctApplication++;
                    break;
            }
        }

        // 8. Render view (Nhớ thêm các biến correct vào compact)
        $this->view('results/detail', compact(
            'result',
            'exam',
            'questions',
            'userAnswers',
            'correctAnswers',
            'totalKnowledge',
            'totalComprehension',
            'totalApplication',
            'correctKnowledge',
            'correctComprehension',
            'correctApplication'
        ));

    }


    // Danh sách lịch sử làm bài
    public function history()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $userId = $_SESSION['user_id'];

        $keyword   = $_GET['keyword']   ?? '';
        $subjectId = $_GET['subjectId'] ?? '';

        $resultModel = new Result();

        $histories = $resultModel->getHistoryByUser(
            $userId,
            $keyword,
            $subjectId
        );

        $subjects = $resultModel->getSubjectsHistory($userId);

        $summary = $resultModel->getSummary($userId);

        // SEARCH
        if (isset($_GET['ajax'])) {

            header('Content-Type: application/json');

            echo json_encode($histories);
            return;
        }

        $this->view('results/history', compact(
            'histories',
            'subjects',
            'summary',
            'keyword',
            'subjectId'
        ));
    }
    

}
