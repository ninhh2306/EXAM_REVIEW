<?php

$base = "";
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Vui Luyện Thi</title>
    <link rel="icon" href="<?= $base ?>/images/logo_transparent.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="register-container">

    <!-- LEFT -->
    <div class="register-left">
        <div class="overlay">

            <div class="logo-container">
                <a href="/">
                    <img src="/images/logo.png" alt="Logo" class="main-logo">
                </a>
            </div>

            <h2 class="left-headline">
                Khám phá tiềm năng tri thức vô hạn<br>
                Khẳng định bản thân qua mỗi bước chân học tập
            </h2>

            <div class="intro-card">
                <div class="re-benefit-item">
                    <div class="re-benefit-icon">📚</div>
                    <div class="re-benefit-text">
                        <strong>Kho tài liệu khổng lồ:</strong>
                        <span>Truy cập kho 100.000+ câu hỏi trắc nghiệm.</span>
                        
                    </div>
                </div>
                <div class="re-benefit-item">
                    <div class="re-benefit-icon">📝</div>
                    <div class="re-benefit-text">
                        <strong>Thi thử không giới hạn:</strong>
                        <span>Miễn phí 100% các đề thi thử bám sát cấu trúc đề minh họa của Bộ GD&ĐT.</span>
                    </div>
                </div>
                <div class="re-benefit-item">
                    <div class="re-benefit-icon">💻</div>
                    <div class="re-benefit-text">
                        <strong>Luyện đề thông minh:</strong>
                        <span>Hệ thống tự động chấm điểm và chỉ ra lỗi sai ngay lập tức.</span>
                    </div>
                </div>
                <div class="re-benefit-item">
                    <div class="re-benefit-icon">📈</div>
                    <div class="re-benefit-text">
                        <strong>Báo cáo học tập:</strong>
                        <span>Theo dõi tiến độ học tập theo thời gian thực.</span>
                    </div>
                </div>

                <p class="community">Bạn chỉ còn cách mục tiêu 1 bước đăng ký!</p>

            </div>

        </div>
    </div>

    <!-- RIGHT -->
    <div class="register-right">
        <div class="register-box">

            <h2 class="right-headline">Bắt đầu hành trình của bạn</h2>

            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/register" autocomplete="off">

                <div class="form-group">
                    <label>Họ tên</label>
                    <input type="text"
                            name="name"
                            placeholder="Nguyễn Văn A"
                            value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                            required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email"
                            name="email"
                            placeholder="example@gmail.com"
                            autocomplete="new-password"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                            required>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>

                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" autocomplete="new-password" required> 

                        <i class="fa-solid fa-eye-slash toggle-password"
                        onclick="togglePassword('password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Xác nhận</label>

                    <div class="password-wrapper">
                        <input type="password"
                                id="confirm_password"
                                name="confirm_password"
                                required>

                        <i class="fa-solid fa-eye-slash toggle-password"
                        onclick="togglePassword('confirm_password', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    Đăng ký ngay
                </button>

                <div class="divider">HOẶC</div>

                 <button type="button" class="btn-google" onclick="loginSocial('google')">
                    <i class="fa-brands fa-google"></i> Tiếp tục với Google
                </button>

                <p class="login-link">
                    Bạn đã có tài khoản?
                    <a href="/login">Đăng nhập ngay</a>
                </p>

            </form>

        </div>
    </div>

</div>



<script>

function loginSocial(platform) {
    let url = '';
 
    if (platform === 'google') {
        url = 'https://google.com';
    }
 
    const width = 500;
    const height = 600;
    const left = (window.innerWidth / 2) - (width / 2);
    const top  = (window.innerHeight / 2) - (height / 2);
 
    window.open(
        url,
        'LoginPopup',
        `width=${width},height=${height},top=${top},left=${left},scrollbars=yes,resizable=yes`
    );
 
    return false;
}


function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);

    // Nếu đang là password (đang ẩn) -> Chuyển sang text (hiện)
    if (input.type === "password") {
        input.type = "text";
        
        // Hiện mắt mở (để báo hiệu đang xem)
        icon.classList.remove("fa-eye-slash"); 
        icon.classList.add("fa-eye");
        icon.classList.add("active");

    } else {
        // Nếu đang hiện -> Chuyển về ẩn
        input.type = "password";

        // Hiện mắt đóng/gạch chéo
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
        icon.classList.remove("active");
    }
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


</script>

</body>
</html>