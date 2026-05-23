<?php
/** @var string $base */

/** @var array $summary */
/** @var array $subjects */
/** @var array $histories */

/** @var string|int $subjectId */
/** @var string $keyword */
?>


<!-- =========================
HISTORY HERO
========================== -->

<section class="history-hero-section">

    <div class="container history-hero-flex">

        <!-- LEFT -->
        <div class="history-hero-text">

            <!-- breadcrumb -->
            <div class="breadcrumb">

                <a href="<?= $base ?>/">Trang chủ</a>
                <span>›</span>

                <span>Lịch sử làm bài</span>

            </div>


            <!-- badge -->
            <div class="history-badge">
                THỐNG KÊ HỌC TẬP
            </div>

            <!-- title -->
            <h1 class="history-title">
                Lịch sử làm bài của bạn
            </h1>

            <!-- desc -->
            <p class="history-desc">
                Lưu giữ dấu ấn học tập — Nơi ghi nhận nỗ lực và sự kiên trì của bạn mỗi ngày
            </p>

        </div>



        <!-- RIGHT -->
        <div class="history-summary-wrap">

            <div class="history-box total">
                <span>Tổng số bài</span>
                <h3><?= $summary['totalExam'] ?></h3>
            </div>

            <div class="history-box avg">
                <span>Điểm trung bình</span>
                <h3><?= number_format($summary['avgScore'] ?? 0, 1) ?></h3>
            </div>

        </div>

    </div>

</section>



<!-- FILTER -->
<section class="history-filter-section">
<div class="container filter-flex">

    <div class="filter-left">

        <a href="<?= $base ?>/history"
           class="history-pill <?= $subjectId=='' ? 'active':'' ?>">
           Tất cả
        </a>

        <?php foreach($subjects as $sub): ?>

        <a href="<?= $base ?>/history?subjectId=<?= $sub['subjectId'] ?>"
           class="history-pill <?= $subjectId==$sub['subjectId'] ? 'active':'' ?>">
           <?php if($sub['subjectId'] === 'thpt'): ?>
                THPT Quốc Gia
            <?php else: ?>
                <?= $sub['subjectName'] ?> - <?= $sub['gradeName'] ?>
            <?php endif; ?>
        </a>

        <?php endforeach; ?>

    </div>


    <div class="filter-right">

        <div class="history-search-wrap">
            <input type="text"
                id="historySearch"
                value="<?= $keyword ?>"
                placeholder="Tìm kiếm bài..."
                class="history-search">
        </div>

    </div>

</div>
</section>



<!-- LIST -->
<section class="history-list-section">

    <div class="container" id="historyList">

    <?php foreach($histories as $item): ?>
        
    <?php

    /* TÍNH THỜI GIAN */
    $start = !empty($item['startTime']) ? strtotime($item['startTime']) : 0;
    $end   = !empty($item['endTime'])   ? strtotime($item['endTime'])   : 0;

    $seconds = max(0, $end - $start);

    $minutes = floor($seconds / 60);
    $remain  = $seconds % 60;

    $timeText = $minutes . ':' . str_pad($remain, 2, '0', STR_PAD_LEFT);


    /* ICON */
    $icon = '📘';

    if($item['subjectName'] == 'Lịch sử') $icon = '⏳';
    if($item['subjectName'] == 'Địa lý') $icon = '🌍';
    if($item['subjectName'] == 'Giáo dục công dân')   $icon = '⚖️';
    if($item['subjectName'] == 'Tiếng Anh')   $icon = '📖';
    if($item['subjectName'] == 'Toán') $icon = 'Σ';
    if($item['subjectName'] == 'Vật lý') $icon = '⚡';
    if($item['subjectName'] == 'Hóa học')   $icon = '🧪';
    if($item['subjectName'] == 'Sinh học')   $icon = '🧬';

    /* MÀU ĐIỂM */
    $score = $item['realTotalQuestions'] > 0
        ? round(($item['totalCorrect'] / $item['realTotalQuestions']) * 10, 1)
        : 0;

    $scoreClass = 'good';

    if($score < 5){
        $scoreClass = 'bad';
    }
    elseif($score < 8){
        $scoreClass = 'mid';
    }

    ?>

    <div class="history-card-row">

        <!-- LEFT -->
        <div class="history-card-left">

            <div class="subject-card__icon"><?= $icon ?></div>

            <div class="history-info">

                <span class="subject-badge-mini">
                    <?= $item['subjectName'] ?>
                </span>

                <h3><?= $item['examTitle'] ?></h3>

                <div class="history-meta">
                    <!-- Icon Lịch cho Ngày tháng -->
                    <span>
                        <i class="fa-regular fa-calendar-days icon-date"></i> 
                        <?= date('d/m/Y', strtotime($item['endTime'])) ?>
                    </span>

                    <!-- Icon Đồng hồ cho Thời gian làm bài -->
                    <span>
                        <i class="fa-regular fa-clock icon-time"></i> 
                        <?= $timeText ?> 
                    </span>

                    <!-- Icon Check cho Số câu đúng -->
                    <span>
                        <i class="fa-regular fa-circle-check icon-check"></i> 
                        <?= $item['totalCorrect'] ?>/<?= $item['realTotalQuestions'] ?> câu đúng
                    </span>
                    
                </div>

            </div>

        </div>


        <!-- RIGHT -->
        <div class="history-card-right">

            <!-- result -->
            <div class="result-col">
                <span class="result-label">KẾT QUẢ</span>

                <div class="history-score">
                    <span class="score-main <?= $scoreClass ?>">
                        <?= number_format($score,1) ?>
                    </span>

                    <span class="score-total">
                        /10
                    </span>

                </div>

            </div>

            <!-- button -->
            <div class="button-col">
                <a href="<?= $base ?>/ket-qua/<?= $item['examSlug'] ?>-<?= $item['resultId'] ?>/chi-tiet"
                class="history-detail-btn">
                Xem chi tiết
                </a>
            </div>

        </div>

    </div>


