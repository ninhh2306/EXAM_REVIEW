<?php

require_once ROOT . '/app/models/User.php';

class UserController extends Controller
{

    // =========================================================
    // VALIDATE FULL NAME
    // =========================================================
    private function validateFullName(&$name)
    {
        // Trim đầu cuối
        $name = trim($name);

        // Gộp nhiều khoảng trắng liên tiếp thành 1
        $name = preg_replace('/\s+/u', ' ', $name);

        // Empty
        if ($name === '') {
            return 'Tên không được để trống!';
        }

        // Độ dài
        if (mb_strlen($name) < 2) {
            return 'Tên phải có ít nhất 2 ký tự!';
        }

        if (mb_strlen($name) > 50) {
            return 'Tên không được vượt quá 50 ký tự!';
        }

        // Ký tự hợp lệ
        // Cho phép:
        // - chữ tiếng Việt
        // - khoảng trắng
        // - dấu '
        // - dấu -
        if (!preg_match("/^[\p{L}\s'-]+$/u", $name)) {
            return 'Tên chứa ký tự không hợp lệ!';
        }

        return null;
    }

    // =========================================================
    // INDEX
    // =========================================================
    public function index()
    {
        $userModel = new User();

        $allUsers = $userModel->getAll();

        $perTab     = 5;
        $total      = count($allUsers);
        $tabCount   = max(1, ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));

        $users = array_slice(
            $allUsers,
            ($currentTab - 1) * $perTab,
            $perTab
        );

