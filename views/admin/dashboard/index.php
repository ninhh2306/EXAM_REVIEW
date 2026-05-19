<?php
/** @var int $totalPlaysMonth */   
/** @var float $avgScore */         
/** @var int $newUsersMonth */      
/** @var float|int $passRate */    
/** @var array $topSubjects */
/** @var int $totalResults */
/** @var array $subject */ 
?>


<div class="d-flex align-items-center justify-content-between mb-4 dashboard-header">
    <h3>Trang Chủ</h3>
</div>
 
 
<!-- ============================================================
     4 STAT CARDS
     ============================================================ -->
<div class="row mb-3">
 
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="stat-label">Lượt làm bài (Tháng)</div>
                        <div class="stat-value"><?= number_format($totalPlaysMonth) ?></div>
                        <p class="stat-note">
                            <span class="note-primary"><i class="fas fa-chart-line"></i></span>
                            Tháng <?= date('m/Y') ?>
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-pencil-alt stat-icon color-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="stat-label">Điểm TB toàn hệ thống</div>
                        <div class="stat-value"><?= $avgScore ?> / 10</div>
                        <p class="stat-note">
                            <span class="note-success"><i class="fas fa-star"></i></span>
                            Tất cả bài thi
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-bar stat-icon color-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="stat-label">Học viên mới</div>
                        <div class="stat-value"><?= number_format($newUsersMonth) ?></div>
                        <p class="stat-note">
                            <span class="note-info"><i class="fas fa-user-plus"></i></span>
                            Tháng <?= date('m/Y') ?>
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users stat-icon color-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="stat-label">Tỉ lệ đỗ đạt (điểm &gt; 8)</div>
                        <div class="stat-value"><?= $passRate ?>%</div>
                        <p class="stat-note">
                            <?php if ($passRate >= 50): ?>
                                <span class="note-up"><i class="fas fa-arrow-up"></i></span>
                            <?php else: ?>
                                <span class="note-down"><i class="fas fa-arrow-down"></i></span>
                            <?php endif; ?>
                            Toàn hệ thống
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy stat-icon color-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
</div>
 
 
<!-- ============================================================
     BIỂU ĐỒ VÙNG + MÔN HỌC PHỔ BIẾN
     ============================================================ -->
<div class="row">
 
    <!-- Biểu đồ vùng -->
    <div class="col-xl-8 col-lg-7">
        <div class="card chart-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6>Lượt nộp bài theo tháng</h6>
                <span class="text-muted" style="font-size:0.8rem;"><?= date('Y') ?></span>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="monthlySubmitChart"></canvas>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Môn học phổ biến -->
    <div class="col-xl-4 col-lg-5">
        <div class="card popular-card mb-4">
 
            <div class="card-header">
                <h6>Môn học nhiều lượt ôn luyện nhất</h6>
            </div>
 
            <div class="card-body">
                <?php
                $barColors = ['bg-warning','bg-success','bg-danger','bg-info','bg-primary'];
                foreach ($topSubjects as $i => $subject):
                    $pct = $totalResults > 0
                        ? round(($subject['totalSubmits'] / $totalResults) * 100)
                        : 0;
                    $color = $barColors[$i % count($barColors)];
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-baseline">
                        <span class="popular-subject-label">
                            <?= htmlspecialchars($subject['subjectName']) ?>
                            <span class="popular-subject-grade">
                                — <?= htmlspecialchars($subject['gradeName']) ?>
                            </span>
                        </span>
                        <span class="popular-subject-count">
                            <?= number_format($subject['totalSubmits']) ?> lượt
                        </span>
                    </div>
                    <div class="progress" style="height:10px;">
                        <div class="progress-bar <?= $color ?>"
                             role="progressbar"
                             style="width:<?= $pct ?>%">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
 
                <?php if (empty($topSubjects)): ?>
                    <p class="text-muted text-center small mt-3">Chưa có dữ liệu</p>
                <?php endif; ?>
            </div>
 
            <div class="card-footer">
                <a class="small text-primary card-link view-all-btn" href="/admin/subjects">
                    Xem tất cả <i class="fas fa-chevron-right"></i>
                </a>
            </div>
 
        </div>
    </div>
 
