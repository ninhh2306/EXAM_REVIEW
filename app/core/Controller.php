<?php

class Controller
{
    // ================= USER =================
    public function view($view, $data = [])
    {
        $data['base'] = APP_URL;

        // ===== GLOBAL DATA CHO HEADER =====
        require_once ROOT . "/app/models/Grade.php";
        require_once ROOT . "/app/models/Subject.php";
        require_once ROOT . "/app/models/Category.php";

        $gradeModel = new Grade();
        $subjectModel = new Subject();
        $categoryModel = new Category();

        
        // Chỉ set nếu chưa có — tránh ghi đè data từ controller con
        if (!isset($data['grades'])) {
            $data['grades'] = $gradeModel->getAllAsc();
        }

        // subjects thường
        if (!isset($data['subjects'])) {
            $data['subjects'] = $subjectModel->getAll();
        }

        // subjects riêng cho THPT
        if (!isset($data['thptSubjects'])) {
           $grade12 = $gradeModel->getBySlug('lop-12');
            $data['thptSubjects'] = $grade12
                ? $subjectModel->getByGrade($grade12['gradeId'])
                : [];
        }

        if (!isset($data['categories'])) {
            $data['categories'] =
                $categoryModel->getAll();
        }

        extract($data);

        $viewPath   = ROOT . "/views/user/$view.php";
        $headerPath = ROOT . "/views/user/layouts/header.php";
        $footerPath = ROOT . "/views/user/layouts/footer.php";

        if (!file_exists($viewPath)) {
            $viewPath = ROOT . "/views/user/errors/404.php";
        }

        require_once $headerPath;
        require_once $viewPath;
        require_once $footerPath;
    }



    // ================= ADMIN =================
    public function viewAdmin($view, $data = [])
    {
        $data['base'] = APP_URL;
        $data['viewPath'] = ROOT . "/views/admin/$view.php";

        if (!file_exists($data['viewPath'])) {
            $data['viewPath'] = ROOT . "/views/user/errors/404.php";
        }

        extract($data);

        require_once ROOT . "/views/admin/layouts/main.php";
    }
}