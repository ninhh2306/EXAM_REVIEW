<?php

$base = "";
// session_start() PHẢI ở đây — trước khi xuất bất kỳ HTML nào
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect nếu đã đăng nhập
if (!empty($_SESSION['user_id'])) {
    header('Location: /user/home');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Đăng nhập - Vui Luyện Thi</title>
    <link rel="icon" href="<?= $base ?>/images/logo_transparent.png">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>


<div class="login-container">
 
    <!-- LEFT SIDE -->
    <div class="left">
        <div class="left-overlay">

            <div class="logo-container">
                <a href="/">
                    <img src="/images/logo.png" alt="Logo" class="main-logo">
                </a>
            </div>
 
            <!-- Giới thiệu login -->
            <div class="intro-section">
                <p class="intro-text">
                    Đắm mình trong không gian học tập tĩnh lặng<br>
                    Tận hưởng sự tập trung và khơi gợi cảm hứng mỗi ngày
                </p>

                <div class="image-login">
                    <img src="/images/login.jpg">
                </div>
            </div>
 
        </div>
    </div>
 
    <!-- RIGHT SIDE -->
    <div class="right">
        <div class="right-overlay">
            <div class="login-box">
    
                <h2 class="right-headline">Chào mừng bạn trở lại!</h2>
                <p class="sub-text">
                    Sẵn sàng bứt phá và chinh phục những mục tiêu mới
                </p>
    
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="error">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <?php unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
    
                <?php if (!empty($_SESSION['success'])): ?>
                    <div class="success-msg">
                        <?= htmlspecialchars($_SESSION['success']) ?>
                        <?php unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
    
                <form method="POST" action="/login" autocomplete="off">
    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="ten@vidu.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            autocomplete="off"
                            required
                        >
                    </div>
    
                    <div class="form-group">
                        <label>Mật khẩu</label>
    
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" autocomplete="new-password">
    
                            <i class="fa-solid fa-eye-slash toggle-password"
                            onclick="togglePassword('password', this)"></i>
                        </div>
                    </div>
    
                    <div class="form-options">
                        <label>
                            <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                        </label>
                        <a href="/forgot-password">Quên mật khẩu?</a>
                    </div>
    
                    
                    <button type="submit" class="btn-login">
                        Đăng nhập
                    </button>
    
                    <div class="divider">Hoặc đăng nhập với</div>
    
                    <div class="social-login">
                        <a href="#" class="btn-google" onclick="loginSocial('google')">
                            <i class="fa-brands fa-google"></i> Google
                        </a>
                        <a href="#" class="btn-facebook" onclick="loginSocial('facebook')">
                            <i class="fa-brands fa-facebook"></i> Facebook
                        </a>
                    </div>
                        
                    <p class="register">
                        Bạn chưa có tài khoản? <a href="/register">Đăng ký ngay</a>
                    </p>
    
                </form>
    
            </div>
        </div>
    </div>
 
</div>
 
 
<script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
 
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash"); 
        icon.classList.add("fa-eye");
        icon.classList.add("active");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
        icon.classList.remove("active");
    }
}

window.addEventListener("load", function () {
    const form = document.querySelector("form");
    if (form) form.reset();
});



function loginSocial(platform) {
    let url = '';
    
    if (platform === 'google') {
        // Đây là link dẫn đến trang chọn tài khoản của Google
        url = 'https://google.com';
    } else if (platform === 'facebook') {
        // Đây là link dẫn đến trang xác nhận của Facebook
        url = 'https://facebook.com';
    }
    
    const width = 500;
    const height = 600;
    const left = (window.innerWidth / 2) - (width / 2);
    const top = (window.innerHeight / 2) - (height / 2);

    window.open(
        url, 
        'LoginPopup', 
        `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,resizable=yes`
    );
    
    return false;
}




</script>
 
 
</body>
</html>
 