</div>
 
 
<!-- ============================================================
     TOP 5 HỌC VIÊN (full width, dạng bảng ngang)
     ============================================================ -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card leaderboard-card mb-4">
 
            <div class="card-header d-flex align-items-center justify-content-between">
                <h6>🏆 Top 5 học viên xuất sắc nhất</h6>
            </div>
 
            <div class="card-body">
 
                <!-- Header bảng -->
                <div class="leaderboard-header">
                    <div></div>
                    <div>Học viên</div>
                    <div class="text-center">Môn học yêu thích</div>
                    <div class="text-center">Số bài làm</div>
                    <div class="text-center">Điểm Trung bình</div>
                </div>
 
                <!-- Dữ liệu -->
                <?php if (empty($topStudents)): ?>
                    <p class="text-muted text-center small mt-3">Chưa có dữ liệu</p>
                <?php else: ?>
                    <?php foreach ($topStudents as $i => $student):
                        $initial = mb_substr($student['fullName'], 0, 1, 'UTF-8');
                    ?>
                    <div class="student-row">
 
                        <!-- Avatar -->
                        <?php if (!empty($student['avatar'])): ?>
                            <img src="<?= htmlspecialchars($student['avatar']) ?>"
                                 alt="" class="student-avatar">
                        <?php else: ?>
                            <div class="student-avatar-placeholder"><?= $initial ?></div>
                        <?php endif; ?>
 
                        <!-- Tên -->
                        <div>
                            <div class="student-name">
                                <?= htmlspecialchars($student['fullName']) ?>
                            </div>
                        </div>
 
                        <!-- Môn yêu thích -->
                        <div class="student-subject text-center">
                            <?= $student['favoriteSubject']
                                ? htmlspecialchars($student['favoriteSubject'])
                                : '—' ?>
                        </div>
 
                        <!-- Số bài -->
                        <div class="student-exams"><?= $student['totalExams'] ?></div>
 
                        <!-- Điểm TB -->
                        <div class="student-score"><?= $student['avgScore'] ?></div>
 
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
 
            </div>
 
            <div class="card-footer">
                <a class="small text-primary card-link view-all-btn" href="/admin/users">
                    Xem tất cả <i class="fas fa-chevron-right"></i>
                </a>
            </div>
 
        </div>
    </div>
</div>
 
 
<!-- ============================================================
     CHART JS - đặt ở cuối, load sau Chart.min.js qua $pageScripts
     ============================================================ -->
<?php ob_start(); ?>
<script>
(function () {
    var rawData = <?= json_encode($monthlyStats ?? []) ?>;
    var labels  = rawData.map(function(r){ return r.month; });
    var values  = rawData.map(function(r){ return parseInt(r.count); });
 
    var ctx = document.getElementById('monthlySubmitChart').getContext('2d');
 
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(108, 99, 255, 0.30)');
    gradient.addColorStop(1, 'rgba(108, 99, 255, 0.02)');
 
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '',   
                data: values,
                fill: true,
                backgroundColor: gradient,
                borderColor: '#6c63ff',
                borderWidth: 2.5,
                pointBackgroundColor: '#6c63ff',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            tooltips: {
                callbacks: {
                    label: function(item) {
                        return ' ' + item.yLabel.toLocaleString('vi-VN') + ' lượt';
                    }
                }
            },
            scales: {
                xAxes: [{
                    gridLines: { display: false },
                    ticks: { fontColor: '#7f8c8d', fontSize: 11 }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        fontColor: '#7f8c8d',
                        fontSize: 11,
                        callback: function(val) {
                            return val.toLocaleString('vi-VN');
                        }
                    },
                    gridLines: { color: 'rgba(0,0,0,0.05)' }
                }]
            }
        }
    });
})();
</script>
<?php $pageScripts = ob_get_clean(); ?>
 