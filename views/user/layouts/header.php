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


    <link href="googleapis.com" rel="stylesheet">
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

                <span onclick="window.location='<?= $base ?>/thpt-quoc-gia'">
                    THPT Quốc Gia <i class="fa-solid fa-caret-down"></i>
                </span>

                <div class="custom-nav-list">

                    <a href="<?= $base ?>/thpt-quoc-gia">
                        <strong>Tất cả đề</strong>
                    </a>

                    <div class="custom-nav-divider"></div>

                    <?php foreach ($thptSubjects ?? [] as $subjectItem): ?>

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

            <div class="navbar__search">

                <i class="fa-solid fa-magnifying-glass navbar__search-icon"></i>

                <input
                    type="text"
                    id="navbarSearch"
                    placeholder="Tìm kiếm môn học, đề thi..."
                    autocomplete="off">

                <div class="search-dropdown" id="searchDropdown"></div>

            </div>

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
                        <div class="navbar-avatar default-avatar default-avatar--md">
                            <?= mb_strtoupper(mb_substr($_SESSION['user_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <?= htmlspecialchars($_SESSION['user_name']) ?>

                    <i class="fa-solid fa-caret-down"></i>
                </div>

                <div class="dropdown-menu">

                    <?php if ($_SESSION['user_role'] === 'admin'): ?>

                        <a href="/admin/dashboard">Dashboard</a>
                        <a href="<?= $base ?>/history">Lịch sử làm bài</a>
                        <a href="<?= $base ?>/progress">Tiến độ học tập</a>
                        <a href="<?= $base ?>/profile">Quản lý tài khoản</a>

                    <?php else: ?>
                        <a href="<?= $base ?>/history">Lịch sử làm bài</a>
                        <a href="<?= $base ?>/progress">Tiến độ học tập</a>
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



<?php if (!empty($_GET['error'])): ?>
<?php
    $errorMessages = [
        'exam_deleted' => 'Đề thi này đã bị xóa khỏi hệ thống. Kết quả của bạn không được lưu!.',
    ];
    $errorMsg = $errorMessages[htmlspecialchars($_GET['error'])] ?? null;
?>
<?php if ($errorMsg): ?>
<div id="flashToast" class="flash-toast flash-toast--error">
    <i class="fa-solid fa-circle-exclamation"></i>
    <?= $errorMsg ?>
</div>


<script>
    setTimeout(function () {
        const toast = document.getElementById('flashToast');
        if (toast) {
            toast.classList.add('flash-toast--hide');
            setTimeout(() => toast.remove(), 400);
        }
    }, 4000);
</script>
<?php endif; ?>
<?php endif; ?>

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



// =============== SEARCH ==================
const input = document.getElementById('navbarSearch');
const dropdown = document.getElementById('searchDropdown');

if (input) {

    input.addEventListener('input', async function () {

        const keyword = this.value.trim();

        if (keyword.length < 2) {
            dropdown.innerHTML = '';
            dropdown.style.display = 'none';
            return;
        }

        const response = await fetch(
            `<?= $base ?>/ajax/search?keyword=` + encodeURIComponent(keyword)
        );

        const data = await response.json();

        let html = '';

        // SUBJECTS
        if (data.subjects.length) {

            html += `<div class="search-group-title">Môn học</div>`;

            data.subjects.forEach(item => {

                html += `
                    <a class="search-result-item"
                    href="<?= $base ?>/${item.gradeSlug}/${item.slug}">
                        📘 ${item.subjectName} - ${item.gradeName}
                    </a>
                `;
            });
        }

        // LESSONS
        if (data.lessons.length) {

            html += `<div class="search-group-title">Bài học</div>`;

            data.lessons.forEach(item => {

                html += `
                    <a class="search-result-item"
                       href="<?= $base ?>/${item.gradeSlug}/${item.subjectSlug}/ly-thuyet/${item.chapterSlug}/${item.slug}">
                        📖 ${item.lessonName} - ${item.subjectName} - ${item.gradeName}
                    </a>
                `;
            });
        }

        // EXAMS
        if (data.exams.length) {

            html += `<div class="search-group-title">Đề thi</div>`;

            data.exams.forEach(item => {

                html += `
                    <a class="search-result-item"
                       href="<?= $base ?>/${item.gradeSlug}/${item.subjectSlug}/trac-nghiem/${item.slug}">
                        📝 ${item.title} - ${item.subjectName} - ${item.gradeName}
                    </a>
                `;
            });
        }

        // POSTS
        if (data.posts.length) {

            html += `<div class="search-group-title">Bài viết</div>`;

            data.posts.forEach(item => {

                html += `
                    <a class="search-result-item"
                       href="<?= $base ?>/tin-tuc/${item.categorySlug}/${item.slug}">
                        📰 ${item.title}
                    </a>
                `;
            });
        }

        dropdown.innerHTML = html;

        dropdown.style.display =
            html ? 'block' : 'none';
    });

    document.addEventListener('click', function (e) {

        if (!e.target.closest('.navbar__search')) {
            dropdown.style.display = 'none';
        }
    });
}


</script>