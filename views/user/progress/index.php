<?php
/** @var string   $base */
/** @var int      $totalQ */
/** @var int      $totalCorrect */
/** @var int      $totalWrong */
/** @var int      $correctPct */
/** @var int      $pctKnowledge */
/** @var int      $pctComprehension */
/** @var int      $pctApplication */
/** @var string   $labelKnowledge */
/** @var string   $labelComprehension */
/** @var string   $labelApplication */
/** @var array    $bySubject */
/** @var array|null $bestSubject */
/** @var array|null $worstSubject */
/** @var float    $avgScore */
/** @var int      $totalExams */
/** @var array|null $advice */
?>
 
<!-- ========== HERO ========== -->
<section class="pg-hero">
    <div class="container">
        <h1 class="pg-hero__title">Theo dõi tiến độ học tập</h1>
        <p class="pg-hero__sub">
            Nhìn lại chặng đường đã qua, đo lường sự tiến bộ và sẵn sàng cho những mục tiêu lớn hơn <br>
            Bạn đang giỏi lên mỗi ngày!
        </p>
    </div>
</section>
 
 
<!-- ========== MAIN CONTENT ========== -->
<section class="pg-section">
    <div class="container">
 
        <!-- ── Card 1: Donut + Bản đồ năng lực ── -->
        <div class="pg-card pg-stats-card">
 
            <!-- Donut -->
            <div class="pg-donut-col">
                <?php
                    $r    = 80;
                    $circ = round(2 * M_PI * $r, 2);
                    $fill = round($circ * $correctPct / 100, 2);
                ?>

                <h3 class="pg-donut__title">Thống kê trả lời</h3> 

                <div class="pg-donut">
                    <svg width="200" height="200" viewBox="0 0 200 200"
                         style="transform:rotate(-90deg); display:block;">
                        <!-- track -->
                        <circle cx="100" cy="100" r="<?= $r ?>"
                                fill="none" stroke="#f0f0f5" stroke-width="22"/>
                        <!-- sai (hồng) -->
                        <circle cx="100" cy="100" r="<?= $r ?>"
                                fill="none" stroke="#f43f5e" stroke-width="22"
                                stroke-dasharray="<?= $circ ?> <?= $circ ?>"
                                stroke-linecap="round"/>
                        <!-- đúng (indigo) — animate bằng JS -->
                        <circle class="pg-donut__arc"
                                cx="100" cy="100" r="<?= $r ?>"
                                fill="none" stroke="#4f46e5" stroke-width="22"
                                stroke-dasharray="0 <?= $circ ?>"
                                stroke-linecap="round"
                                data-fill="<?= $fill ?>"
                                data-circ="<?= $circ ?>"/>
                    </svg>
                    <div class="pg-donut__center">
                        <span class="pg-donut__pct" data-target="<?= $correctPct ?>">0%</span>
                        <span class="pg-donut__lbl">ĐÚNG</span>
                    </div>
                </div>
 
                <div class="pg-stat-summary">
                    <p class="pg-stat-summary__sub">
                        Tổng cộng bạn đã trả lời đúng
                        <strong><?= number_format($totalCorrect) ?>/<?= number_format($totalQ) ?></strong>
                        câu hỏi đã làm
                    </p>
                    <div class="pg-legend">
                        <div class="pg-legend__item">
                            <span class="pg-legend__dot pg-legend__dot--blue"></span>
                            <span class="pg-legend__text">Đúng (<?= $correctPct ?>%)</span>
                        </div>
                        <div class="pg-legend__item">
                            <span class="pg-legend__dot pg-legend__dot--pink"></span>
                            <span class="pg-legend__text">Sai (<?= 100 - $correctPct ?>%)</span>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- Bản đồ năng lực -->
            <div class="pg-skillmap">
                <h3 class="pg-skillmap__title">Bản đồ Năng lực</h3>

                <!-- Thẻ 1: Nhận biết -->
                <div class="pg-skill">
                    <div class="pg-skill__header">
                        <span class="pg-skill__name">Nhận biết</span>
                        <span class="pg-skill__note pg-skill__note--Knowledge">
                            <?= htmlspecialchars($labelKnowledge) ?>
                        </span>
                    </div>
                    <div class="pg-skill__content">
                        <div class="pg-skill__track">
                            <!-- Dùng data-width để Script cuối file tự kích hoạt -->
                            <div class="pg-skill__fill pg-skill__fill--Knowledge" data-width="<?= $pctKnowledge ?>"></div>
                        </div>
                        <span class="pg-skill__percent"><?= $pctKnowledge ?>%</span>
                    </div>
                </div>

                <!-- Thẻ 2: Thông hiểu -->
                <div class="pg-skill">
                    <div class="pg-skill__header">
                        <span class="pg-skill__name">Thông hiểu</span>
                        <span class="pg-skill__note pg-skill__note--Comprehension">
                            <?= htmlspecialchars($labelComprehension) ?>
                        </span>
                    </div>
                    <div class="pg-skill__content">
                        <div class="pg-skill__track">
                            <div class="pg-skill__fill pg-skill__fill--Comprehension" data-width="<?= $pctComprehension ?>"></div>
                        </div>
                        <span class="pg-skill__percent"><?= $pctComprehension ?>%</span>
                    </div>
                </div>

                <!-- Thẻ 3: Vận dụng -->
                <div class="pg-skill">
                    <div class="pg-skill__header">
                        <span class="pg-skill__name">Vận dụng</span>
                        <span class="pg-skill__note pg-skill__note--Application">
                            <?= htmlspecialchars($labelApplication) ?>
                        </span>
                    </div>
                    <div class="pg-skill__content">
                        <div class="pg-skill__track">
                            <div class="pg-skill__fill pg-skill__fill--Application" data-width="<?= $pctApplication ?>"></div>
                        </div>
                        <span class="pg-skill__percent"><?= $pctApplication ?>%</span>
                    </div>
                </div>
            </div>
        </div><!-- /pg-stats-card -->
 
 
        <!-- ── Card 2: Môn tốt nhất + AI Advice ── -->
        <div class="pg-bottom-row">
 
            <!-- Môn học tốt nhất -->
            <div class="pg-card pg-best-subject">
                <?php
                    $pgIcon = '📘';
                    if (!empty($bestSubject['subjectName'])) {
                        $n = $bestSubject['subjectName'];
                        if ($n === 'Lịch sử')            $pgIcon = '⏳';
                        if ($n === 'Địa lý')             $pgIcon = '🌍';
                        if ($n === 'Tiếng Anh')          $pgIcon = '📖';
                        if ($n === 'Giáo dục công dân')  $pgIcon = '⚖️';
                        if ($n === 'Toán')               $pgIcon = 'Σ';
                        if ($n === 'Vật lý')             $pgIcon = '⚡';
                        if ($n === 'Hóa học')            $pgIcon = '🧪';
                        if ($n === 'Sinh học')           $pgIcon = '🧬';
                        
                    }
                ?>
 
                <?php if ($bestSubject): ?>
                    <div class="pg-best-subject__icon"><?= $pgIcon ?></div>
                    <div class="pg-best-subject__name">
                        <?= htmlspecialchars($bestSubject['subjectName']) ?>
                        <?= !empty($bestSubject['gradeName']) ? '- ' . htmlspecialchars($bestSubject['gradeName']) : '' ?>
                    </div>

                    <p class="pg-best-subject__desc">
                        Môn học bạn yêu thích nhất là môn <?= htmlspecialchars($bestSubject['subjectName']) ?>
                        với điểm trung bình
                        <span class="pg-best-subject__score"><?= $bestSubject['avgScore'] ?></span>
                    </p>
                    <a href="<?= $base ?>/history" class="pg-best-subject__btn">
                        Hoàn thành <?= $bestSubject['examCount'] ?> bài tập
                    </a>
                <?php else: ?>
                    <div class="pg-best-subject__icon">📚</div>
                    <div class="pg-best-subject__name">Chưa có dữ liệu</div>
                    <p class="pg-best-subject__desc">Hãy làm bài thi đầu tiên để xem thống kê nhé!</p>
                    <a href="<?= $base ?>/" class="pg-best-subject__btn">Khám phá đề thi</a>
                <?php endif; ?>
            </div>
 
            <!-- AI Advice -->
            <div class="pg-card pg-advice">
                <span class="pg-advice__badge">AI ADVICE</span>
                <div class="pg-advice__icon">💡</div>
                <h3 class="pg-advice__title">Bí kíp tối ưu điểm số</h3>
                <p class="pg-advice__body">
                    <?php if ($advice): ?>
                        <?= $advice['message'] ?>
                    <?php else: ?>
                       <?php if ($worstSubject): ?>
                            <?php $gradeName = !empty($worstSubject['gradeName']) ? ' - ' . $worstSubject['gradeName'] : ''; ?>
                            Môn <strong><?= htmlspecialchars($worstSubject['subjectName'] . $gradeName) ?></strong>
                            của bạn đang hơi thấp (<?= $worstSubject['avgScore'] ?>).
                            Dành thêm 15 phút hôm nay để ôn tập nhé!
                        <?php else: ?>
                            Bạn đang học rất tốt! Hãy tiếp tục duy trì phong độ và thử sức với các đề khó hơn nhé.
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
                <?php if ($advice && $advice['subject']): ?>
                    <a href="<?= $base ?>/" class="pg-advice__cta">
                        Ôn tập <?= htmlspecialchars($advice['subject']) ?> ngay
                    </a>
                <?php else: ?>
                    <a href="<?= $base ?>/" class="pg-advice__cta">Khám phá thêm đề thi</a>
                <?php endif; ?>
            </div>
 
        </div><!-- /pg-bottom-row -->
 
    </div><!-- /container -->
</section>
 


<script>
(function () {
    const arc   = document.querySelector('.pg-donut__arc');
    const numEl = document.querySelector('.pg-donut__pct');
    if (!arc || !numEl) return;
 
    const fillVal = parseFloat(arc.dataset.fill);
    const circVal = parseFloat(arc.dataset.circ);
    const target  = parseInt(numEl.dataset.target, 10);
    let   startTs = null;
 
    function animateDonut(ts) {
        if (!startTs) startTs = ts;
        const p    = Math.min((ts - startTs) / 1000, 1);
        const ease = 1 - Math.pow(1 - p, 3);
        arc.setAttribute('stroke-dasharray', (fillVal * ease) + ' ' + circVal);
        numEl.textContent = Math.round(target * ease) + '%';
        if (p < 1) requestAnimationFrame(animateDonut);
    }
 
    function animateBars() {
        document.querySelectorAll('.pg-skill__fill').forEach(function (el, i) {
            setTimeout(function () {
                el.style.width = (el.dataset.width || 0) + '%';
            }, i * 160);
        });
    }
 
    setTimeout(function () {
        requestAnimationFrame(animateDonut);
        animateBars();
    }, 300);
})();
</script>