<?php
/** @var array $grade */
/** @var array $subject */
/** @var array $exams */
/** @var array $questions */
?>

<!-- HERO -->
<section class="exam-hero-section">
    <div class="container">
        <div class="breadcrumb">
            <a href="/">Trang chủ</a>
            <span>›</span>
            <a href="/<?= $grade['slug'] ?>"><?= $grade['gradeName'] ?></a>
            <span>›</span>
            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>"><?= $subject['subjectName'] ?></a>
            <span>›</span>
            <span>Ôn luyện trắc nghiệm</span>
        </div>
        <h1 class="exam-hero__title">Ôn luyện trắc nghiệm — <?= $subject['subjectName'] ?></h1>
        <div class="exam-hero__meta">
            <span><i class="fa-solid fa-user-graduate"></i> <?= $subject['subjectName'] ?></span>
            <span><i class="fa-regular fa-file-lines"></i> <?= count($exams) ?> đề ôn luyện</span>
        </div>
    </div>
</section>


<!-- DANH SÁCH ĐỀ THI THEO CHƯƠNG -->
<section class="index-list-section">
<div class="container">

    <?php if (empty($exams)): ?>
        <p class="index-empty">Chưa có đề thi nào.</p>

    <?php elseif (!empty($chapters)): ?>
        <?php
        // Group exams theo chapterId
        $examsByChapter = [];
        $examsNoChapter = [];
        foreach ($exams as $exam) {
            if (!empty($exam['chapterId'])) {
                $examsByChapter[$exam['chapterId']][] = $exam;
            } else {
                $examsNoChapter[] = $exam;
            }
        }
        ?>

        <?php foreach ($chapters as $chapter): ?>
            <?php
            $chapterExams = $examsByChapter[$chapter['chapterId']] ?? [];
            if (empty($chapterExams)) continue;
            ?>
            <div class="chapter-block">
                <div class="chapter-block__header">
                    <!-- <span class="chapter-block__label">Chủ đề <?= $chapter['sortOrder'] ?></span> -->
                    <h2 class="chapter-block__title"><?= htmlspecialchars($chapter['chapterName']) ?></h2>
                    <span class="chapter-block__count"><?= count($chapterExams) ?> đề ôn luyện </span>
                </div>

                <div class="index-card-grid">
                    <?php foreach ($chapterExams as $exam): ?>
                    <div class="index-card">
                        <div class="index-card__body">

                            <h3 class="index-card__title">
                                <?= htmlspecialchars($exam['title']) ?>
                            </h3>

                            <div class="index-card__meta">

                                <span>
                                    <i class="fa-regular fa-circle-question"></i>
                                    <?= $exam['totalQuestions'] ?> câu
                                </span>

                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    <?= $exam['duration'] ?> phút
                                </span>

                                <span>
                                    <i class="fa-solid fa-eye"></i>
                                    <?= $exam['viewCount'] ?> lượt làm
                                </span>
                                
                            </div>
                        </div>
                        <div class="index-card__footer">
                            <?php if(empty($_SESSION['user_id'])): ?>
                            <a href="javascript:void(0)"
                                onclick="openLoginModal()"
                                class="index-card__btn index-card__btn--green">
                            <?php else: ?>
                            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem/<?= $exam['slug'] ?>"
                                class="index-card__btn index-card__btn--green">
                            <?php endif; ?>
                                <i class="fa-solid fa-pen-to-square"></i> Làm đề
                            </a>
                            <?php if(!empty($_SESSION['user_id'])): ?>
                            <?php endif; ?>

                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($examsNoChapter)): ?>
            <div class="chapter-block">
                <div class="chapter-block__header">
                    <h2 class="chapter-block__title">Đề thi tổng hợp</h2>
                    <span class="chapter-block__count"><?= count($examsNoChapter) ?> đề thi</span>
                </div>
                <div class="index-card-grid">
                    <?php foreach ($examsNoChapter as $exam): ?>
                    <div class="index-card">
                        <div class="index-card__body">
                            <h3 class="index-card__title"><?= htmlspecialchars($exam['title']) ?></h3>
                            <div class="index-card__meta">
                                <span><i class="fa-regular fa-circle-question"></i> <?= $exam['totalQuestions'] ?> câu</span>
                                <span><i class="fa-regular fa-clock"></i> <?= $exam['duration'] ?> phút</span>
                            </div>
                        </div>

                        <div class="index-card__footer">
                            <?php if(empty($_SESSION['user_id'])): ?>
                            <a href="javascript:void(0)"
                                onclick="openLoginModal()"
                                class="index-card__btn index-card__btn--green">
                            <?php else: ?>
                            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem/<?= $exam['slug'] ?>"
                                class="index-card__btn index-card__btn--green">
                            <?php endif; ?>
                               class="index-card__btn index-card__btn--green">
                                <i class="fa-solid fa-pen-to-square"></i> Làm đề
                            </a>

                            <?php if(!empty($_SESSION['user_id'])): ?>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Fallback: không có chapters -->
        <div class="index-card-grid">
            <?php foreach ($exams as $exam): ?>
            <div class="index-card">
                <div class="index-card__body">
                    <h3 class="index-card__title"><?= htmlspecialchars($exam['title']) ?></h3>
                    <div class="index-card__meta">
                        <span><i class="fa-regular fa-circle-question"></i> <?= $exam['totalQuestions'] ?> câu</span>
                        <span><i class="fa-regular fa-clock"></i> <?= $exam['duration'] ?> phút</span>
                    </div>
                </div>
                <div class="index-card__footer">
                    <?php if(empty($_SESSION['user_id'])): ?>
                    <a href="javascript:void(0)"
                        onclick="openLoginModal()"
                        class="index-card__btn index-card__btn--green">
                    <?php else: ?>
                    <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem/<?= $exam['slug'] ?>"
                        class="index-card__btn index-card__btn--green">
                    <?php endif; ?>
                        <i class="fa-solid fa-pen-to-square"></i> Làm đề
                    </a>

                    <?php if(!empty($_SESSION['user_id'])): ?>
                    <?php endif; ?>

                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>
</section>

<!-- Yêu cầu đăng nhập -->
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

            <a href="/login"
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


