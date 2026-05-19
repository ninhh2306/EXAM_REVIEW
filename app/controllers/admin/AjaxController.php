<?php

require_once ROOT . "/app/core/Controller.php";

require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Chapter.php";
require_once ROOT . "/app/models/Lesson.php";
require_once ROOT . "/app/models/User.php";

class AjaxController extends Controller
{
    // ================= SUBJECTS =================
    public function subjects()
    {
        $gradeId = $_GET['grade_id'] ?? 0;

        $subjectModel = new Subject();

        $subjects = $subjectModel->getByGrade($gradeId);

        header('Content-Type: application/json');

        echo json_encode($subjects);
    }

    // ================= CHAPTERS =================
    public function chapters()
    {
        $subjectId = $_GET['subject_id'] ?? 0;

        $chapterModel = new Chapter();

        $chapters = $chapterModel->getBySubject($subjectId);

        header('Content-Type: application/json');

        echo json_encode($chapters);
    }

    // ================= LESSONS =================
    public function lessons()
    {
        $chapterId = $_GET['chapter_id'] ?? 0;

        $lessonModel = new Lesson();

        $lessons = $lessonModel->getByChapter(
            $chapterId
        );

        header('Content-Type: application/json');

        echo json_encode($lessons);
    }


    // ================= EXAM =================
    public function exams()
    {
        $lessonId = $_GET['lesson_id'] ?? 0;

        require_once ROOT . '/app/models/Exam.php';

        $examModel = new Exam();

        $exams = $examModel->getByLesson($lessonId);

        header('Content-Type: application/json');

        echo json_encode($exams);
    }


    // ================= QUESTION =================
    public function questions()
    {
        $examId = $_GET['exam_id'] ?? 0;

        require_once ROOT . '/app/models/Question.php';

        $questionModel = new Question();

        $questions = $questionModel->getByExamOrdered(
            $examId,
            $exam['questionOrder']
        );

        header('Content-Type: application/json');

        echo json_encode($questions);
    }



    public function examQuestions()
    {
        $lessonId = (int)($_GET['lesson_id'] ?? 0);

        if (!$lessonId) {
            header('Content-Type: application/json');
            echo json_encode([]);
            return;
        }

        require_once ROOT . '/app/models/Question.php';

        $questionModel = new Question();

        $questions = $questionModel->getByLesson($lessonId);

        header('Content-Type: application/json');
        echo json_encode($questions);
    }


    // ================= SEARCH SUBJECTS =================
    public function searchSubjects()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        require_once ROOT . '/app/models/Subject.php';

        $subjectModel = new Subject();

        $subjects = $subjectModel->searchAdminSubjects(
            $keyword
        );

        header('Content-Type: application/json');

        echo json_encode($subjects);
    }


    // ================= SEARCH CHAPTERS =================
    public function searchChapters()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        require_once ROOT . '/app/models/Chapter.php';

        $chapterModel = new Chapter();

        $chapters = $chapterModel->searchAdminChapters($keyword);

        header('Content-Type: application/json');

        echo json_encode($chapters);
    }


    // ================= SEARCH LESSONS =================
    public function searchLessons()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        require_once ROOT . '/app/models/Lesson.php';

        $lessonModel = new Lesson();

        $lessons = $lessonModel->searchAdminLessons($keyword);

        header('Content-Type: application/json');

        echo json_encode($lessons);
    }



    // ================= SEARCH EXAMS =================
    public function searchExams()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        require_once ROOT . '/app/models/Exam.php';

        $examModel = new Exam();

        $exams = $examModel->searchAdminExams($keyword);

        header('Content-Type: application/json');

        echo json_encode($exams);
    }


    // ================= SEARCH POSTS =================
    public function searchPosts()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        require_once ROOT . '/app/models/Post.php';

        $postModel = new Post();

        $posts = $postModel->searchAdminPosts($keyword);

        header('Content-Type: application/json');

        echo json_encode($posts);
    }


    // ================= SEARCH USERS =================
    public function searchUsers()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        require_once ROOT . '/app/models/User.php';

        $userModel = new User();

        $users = $userModel->searchAdminUsers($keyword);

        header('Content-Type: application/json');

        echo json_encode($users);
    }


    
}