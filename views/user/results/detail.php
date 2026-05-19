<?php
$isAdmin = $isAdmin ?? false;
?>

<?php
/** @var string $base */

/** @var array $exam */
/** @var array $questions */

/** @var array $userAnswers */
/** @var array $correctAnswers */

/** @var int $correctKnowledge */
/** @var int $totalKnowledge */

/** @var int $correctComprehension */
/** @var int $totalComprehension */

/** @var int $correctApplication */
/** @var int $totalApplication */
?>


<?php
/* ── Tính toán thống kê ─────────────────────────────────── */
$totalQuestions  = count($questions);
$totalCorrect    = 0;
$totalWrong      = 0;
$totalUnanswered = 0;
 
foreach ($questions as $q) {
    $qId        = $q['questionId'];
    $userAnswer = $userAnswers[$qId] ?? null;
 
    $correctAnswer = null;
    foreach ($q['answers'] as $a) {
        if ($a['isCorrect']) { $correctAnswer = $a['answerId']; break; }
    }
 
    if (!$userAnswer)                          $totalUnanswered++;
    elseif ($userAnswer == $correctAnswer)     $totalCorrect++;
    else                                       $totalWrong++;
}
 
$percent = $totalQuestions > 0 ? round(($totalCorrect / $totalQuestions) * 100) : 0;
 
if ($percent < 50) {
    $commentText  = 'Kết quả chưa được tốt lắm, hãy ôn luyện thêm nhé!';
    $commentColor = '#dc2626';   /* đỏ */
    $commentIcon  = 'fa-face-frown-open';

} elseif ($percent <= 80) {
    $commentText  = 'Bạn đã hoàn thành bài thi với kết quả khá tốt, tiếp tục cố gắng nhé!';
    $commentColor = '#ea580c';   /* vàng */
    $commentIcon  = 'fa-face-smile-wink'; 
    
} else {
    $commentText  = 'Bạn đã hoàn thành bài thi rất tốt, tiếp tục cố gắng nhé!';
    $commentColor = '#16a34a';   /* xanh lá */
    $commentIcon  = 'fa-face-grin-stars';
}
?>


<?php if (!$isAdmin): ?>

<section class="exam-hero-section">
    <div class="container">

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?= $base ?>/">Trang chủ</a>
            <span>›</span>
            <span>Kết quả chi tiết</span>
        </div>

        <!-- Tiêu đề -->
        <h1 class="exam-hero__title"><?= $exam['title'] ?></h1>

    </div>
</section>

<?php endif; ?>


<?php if ($isAdmin): ?>

<section class="admin-result-title-wrap">

    <div class="container">

        <h1 class="admin-result-title">
            <?= htmlspecialchars($exam['title']) ?>
        </h1>

    </div>

</section>

<?php endif; ?>


