<?php

require_once ROOT . '/app/models/User.php';

class ProfileController extends Controller
{
    // ─────────────────────────────────────────
    // Middleware: yêu cầu đăng nhập
    // ─────────────────────────────────────────
    private function requireLogin()
    {
        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
    }

    // ─────────────────────────────────────────
    // Validate họ tên
    // ─────────────────────────────────────────
    public function validateFullName(&$name)
    {
        $name = trim($name);

        // Auto normalize spaces
        $name = preg_replace('/\s+/u', ' ', $name);

        if ($name === '') {
            return 'Họ tên không được để trống.';
        }

        if (mb_strlen($name) < 2) {
            return 'Họ tên phải có ít nhất 2 ký tự.';
        }

        if (mb_strlen($name) > 50) {
            return 'Họ tên không được vượt quá 50 ký tự.';
        }

        if (!preg_match("/^[\p{L}\s'-]+$/u", $name)) {
            return 'Họ tên chứa ký tự không hợp lệ.';
        }

        return null;
    }

    // ─────────────────────────────────────────
    // Trang profile
    // ─────────────────────────────────────────
    public function index()
    {
        $this->requireLogin();

        $userModel = new User();
        $user = $userModel->find($_SESSION['user_id']);

        if (!$user) {
            header("Location: /login");
            exit;
        }

        // Flash success
        $flashSuccess = $_SESSION['flash_success'] ?? null;

        // Flash error top page
        $flashError = $_SESSION['flash_error'] ?? null;

        // Errors từng form
        $profileErrors = $_SESSION['profile_errors'] ?? [];

        // Dữ liệu cũ user nhập
        $profileOld = $_SESSION['profile_old'] ?? [];

        // Form cần mở lại
        $openForm = $_SESSION['profile_open_form'] ?? null;

        $hasProfileError = !empty($profileErrors);

        // Clear session flash
        unset(
            $_SESSION['flash_success'],
            $_SESSION['flash_error'],
            $_SESSION['profile_errors'],
            $_SESSION['profile_old'],
            $_SESSION['profile_open_form']
        );

        $this->view('profile/index', [
            'user'             => $user,
            'flashSuccess'     => $flashSuccess,

            'profileErrors'    => $profileErrors,
            'profileOld'       => $profileOld,
            'openForm'         => $openForm,

            'hasProfileError'  => $hasProfileError,
        ]);
    }

    // ─────────────────────────────────────────
    // Cập nhật họ tên
    // ─────────────────────────────────────────
    public function updateName()
    {
        $this->requireLogin();

        $name = $_POST['fullName'] ?? '';

        // Chuẩn hóa khoảng trắng
        $name = preg_replace('/\s+/u', ' ', $name);
        $name = trim($name);

        // Validate
        $nameError = $this->validateFullName($name);

        if ($nameError) {

            $_SESSION['profile_errors'] = [
                'name' => $nameError
            ];

            $_SESSION['profile_old'] = [
                'fullName' => $name
            ];

            $_SESSION['profile_open_form'] = 'name';

            header("Location: /profile");
            exit;
        }

        $userModel = new User();

        $userModel->updateName(
            $_SESSION['user_id'],
            $name
        );

        // Update session
        $_SESSION['user_name'] = $name;

        $_SESSION['flash_success'] = 'Cập nhật họ tên thành công!';

        header("Location: /profile");
        exit;
    }

