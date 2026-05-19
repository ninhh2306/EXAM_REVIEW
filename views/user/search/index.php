<?php
/** @var string $keyword */
?>

<section class="search-page">
    <div class="container">

        <h1 class="search-title">
            Kết quả tìm kiếm:
            "<?= htmlspecialchars($keyword) ?>"
        </h1>

        <!-- SUBJECTS -->
        <?php if (!empty($subjects)): ?>
            <div class="search-group">
                <h2>Môn học</h2>

                <?php foreach ($subjects as $item): ?>
                    <a href="<?= $base ?>/<?= $item['gradeSlug'] ?>/<?= $item['slug'] ?>"
                    class="search-item">

                        <div class="search-item-title">
                            <?= htmlspecialchars($item['subjectName']) ?>

                            <span class="search-grade">
                                - <?= htmlspecialchars($item['gradeName']) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>


        <!-- LESSONS -->
        <?php if (!empty($lessons)): ?>
            <div class="search-group">
                <h2>Bài học</h2>

                <?php foreach ($lessons as $item): ?>
                    <a href="<?= $base ?>/<?= $item['gradeSlug'] ?>/<?= $item['slug'] ?>"
                    class="search-item">

                        <div class="search-item-title">
                            <?= htmlspecialchars($item['lessonName']) ?>

                            <span class="search-grade">
                                - <?= htmlspecialchars($item['subjectName']) ?>
                                <?= htmlspecialchars($item['gradeName']) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
                
            </div>
        <?php endif; ?>


        <!-- EXAMS -->
        <?php if (!empty($exams)): ?>
            <div class="search-group">
                <h2>Đề thi</h2>

                <?php foreach ($exams as $item): ?>
                    <a href="<?= $base ?>/<?= $item['gradeSlug'] ?>/<?= $item['slug'] ?>"
                    class="search-item">

                        <div class="search-item-title">
                            <?= htmlspecialchars($item['title']) ?>

                            <span class="search-grade">
                                - <?= htmlspecialchars($item['subjectName']) ?>
                                <?= htmlspecialchars($item['gradeName']) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
                
            </div>
        <?php endif; ?>


        <!-- POSTS -->
        <?php if (!empty($posts)): ?>
            <div class="search-group">
                <h2>Bài viết</h2>

                <?php foreach ($posts as $item): ?>

                    <a class="search-item"
                       href="<?= $base ?>/tin-tuc/<?= $item['categorySlug'] ?>/<?= $item['slug'] ?>">

                        <?= htmlspecialchars($item['title']) ?>
                    </a>

                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>