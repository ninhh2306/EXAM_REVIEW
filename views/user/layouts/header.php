<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

$base = $base ?? '/EXAM_REVIEW';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Vui Luyện Thi') ?></title>
    <link rel="icon" href="<?= $base ?>/images/logo_transparent.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,800&display=swap" rel="stylesheet">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css">
    
    <link rel="stylesheet" href="<?= $base ?>/css/style.css">
    
</head>


<body>

<!-- =========================================
     NAVBAR
========================================= -->
<header class="navbar">
    <div class="container navbar__inner">

        <a href="<?= $base ?>/" class="navbar__logo">
            <img src="<?= $base ?>/images/logo.png" alt="Logo">
            <span class="navbar__logo-text"></span>
        </a>

        <!-- NAV -->
        <nav class="navbar__nav">

            <!-- KHỐI LỚP -->
            <div class="nav-item">
                <span>Khối lớp <i class="fa-solid fa-caret-down"></i></span>
                <div class="custom-nav-list">
                    <?php foreach ($grades ?? [] as $gradeItem): ?>
                        <a href="<?= $base ?>/<?= $gradeItem['slug'] ?>">
                            <?= htmlspecialchars($gradeItem['gradeName']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- THPT QUỐC GIA -->
            <div class="nav-item">
                <!-- CLICK → tất cả đề -->
                <span onclick="window.location='<?= $base ?>/thpt-quoc-gia'">
                    THPT Quốc Gia <i class="fa-solid fa-caret-down"></i>
                </span>

                <!-- DROPDOWN -->
                <div class="custom-nav-list">
                    <a href="<?= $base ?>/thpt-quoc-gia">
                        <strong>Tất cả đề</strong>
                    </a>

                    <div class="custom-nav-divider"></div>

                    <?php foreach ($subjects ?? [] as $subjectItem): ?>
                        <a href="<?= $base ?>/thpt-quoc-gia/<?= $subjectItem['slug'] ?>">
                            <?= htmlspecialchars($subjectItem['subjectName']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- TIN TỨC -->
            <div class="nav-item">
                <span onclick="window.location='<?= $base ?>/tin-tuc'">
                    Tin tức <i class="fa-solid fa-caret-down"></i>
                </span>
                
                <div class="custom-nav-list">
                    <?php foreach ($categories ?? [] as $cate): ?>
                        <a href="<?= $base ?>/tin-tuc/<?= $cate['slug'] ?>">
                            <?= htmlspecialchars($cate['categoryName']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </nav>


        <div class="navbar__right">

            <form class="navbar__search" action="<?= $base ?>/search" method="GET">
                <i class="fa-solid fa-magnifying-glass navbar__search-icon"></i>

                <input
                    type="text"
                    name="keyword"
                    placeholder="Tìm kiếm môn học, đề thi"
                >
            </form>

            <?php if (!empty($_SESSION['user_id'])): ?>


            <div class="user-dropdown">
                <div class="user-btn">
                    <?php
                        $avatar = $_SESSION['user_avatar'] ?? null;
                    ?>
                    <?php if ($avatar): ?>
                        <img src="<?= htmlspecialchars($avatar) ?>"
                            alt="avatar"
                            class="navbar-avatar">
                    <?php else: ?>
                        <div class="navbar-avatar navbar-avatar--default">
                            <?= mb_strtoupper(mb_substr($_SESSION['user_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <?= htmlspecialchars($_SESSION['user_name']) ?>

                    <i class="fa-solid fa-caret-down"></i>
                </div>

                <div class="dropdown-menu">

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>

                        <a href="/admin/dashboard">Dashboard</a>

                        <a href="<?= $base ?>/profile">Quản lý tài khoản</a>

                    <?php else: ?>

                        <a href="<?= $base ?>/lich-su-lam-bai">Lịch sử làm bài</a>
                        <a href="<?= $base ?>/exams">Danh sách đề thi</a>
                        <a href="<?= $base ?>/profile">Quản lý tài khoản</a>

                    <?php endif; ?>

                    <div class="dropdown-divider"></div>
                    <a href="<?= $base ?>/logout" class="logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất
                    </a>

                </div>
            </div>

            <?php else: ?>

                <a href="<?= $base ?>/login" class="btn btn-primary">
                    <i class="fa-solid fa-user"></i> Đăng nhập
                </a>

            <?php endif; ?>

        </div>

    </div>


</header>


<script>

document.addEventListener("DOMContentLoaded", function () {
    const btn = document.querySelector(".user-btn");
    const menu = document.querySelector(".dropdown-menu");

    if (!btn || !menu) return;

    btn.addEventListener("click", function (e) {
        e.stopPropagation();
        menu.classList.toggle("open");
    });

    document.addEventListener("click", function () {
        menu.classList.remove("open");
    });
});



</script>