<?php
/** @var string $base */
/** @var array $grade */
/** @var array $subject */
/** @var array $lessons */
/** @var array $exams */
?>

<?php if(!$subject): ?>

<div class="container" style="padding:40px 0;">
    <h2>Không tìm thấy môn học</h2>
</div>

<?php return; endif; ?>


<!-- =========================
HERO MÔN HỌC
========================== -->

<section class="subject-hero-section">

    <div class="container hero-flex">

        <!-- LEFT -->
        <div class="hero-text">

            <!-- breadcrumb -->
            <div class="breadcrumb">
                <a href="<?= $base ?>/">Trang chủ</a>
                <span>›</span>
                <a href="<?= $base ?>/<?= $grade['slug'] ?>"><?= $grade['gradeName'] ?></a>
                <span>›</span>
                <span><?= $subject['subjectName'] ?></span>
            </div>

            <!-- badge -->
            <div class="subject-badge">
                CHƯƠNG TRÌNH MỚI 2024
            </div>

            <!-- title -->
            <h1 class="hero-subject-title">
                <?= $subject['subjectName'] ?> - <?= $grade['gradeName'] ?>
            </h1>

            <!-- description -->
            <p class="subject-intro">
                <?= $subject['detailDesc'] ?>
            </p>

        </div>

        <!-- RIGHT ICON -->
        <div class="subject-hero-icon">
            <i class="fa-solid fa-book-open"></i>
        </div>

    </div>

</section>



<!-- =========================
CONTENT LIST
========================== -->

<section class="subject-content-section">
<div class="container">

    <div class="content-blocks-grid">

        <!-- BLOCK 1: BÀI HỌC LÝ THUYẾT -->
        <div class="content-block">

            <div class="content-block__header content-block__header--blue">
                BÀI HỌC LÝ THUYẾT
            </div>

            <div class="content-block__body">
                <?php if(empty($lessons)): ?>
                    <p class="content-block__empty">Chưa có bài học.</p>
                <?php else: ?>
                    <ul class="content-block__list">
                        <?php foreach(array_slice($lessons, 0, 6) as $lesson): ?>
                        <li>
                            <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/ly-thuyet/<?= $lesson['chapterSlug'] ?>/<?= $lesson['slug'] ?>">
                                ▶ Bài <?= $lesson['sortOrder'] ?>: <?= $lesson['lessonName'] ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/ly-thuyet" class="content-block__viewall">
                        Xem tất cả bài học →
                    </a>
                <?php endif; ?>
            </div>

        </div>


        <!-- BLOCK 2: ÔN LUYỆN TRẮC NGHIỆM -->
        <div class="content-block">

            <div class="content-block__header content-block__header--blue">
                ÔN LUYỆN TRẮC NGHIỆM
            </div>

            <div class="content-block__body">
                <?php if(empty($exams)): ?>
                    <p class="content-block__empty">Chưa có đề thi.</p>
                <?php else: ?>
                    <ul class="content-block__list">
                        <?php foreach(array_slice($exams, 0, 5) as $exam): ?>
                        <li>
                            <?php if(empty($_SESSION['user_id'])): ?>
                                <a href="javascript:void(0)" onclick="openLoginModal()">
                                    ▶ <?= $exam['title'] ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem/<?= $exam['slug'] ?>">
                                    ▶ <?= $exam['title'] ?>
                                </a>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem" class="content-block__viewall">
                        Xem tất cả đề ôn luyện →
                    </a>
                <?php endif; ?>
            </div>

        </div>

    </div>

</div>
</section>


<!-- Modal yêu cầu đăng nhập -->
<div id="loginModal" class="submit-modal" style="display:none;">
    <div class="submit-modal__overlay" onclick="closeLoginModal()"></div>

    <div class="submit-modal__box">
        <h3 class="submit-modal__title">Đăng nhập để ôn luyện</h3>
        <p class="submit-modal__msg">
            Bạn cần đăng nhập để làm bài thi, xem điểm và lưu kết quả học tập
        </p>

        <div class="submit-modal__actions">
            <button class="submit-modal__btn submit-modal__btn--cancel"
                    onclick="closeLoginModal()">
                Huỷ
            </button>

            <a href="<?= $base ?>/login"
               class="submit-modal__btn submit-modal__btn--confirm"
               style="text-decoration:none;text-align:center;">
                Đăng nhập
            </a>
        </div>
    </div>
</div>


<script>
function openLoginModal() {
    document.getElementById('loginModal').style.display = 'flex';
}

function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}
</script>