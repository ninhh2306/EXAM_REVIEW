<?php

require_once ROOT . '/app/models/Subject.php';
require_once ROOT . '/app/models/Grade.php';

class SubjectController extends Controller
{
    public function index()
    {
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $allSubjects = $subjectModel->getAll();
        $grades      = $gradeModel->getAll();

        // --- Phân trang tab ---
        $perTab      = 5;
        $total       = count($allSubjects);
        $tabCount    = max(1, (int) ceil($total / $perTab));
        $currentTab  = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));
        $offset      = ($currentTab - 1) * $perTab;
        $subjects    = array_slice($allSubjects, $offset, $perTab); // chỉ 10 item

        $this->viewAdmin("subjects/index", [
            "title"      => "Quản lý Môn học",
            "subjects"   => $subjects,      // đã cắt sẵn
            "grades"     => $grades,
            "tabCount"   => $tabCount,
            "currentTab" => $currentTab,
        ]);
    }


    public function store()
    {
        $name    = trim($_POST['name']);
        $slugInp = trim($_POST['slug']);
        $gradeId = $_POST['gradeId'];

        $subjectModel = new Subject();

        // SLUG
        $slug = empty($slugInp)
            ? $subjectModel->toSlug($name)
            : $subjectModel->toSlug($slugInp);

        // ❌ CHECK TRÙNG (NAME + SLUG)
        if ($subjectModel->existsFull($name, $slug, $gradeId)) {
            header("Location: /admin/subjects/create?error=exists");
            return;
        }

        // IMAGE
        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                ROOT . "/public/images/subjects/" . $image
            );
        }

        // TEXT
        $desc   = $_POST['description'] ?? null;
        $detail = $_POST['detailDesc'] ?? null;

        $subjectModel->create($name, $slug, $gradeId, $image, $desc, $detail);

        header("Location: /admin/subjects?success=created");
    }


    public function create()
    {
        $gradeModel = new Grade();
        $grades = $gradeModel->getAll();

        $this->viewAdmin("subjects/create", [
            "title" => "Thêm môn học",
            "grades" => $grades
        ]);
    }


    public function edit($id)
    {
        $subjectModel = new Subject();
        $gradeModel   = new Grade();

        $subject = $subjectModel->find($id);
        $grades  = $gradeModel->getAll();

        return $this->viewAdmin('subjects/edit', [
            'subject' => $subject,
            'grades'  => $grades
        ]);
    }


    public function update()
    {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            header("Location: /admin/subjects");
            return;
        }

        $subjectModel = new Subject();

        $name    = trim($_POST['name']);
        $slugInp = trim($_POST['slug']);
        $gradeId = $_POST['gradeId'];

        // slug auto
        $slug = empty($slugInp)
            ? $subjectModel->toSlug($name)
            : $subjectModel->toSlug($slugInp);

        // ❌ CHECK TRÙNG
        if ($subjectModel->existsFullExcept($name, $slug, $gradeId, $id)) {
            header("Location: /admin/subjects/edit/$id?error=exists");
            return;
        }

        $data = [
            'subjectName' => $name,
            'slug'        => $slug,
            'gradeId'     => $gradeId,
            'description' => $_POST['description'],
            'detailDesc'  => $_POST['detailDesc'],
        ];

        // IMAGE
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                ROOT . "/public/images/subjects/" . $image
            );

            $data['image'] = $image;
        }

        $subjectModel->update($id, $data);

        header("Location: /admin/subjects?success=updated");
    }

    public function delete($id)
    {
        $subjectModel = new Subject();

        $result = $subjectModel->delete($id); // ✅ THIẾU DÒNG NÀY

        if ($result) {
            header("Location: /admin/subjects?success=deleted");
        } else {
            header("Location: /admin/subjects?error=delete_failed");
        }
        exit;
    }


    public function getByGrade()
    {
        $gradeId = $_GET['grade_id'] ?? 0;

        $model = new Subject();
        $data = $model->getByGrade($gradeId);

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }


    

    
}