<?php endforeach; ?>

<?php if(empty($histories)): ?>

<div class="history-empty">
    Không có kết quả phù hợp
</div>

<?php endif; ?>

</div>
</section>


<script>

const historySearch = document.getElementById('historySearch');
const historyList   = document.getElementById('historyList');

let debounce;

if (historySearch) {

    historySearch.addEventListener('input', function () {

        clearTimeout(debounce);

        debounce = setTimeout(async () => {

            const keyword = this.value.trim();

            const response = await fetch(
                `<?= $base ?>/history?ajax=1&keyword=` +
                encodeURIComponent(keyword)
            );

            const data = await response.json();

            let html = '';

            // EMPTY
            if (!data.length) {
                html = `
                    <div class="history-empty">
                        Không có kết quả phù hợp
                    </div>
                `;

                historyList.innerHTML = html;
                return;
            }

            data.forEach(item => {

                let icon = '📘';

                if(item.subjectName === 'Lịch sử') icon = '⏳';
                if(item.subjectName === 'Địa lý') icon = '🌍';
                if(item.subjectName === 'Giáo dục công dân') icon = '⚖️';
                if(item.subjectName === 'Tiếng Anh') icon = '📖';
                if(item.subjectName === 'Toán') icon = 'Σ';
                if(item.subjectName === 'Vật lý') icon = '⚡';
                if(item.subjectName === 'Hóa học') icon = '🧪';
                if(item.subjectName === 'Sinh học') icon = '🧬';

                let scoreClass = 'good';

                if(parseFloat(item.score) < 5){
                    scoreClass = 'bad';
                }
                else if(parseFloat(item.score) < 8){
                    scoreClass = 'mid';
                }

                html += `
                <div class="history-card-row">
                    <div class="history-card-left">
                        <div class="subject-card__icon">${icon}</div>

                        <div class="history-info">
                            <span class="subject-badge-mini">
                                ${item.subjectName}
                            </span>

                            <h3>${item.examTitle}</h3>

                            <div class="history-meta">
                                <span>
                                    <i class="fa-regular fa-calendar-days icon-date"></i>
                                    ${formatDate(item.endTime)}
                                </span>

                                <span>
                                    <i class="fa-regular fa-circle-check icon-check"></i>
                                    ${item.totalCorrect}/${item.realTotalQuestions} câu đúng
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="history-card-right">

                        <div class="result-col">
                            <span class="result-label">
                                KẾT QUẢ
                            </span>

                            <div class="history-score">
                                <span class="score-main ${scoreClass}">
                                    ${
                                        item.realTotalQuestions > 0
                                            ? ((item.totalCorrect / item.realTotalQuestions) * 10).toFixed(1)
                                            : '0.0'
                                    }
                                </span>

                                <span class="score-total">
                                    /10
                                </span>
                            </div>
                        </div>

                        <div class="button-col">
                            <a href="<?= $base ?>/ket-qua/${item.examSlug}-${item.resultId}/chi-tiet"
                               class="history-detail-btn">
                               Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
                `;
            });

            historyList.innerHTML = html;

        }, 300);

    });

}


function formatDate(dateString) {

    const d = new Date(dateString);

    return d.toLocaleDateString('vi-VN');
}

</script>