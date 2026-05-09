<?php

require_once ROOT . "/app/core/Controller.php";
require_once ROOT . "/app/models/Grade.php";
require_once ROOT . "/app/models/Subject.php";
require_once ROOT . "/app/models/Lesson.php";
require_once ROOT . "/app/models/Exam.php";
require_once ROOT . "/app/models/Chapter.php";

class LessonController extends Controller
{
    // ===============================
    // DANH SÁCH BÀI HỌC
    // ===============================
    public function index($gradeSlug, $subjectSlug)
    {
        $gradeModel   = new Grade();
        $subjectModel = new Subject();
        $lessonModel  = new Lesson();
        $chapterModel = new Chapter();

        // ===== CHECK GRADE =====
        $grade = $gradeModel->getBySlug($gradeSlug);
        if (!$grade) {
            $this->view('errors/404');
            return;
        }

        // ===== CHECK SUBJECT =====
        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']);
        if (!$subject) {
            $this->view('errors/404');
            return;
        }

        // ===== DATA =====
        $lessons = $lessonModel->getBySubject(
            $subject['subjectId']
        );

        $chapters = $chapterModel->getBySubject(
            $subject['subjectId']
        );
        
        // ===== VIEW =====
        $this->view('lessons/index', compact(
            'grade',
            'subject',
            'lessons',
            'chapters'
        ));
    }


    // ===============================
    // (OPTIONAL) LIST CŨ
    // ===============================
    public function bySlug($gradeSlug, $subjectSlug)
    {
        $gradeModel   = new Grade();
        $subjectModel = new Subject();
        $lessonModel  = new Lesson();
        $chapterModel = new Chapter();

        $grade = $gradeModel->getBySlug($gradeSlug);
        if (!$grade) { 
            $this->view('errors/404'); 
            return; 
        }

        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']);
        if (!$subject) { 
            $this->view('errors/404'); 
            return; 
        }

        $lessons  = $lessonModel->getBySubject($subject['subjectId']);
        $chapters = $chapterModel->getBySubject($subject['subjectId']);

        $this->view('lessons/list', compact(
            'grade',
            'subject',
            'lessons',
            'chapters'
        ));
    }


    // ===============================
    // CHI TIẾT BÀI HỌC
    // ===============================
    public function show($gradeSlug, $subjectSlug, $chapterSlug, $lessonSlug)
    {
        $gradeModel   = new Grade();
        $subjectModel = new Subject();
        $lessonModel  = new Lesson();
        $chapterModel = new Chapter();
        $examModel    = new Exam();

        // ===== CHECK GRADE =====
        $grade = $gradeModel->getBySlug($gradeSlug);
        if (!$grade) {
            $this->view('errors/404');
            return;
        }

        // ===== CHECK SUBJECT =====
        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']);
        if (!$subject) {
            $this->view('errors/404');
            return;
        }

        // ===== CHECK CHAPTER =====
        $chapter = $chapterModel->getBySlug($chapterSlug, $subject['subjectId']);
        if (!$chapter) {
            $this->view('errors/404');
            return;
        }

        // ===== LESSON =====
        $lesson = $lessonModel->getByChapterAndSlug($chapterSlug, $lessonSlug);
        if (!$lesson) {
            $this->view('errors/404');
            return;
        }

        // ===== RELATED =====
        $relatedLessons = $lessonModel->getBySubject($subject['subjectId']);
        $relatedExams   = $examModel->getBySubject($subject['subjectId']);

        // ===== VIEW =====
        $this->view('lessons/detail', compact(
            'grade',
            'subject',
            'chapter',
            'lesson',
            'relatedLessons',
            'relatedExams'
        ));
    }
}