    // ─────────────────────────────────────────
    // Cập nhật email
    // ─────────────────────────────────────────
    public function updateEmail()
    {
        $this->requireLogin();

        $email = trim($_POST['email'] ?? '');

        if (
            empty($email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {

            $_SESSION['profile_errors'] = [
                'email' => 'Email không hợp lệ.'
            ];

            $_SESSION['profile_old'] = [
                'email' => $email
            ];

            $_SESSION['profile_open_form'] = 'email';

            header("Location: /profile");
            exit;
        }

        $userModel = new User();

        $existing = $userModel->findByEmail($email);

        if (
            $existing &&
            $existing['userId'] != $_SESSION['user_id']
        ) {

            $_SESSION['profile_errors'] = [
                'email' => 'Email này đã được sử dụng.'
            ];

            $_SESSION['profile_old'] = [
                'email' => $email
            ];

            $_SESSION['profile_open_form'] = 'email';

            header("Location: /profile");
            exit;
        }

        $userModel->updateEmail(
            $_SESSION['user_id'],
            $email
        );

        $_SESSION['user_email'] = $email;

        $_SESSION['flash_success'] = 'Cập nhật email thành công!';

        header("Location: /profile");
        exit;
    }

    // ─────────────────────────────────────────
    // Đổi mật khẩu
    // ─────────────────────────────────────────
    public function updatePassword()
    {
        $this->requireLogin();

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Empty
        if (
            empty($currentPassword) ||
            empty($newPassword) ||
            empty($confirmPassword)
        ) {

            $_SESSION['profile_errors'] = [
                'password' => 'Vui lòng nhập đầy đủ thông tin.'
            ];

            $_SESSION['profile_open_form'] = 'password';

            header("Location: /profile");
            exit;
        }

        // Không khớp
        if ($newPassword !== $confirmPassword) {

            $_SESSION['profile_errors'] = [
                'password' => 'Mật khẩu xác nhận không khớp.'
            ];

            $_SESSION['profile_open_form'] = 'password';

            header("Location: /profile");
            exit;
        }

        // Độ dài
        if (strlen($newPassword) < 6) {

            $_SESSION['profile_errors'] = [
                'password' => 'Mật khẩu mới phải có ít nhất 6 ký tự.'
            ];

            $_SESSION['profile_open_form'] = 'password';

            header("Location: /profile");
            exit;
        }

        $userModel = new User();

        $user = $userModel->find($_SESSION['user_id']);

        // Sai mật khẩu cũ
        if (!password_verify($currentPassword, $user['password'])) {

            $_SESSION['profile_errors'] = [
                'password' => 'Mật khẩu hiện tại không đúng.'
            ];

            $_SESSION['profile_open_form'] = 'password';

            header("Location: /profile");
            exit;
        }

        // Update password
        $userModel->updatePassword(
            $_SESSION['user_id'],
            password_hash($newPassword, PASSWORD_DEFAULT)
        );

        $_SESSION['flash_success'] = 'Đổi mật khẩu thành công!';

        header("Location: /profile");
        exit;
    }

    // ─────────────────────────────────────────
    // Upload avatar
    // ─────────────────────────────────────────
    public function updateAvatar()
    {
        $this->requireLogin();

        if (
            empty($_FILES['avatar']) ||
            $_FILES['avatar']['error'] !== UPLOAD_ERR_OK
        ) {

            $_SESSION['flash_error'] = 'Vui lòng chọn ảnh hợp lệ.';

            header("Location: /profile");
            exit;
        }

        $file = $_FILES['avatar'];

        $mimeType = mime_content_type($file['tmp_name']);

        $allowed = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif'
        ];

        if (!in_array($mimeType, $allowed)) {

            $_SESSION['flash_error'] =
                'Chỉ chấp nhận ảnh JPG, PNG, WEBP, GIF.';

            header("Location: /profile");
            exit;
        }

        // Max 2MB
        if ($file['size'] > 2 * 1024 * 1024) {

            $_SESSION['flash_error'] =
                'Ảnh không được vượt quá 2MB.';

            header("Location: /profile");
            exit;
        }

        // Tạo thư mục
        $uploadDir = ROOT . '/public/images/avatars/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $userModel = new User();

        $user = $userModel->find($_SESSION['user_id']);

        // Xóa avatar cũ
        if (!empty($user['avatar'])) {

            $oldFile = ROOT . '/public' . $user['avatar'];

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Lưu file mới
        $ext = pathinfo(
            $file['name'],
            PATHINFO_EXTENSION
        );

        $filename =
            'avatar_' .
            $_SESSION['user_id'] .
            '_' .
            time() .
            '.' .
            $ext;

        $destPath = $uploadDir . $filename;

        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destPath
            )
        ) {

            $_SESSION['flash_error'] =
                'Upload thất bại, vui lòng thử lại.';

            header("Location: /profile");
            exit;
        }

        $avatarPath = '/images/avatars/' . $filename;

        $userModel->updateAvatar(
            $_SESSION['user_id'],
            $avatarPath
        );

        $_SESSION['user_avatar'] = $avatarPath;

        $_SESSION['flash_success'] =
            'Cập nhật ảnh đại diện thành công!';

        header("Location: /profile");
        exit;
    }

    // ─────────────────────────────────────────
    // Xóa tài khoản
    // ─────────────────────────────────────────
    public function deleteAccount()
    {
        $this->requireLogin();

        $password = $_POST['confirm_delete_password'] ?? '';

        $userModel = new User();

        $user = $userModel->find($_SESSION['user_id']);

        // ==== KHÔNG CHO ADMIN TỰ XÓA TÀI KHOẢN ============
        if ($user['role'] === 'admin') {

            $_SESSION['flash_error'] =
                'Bạn không thể xóa tài khoản với vai trò Admin của mình.';

            header("Location: /profile");
            exit;
        }

        // Sai mật khẩu
        if (!password_verify($password, $user['password'])) {

            $_SESSION['flash_error'] =
                'Mật khẩu xác nhận không đúng.';

            header("Location: /profile");
            exit;
        }

        // Xóa DB
        $userModel->delete($_SESSION['user_id']);

        // Xóa avatar file
        if (!empty($user['avatar'])) {

            $oldFile = ROOT . '/public' . $user['avatar'];

            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Logout
        $_SESSION = [];

        session_unset();

        session_destroy();

        header("Location: /login");
        exit;
    }
}