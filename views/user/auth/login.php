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
    
                <?php if (!empty($success)): ?>
                    <div class="alert-success">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert-error">
                        <?= htmlspecialchars($error) ?>
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

                        <a href="javascript:void(0)"
                            onclick="openForgotModal()">
                            Quên mật khẩu?
                        </a>
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
 

<!-- =======================================
FORGOT PASSWORD MODAL
======================================= -->

<div class="forgot-modal" id="forgotModal">

    <div class="forgot-modal-box">

        <div class="forgot-header">
            Lấy lại mật khẩu
        </div>

        <div id="forgotAlert"></div>

        <!-- STEP 1 -->
        <div id="forgotStep1">
            <div class="form-group">
                <label>Email</label>
                <input type="email" id="forgotEmail" placeholder="Nhập email của bạn">
            </div>

            <div class="form-group">
                <label>Mã xác nhận</label>
                <input type="text" id="forgotCode" placeholder="Nhập mã OTP">
            </div>

            <div class="forgot-actions">
                <button class="btn-login"
                        id="sendOtpBtn"
                        onclick="sendResetCode()">
                    Gửi mã
                </button>
                <button class="btn-login" onclick="verifyResetCode()">Xác nhận OTP</button>
                <button class="btn-cancel-forgot" onclick="closeForgotModal()">Hủy</button>
            </div>
        </div>


        <!-- STEP 2 -->
        <div id="forgotStep2" style="display:none"> 

            <div class="form-group">
                <label>Mật khẩu mới</label>
                <div class="password-wrapper">
                    <input type="password" id="newPassword" autocomplete="new-password" placeholder="Nhập mật khẩu mới">
                    <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('newPassword', this)"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <div class="password-wrapper">
                    <input type="password" id="confirmPassword" autocomplete="new-password" placeholder="Nhập lại mật khẩu mới">
                    <i class="fa-solid fa-eye-slash toggle-password" onclick="togglePassword('confirmPassword', this)"></i>
                </div>
            </div>

            
            <div class="forgot-actions">
                <button class="btn-login" onclick="resetPassword()">
                    Đổi mật khẩu
                </button>
                <button class="btn-cancel-forgot" type="button" onclick="closeForgotModal()">
                    Hủy
                </button>
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


const alertBox = document.querySelector('.alert-error');

if (alertBox) {
    setTimeout(() => {
        alertBox.style.opacity = '0';
        alertBox.style.transition = '0.3s';

        setTimeout(() => {
            alertBox.remove();
        }, 300);

    }, 4000);
}



// ======================================
// FORGOT PASSWORD
// ======================================

function openForgotModal() {
    document.getElementById('forgotModal')
        .classList.add('active');
}

function closeForgotModal() {

    document.getElementById('forgotModal')
        .classList.remove('active');

    document.getElementById('forgotStep1')
        .style.display = 'block';

    document.getElementById('forgotStep2')
        .style.display = 'none';

    document.getElementById('forgotEmail').value = '';
    document.getElementById('forgotCode').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';

    document.getElementById('forgotAlert')
        .innerHTML = '';
}

function showForgotAlert(msg, type='error') {

    const box =
        document.getElementById('forgotAlert');

    box.innerHTML = `
        <div class="alert-${type} forgot-alert-box">
            ${msg}
        </div>
    `;

    setTimeout(() => {

        const alert =
            box.querySelector('.forgot-alert-box');

        if (alert) {

            alert.style.opacity = '0';

            setTimeout(() => {
                box.innerHTML = '';
            }, 300);
        }

    }, 2500);
}


document.getElementById('forgotCode')
    .addEventListener('keypress', function(e) {

    if (e.key === 'Enter') {
        verifyResetCode();
    }
});



// SEND OTP
let otpCooldown = 0;
let otpTimer = null;

function startOtpCooldown() {

    const btn =
        document.getElementById('sendOtpBtn');

    if (!btn) return;

    otpCooldown = 60;

    btn.disabled = true;

    btn.innerText =
        `Gửi lại (${otpCooldown}s)`;

    clearInterval(otpTimer);

    otpTimer = setInterval(() => {

        otpCooldown--;

        btn.innerText =
            `Gửi lại (${otpCooldown}s)`;

        if (otpCooldown <= 0) {

            clearInterval(otpTimer);

            btn.disabled = false;

            btn.innerText = 'Gửi mã';
        }

    }, 1000);
}



async function sendResetCode() {

    try {

        const email =
            document.getElementById('forgotEmail').value;

        const formData = new FormData();

        formData.append('email', email);

        const res = await fetch('/forgot-password/send', {
            method:'POST',
            body:formData
        });

        const text = await res.text();

        console.log(text);

        const data = JSON.parse(text);

        if (data.success) {

            startOtpCooldown();

            showForgotAlert(
                'Mã OTP đã được gửi về email',
                'success'
            );

        } else {

            showForgotAlert(data.message);
        }

    } catch (err) {

        console.log(err);

        showForgotAlert(
            'Không thể gửi mã OTP'
        );
    }
}



// VERIFY OTP
async function verifyResetCode() {

    const code =
        document.getElementById('forgotCode')
        .value
        .trim();

    if (!code) {

        showForgotAlert(
            'Vui lòng nhập mã OTP'
        );

        return;
    }

    const formData = new FormData();

    formData.append('code', code);

    try {

        const res = await fetch(
            '/forgot-password/verify',
            {
                method:'POST',
                body:formData
            }
        );

        const text = await res.text();
        console.log(text);
        const data = JSON.parse(text);

        if (data.success) {

            document.getElementById('forgotStep1')
                .style.display = 'none';

            document.getElementById('forgotStep2')
                .style.display = 'block';

            showForgotAlert(
                'Xác thực OTP thành công',
                'success'
            );

        } else {

            showForgotAlert(data.message);
        }

    } catch (e) {

        showForgotAlert(
            'Có lỗi xảy ra'
        );
    }
}


// RESET PASSWORD
async function resetPassword() {

    const password =
        document.getElementById('newPassword')
        .value;

    const confirm =
        document.getElementById('confirmPassword')
        .value;

    if (!password || !confirm) {

        showForgotAlert(
            'Vui lòng nhập đầy đủ mật khẩu'
        );

        return;
    }

    const formData = new FormData();

    formData.append('password', password);
    formData.append('confirm', confirm);

    try {

        const res = await fetch(
            '/forgot-password/reset',
            {
                method:'POST',
                body:formData
            }
        );

        const text = await res.text();
        console.log(text);
        const data = JSON.parse(text);

        if (data.success) {

            showForgotAlert(
                'Đổi mật khẩu thành công',
                'success'
            );

            setTimeout(() => {
                location.reload();
            }, 1500);

        } else {

            showForgotAlert(data.message);
        }

    } catch (e) {

        showForgotAlert(
            'Có lỗi xảy ra'
        );
    }
}


</script>
 
 
</body>
</html>
 