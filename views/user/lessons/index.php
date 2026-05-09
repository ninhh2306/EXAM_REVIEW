<?php
/** @var string $base */
/** @var array $grade */
/** @var array $subject */
/** @var array $lessons */
/** @var array $chapters */
?>

<!-- HERO -->
<section class="lesson-hero-section">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= $base ?>/">Trang chủ</a>
            <span>›</span>
            <a href="<?= $base ?>/<?= $grade['slug'] ?>">
                <?= $grade['gradeName'] ?>
            </a>
            <span>›</span>
            <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>">
                <?= $subject['subjectName'] ?>
            </a>
            <span>›</span>
            <span>Lý thuyết</span>
        </div>

        <h1 class="exam-hero__title">
            Bài học lý thuyết — <?= $subject['subjectName'] ?>
        </h1>

        <div class="exam-hero__meta">
            <span>
                <i class="fa-solid fa-user-graduate"></i>
                <?= $subject['subjectName'] ?>
            </span>
            <span>
                <i class="fa-regular fa-file-lines"></i>
                <?= count($lessons) ?> bài học
            </span>
        </div>
    </div>
</section>


<!-- DANH SÁCH -->
<section class="index-list-section">
<div class="container">

<?php if (empty($lessons)): ?>

    <p class="index-empty">Chưa có bài học nào.</p>

<?php else: ?>

    <?php
    $lessonsByChapter = [];
    $lessonsNoChapter = [];

    foreach ($lessons as $lesson) {
        if (!empty($lesson['chapterId'])) {
            $lessonsByChapter[$lesson['chapterId']][] = $lesson;
        } else {
            $lessonsNoChapter[] = $lesson;
        }
    }
    ?>

    <!-- ===== HIỂN THỊ THEO CHAPTER ===== -->
    <?php if (!empty($chapters)): ?>
        <?php foreach ($chapters as $chapter): ?>

            <?php
            $chapterLessons = $lessonsByChapter[$chapter['chapterId']] ?? [];
            if (empty($chapterLessons)) continue;
            ?>

            <div class="chapter-block">
                <div class="chapter-block__header">
                    <h2 class="chapter-block__title">
                        Chương <?= $chapter['sortOrder'] ?>: <?= htmlspecialchars($chapter['chapterName']) ?>
                    </h2>
                    <span class="chapter-block__count">
                        <?= count($chapterLessons) ?> bài học
                    </span>
                </div>

                <div class="index-card-grid">
                    <?php foreach ($chapterLessons as $lesson): ?>
                        <div class="index-card">

                            <div class="index-card__body">
                                <h3 class="index-card__title">
                                    Bài <?= $lesson['sortOrder'] ?>:
                                    <?= htmlspecialchars($lesson['lessonName']) ?>
                                </h3>
                            </div>

                            <div class="index-card__footer">
                                <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/ly-thuyet/<?= $chapter['slug'] ?>/<?= $lesson['slug'] ?>"
                                   class="index-card__btn index-card__btn--green">
                                    <i class="fa-solid fa-play"></i> Xem ngay
                                </a>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>


    <!-- ===== KHÔNG CÓ CHAPTER ===== -->
    <?php if (!empty($lessonsNoChapter)): ?>
        <div class="chapter-block">

            <div class="chapter-block__header">
                <h2 class="chapter-block__title">Bài học khác</h2>
            </div>

            <div class="index-card-grid">

                <?php foreach ($lessonsNoChapter as $lesson): ?>
                    <div class="index-card">

                        <div class="index-card__body">
                            <h3 class="index-card__title">
                                Bài <?= $lesson['sortOrder'] ?>:
                                <?= htmlspecialchars($lesson['lessonName']) ?>
                            </h3>
                        </div>

                        <div class="index-card__footer">
                            <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/ly-thuyet/<?= $lesson['slug'] ?>"
                               class="index-card__btn index-card__btn--green">
                                <i class="fa-solid fa-play"></i> Xem ngay
                            </a>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

</div>
</section>