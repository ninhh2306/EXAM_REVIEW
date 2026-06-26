<?php

require_once ROOT . '/app/models/User.php';
require_once ROOT . '/app/helpers/MailHelper.php';

class AuthController extends Controller
{

// =========================================================
// VALIDATE FULL NAME
// =========================================================
    private function validateFullName(&$name)
    {
        // Trim đầu cuối
        $name = trim($name);

        // Gộp nhiều khoảng trắng
        $name = preg_replace('/\s+/u', ' ', $name);

        // Empty
        if ($name === '') {
            return 'Họ tên không được để trống';
        }

        // Length
        if (mb_strlen($name) < 2) {
            return 'Họ tên phải có ít nhất 2 ký tự';
        }

        if (mb_strlen($name) > 50) {
            return 'Họ tên không được vượt quá 50 ký tự';
        }

        // Ký tự hợp lệ
        if (!preg_match("/^[\p{L}\s'-]+$/u", $name)) {
            return 'Họ tên chứa ký tự không hợp lệ';
        }

        return null;
    }


        public function showRegister()
    {
        $error = $_SESSION['error'] ?? null;
        $old   = $_SESSION['old'] ?? [];

        unset(
            $_SESSION['error'],
            $_SESSION['old']
        );

        require ROOT . '/views/user/auth/register.php';
    }

    public function register()
    {
        $name     = trim($_POST['name']             ?? '');
        $email    = trim($_POST['email']            ?? '');
        $password =      $_POST['password']         ?? '';
        $confirm  =      $_POST['confirm_password'] ?? '';

        // Normalize + validate name
        $nameError = $this->validateFullName($name);

        // OLD INPUT
        $_SESSION['old'] = [
            'name'  => $name,
            'email' => $email,
        ];

        // Empty
        if (
            empty($name) ||
            empty($email) ||
            empty($password)
        ) {

            $_SESSION['error'] =
                "Vui lòng nhập đầy đủ thông tin";

            header("Location: /register");
            exit;
        }

        // NAME ERROR
        if ($nameError) {

            $_SESSION['error'] = $nameError;

            header("Location: /register");
            exit;
        }

        // EMAIL
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $_SESSION['error'] =
                "Email không hợp lệ";

            header("Location: /register");
            exit;
        }

        // PASSWORD LENGTH
        if (strlen($password) < 6) {

            $_SESSION['error'] =
                "Mật khẩu phải có ít nhất 6 ký tự";

            header("Location: /register");
            exit;
        }

        // CONFIRM PASSWORD
        if ($password !== $confirm) {

            $_SESSION['error'] =
                "Mật khẩu xác nhận không khớp";

            header("Location: /register");
            exit;
        }

        $userModel = new User();

        // EMAIL EXISTS
        if ($userModel->findByEmail($email)) {

            $_SESSION['error'] =
                "Email này đã được đăng ký";

            header("Location: /register");
            exit;
        }

        // HASH PASSWORD
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        // CREATE USER
        $userModel->create(
            $name,
            $email,
            $hashedPassword
        );

        // CLEAR OLD
        unset($_SESSION['old']);

        $_SESSION['success'] =
            "Đăng ký thành công! Chào mừng bạn đến với Vui Luyện Thi, hãy đăng nhập để bắt đầu nhé.";

