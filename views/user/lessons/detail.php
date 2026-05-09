<?php
/** @var array $grade */
/** @var array $subject */
/** @var array $chapter */
/** @var array $lesson */
/** @var array $relatedLessons */
/** @var array $relatedExams */
?>

<!-- HERO -->
<section class="lesson-hero-section">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Trang chủ</a>
            <span>›</span>

            <a href="/<?= $grade['slug'] ?>">
                <?= $grade['gradeName'] ?>
            </a>
            <span>›</span>

            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>">
                <?= $subject['subjectName'] ?>
            </a>
            <span>›</span>

            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/ly-thuyet">
                Lý thuyết
            </a>
            <span>›</span>

             <span class="breadcrumb-current">
                Chương <?= $chapter['sortOrder'] ?>
            </span>
            <span>›</span>

            <span><?= $lesson['lessonName'] ?></span>
        </div>

        <h1 class="lesson-hero__title">
            Bài <?= $lesson['sortOrder'] ?>:
            <?= $lesson['lessonName'] ?>
        </h1>
    </div>
</section>


<!-- NỘI DUNG -->
<section class="lesson-detail-section">
    <div class="container">
        <div class="lesson-detail-layout">

            <!-- ===== SIDEBAR ===== -->
            <aside class="lesson-sidebar">

                <!-- ===== BÀI HỌC LIÊN QUAN ===== -->
                <div class="content-block">
                    <div class="content-block__header content-block__header--blue">
                        BÀI HỌC CÙNG CHỦ ĐỀ
                    </div>

                    <div class="content-block__body">
                        <?php if (!empty($relatedLessons)): ?>
                            <ul class="content-block__list">
                                <?php 
                                $count = 0;
                                foreach ($relatedLessons as $rl):

                                    if ($rl['lessonId'] == $lesson['lessonId']) continue;
                                    if ($count >= 5) break;

                                    // ⚠️ cần chapterSlug
                                    if (empty($rl['chapterSlug'])) continue;
                                ?>
                                <li>
                                   
                                    <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/ly-thuyet/<?= $rl['chapterSlug'] ?>/<?= $rl['slug'] ?>">
                                        ▶ Bài <?= $rl['sortOrder'] ?>: <?= $rl['lessonName'] ?>
                                    </a>
                                </li>
                                <?php 
                                    $count++;
                                endforeach; 
                                ?>
                            </ul>
                        <?php else: ?>
                            <p class="content-block__empty">Chưa có bài học khác.</p>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- ===== ĐỀ ÔN LUYỆN ===== -->
                <div class="content-block" style="margin-top: 16px;">
                    <div class="content-block__header content-block__header--blue">
                        ĐỀ ÔN LUYỆN CÙNG CHỦ ĐỀ
                    </div>

                    <div class="content-block__body">
                        <?php if (!empty($relatedExams)): ?>
                            <ul class="content-block__list">
                                <?php 
                                $count = 0;
                                foreach ($relatedExams as $exam):
                                    if ($count >= 5) break;
                                ?>
                                <li>
                                    <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem/<?= $exam['slug'] ?>">
                                        ▶ <?= $exam['title'] ?>
                                    </a>
                                </li>
                                <?php 
                                    $count++;
                                endforeach; 
                                ?>
                            </ul>
                        <?php else: ?>
                            <p class="content-block__empty">Chưa có đề ôn luyện.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </aside>


            <!-- ===== CONTENT ===== -->
            <div class="lesson-content-wrap">

                <div class="lesson-content" id="lessonContent">
                    <?= $lesson['content'] ?>
                </div>

                <div class="lesson-content__toggle" id="toggleBtn">
                    <button onclick="toggleContent()" class="btn btn-outline">
                        Xem thêm ↓
                    </button>
                </div>

            </div>

        </div>
    </div>
</section>


<script>
function toggleContent() {
    const content = document.getElementById('lessonContent');
    const btn = document.querySelector('#toggleBtn button');

    if (content.classList.contains('expanded')) {
        content.classList.remove('expanded');
        btn.textContent = 'Xem thêm ↓';
        content.scrollIntoView({ behavior: 'smooth' });
    } else {
        content.classList.add('expanded');
        btn.textContent = 'Thu gọn ↑';
    }
}
</script>