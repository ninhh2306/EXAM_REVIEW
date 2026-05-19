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
            <a href="<?= $base ?>/<?= $grade['slug'] ?>"><?= $grade['gradeName'] ?></a>
            <span>›</span>
            <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>"><?= $subject['subjectName'] ?></a>
            <span>›</span>
            <span>Lý thuyết</span>
        </div>

        <h1 class="exam-hero__title">
            Bài học lý thuyết — <?= $subject['subjectName'] ?>
        </h1>

        <div class="exam-hero__meta">
            <span><i class="fa-solid fa-user-graduate"></i> <?= $subject['subjectName'] ?></span>
            <span><i class="fa-regular fa-file-lines"></i> <?= count($lessons) ?> bài học</span>
        </div>
    </div>
</section>


<section class="index-list-section">
<div class="container">

    <?php if (empty($lessons) || empty($chapters)): ?>

        <p class="index-empty">Chưa có bài học nào.</p>

    <?php else: ?>

        <?php
        $lessonsByChapter = [];
        foreach ($lessons as $lesson) {
            if (!empty($lesson['chapterId'])) {
                $lessonsByChapter[$lesson['chapterId']][] = $lesson;
            }
            // bài không có chapter → bỏ qua, không hiển thị
        }
        ?>

        <?php $chapterIndex = 0; ?>

        <?php foreach ($chapters as $chapter): ?>
            <?php
            $chapterLessons = $lessonsByChapter[$chapter['chapterId']] ?? [];
            if (empty($chapterLessons)) continue;
            $chapterIndex++;
            ?>

            <div class="chapter-block">
                <div class="chapter-block__header">
                    <h2 class="chapter-block__title">
                        Chương <?= $chapterIndex ?>:
                        <?= htmlspecialchars($chapter['chapterName']) ?>
                    </h2>
                    <span class="chapter-block__count"><?= count($chapterLessons) ?> bài học</span>
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

        <?php if ($chapterIndex === 0): ?>
            <p class="index-empty">Chưa có bài học nào.</p>
        <?php endif; ?>

    <?php endif; ?>

</div>
</section>