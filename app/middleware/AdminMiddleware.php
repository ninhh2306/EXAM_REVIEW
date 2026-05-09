<?php

class AdminMiddleware
{
    public static function handle()
    {
        // KHÔNG gọi session_start() ở đây nữa
        // vì index.php đã start rồi

        // Chưa đăng nhập → về trang login chung
        if (empty($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        // Đã đăng nhập nhưng không phải admin
        if ($_SESSION['user_role'] !== 'admin') {
            header("Location: /");
            exit;
        }
    }
}