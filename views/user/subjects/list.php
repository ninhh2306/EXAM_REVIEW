<?php
/** @var array $grade */
/** @var array $subject */
/** @var array $subjects */

?>

<!-- =========================
 HERO GIỚI THIỆU KHỐI LỚP
========================= -->

<section class="grade-hero">
    <div class="container hero-flex">
        <div class="hero-text">

<!-- ======== BREADCRUMB ======== -->

            <div class="breadcrumb">
                <a href="<?= $base ?>/">Trang chủ</a>
                <span>›</span>
                
                <span><?= $grade['gradeName'] ?></span>
                
            </div>


            <h1>
            Chương trình học <?= $grade['gradeName'] ?>
            </h1>

            <p class="hero-desc">
            Hành trình chinh phục kiến thức bắt đầu từ những bước chân vững chãi nhất.
            <br>

            <strong class="brand-highlight">Vui Luyện Thi</strong> tự hào đồng hành cùng bạn,
            thắp sáng đam mê và <span class="goal-highlight">chinh phục mọi đỉnh cao kiến thức</span>.
            </p>

            <div class="hero-buttons">

            <a href="#" class="btn btn-primary btn-lg">
            Bắt đầu học ngay
            </a>

            <a href="#" class="btn btn-outline btn-lg">
            Lộ trình học tập
            </a>

        </div>

    </div>


        <div class="hero-icon">

        <i class="fa-solid fa-book"></i>

        </div>

    </div>

</section>



<!-- =========================
 DANH SÁCH MÔN HỌC
========================= -->

<section class="subjects-section">
    <div class="container">
        <div class="section-title">

            <div>
                <h2>Danh sách môn học</h2>
                <p>Chọn môn học bạn muốn ôn luyện hôm nay</p>
            </div>

            <a href="<?= $base ?>/<?= $grade['slug'] ?>" class="view-all">
            Xem tất cả môn
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>


    <div class="subjects-grid">

<?php foreach($subjects as $subject): ?>


 <!-- ======== THẺ MÔN HỌC ========== -->

<div class="subject-card">

    <div class="subject-icon">
        <img src="/images/subjects/<?= $subject['image'] ?>" 
        alt="<?= $subject['subjectName'] ?>">
    </div>

    <h3 class="subject-title"><?= $subject['subjectName'] ?></h3>

    <?php if (!empty($subject['description'])): ?>
        <p class="subject-desc">
            <?= $subject['description'] ?>
        </p>
    <?php endif; ?>

    <a class="subject-btn"
    href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>">
        Xem tất cả
    </a>

</div>

<?php endforeach; ?>

</div>

</div>

</section>




<!-- =========================
 WHY CHOOSE US
========================= -->

<section class="why-section">

<div class="container">

<h2 class="why-title">
    Tại sao chọn Vui Luyện Thi?
</h2>

<p class="why-sub">
    Nền tảng ôn thi hàng đầu dành cho học sinh THPT
</p>

<div class="why-grid">


<div class="why-card">

<div class="why-icon">
<i class="fa-solid fa-book-open"></i>
</div>

<h3>Ngân hàng đề khổng lồ</h3>

<p>
    Hơn 50.000+ câu hỏi trắc nghiệm được biên soạn sát với đề thi.
</p>

</div>


<div class="why-card">

<div class="why-icon">
<i class="fa-solid fa-lightbulb"></i>
</div>

<h3>Lời giải chi tiết</h3>

<p>
    Mỗi câu hỏi đều có hướng dẫn giải chi tiết giúp hiểu sâu kiến thức.
</p>

</div>


<div class="why-card">

<div class="why-icon">
<i class="fa-solid fa-chart-line"></i>
</div>

<h3>Theo dõi tiến độ</h3>

<p>
    Phân tích điểm mạnh điểm yếu giúp bạn học tập hiệu quả hơn.
</p>

</div>


</div>

</div>

</section>


