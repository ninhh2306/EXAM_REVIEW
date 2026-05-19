<?php

require_once ROOT . "/app/core/Controller.php";
require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Lesson.php";
require_once ROOT . "/app/models/Exam.php";
require_once ROOT . "/app/models/Grade.php";

class SubjectController extends Controller
{
    public function index($gradeSlug)
    {
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $grade = $gradeModel->getBySlug($gradeSlug);

        if (!$grade) {
            return $this->view('errors/404');
        }

        $subjects = $subjectModel->getByGrade($grade['gradeId']);

        return $this->view('subjects/list', [
            'grade' => $grade,
            'subjects' => $subjects
        ]);
    }

    public function show($gradeSlug, $subjectSlug)
    {
        $subjectModel = new Subject();
        $lessonModel  = new Lesson();
        $examModel    = new Exam();
        $gradeModel   = new Grade();

        // lấy grade
        $grade = $gradeModel->getBySlug($gradeSlug);

        if (!$grade) {
            return $this->view('errors/404');
        }

        // lấy subject theo slug + gradeId
        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']);

        if (!$subject) {
            return $this->view('errors/404');
        }

        $lessons = $lessonModel->getBySubject($subject['subjectId']);
        $exams = $examModel->getBySubject($subject['subjectId']);

        return $this->view("subjects/detail", [
            "subject" => $subject,
            "grade"   => $grade,
            "lessons" => $lessons,
            "exams"   => $exams
        ]);
    }


}