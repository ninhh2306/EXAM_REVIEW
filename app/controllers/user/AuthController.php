<?php

require_once ROOT . '/app/models/User.php';

class AuthController extends Controller
{
    public function showLogin()
    {
        require_once ROOT . '/views/user/auth/login.php';
    }

    public function login()
    {
        
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
            header("Location: /login");
            exit;
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $_SESSION['error'] = "Email không tồn tại";
            header("Location: /login");
            exit;
        }

        // Kiểm tra status active
        if (isset($user['status']) && $user['status'] !== 'active') {
            $_SESSION['error'] = "Tài khoản đã bị khóa";
            header("Location: /login");
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['error'] = "Sai mật khẩu";
            header("Location: /login");
            exit;
        }

        // Tên cột đúng theo DB: userId, fullName, email, role
        $_SESSION['user_id']    = $user['userId'];
        $_SESSION['user_name']  = $user['fullName'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? null;
        $_SESSION['user_role']  = $user['role'];

        if ($user['role'] === 'admin') {
            header("Location: /admin");
        } else {
            header("Location: /");
        }
        exit;
    }

    public function logout()
    {
    // session_start();

    $_SESSION = [];

    session_unset();
    session_destroy();

    header("Location: /login");
    exit;

    }

    public function showRegister()
    {
        require_once ROOT . '/views/user/auth/register.php';
    }

    public function register()
    {
        $name     = trim($_POST['name']             ?? '');
        $email    = trim($_POST['email']            ?? '');
        $password =      $_POST['password']         ?? '';
        $confirm  =      $_POST['confirm_password'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin";
            header("Location: /register");
            exit;
        }

        if ($password !== $confirm) {
            $_SESSION['error'] = "Mật khẩu không khớp";
            header("Location: /register");
            exit;
        }

        $userModel = new User();

        if ($userModel->findByEmail($email)) {
            $_SESSION['error'] = "Email này đã được đăng ký";
            header("Location: /register");
            exit;
        }

        // Luôn hash password trước khi lưu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userModel->create($name, $email, $hashedPassword);

        $_SESSION['success'] = "Đăng ký thành công! Vui lòng đăng nhập.";
        header("Location: /login");
        exit;
    }
}