<?php
/** @var array $results */
/** @var int $tabCount */
/** @var int $currentTab */
?>

<div class="admin-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Kết quả ôn luyện</span>
    </div>

    <!-- Title -->
    <div class="admin-title text-center">
        Danh sách Kết quả ôn luyện
    </div>

    <!-- ALERT -->
    <?php
        $successMessages = [
            'deleted' => 'Xóa kết quả thành công!'
        ];
    ?>

    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $successMessages[$_GET['success']] ?>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="admin-toolbar admin-toolbar-right">

        <input type="text"
                id="resultSearch"
                class="admin-search"
                placeholder="Tìm kiếm kết quả...">

    </div>

    <!-- TABLE -->
    <table class="admin-table result-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Môn học</th>
                <th>Bài học</th>
                <th>Đề thi</th>
                <th>Điểm</th>
                <th>Kết quả</th>
                <th>Thời gian làm</th>
                <th>Ngày làm</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody id="resultTableBody">

        <?php if (!empty($results)): ?>

            <?php foreach ($results as $r): ?>

                <?php
                    // Tính thời gian làm bài
                    $start = !empty($r['startTime'])
                        ? strtotime($r['startTime'])
                        : 0;

                    $end = !empty($r['endTime'])
                        ? strtotime($r['endTime'])
                        : 0;

                    $seconds = max(0, $end - $start);

                    $minutes = floor($seconds / 60);
                    $remainSeconds = $seconds % 60;
                ?>

                <tr>
                    <!-- ID -->
                    <td><?= $r['resultId'] ?></td>

                    <!-- USER -->
                    <td>
                        <?php if (!empty($r['fullName'])): ?>
                            <?= htmlspecialchars($r['fullName']) ?>
                        <?php else: ?>
                            <span class="text-muted">Ẩn danh</span>
                        <?php endif; ?>
                    </td>

                    <!-- MÔN HỌC -->
                    <td>
                        <?php if (!empty($r['subjectName'])): ?>
                            <?= htmlspecialchars($r['subjectName']) ?>
                            <?php if (!empty($r['gradeName'])): ?>
                                <div class="text-muted small">
                                    <?= htmlspecialchars($r['gradeName']) ?>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- BÀI HỌC -->
                    <td>
                        <?php if (!empty($r['lessonName'])): ?>
                            <?php if (!empty($r['chapterName'])): ?>
                                <div class="text-muted small">
                                    Chương <?= $r['chapterSortOrder'] ?>: <?= htmlspecialchars($r['chapterName']) ?>
                                </div>
                            <?php endif; ?>
                            Bài <?= $r['lessonSortOrder'] ?>: <?= htmlspecialchars($r['lessonName']) ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>

                    <!-- ĐỀ THI -->
                    <td>
                        <?php if (!empty($r['examTitle'])): ?>
                            <?= htmlspecialchars($r['examTitle']) ?>
                        <?php else: ?>
                            <span class="text-muted">Không có đề</span>
                        <?php endif; ?>
                    </td>

                    <!-- ĐIỂM -->
                    <td>
                        <span class="score-badge">
                            <?= number_format((float)($r['realScore'] ?? 0), 1) ?>/10
                        </span>
                    </td>

                    <!-- KẾT QUẢ -->
                    <td>
                        <span class="badge-result">
                            <?= (int)$r['totalCorrect'] ?>/<?= (int)($r['realTotalQuestions'] ?? 0) ?> đúng
                        </span>
                    </td>

                    <!-- THỜI GIAN -->
                    <td>
                        <?php if ($seconds > 0): ?>
                            <?= $minutes ?> phút <?= $remainSeconds ?> giây
                        <?php else: ?>
                            <span class="text-muted"> - </span>
                        <?php endif; ?>
                    </td>

                    <!-- NGÀY LÀM -->
                    <td>
                        <?php if (!empty($r['endTime'])): ?>
                            <?= date('d/m/Y H:i', strtotime($r['endTime'])) ?>
                        <?php else: ?>
                            <span class="text-muted">Chưa hoàn thành</span>
                        <?php endif; ?>
                    </td>

                    <!-- ACTION -->
                    <td>
                        <div class="admin-actions">
                            <a href="/admin/results/show/<?= $r['resultId'] ?>"
                            class="action-btn btn-view" title="Xem chi tiết">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>

        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">
                    Không có dữ liệu
                </td>
            </tr>
        <?php endif; ?>

        </tbody>

    </table>

    <!-- PAGINATION -->
    <?php if ($tabCount > 1): ?>

    <?php $baseUrl = '/admin/results';?>

    <div class="tab-pagination-wrapper"
         id="resultPagination">

        <div class="tab-pagination">
            <?php for ($i = 1; $i <= $tabCount; $i++): ?>
                <?php
                    $params = $_GET;
                    $params['tab'] = $i;
                    unset($params['success']);
                    unset($params['error']);

                    $url = $baseUrl . '?' . http_build_query($params);?>

                <a href="<?= $url ?>"
                   class="tab-btn <?= $i === $currentTab ? 'active' : '' ?>">
                    Tab <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>

    </div>

    <?php endif; ?>

</div>