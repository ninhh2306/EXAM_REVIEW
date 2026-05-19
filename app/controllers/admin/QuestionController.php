<?php

require_once ROOT . "/app/core/Controller.php";

require_once ROOT . "/app/models/Question.php";
require_once ROOT . "/app/models/Answer.php";

require_once ROOT . "/app/models/Grade.php";
require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Chapter.php";
require_once ROOT . "/app/models/Lesson.php";

use PhpOffice\PhpSpreadsheet\IOFactory;


class QuestionController extends Controller
{
    // =========================
    // INDEX
    // =========================
    public function index()
    {
        $questionModel = new Question();

        $tab   = max(1, (int)($_GET['tab'] ?? 1));
        $limit = 30;

        $data = $questionModel->getAllPaginated($tab, $limit);

        $questions  = $data['questions'];
        $total      = $data['total'];
        $tabCount   = ceil($total / $limit);
        $currentTab = $tab;

        $this->viewAdmin('questions/index', compact(
            'questions',
            'tabCount',
            'currentTab'
        ));
    }

    // =========================
    // CREATE
    // =========================
    public function create()
    {
        $gradeModel = new Grade();
        $grades     = $gradeModel->all();

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $this->viewAdmin('questions/create', compact(
            'grades',
            'flashError',
            'flashOld'
        ));
    }

