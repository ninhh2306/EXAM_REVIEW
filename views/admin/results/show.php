<?php $page = 'results/show'; ?>

<?php
/** @var string $base */
/** @var array $result */
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

$totalQuestions  = count($questions);
$totalCorrect    = 0;
$totalWrong      = 0;
$totalUnanswered = 0;

foreach ($questions as $q) {
    $qId           = $q['questionId'];
    $userAnswer    = $userAnswers[$qId] ?? null;
    $correctAnswer = $correctAnswers[$qId] ?? null;

    if (!$userAnswer)                        $totalUnanswered++;
    elseif ($userAnswer == $correctAnswer)   $totalCorrect++;
    else                                     $totalWrong++;
}

$percent = $totalQuestions > 0
    ? round(($totalCorrect / $totalQuestions) * 100)
    : 0;

if ($percent < 50) {
    $commentText  = 'Kết quả chưa được tốt lắm, hãy ôn luyện thêm nhé!';
    $commentColor = '#dc2626';
    $commentIcon  = 'fa-frown-open';
} elseif ($percent <= 80) {
    $commentText  = 'Bạn đã hoàn thành bài thi với kết quả khá tốt!';
    $commentColor = '#ea580c';
    $commentIcon  = 'fa-smile-wink';
} else {
    $commentText  = 'Bạn đã hoàn thành bài thi rất tốt!';
    $commentColor = '#16a34a';
    $commentIcon  = 'fa-grin-stars';
}
?>