        header("Location: /login");
        exit;
    }


    public function showLogin()
    {
        $error   = $_SESSION['error'] ?? null;
        $success = $_SESSION['success'] ?? null;

        unset(
            $_SESSION['error'],
            $_SESSION['success']
        );

        require ROOT . '/views/user/auth/login.php';
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
        if (isset($user['status']) && (int)$user['status'] !== 1) {
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


    // ========================================
    // SEND RESET OTP
    // ========================================
    public function sendResetCode()
    {
        header('Content-Type: application/json');

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {

            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng nhập email'
            ]);
            exit;
        }

        $userModel = new User();

        $user = $userModel->findByEmail($email);

        if (!$user) {

            echo json_encode([
                'success' => false,
                'message' => 'Email không tồn tại'
            ]);
            exit;
        }

        // =========================
        // LIMIT 60s
        // =========================

        if (
            isset($_SESSION['reset_last_send']) &&
            (time() - $_SESSION['reset_last_send']) < 60
        ) {

            $remain =
                60 - (time() - $_SESSION['reset_last_send']);

            echo json_encode([
                'success' => false,
                'message' =>
                    "Vui lòng đợi {$remain}s để gửi lại mã"
            ]);
            exit;
        }

        // OTP
        $code = random_int(100000, 999999);

        $_SESSION['reset_email']      = $email;
        $_SESSION['reset_code']       = (string)$code;
        $_SESSION['reset_expire']     = time() + 300; // 5 phút
        $_SESSION['reset_attempts']   = 0;
        $_SESSION['reset_last_send']  = time();

        // =========================
        // SEND MAIL
        // =========================

        $subject = 'Mã OTP đặt lại mật khẩu';

        $message = "
        <div style='font-family:Arial;padding:20px'>

            <h2 style='color:#2563eb'>
                Vui Luyện Thi
            </h2>

            <p>Xin chào,</p>

            <p>
                Đây là mã OTP để đặt lại mật khẩu:
            </p>

            <div style='
                font-size:32px;
                font-weight:bold;
                letter-spacing:6px;
                color:#111827;
                margin:20px 0;
            '>
                {$code}
            </div>

            <p>
                Mã có hiệu lực trong
                <b>5 phút</b>.
            </p>

            <p>
                Nếu không phải bạn yêu cầu,
                hãy bỏ qua email này.
            </p>

        </div>
        ";

        $sent = MailHelper::send(
            $email,
            $subject,
            $message
        );

        if (!$sent) {

            echo json_encode([
                'success' => false,
                'message' => 'Không gửi được email OTP'
            ]);

            exit;
        }

        echo json_encode([
            'success' => true
        ]);
    }


    // ========================================
    // VERIFY RESET OTP
    // ========================================
    public function verifyResetCode()
    {
        header('Content-Type: application/json');

        $code = trim($_POST['code'] ?? '');

        // chưa gửi
        if (empty($_SESSION['reset_code'])) {

            echo json_encode([
                'success' => false,
                'message' => 'Vui lòng gửi mã OTP'
            ]);
            exit;
        }

        // hết hạn
        if (time() > $_SESSION['reset_expire']) {

            unset(
                $_SESSION['reset_code']
            );

            echo json_encode([
                'success' => false,
                'message' =>
                    'Mã OTP đã hết hạn'
            ]);
            exit;
        }

        // quá số lần nhập
        if (
            ($_SESSION['reset_attempts'] ?? 0) >= 5
        ) {

            unset(
                $_SESSION['reset_code']
            );

            echo json_encode([
                'success' => false,
                'message' =>
                    'Bạn đã nhập sai quá 5 lần. Vui lòng gửi lại mã mới.'
            ]);
            exit;
        }

        // sai otp
        if ($code !== $_SESSION['reset_code']) {

            $_SESSION['reset_attempts']++;

            $remain =
                5 - $_SESSION['reset_attempts'];

            echo json_encode([
                'success' => false,
                'message' =>
                    "Mã OTP không đúng. Còn {$remain} lần thử."
            ]);
            exit;
        }

        // success
        $_SESSION['reset_verified'] = true;

        echo json_encode([
            'success' => true
        ]);
    }


    // ========================================
    // RESET PASSWORD
    // ========================================
    public function resetPassword()
    {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['reset_verified'])) {

            echo json_encode([
                'success' => false,
                'message' => 'OTP chưa xác thực'
            ]);
            exit;
        }

        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if (strlen($password) < 6) {

            echo json_encode([
                'success' => false,
                'message' =>
                    'Mật khẩu phải tối thiểu 6 ký tự'
            ]);
            exit;
        }

        if ($password !== $confirm) {

            echo json_encode([
                'success' => false,
                'message' =>
                    'Mật khẩu xác nhận không khớp'
            ]);
            exit;
        }

        $hash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $userModel = new User();

        $rowsUpdated = $userModel->updatePasswordByEmail(
            $_SESSION['reset_email'],
            $hash
        );

        if (!$rowsUpdated) {
            echo json_encode([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản để cập nhật. Email: ' . $_SESSION['reset_email']
            ]);
            exit;
        }

        // clear
        unset(
            $_SESSION['reset_email'],
            $_SESSION['reset_code'],
            $_SESSION['reset_expire'],
            $_SESSION['reset_attempts'],
            $_SESSION['reset_last_send'],
            $_SESSION['reset_verified']
        );

        echo json_encode([
            'success' => true
        ]);
    }


}