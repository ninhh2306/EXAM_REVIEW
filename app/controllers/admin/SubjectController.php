<?php

require_once ROOT . '/app/models/Subject.php';
require_once ROOT . '/app/models/Grade.php';

class SubjectController extends Controller
{
    // =========================================================
    // AJAX - GET SUBJECTS BY GRADE
    // =========================================================
    public function getByGrade()
    {
        $gradeId = $_GET['grade_id'] ?? 0;

        $subjectModel = new Subject();

        $subjects = $subjectModel->getByGrade($gradeId);

        header('Content-Type: application/json');

        echo json_encode($subjects);
        exit;
    }

    // =========================================================
    // VALIDATE SUBJECT NAME
    // =========================================================
    private function validateSubjectName(&$name)
    {
        $name = trim($name);

        // Gộp nhiều khoảng trắng
        $name = preg_replace('/\s+/u', ' ', $name);

        if ($name === '') {
            return 'Tên môn học không được để trống';
        }

        if (mb_strlen($name) < 2) {
            return 'Tên môn học phải có ít nhất 2 ký tự';
        }

        if (mb_strlen($name) > 50) {
            return 'Tên môn học không được vượt quá 50 ký tự';
        }


        if (
            !preg_match(
                "/^[\p{L}\p{N}\s\-\(\),]+$/u",
                $name
            )
        ) {
            return 'Tên môn học chứa ký tự không hợp lệ';
        }

        return null;
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $allSubjects = $subjectModel->getAll();
        $grades      = $gradeModel->getAll();

        $perTab      = 5;
        $total       = count($allSubjects);

        $tabCount    = max(1, ceil($total / $perTab));

        $currentTab  = max(
            1,
            min($tabCount, (int)($_GET['tab'] ?? 1))
        );

        $subjects = array_slice(
            $allSubjects,
            ($currentTab - 1) * $perTab,
            $perTab
        );

        $this->viewAdmin("subjects/index", [
            "title"      => "Quản lý Môn học",
            "subjects"   => $subjects,
            "grades"     => $grades,
            "tabCount"   => $tabCount,
            "currentTab" => $currentTab,
        ]);
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        $gradeModel = new Grade();

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_old']
        );