<div class="admin-page">

    <!-- Breadcrumb — dùng class .breadcrumb sẵn có của admin -->
    <div class="breadcrumb">
        <a href="<?= $base ?>/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="<?= $base ?>/admin/results">Kết quả</a>
        <span>›</span>
        <span>Xem chi tiết</span>
    </div>

    <div class="ar-wrap">

        <!-- Page header -->
        <div class="ar-page-header">
            <h1 class="ar-exam-title"><?= htmlspecialchars($exam['title']) ?></h1>

            <div class="ar-meta-row">
                <div class="ar-meta-card">
                    <div class="ar-meta-label">Học sinh</div>
                    <div class="ar-meta-value"><?= htmlspecialchars($result['fullName']) ?></div>
                </div>
                <div class="ar-meta-card">
                    <div class="ar-meta-label">Điểm</div>
                    <div class="ar-meta-value"><?= number_format($result['realScore'], 2) ?> / 10</div>
                </div>
                <div class="ar-meta-card">
                    <div class="ar-meta-label">Ngày làm</div>
                    <div class="ar-meta-value"><?= date('d/m/Y H:i', strtotime($result['endTime'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Layout -->
        <div class="ar-layout">

            <!-- LEFT -->
            <div>
                <?php foreach ($questions as $index => $q): ?>
                    <?php
                    $qId           = $q['questionId'];
                    $userAnswer    = $userAnswers[$qId]    ?? null;
                    $correctAnswer = $correctAnswers[$qId] ?? null;
                    $isCorrect     = ($userAnswer && $userAnswer == $correctAnswer);

                    $levelMap  = ['knowledge' => 'Nhận biết', 'comprehension' => 'Thông hiểu', 'application' => 'Vận dụng'];
                    $levelText = $levelMap[$q['level']] ?? 'Khác';

                    if ($isCorrect)      $qClass = 'ar-q-correct';
                    elseif ($userAnswer) $qClass = 'ar-q-wrong';
                    else                 $qClass = '';
                    ?>

                    <div id="ar-question-<?= $qId ?>" class="ar-question <?= $qClass ?>">

                        <span class="ar-level-badge ar-level-<?= $q['level'] ?>">
                            <?= $levelText ?>
                        </span>

                        <div class="ar-q-header">
                            <span class="ar-q-icon">
                                <?php if ($isCorrect): ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php elseif ($userAnswer): ?>
                                    <i class="fas fa-times-circle"></i>
                                <?php else: ?>
                                    <i class="fas fa-minus-circle"></i>
                                <?php endif; ?>
                            </span>
                            <div class="ar-q-num"><?= sprintf('%02d', $index + 1) ?></div>
                            <div class="ar-q-content"><?= $q['content'] ?></div>
                        </div>

                        <div class="ar-answer-list">
                            <?php foreach ($q['answers'] as $i => $a): ?>
                                <?php
                                $isUser  = ($userAnswer == $a['answerId']);
                                $isRight = (bool) $a['isCorrect'];
                                $aCls    = 'ar-answer';
                                if ($isRight)             $aCls .= ' ar-ans-correct';
                                if ($isUser && !$isRight) $aCls .= ' ar-ans-wrong';
                                ?>
                                <div class="<?= $aCls ?>">
                                    <span class="ar-answer-label"><?= chr(65 + $i) ?></span>
                                    <span class="ar-answer-text"><?= $a['content'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- RIGHT: sidebar -->
            <div class="ar-sidebar">

                <!-- Map câu hỏi -->
                <div class="ar-card">
                    <div class="ar-card-header">
                        <span class="ar-card-header-icon">
                            <i class="fas fa-th"></i>
                        </span>
                        <span class="ar-card-header-title">Câu hỏi</span>
                        <button type="button" class="ar-toggle-btn" onclick="arToggle('ar-map-body', this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <div id="ar-map-body" class="ar-card-body">
                        <div class="ar-map-grid">
                            <?php foreach ($questions as $index => $q): ?>
                                <?php
                                $qId           = $q['questionId'];
                                $userAnswer    = $userAnswers[$qId]    ?? null;
                                $correctAnswer = $correctAnswers[$qId] ?? null;

                                if (!$userAnswer)                      $mCls = 'ar-map-empty';
                                elseif ($userAnswer == $correctAnswer) $mCls = 'ar-map-correct';
                                else                                   $mCls = 'ar-map-wrong';
                                ?>
                                <button type="button"
                                        class="ar-map-btn <?= $mCls ?>"
                                        data-target="ar-question-<?= $qId ?>">
                                    <?= $index + 1 ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="ar-map-legend">
                            <div class="ar-legend-row"><span class="ar-dot ar-dot-correct"></span>Đúng</div>
                            <div class="ar-legend-row"><span class="ar-dot ar-dot-wrong"></span>Sai</div>
                            <div class="ar-legend-row"><span class="ar-dot ar-dot-empty"></span>Chưa trả lời</div>
                        </div>
                    </div>
                </div>

                <!-- Thống kê nhanh -->
                <div class="ar-card">
                    <div class="ar-card-header">
                        <span class="ar-card-header-icon">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        <span class="ar-card-header-title">Thống kê nhanh</span>
                        <button type="button" class="ar-toggle-btn" onclick="arToggle('ar-stats-body', this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                    <div id="ar-stats-body">
                        <div class="ar-qs-comment" style="--ar-cc: <?= $commentColor ?>">
                            <i class="fas <?= $commentIcon ?>"></i>
                            <span><?= $commentText ?></span>
                        </div>
                        <div class="ar-qs-score">
                            <span class="ar-qs-score-num"><?= $totalCorrect ?></span>
                            <span class="ar-qs-score-total">/ <?= $totalQuestions ?></span>
                        </div>
                        <div class="ar-qs-progress">
                            <div class="ar-qs-bar">
                                <div class="ar-qs-fill" style="width:<?= $percent ?>%"></div>
                            </div>
                            <span class="ar-qs-pct"><?= $percent ?>%</span>
                        </div>
                        <div class="ar-qs-breakdown">
                            <div class="ar-qs-item">
                                <span class="ar-qs-label ar-level-knowledge">Nhận biết</span>
                                <span class="ar-qs-value"><?= $correctKnowledge ?> / <?= $totalKnowledge ?></span>
                            </div>
                            <div class="ar-qs-item">
                                <span class="ar-qs-label ar-level-comprehension">Thông hiểu</span>
                                <span class="ar-qs-value"><?= $correctComprehension ?> / <?= $totalComprehension ?></span>
                            </div>
                            <div class="ar-qs-item">
                                <span class="ar-qs-label ar-level-application">Vận dụng</span>
                                <span class="ar-qs-value"><?= $correctApplication ?> / <?= $totalApplication ?></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /ar-sidebar -->
        </div><!-- /ar-layout -->
    </div><!-- /ar-wrap -->
</div><!-- /admin-page -->


<script>
window.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.ar-map-btn').forEach(btn => {

        btn.addEventListener('click', function () {

            const el = document.getElementById(this.dataset.target);

            if (!el) return;

            el.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            el.classList.add('ar-highlight');

            setTimeout(() => {
                el.classList.remove('ar-highlight');
            }, 1500);

        });

    });

});


function arToggle(bodyId, btn) {
    const body = document.getElementById(bodyId);
    const icon = btn.querySelector('i');
    const hiding = !body.classList.contains('ar-collapsed');
    body.classList.toggle('ar-collapsed', hiding);
    icon.className = hiding
        ? 'fas fa-plus'
        : 'fas fa-minus';
}
</script>