<section class="exam-section">
    <div class="container">
        <div class="exam-layout">
            <!-- ================= LEFT ================= -->
            <div>

                <?php foreach ($questions as $index => $q): ?>
                    <?php

                    //Lấy đáp án câu hỏi đúng/sai
                    $qId = $q['questionId'];
                    $userAnswer = $userAnswers[$qId] ?? null;

                    $correctAnswer = null;
                    foreach ($q['answers'] as $a) {
                        if ($a['isCorrect']) {
                            $correctAnswer = $a['answerId'];
                            break;
                        }
                    }

                    $isCorrect = ($userAnswer && $userAnswer == $correctAnswer);

                    // Gán nhãn mức độ câu hỏi
                    $levelMap = [
                        'knowledge'     => 'Nhận biết',
                        'comprehension' => 'Thông hiểu',
                        'application'   => 'Vận dụng'
                    ];
                    $q['level_text'] = $levelMap[$q['level']] ?? 'Khác';
                    ?>

                    <div id="question-<?= $qId ?>"
                        class="question-block 
                        <?= $isCorrect ? 'question-correct' : ($userAnswer ? 'question-wrong' : '') ?>">

                        <!-- BADGE MỨC ĐỘ -->
                        <span class="question-badge-level level-<?= $q['level'] ?>">
                            <?= $q['level_text'] ?>
                        </span>

                        <!-- HEADER -->
                        <div class="question-header">
                            <div class="question-number">
                                <!-- 1. Icon nằm bên trái ngoài cùng -->
                                <span class="question-status-icon">
                                    <?php if ($isCorrect): ?>
                                        <i class="fa-solid fa-circle-check text-success"></i> <!-- V -->
                                    <?php elseif ($userAnswer): ?>
                                        <i class="fa-solid fa-circle-xmark text-danger"></i>  <!-- X -->
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-minus text-secondary"></i> <!-- - -->
                                    <?php endif; ?>
                                </span>

                                <!-- 2. Số thứ tự câu hỏi -->
                                <div class="q-num-badge">
                                    <?= sprintf('%02d', $index + 1) ?>
                                </div>

                                <!-- 3. Nội dung câu hỏi -->
                                <div class="question-content">
                                    <?= $q['content'] ?>
                                </div>
                                
                            </div>
                        </div>

                        <!-- ANSWERS — dùng class riêng để tắt hover -->
                        <div class="answer-list">
                            <?php foreach ($q['answers'] as $i => $a): ?>
    
                                <?php
                                $isUser  = ($userAnswer == $a['answerId']);
                                $isRight = ($a['isCorrect']);
    
                                $cls = 'answer-item answer-item--readonly';
                                if ($isRight)             $cls .= ' answer-correct';
                                if ($isUser && !$isRight) $cls .= ' answer-wrong';
                                ?>
    
                                <div class="<?= $cls ?>">
                                    <span class="answer-label"><?= chr(65 + $i) ?></span>
                                    <span class="answer-text"><?= $a['content'] ?></span>
                                </div>
    
                            <?php endforeach; ?>
                        </div>

                    </div>

                <?php endforeach; ?>
            </div>

            
            <!-- ================= RIGHT ================= -->
            <section class="exam-sidebar">
                <!--- BẢN ĐỒ CÂU HỎI --->
                <div class="answer-sheet">
                    <!-- Header -->
                    <div class="answer-sheet__header">
                    <span class="sheet-header-icon"><i class="fa-solid fa-table-cells fa-xl"></i></span>
                            <span class="sheet-header-title">Câu hỏi</span>
                                <button type="button" class="sheet-toggle-btn" id="sheetToggleBtn" onclick="toggleAnswerSheet()">
                                    <i class="fa-solid fa-minus" id="sheetToggleIcon"></i>
                                </button>
                    </div>

                    <!-- Phần nội dung có thể ẩn/hiện -->
                    <div class="answer-sheet__body" id="answerSheetBody">

                        <div class="answer-sheet__grid">

                            <?php foreach ($questions as $index => $q): ?>

                                <?php
                                $qId = $q['questionId'];
                                $userAnswer = $userAnswers[$qId] ?? null;

                                $correctAnswer = $correctAnswers[$qId] ?? null;

                                if (!$userAnswer) {
                                    $class = 'sheet-empty';
                                } elseif ($userAnswer == $correctAnswer) {
                                    $class = 'sheet-correct';
                                } else {
                                    $class = 'sheet-wrong';
                                }
                                ?>

                                <button class="sheet-btn <?= $class ?>"
                                        data-target="question-<?= $qId ?>">
                                    <?= $index + 1 ?>
                                </button>

                            <?php endforeach; ?>

                        </div>

                        <div class="result-legend-item">
                            <span><span class="result-dot result-dot--correct"></span> Đúng </span>
                            <span><span class="result-dot result-dot--wrong"></span> Sai</span>
                            <span><span class="result-dot result-dot--unanswered"></span> Chưa trả lời</span>

                        </div>
                    </div>

                </div>


                <!-- --- Thống kê nhanh --- -->
                <div class="quick-stats-card" id="quickStatsCard">
                    <!-- Gradient header -->
                    <div class="qs-header">
                        <span class="qs-header__icon"><i class="fa-solid fa-chart-simple"></i></span>
                        <span class="qs-header__title">Thống kê nhanh</span>
                                <button type="button" class="sheet-toggle-btn" id="statsToggleBtn" onclick="toggleQuickStats()">
                                    <i class="fa-solid fa-minus" id="statsToggleIcon"></i>
                                </button>
                    </div>

                    <div id="quickStatsBody">
                        <!-- Nhận xét -->
                        <div class="qs-comment" style="--comment-color: <?= $commentColor ?>">
                            <i class="fa-solid <?= $commentIcon ?> qs-comment__icon"></i>
                            <span><?= $commentText ?></span>
                        </div>
        
                        <!-- Điểm số -->
                        <div class="qs-score">
                            <span class="qs-score__num"><?= $totalCorrect ?></span>
                            <span class="qs-score__total">/ <?= $totalQuestions ?></span>
                        </div>
        
                        <!-- Thanh tiến trình -->
                        <div class="qs-progress-wrap">
                            <div class="qs-progress-bar">
                                <div class="qs-progress-fill" style="width: <?= $percent ?>%"></div>
                            </div>
                            <span class="qs-progress-pct"><?= $percent ?>%</span>
                        </div>

                        <!-- Breakdown theo mức độ -->
                        <div class="qs-breakdown">

                            <div class="qs-item">
                                <span class="qs-label level-knowledge">Nhận biết</span>
                                <span class="qs-value">
                                    <?= $correctKnowledge ?> / <?= $totalKnowledge ?>
                                </span>
                            </div>

                            <div class="qs-item">
                                <span class="qs-label level-comprehension">Thông hiểu</span>
                                <span class="qs-value">
                                    <?= $correctComprehension ?> / <?= $totalComprehension ?>
                                </span>
                            </div>

                            <div class="qs-item">
                                <span class="qs-label level-application">Vận dụng</span>
                                <span class="qs-value">
                                    <?= $correctApplication ?> / <?= $totalApplication ?>
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </section>

        </div>

    </div>
