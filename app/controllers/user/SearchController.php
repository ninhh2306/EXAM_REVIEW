<?php

require_once ROOT . "/app/core/Controller.php";

require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Lesson.php";
require_once ROOT . "/app/models/Exam.php";
require_once ROOT . "/app/models/Post.php";

class SearchController extends Controller
{
    public function ajax()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        if ($keyword === '') {

            header('Content-Type: application/json');

            echo json_encode([
                'subjects' => [],
                'lessons'  => [],
                'exams'    => [],
                'posts'    => []
            ]);

            return;
        }

        $subjectModel = new Subject();
        $lessonModel  = new Lesson();
        $examModel    = new Exam();
        $postModel    = new Post();

        $subjects = $subjectModel->searchSubjects($keyword);
        $lessons = $lessonModel->searchLessons($keyword);
        $exams = $examModel->searchExams($keyword);
        $posts = $postModel->searchPosts($keyword);

        header('Content-Type: application/json');

        echo json_encode([
            'subjects' => $subjects,
            'lessons'  => $lessons,
            'exams'    => $exams,
            'posts'    => $posts
        ]);
    }


}