        $this->viewAdmin("subjects/create", [
            "title"      => "Thêm môn học",
            "grades"     => $gradeModel->getAllAsc(),
            "flashError" => $flashError,
            "flashOld"   => $flashOld,
        ]);
    }

    // =========================================================
    // STORE
    // =========================================================
    public function store()
    {
        $subjectModel = new Subject();

        $name     = trim($_POST['name'] ?? '');
        $slugInp  = trim($_POST['slug'] ?? '');
        $gradeId  = $_POST['gradeId'] ?? '';

        $desc     = trim($_POST['description'] ?? '');
        $detail   = trim($_POST['detailDesc'] ?? '');

        // VALIDATE NAME
        $nameError = $this->validateSubjectName($name);

        // OLD DATA
        $old = [
            'gradeId'    => $gradeId,
            'name'       => $name,
            'slug'       => $slugInp,
            'description'=> $desc,
            'detailDesc' => $detail,
        ];

        $_SESSION['flash_old'] = $old;

        // EMPTY
        if (
            empty($name) ||
            empty($gradeId)
        ) {

            $_SESSION['flash_error'] =
                'Vui lòng nhập đầy đủ thông tin';

            header("Location: /admin/subjects/create");
            exit;
        }

        // NAME ERROR
        if ($nameError) {

            $_SESSION['flash_error'] = $nameError;

            header("Location: /admin/subjects/create");
            exit;
        }

        // SLUG
        $slug = empty($slugInp)
            ? $subjectModel->toSlug($name)
            : $subjectModel->toSlug($slugInp);

        // EXISTS
        if (
            $subjectModel->existsFull(
                $name,
                $slug,
                $gradeId
            )
        ) {

            $_SESSION['flash_error'] =
                'Tên hoặc slug môn học đã tồn tại trong khối lớp';

            header("Location: /admin/subjects/create");
            exit;
        }

        // IMAGE
        $image = null;

        if (!empty($_FILES['image']['name'])) {

            $image = time() . '_' .
                basename($_FILES['image']['name']);

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                ROOT . "/public/images/subjects/" . $image
            );
        }

        // CREATE
        $subjectModel->create(
            $name,
            $slug,
            $gradeId,
            $image,
            $desc,
            $detail
        );

        unset($_SESSION['flash_old']);

        header("Location: /admin/subjects?success=created");
        exit;
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit($id)
    {
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $subject = $subjectModel->find($id);

        if (!$subject) {
            header("Location: /admin/subjects");
            exit;
        }

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_old']
        );

        if (!empty($flashOld)) {

            $subject = array_merge($subject, [
                'gradeId'     => $flashOld['gradeId'],
                'subjectName' => $flashOld['name'],
                'slug'        => $flashOld['slug'],
                'description' => $flashOld['description'],
                'detailDesc'  => $flashOld['detailDesc'],
            ]);
        }

        $this->viewAdmin('subjects/edit', [
            'subject'    => $subject,
            'grades'     => $gradeModel->getAll(),
            'flashError' => $flashError,
        ]);
    }

    // =========================================================
    // UPDATE
    // =========================================================
    public function update()
    {
        $subjectModel = new Subject();

        $id = $_POST['id'] ?? null;

        if (!$id) {
            header("Location: /admin/subjects");
            exit;
        }

        $subject = $subjectModel->find($id);

        if (!$subject) {
            header("Location: /admin/subjects");
            exit;
        }

        $name     = trim($_POST['name'] ?? '');
        $slugInp  = trim($_POST['slug'] ?? '');
        $gradeId  = $_POST['gradeId'] ?? '';

        $desc     = trim($_POST['description'] ?? '');
        $detail   = trim($_POST['detailDesc'] ?? '');

        // VALIDATE NAME
        $nameError = $this->validateSubjectName($name);

        // OLD
        $_SESSION['flash_old'] = [
            'gradeId'    => $gradeId,
            'name'       => $name,
            'slug'       => $slugInp,
            'description'=> $desc,
            'detailDesc' => $detail,
        ];

        // EMPTY
        if (
            empty($name) ||
            empty($gradeId)
        ) {

            $_SESSION['flash_error'] =
                'Vui lòng nhập đầy đủ thông tin';

            header("Location: /admin/subjects/edit/$id");
            exit;
        }

        // NAME ERROR
        if ($nameError) {

            $_SESSION['flash_error'] = $nameError;

            header("Location: /admin/subjects/edit/$id");
            exit;
        }

        // SLUG
        $slug = empty($slugInp)
            ? $subjectModel->toSlug($name)
            : $subjectModel->toSlug($slugInp);

        // EXISTS
        if (
            $subjectModel->existsFullExcept(
                $name,
                $slug,
                $gradeId,
                $id
            )
        ) {

            $_SESSION['flash_error'] =
                'Tên hoặc slug môn học đã tồn tại trong khối lớp';

            header("Location: /admin/subjects/edit/$id");
            exit;
        }

        $data = [
            'subjectName' => $name,
            'slug'        => $slug,
            'gradeId'     => $gradeId,
            'description' => $desc,
            'detailDesc'  => $detail,
        ];

        // IMAGE
        if (!empty($_FILES['image']['name'])) {

            $image = time() . '_' .
                basename($_FILES['image']['name']);

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                ROOT . "/public/images/subjects/" . $image
            );

            $data['image'] = $image;
        }

        $subjectModel->update($id, $data);

        unset($_SESSION['flash_old']);

        header("Location: /admin/subjects?success=updated");
        exit;
    }

    // =========================================================
    // DELETE
    // =========================================================
    public function delete($id)
    {
        $subjectModel = new Subject();

        if ($subjectModel->hasChapters($id)) {
            header("Location: /admin/subjects?error=has_chapters");
            exit;
        }

        if ($subjectModel->hasExams($id)) {
            header("Location: /admin/subjects?error=has_exams");
            exit;
        }

        $subjectModel->delete($id);

        header("Location: /admin/subjects?success=deleted");
        exit;
    }

    
}