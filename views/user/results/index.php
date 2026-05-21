<?php
/** @var array $grade */
/** @var array $subject */
/** @var array $exam */
/** @var array $result */

// ── 1. Tính thời gian làm bài ───────────────────────────────
$timeTaken = '0:00';

if (!empty($result['startTime']) && !empty($result['endTime'])) {
    $start = strtotime($result['startTime']);
    $end   = strtotime($result['endTime']);

    if ($start !== false && $end !== false) {
        $diff = max(0, $end - $start);

        // Nếu bạn muốn giới hạn max 1 bài (ví dụ 60 phút)
        // $diff = min($diff, 3600);

        $minutes = floor($diff / 60);
        $seconds = $diff % 60;

        // Format dạng mm:ss (ví dụ 2:05)
        $timeTaken = sprintf('%d:%02d', $minutes, $seconds);
    }
}

// ── 2. Tính điểm và số câu đúng/tổng ────────────────────────
$totalCorrect   = (int)($result['totalCorrect'] ?? 0);
$totalQuestions = (int)($exam['totalQuestions'] ?? $result['totalQuestions'] ?? 0);
 
// Tính lại điểm: số câu đúng / TỔNG SỐ CÂU ĐỀ THI (kể cả câu bỏ qua) × 10
$realScore = $totalQuestions > 0
    ? round(($totalCorrect / $totalQuestions) * 10, 1)
    : 0.0;

// ── 3. Màu vòng tròn điểm ───────────────────────────────────
$score = (float)($result['score'] ?? 0);
if ($realScore >= 8)     $scoreClass = 'score--high';
elseif ($realScore >= 5)  $scoreClass = 'score--mid';
else                  $scoreClass = 'score--low';

// ── 4. SVG vòng tròn (r=45, circumference ≈ 282.74) ─────────
$circumference = 282.74;
// dashOffset = full - progress; bắt đầu từ full (ẩn), animate tới giá trị thật
$targetOffset  = $circumference - ($circumference * ($realScore / 10));
?>

<!-- BREADCRUMB -->
<section class="exam-hero-section">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= $base ?>/">Trang chủ</a>
            <span>›</span>
            <?php if (!empty($subject) && !empty($grade)): ?>
                <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>">
                    <?= htmlspecialchars($subject['subjectName']) ?>
                </a>
            <?php else: ?>
                <span>Môn học</span>
            <?php endif; ?>
            <span>›</span>
            <span>Kết quả bài thi</span>
        </div>
    </div>
</section>

<!-- Chúc mừng -->
<div class="container">
    <div class="congrats">
        <div class="congrats__icon">✨</div>
        <h1 class="congrats__title">Chúc mừng! Bạn đã hoàn thành bài thi</h1>
        <p class="congrats__subtitle"><?= htmlspecialchars($exam['title'] ?? '') ?></p>
    </div>
</div>

