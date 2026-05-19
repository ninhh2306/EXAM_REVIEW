<?php

require_once ROOT . '/app/core/Controller.php';

require_once ROOT . '/app/models/Result.php';
require_once ROOT . '/app/models/ResultDetail.php';
require_once ROOT . '/app/models/Exam.php';
require_once ROOT . '/app/models/Question.php';

class ResultController extends Controller
{

    // =====================================================
    // INDEX
    // =====================================================
    public function index()
    {
        $resultModel = new Result();

        $page = max(1, (int)($_GET['tab'] ?? 1));

        $limit = 20;

        $data = $resultModel->getAllPaginated(
            $page,
            $limit
        );

        $results = $data['results'];

        $total = $data['total'];

        $tabCount = ceil($total / $limit);

        $currentTab = $page;

        $this->viewAdmin('results/index', compact(
            'results',
            'tabCount',
            'currentTab'
        ));
    }


    // =====================================================
    // SHOW DETAIL
    // =====================================================
    public function show($id)
    {
        $resultModel       = new Result();
        $resultDetailModel = new ResultDetail();
        $examModel         = new Exam();
        $questionModel     = new Question();

        // 1. Kết quả
        $result = $resultModel->getFullDetail($id);

        if (!$result) {
            $this->view('errors/404');
            return;
        }

        // 2. Đề thi
        $exam = $examModel->getById($result['examId']);

        // 3. Questions
        $questions = $questionModel->getByExamWithAnswers(
            $result['examId']
        );

        // 4. Chi tiết bài làm
        $details = $resultDetailModel->getByResult($id);

        // USER ANSWERS
        $userAnswers = [];

        foreach ($details as $d) {
            $userAnswers[$d['questionId']]
                = $d['selectedAnswerId'];
        }

        // CORRECT ANSWERS
        $correctAnswers = [];

        foreach ($questions as $q) {

            foreach ($q['answers'] as $a) {

                if ($a['isCorrect']) {

                    $correctAnswers[$q['questionId']]
                        = $a['answerId'];

                    break;
                }
            }
        }

        // THỐNG KÊ
        $totalKnowledge = 0;
        $correctKnowledge = 0;

        $totalComprehension = 0;
        $correctComprehension = 0;

        $totalApplication = 0;
        $correctApplication = 0;

        foreach ($questions as $q) {

            $qId = $q['questionId'];

            $isRight = (
                isset($userAnswers[$qId]) &&
                $userAnswers[$qId]
                    == ($correctAnswers[$qId] ?? null)
            );

            switch ($q['level']) {

                case 'knowledge':

                    $totalKnowledge++;

                    if ($isRight) {
                        $correctKnowledge++;
                    }

                    break;

                case 'comprehension':

                    $totalComprehension++;

                    if ($isRight) {
                        $correctComprehension++;
                    }

                    break;

                case 'application':

                    $totalApplication++;

                    if ($isRight) {
                        $correctApplication++;
                    }

                    break;
            }
        }

        $this->viewAdmin('results/show', compact(
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

    // =====================================================
    // AJAX SEARCH
    // =====================================================
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $resultModel = new Result();

        $results = $resultModel->searchAdmin($keyword);

        header('Content-Type: application/json');

        echo json_encode([
            'success' => true,
            'results' => $results
        ]);
    }
}