        $this->viewAdmin('users/index', [
            'title'      => 'Quản lý Người dùng',
            'users'      => $users,
            'tabCount'   => $tabCount,
            'currentTab' => $currentTab,
        ]);
    }


    // =========================================================
    // CREATE
    // =========================================================
    public function create()
    {
        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_old']
        );

        $this->viewAdmin('users/create', [
            'title'      => 'Thêm Người dùng',
            'flashError' => $flashError,
            'flashOld'   => $flashOld,
        ]);
    }


    // =========================================================
    // STORE
    // =========================================================
    public function store()
    {
        $userModel = new User();

        $fullName = trim($_POST['fullName'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = $_POST['role'] ?? 'user';

        $status = isset($_POST['status']) ? 1 : 0;

        // VALIDATE + NORMALIZE NAME
        $nameError = $this->validateFullName($fullName);

        // OLD DATA
        $old = [
            'fullName' => $fullName,
            'email'    => $email,
            'role'     => $role,
            'status'   => $status,
        ];

        // NAME ERROR
        if ($nameError) {

            $_SESSION['flash_error'] = $nameError;
            $_SESSION['flash_old']   = $old;

            header('Location: /admin/users/create');
            exit;
        }

        // PASSWORD CHECK
        if (strlen($password) < 6) {

            $_SESSION['flash_error'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
            $_SESSION['flash_old']   = $old;

            header('Location: /admin/users/create');
            exit;
        }

        // EMAIL EXISTS
        if ($userModel->findByEmail($email)) {

            $_SESSION['flash_error'] = 'Email đã tồn tại!';
            $_SESSION['flash_old']   = $old;

            header('Location: /admin/users/create');
            exit;
        }

        // HASH PASSWORD
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // DEFAULT AVATAR
        $avatar = null;

        // UPLOAD AVATAR
        if (!empty($_FILES['avatar']['name'])) {

            $uploadDir = ROOT . '/public/images/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['avatar']['name']);

            move_uploaded_file(
                $_FILES['avatar']['tmp_name'],
                $uploadDir . $fileName
            );

            $avatar = '/images/avatars/' . $fileName;
        }

        // INSERT
        $sql = "
            INSERT INTO users (
                fullName,
                email,
                password,
                avatar,
                role,
                status
            )
            VALUES (
                :fullName,
                :email,
                :password,
                :avatar,
                :role,
                :status
            )
        ";

        $stmt = $userModel->conn->prepare($sql);

        $stmt->execute([
            'fullName' => $fullName,
            'email'    => $email,
            'password' => $hashedPassword,
            'avatar'   => $avatar,
            'role'     => $role,
            'status'   => $status,
        ]);

        header('Location: /admin/users?success=created');
        exit;
    }


    // =========================================================
    // EDIT
    // =========================================================
    public function edit($id)
    {
        $userModel = new User();

        $user = $userModel->find($id);

        if (!$user) {
            header('Location: /admin/users');
            exit;
        }

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_old']
        );

        // Nếu submit lỗi → ưu tiên flashOld
        $d = !empty($flashOld)
            ? array_merge($user, $flashOld)
            : $user;

        $this->viewAdmin('users/edit', [
            'title'      => 'Cập nhật Người dùng',
            'user'       => $d,
            'flashError' => $flashError,
        ]);
    }


    // =========================================================
    // UPDATE
    // =========================================================
    public function update()
    {
        $userModel = new User();

        $userId   = (int)($_POST['userId'] ?? 0);

        $fullName = trim($_POST['fullName'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role     = $_POST['role'] ?? 'user';

        $status = isset($_POST['status']) ? 1 : 0;

        $user = $userModel->find($userId);

        if (!$user) {
            header('Location: /admin/users');
            exit;
        }

        // VALIDATE + NORMALIZE NAME
        $nameError = $this->validateFullName($fullName);

        // OLD DATA
        $old = [
            'userId'    => $userId,
            'fullName'  => $fullName,
            'email'     => $email,
            'role'      => $role,
            'status'    => $status,
            'avatar'    => $user['avatar'],
            'createdAt' => $user['createdAt'],
            'updatedAt' => $user['updatedAt'],
        ];

        // NAME ERROR
        if ($nameError) {

            $_SESSION['flash_error'] = $nameError;
            $_SESSION['flash_old']   = $old;

            header('Location: /admin/users/edit/' . $userId);
            exit;
        }

        // EMAIL EXISTS
        if ($userModel->findByEmailExceptId($email, $userId)) {

            $_SESSION['flash_error'] = 'Email đã tồn tại!';
            $_SESSION['flash_old']   = $old;

            header('Location: /admin/users/edit/' . $userId);
            exit;
        }

        // PASSWORD CHECK
        if (!empty($password) && strlen($password) < 6) {

            $_SESSION['flash_error'] = 'Mật khẩu phải có ít nhất 6 ký tự!';
            $_SESSION['flash_old']   = $old;

            header('Location: /admin/users/edit/' . $userId);
            exit;
        }

        // AVATAR
        $avatar = $user['avatar'];

        if (!empty($_FILES['avatar']['name'])) {

            $uploadDir = ROOT . '/public/images/avatars/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = time() . '_' . basename($_FILES['avatar']['name']);

            move_uploaded_file(
                $_FILES['avatar']['tmp_name'],
                $uploadDir . $fileName
            );

            $avatar = '/images/avatars/' . $fileName;
        }

        // PASSWORD
        $hashedPassword = $user['password'];

        if (!empty($password)) {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );
        }

        // ======  CHECK HẠ QUYỀN ADMIN DUY NHẤT ===========
        if ($user['role'] === 'admin' && $role === 'user') {
            if ($userModel->countAdmins() <= 1) {
                $_SESSION['flash_error'] = 'Không thể hạ quyền Admin duy nhất của hệ thống!';
                $_SESSION['flash_old']   = $old;
                header('Location: /admin/users/edit/' . $userId);
                exit;
            }
        }

        // UPDATE
        $sql = "
            UPDATE users
            SET
                fullName = :fullName,
                email    = :email,
                password = :password,
                avatar   = :avatar,
                role     = :role,
                status   = :status
            WHERE userId = :id
        ";

        $stmt = $userModel->conn->prepare($sql);

        $stmt->execute([
            'fullName' => $fullName,
            'email'    => $email,
            'password' => $hashedPassword,
            'avatar'   => $avatar,
            'role'     => $role,
            'status'   => $status,
            'id'       => $userId,
        ]);

        header('Location: /admin/users?success=updated');
        exit;
    }


    // =========================================================
    // DELETE
    // =========================================================
    public function delete($id)
    {
        $userModel = new User();

        $user = $userModel->find($id);

        if (!$user) {

            header('Location: /admin/users?error=delete_failed');
            exit;
        }

        // ======= KHÔNG CHO XÓA TÀI KHOẢN ADMIN ================
        if ($user['role'] === 'admin') {
            $_SESSION['error'] = 'Không được phép xóa tài khoản Admin! Hãy hạ quyền xuống User trước.';
            header('Location: /admin/users');
            exit;
        }

        try {

            // XÓA AVATAR FILE
            if (!empty($user['avatar'])) {

                $filePath = ROOT . '/public' . $user['avatar'];

                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // XÓA USER
            $userModel->delete($id);

            header('Location: /admin/users?success=deleted');
            exit;

        } catch (Exception $e) {

            header('Location: /admin/users?error=delete_failed');
            exit;
        }
    }
}