    // =========================
    // STORE
    // =========================
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/questions');
            exit;
        }

        $questionModel = new Question();

        $hasExcelFile = isset($_FILES['excelFile'])
            && $_FILES['excelFile']['error'] === 0;

        // ======= DATA =======
        $gradeId       = trim($_POST['gradeId']       ?? '');
        $subjectId     = trim($_POST['subjectId']     ?? '');
        $chapterId     = trim($_POST['chapterId']     ?? '');
        $lessonId      = trim($_POST['lessonId']      ?? '');
        $questionType  = trim($_POST['questionType']  ?? 'lesson');
        $level         = trim($_POST['level']         ?? '');
        $content       = trim($_POST['content']       ?? '');
        $answerA       = trim($_POST['answerA']       ?? '');
        $answerB       = trim($_POST['answerB']       ?? '');
        $answerC       = trim($_POST['answerC']       ?? '');
        $answerD       = trim($_POST['answerD']       ?? '');
        $correctAnswer = $_POST['correctAnswer']      ?? '';

        $_SESSION['flash_old'] = $_POST;

        // ======= VALIDATE CHUNG =======
        if (empty($gradeId)) {
            $_SESSION['flash_error'] = 'Vui lòng chọn khối lớp';
            header('Location: /admin/questions/create');
            exit;
        }

        if (empty($subjectId)) {
            $_SESSION['flash_error'] = 'Vui lòng chọn môn học';
            header('Location: /admin/questions/create');
            exit;
        }

        // ======= VALIDATE THEO TYPE =======
        if ($questionType === 'lesson') {

            if (empty($chapterId)) {
                $_SESSION['flash_error'] = 'Vui lòng chọn chương học';
                header('Location: /admin/questions/create');
                exit;
            }

            if (empty($lessonId)) {
                $_SESSION['flash_error'] = 'Vui lòng chọn bài học';
                header('Location: /admin/questions/create');
                exit;
            }

            if (!$hasExcelFile && empty($level)) {
                $_SESSION['flash_error'] = 'Vui lòng chọn mức độ';
                header('Location: /admin/questions/create');
                exit;
            }

        } else {
            // THPT: clear chapter/lesson, giữ level
            $chapterId = null;
            $lessonId  = null;

            if (!$hasExcelFile && empty($level)) {
                $_SESSION['flash_error'] = 'Vui lòng chọn mức độ';
                header('Location: /admin/questions/create');
                exit;
            }
        }

        // ======= VALIDATE ANSWERS (nhập tay) =======
        if (!$hasExcelFile) {

            $answers = [$answerA, $answerB, $answerC, $answerD];

            $filledAnswers = array_filter($answers, fn($a) => trim($a) !== '');

            if (count($filledAnswers) < 2) {
                $_SESSION['flash_error'] = 'Câu hỏi phải có ít nhất 2 đáp án';
                header('Location: /admin/questions/create');
                exit;
            }

            if ($correctAnswer === '') {
                $_SESSION['flash_error'] = 'Vui lòng chọn đáp án đúng';
                header('Location: /admin/questions/create');
                exit;
            }

            if (empty($answers[$correctAnswer])) {
                $_SESSION['flash_error'] = 'Đáp án đúng không được để trống';
                header('Location: /admin/questions/create');
                exit;
            }
        }

        // ======= NULLABLE =======
        $chapterId = !empty($chapterId) ? $chapterId : null;
        $lessonId  = !empty($lessonId)  ? $lessonId  : null;
        $level     = !empty($level)     ? $level     : null;

        // ======= IMPORT EXCEL =======
        if ($hasExcelFile) {

            require ROOT . '/vendor/autoload.php';

            $spreadsheet = IOFactory::load($_FILES['excelFile']['tmp_name']);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray();

            unset($rows[0]); // bỏ header

            $mapCorrect = ['A' => 0, 'B' => 1, 'C' => 2, 'D' => 3];

            $levelMap = [
                'knowledge'    => 'knowledge',
                'comprehension'=> 'comprehension',
                'application'  => 'application',
                'nhận biết'    => 'knowledge',
                'thông hiểu'   => 'comprehension',
                'vận dụng'     => 'application',
            ];

            $success = 0;

            foreach ($rows as $row) {

                $question   = trim($row[0] ?? '');
                $a          = trim($row[1] ?? '');
                $b          = trim($row[2] ?? '');
                $c          = trim($row[3] ?? '');
                $d          = trim($row[4] ?? '');
                $correct    = strtoupper(trim($row[5] ?? ''));
                $levelExcel = trim($row[6] ?? '');

                // bỏ qua dòng thiếu dữ liệu
                if (empty($question) || empty($a) || empty($b)) {
                    continue;
                }

                if (!isset($mapCorrect[$correct])) {
                    continue;
                }

                // đọc level — bắt buộc cho cả lesson lẫn thpt
                $levelKey   = mb_strtolower($levelExcel, 'UTF-8');
                $levelValue = $levelMap[$levelKey] ?? null;

                if (empty($levelValue)) {
                    continue; // bỏ qua dòng không có level hợp lệ
                }

                $ok = $questionModel->createQuestionWithAnswers([
                    'gradeId'      => $gradeId,
                    'subjectId'    => $subjectId,
                    'chapterId'    => $questionType === 'lesson' ? $chapterId : null,
                    'lessonId'     => $questionType === 'lesson' ? $lessonId  : null,
                    'content'      => $question,
                    'level'        => $levelValue,
                    'questionType' => $questionType,
                    'answers'      => [$a, $b, $c, $d],
                    'correctAnswer'=> $mapCorrect[$correct],
                ], $_SESSION['user']['userId'] ?? 1);

                if ($ok) $success++;
            }

            unset($_SESSION['flash_old']);
            $_SESSION['success'] = "Import thành công {$success} câu hỏi";
            header('Location: /admin/questions');
            exit;
        }

        // ======= INSERT THỦ CÔNG =======
        $ok = $questionModel->createQuestionWithAnswers([
            'gradeId'      => $gradeId,
            'subjectId'    => $subjectId,
            'chapterId'    => $chapterId,
            'lessonId'     => $lessonId,
            'content'      => $content,
            'level'        => $level,
            'questionType' => $questionType,
            'answers'      => $answers,
            'correctAnswer'=> $correctAnswer,
        ], $_SESSION['user']['userId'] ?? 1);

        if (!$ok) {
            $_SESSION['flash_error'] = 'Thêm câu hỏi thất bại';
            header('Location: /admin/questions/create');
            exit;
        }

        unset($_SESSION['flash_old']);
        $_SESSION['success'] = 'Thêm câu hỏi thành công';
        header('Location: /admin/questions');
        exit;
    }

    // =========================
    // EDIT
    // =========================
    public function edit($id)
    {
        $questionModel = new Question();
        $question      = $questionModel->getFullById($id);

        if (!$question) {
            header('Location: /admin/questions');
            exit;
        }

        $gradeModel   = new Grade();
        $subjectModel = new Subject();
        $chapterModel = new Chapter();
        $lessonModel  = new Lesson();

        $grades   = $gradeModel->all();
        $subjects = $subjectModel->getByGrade($question['gradeId']);
        $chapters = !empty($question['subjectId'])
            ? $chapterModel->getBySubject($question['subjectId'])
            : [];
        $lessons  = !empty($question['chapterId'])
            ? $lessonModel->getByChapter($question['chapterId'])
            : [];

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old']   ?? [];

        unset($_SESSION['flash_error'], $_SESSION['flash_old']);

        $this->viewAdmin('questions/edit', compact(
            'question',
            'grades',
            'subjects',
            'chapters',
            'lessons',
            'flashError',
            'flashOld'
        ));
    }

    // =========================
    // UPDATE
    // =========================
    public function update()
    {
        $questionId = (int)($_POST['questionId'] ?? 0);

        if (!$questionId) {
            header('Location: /admin/questions');
            exit;
        }

        $_SESSION['flash_old'] = $_POST;

        $data = [
            'gradeId'      => trim($_POST['gradeId']      ?? ''),
            'subjectId'    => trim($_POST['subjectId']    ?? ''),
            'chapterId'    => trim($_POST['chapterId']    ?? ''),
            'lessonId'     => trim($_POST['lessonId']     ?? ''),
            'content'      => trim($_POST['content']      ?? ''),
            'level'        => trim($_POST['level']        ?? ''),
            'questionType' => trim($_POST['questionType'] ?? 'lesson'),
            'correctAnswer'=> $_POST['correctAnswer']     ?? '',
            'answerA'      => trim($_POST['answerA']      ?? ''),
            'answerB'      => trim($_POST['answerB']      ?? ''),
            'answerC'      => trim($_POST['answerC']      ?? ''),
            'answerD'      => trim($_POST['answerD']      ?? ''),
        ];

        // ======= VALIDATE CHUNG =======
        if (empty($data['gradeId'])) {
            $_SESSION['flash_error'] = 'Vui lòng chọn khối lớp';
            header('Location: /admin/questions/edit/' . $questionId);
            exit;
        }

        if (empty($data['subjectId'])) {
            $_SESSION['flash_error'] = 'Vui lòng chọn môn học';
            header('Location: /admin/questions/edit/' . $questionId);
            exit;
        }

        // ======= VALIDATE THEO TYPE =======
        if ($data['questionType'] === 'lesson') {

            if (empty($data['chapterId'])) {
                $_SESSION['flash_error'] = 'Vui lòng chọn chương học';
                header('Location: /admin/questions/edit/' . $questionId);
                exit;
            }

            if (empty($data['lessonId'])) {
                $_SESSION['flash_error'] = 'Vui lòng chọn bài học';
                header('Location: /admin/questions/edit/' . $questionId);
                exit;
            }

            if (empty($data['level'])) {
                $_SESSION['flash_error'] = 'Vui lòng chọn mức độ';
                header('Location: /admin/questions/edit/' . $questionId);
                exit;
            }

        } else {
            // THPT: clear chapter/lesson, validate level
            $data['chapterId'] = null;
            $data['lessonId']  = null;

            if (empty($data['level'])) {
                $_SESSION['flash_error'] = 'Vui lòng chọn mức độ';
                header('Location: /admin/questions/edit/' . $questionId);
                exit;
            }
        }

        // ======= VALIDATE ANSWERS =======
        $answers = [
            $data['answerA'],
            $data['answerB'],
            $data['answerC'],
            $data['answerD'],
        ];

        $filledAnswers = array_filter($answers, fn($a) => trim($a) !== '');

        if (count($filledAnswers) < 2) {
            $_SESSION['flash_error'] = 'Câu hỏi phải có ít nhất 2 đáp án';
            header('Location: /admin/questions/edit/' . $questionId);
            exit;
        }

        if ($data['correctAnswer'] === '') {
            $_SESSION['flash_error'] = 'Vui lòng chọn đáp án đúng';
            header('Location: /admin/questions/edit/' . $questionId);
            exit;
        }

        if (empty($answers[$data['correctAnswer']])) {
            $_SESSION['flash_error'] = 'Đáp án đúng không được để trống';
            header('Location: /admin/questions/edit/' . $questionId);
            exit;
        }

        // ======= NULLABLE =======
        $data['chapterId'] = !empty($data['chapterId']) ? $data['chapterId'] : null;
        $data['lessonId']  = !empty($data['lessonId'])  ? $data['lessonId']  : null;
        $data['level']     = !empty($data['level'])     ? $data['level']     : null;

        // ======= UPDATE =======
        $questionModel = new Question();

        $ok = $questionModel->updateQuestionWithAnswers($questionId, $data);

        if (!$ok) {
            $_SESSION['flash_error'] = 'Cập nhật thất bại';
            header('Location: /admin/questions/edit/' . $questionId);
            exit;
        }

        unset($_SESSION['flash_old']);
        $_SESSION['success'] = 'Cập nhật câu hỏi thành công';
        header('Location: /admin/questions');
        exit;
    }

    // =========================
    // DELETE
    // =========================
    public function delete($id)
    {
        $questionModel = new Question();

        if ($questionModel->isUsedInExam($id)) {
            $_SESSION['flash_error'] = 'Không thể xóa câu hỏi đang được dùng trong đề thi!';
            header('Location: /admin/questions');
            exit;
        }

        $ok = $questionModel->delete($id);

        if (!$ok) {
            $_SESSION['flash_error'] = 'Xóa thất bại';
            header('Location: /admin/questions');
            exit;
        }

        $_SESSION['success'] = 'Xóa câu hỏi thành công';
        header('Location: /admin/questions');
        exit;
    }

    // =========================
    // SEARCH
    // =========================
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $questionModel = new Question();

        $questions = $questionModel->searchAjax($keyword);

        header('Content-Type: application/json');
        echo json_encode($questions);
    }
}