<!-- Card kết quả -->
<div class="container">
    <div class="result-card">

        <!-- Vòng tròn điểm -->
        <div class="result-card__score-wrap">
            <div class="score-circle <?= $scoreClass ?>" id="scoreCircle">
                <svg viewBox="0 0 100 100" class="score-circle__svg">
                    <!-- Track -->
                    <circle cx="50" cy="50" r="45"
                            fill="none" stroke="#E8EAF6" stroke-width="10"/>
                    <!-- Progress – ban đầu ẩn hoàn toàn, JS sẽ animate -->
                    <circle cx="50" cy="50" r="45"
                            fill="none"
                            class="score-circle__progress"
                            id="scoreProgress"
                            stroke-linecap="round"
                            stroke-dasharray="<?= round($circumference, 2) ?>"
                            stroke-dashoffset="<?= round($circumference, 2) ?>"
                            transform="rotate(-90 50 50)"/>
                </svg>
                <div class="score-circle__inner">
                    <span class="score-circle__number"><?= number_format($realScore, 1) ?></span>
                    <span class="score-circle__label">ĐIỂM SỐ</span>
                </div>
            </div>
        </div>

        <!-- Thống kê -->
        <div class="result-card__stats">

            <div class="stat-box">
                <div class="stat-box__icon stat-box__icon--pink">✔</div>
                <div class="stat-box__info">
                    <span class="stat-box__label">Số câu đúng</span>
                    <span class="stat-box__value">
                        <?= $totalCorrect ?> / <?= $totalQuestions ?>
                    </span>
                </div>
            </div>

            <div class="stat-box">
                <div class="stat-box__icon stat-box__icon--blue">🕐</div>
                <div class="stat-box__info">
                    <span class="stat-box__label">Thời gian làm</span>
                    <span class="stat-box__value"><?= $timeTaken ?></span>
                </div>
            </div>

        </div>

        <!-- Buttons -->
        <div class="result-card__actions">
            <!-- 1. Thử làm lại: Dùng slug của bài thi hiện tại -->
            <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem/<?= $exam['slug'] ?>-<?= $exam['examId'] ?>"
               class="result-btn result-btn--yellow">
                🔄 Thử làm lại
            </a>

            <!-- 2. Xem đáp án chi tiết: Cần slug bài thi và ID kết quả -->
            <a href="<?= $base ?>/ket-qua/<?= $exam['slug'] ?>-<?= $result['resultId'] ?>/chi-tiet"
               class="result-btn result-btn--blue">
                👁️ Xem đáp án chi tiết
            </a>

            <!-- 3. Làm đề khác: Quay lại danh sách đề trắc nghiệm của môn đó -->
            <a href="<?= $base ?>/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem"
               class="result-btn result-btn--pink">
                📋 Làm đề khác
            </a>
        </div>

    </div>
</div>

<!-- Đề thi gợi ý -->
<?php if (!empty($suggested)): ?>
<div class="container">
    <div class="suggested">
 
        <div class="suggested__head">
            <h2 class="suggested__title">Đề thi gợi ý cho bạn</h2>
            <div class="suggested__line"></div>
        </div>
 
        <div class="suggested__grid">
            <?php foreach ($suggested as $suggestedExam): ?>
                <a href="/<?= htmlspecialchars($grade['slug'] ?? '') ?>/<?= htmlspecialchars($subject['slug'] ?? '')
                     ?>/trac-nghiem/<?= htmlspecialchars($suggestedExam['slug'] ?? '') ?>-<?= (int)($suggestedExam['examId'] ?? 0) ?>"
                   class="suggested-card">
 
                    <!-- Tên đề -->
                    <h3 class="suggested-card__title">
                        <?= htmlspecialchars($suggestedExam['title'] ?? '') ?>
                    </h3>
 
                    <!-- Footer: số câu + số phút + mũi tên -->
                    <div class="suggested-card__footer">
                        <span class="suggested-card__meta">
                            <span>
                                <i class="fa-regular fa-circle-question"></i> 
                                <?= (int)($suggestedExam['totalQuestions'] ?? 0) ?> câu
                            </span>

                            <span>
                                <i class="fa-regular fa-clock"></i> 
                                <?= (int)($suggestedExam['duration'] ?? 0) ?> phút
                            </span>

                        </span>
                        <span class="suggested-card__arrow">→</span>
                    </div>
 
                </a>
            <?php endforeach; ?>
        </div>
 
    </div>
</div>
<?php endif; ?>

<!-- ── Score ring animation ─────────────────────────────── -->
<script>
(function () {
    var circumference = 282.74;
    var targetOffset  = <?= round($targetOffset, 2) ?>;

    // Delay nhỏ để animation nổi bật hơn sau khi trang load
    setTimeout(function () {
        var ring = document.getElementById('scoreProgress');
        if (!ring) return;

        // Dùng Web Animation API nếu có, fallback bằng style transition
        if (ring.animate) {
            ring.animate(
                [
                    { strokeDashoffset: circumference + '' },
                    { strokeDashoffset: targetOffset + '' }
                ],
                {
                    duration: 1400,
                    easing: 'cubic-bezier(0.4,0,0.2,1)',
                    fill: 'forwards'
                }
            );
        } else {
            ring.style.transition = 'stroke-dashoffset 1.4s cubic-bezier(0.4,0,0.2,1)';
            ring.style.strokeDashoffset = targetOffset;
        }

        // Giữ giá trị cuối
        ring.addEventListener('animationend', function () {
            ring.style.strokeDashoffset = targetOffset;
        });
    }, 300);
})();
</script>