</section>


<script>

// <!-- ================= SCROLL ================= -->
document.querySelectorAll('.sheet-btn').forEach(btn => {
    btn.addEventListener('click', function () {

        let id = this.dataset.target;
        let el = document.getElementById(id);

        if (el) {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });

            el.classList.add('highlight-active');

            setTimeout(() => {
                el.classList.remove('highlight-active');
            }, 1500);
        }
    });
});


// ────────────── TOGGLE SIDEBAR ────────────────────
function toggleAnswerSheet() {
    const sheet = document.querySelector('.answer-sheet');
    const body  = document.getElementById('answerSheetBody');
    const icon  = document.getElementById('sheetToggleIcon');
    const isCollapsed = sheet.classList.contains('collapsed');

    // Đóng card
    if (isCollapsed) {
        sheet.classList.remove('collapsed');
        body.style.display = 'block';
        icon.className = 'fa-solid fa-minus';

    // Mở card
    } else {
        sheet.classList.add('collapsed');
        body.style.display = 'none';
        icon.className = 'fa-solid fa-plus';
    }
}



// ────────────── TOGGLE QUICK STATS ────────────────────
function toggleQuickStats() {
    const card = document.getElementById('quickStatsCard');
    const body = document.getElementById('quickStatsBody');
    const icon = document.getElementById('statsToggleIcon');

    const isCollapsed = card.classList.contains('collapsed');

    if (isCollapsed) {
        card.classList.remove('collapsed');
        body.style.display = 'block';
        icon.className = 'fa-solid fa-minus';
    } else {
        card.classList.add('collapsed');
        body.style.display = 'none';
        icon.className = 'fa-solid fa-plus';
    }
}


</script>

    