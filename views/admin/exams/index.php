<?php

/** @var array $exams */
/** @var int $tabCount */
/** @var int $currentTab */

?>

<div class="admin-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">

        <a href="/admin/dashboard">
            Dashboard
        </a>

        <span>›</span>

        <span>
            Đề thi
        </span>

    </div>


    <!-- TITLE -->
    <div class="admin-title text-center">
        Danh sách Đề thi
    </div>


    <?php
        $successMessages = [
            'created' => 'Thêm đề thi thành công!',
            'updated' => 'Cập nhật đề thi thành công!',
            'deleted' => 'Xóa đề thi thành công!',
        ];
        $errorMessages = [
            'create_failed' => 'Thêm đề thi thất bại!',
            'update_failed' => 'Cập nhật đề thi thất bại!',
            'delete_failed' => 'Xóa đề thi thất bại!',
        ];

    ?>

    <!-- ALERT -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert-error" id="autoAlert">
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>


    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error" id="autoAlert">
            <?= $errorMessages[$_GET['error']] ?? 'Đã xảy ra lỗi!' ?>
        </div>

    <?php endif; ?>


    <!-- TOOLBAR -->
    <div class="admin-toolbar admin-toolbar-right">

        <input
            type="text"
            id="examSearch"
            class="admin-search"
            placeholder="Tìm kiếm đề thi...">

        <a
            href="/admin/exams/create"
            class="admin-btn btn-add no-underline">
            + Thêm
        </a>

    </div>


    <!-- TABLE -->
    <table class="admin-table exam-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Tên đề</th>
                <th>Môn học</th>
                <th>Bài học</th>
                <th>Loại đề</th>
                <th>Tạo đề</th>
                <th>Số câu</th>
                <th>Thời gian</th>
                <th>Lượt làm</th>
                <th>Trạng thái</th>
                <th>Người tạo</th> 
                <th>Hành động</th>
            </tr>
        </thead>


        <tbody id="examTableBody">
            <?php if (!empty($exams)): ?>
                <?php foreach ($exams as $exam): ?>

                    <tr>
                        <td>
                            <?= $exam['examId'] ?>
                        </td>

                        <td>
                            <div class="font-weight-bold">
                                <?= htmlspecialchars($exam['title']) ?>
                            </div>

                            <div class="text-muted small">
                                <?= htmlspecialchars($exam['slug']) ?>
                            </div>

                        </td>

                        <!-- MÔN HỌC + KHỐI LỚP -->
                        <td>
                            <?= htmlspecialchars($exam['subjectName']) ?>
                            <div class="text-muted small">
                                <?= htmlspecialchars($exam['gradeName']) ?>
                            </div>
                        </td>

                        <!-- BÀI HỌC -->
                        <td>
                            <?php if (!empty($exam['lessonName'])): ?>
                                <div class="text-muted small">Chương <?= $exam['chapterSortOrder'] ?>: <?= htmlspecialchars($exam['chapterName']) ?></div>
                                <div>Bài <?= $exam['lessonSortOrder'] ?>: <?= htmlspecialchars($exam['lessonName']) ?></div>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>

                        <!-- TYPE -->
                        <td>
                            <?php if ($exam['examType'] === 'lesson'): ?>
                                <span class="type-badge type-lesson">
                                    Ôn luyện
                                </span>

                            <?php elseif ($exam['examType'] === 'thpt'): ?>
                                <span class="type-badge type-thpt">
                                    THPT
                                </span>

                            <?php else: ?>
                                <span class="type-badge type-random">
                                    Random
                                </span>
                            <?php endif; ?>
                        </td>


                        <!-- GENERATION -->
                        <td>

                            <?php if ($exam['generationType'] === 'manual'): ?>

                                <span class="badge-status badge-inactive">
                                    Thủ công
                                </span>

                            <?php else: ?>
                                <span class="badge-status badge-active">
                                    Tự động
                                </span>
                            <?php endif; ?>

                        </td>

                        <td>
                            <?= $exam['realTotalQuestions'] ?> câu
                        </td>

                        <td>
                            <?= $exam['duration'] ?> phút
                        </td>

                        <td>
                            <?php
                                echo $exam['examType'] === 'random'
                                    ? ($exam['playCount'] ?? 0)
                                    : ($exam['viewCount'] ?? 0);
                            ?>
                        </td>


                        <!-- STATUS -->
                        <td>
                            <?php if ($exam['isActive']): ?>
                                <span class="badge-status badge-active">
                                    Hiển thị
                                </span>

                            <?php else: ?>
                                <span class="badge-status badge-inactive">
                                    Ẩn
                                </span>
                            <?php endif; ?>

                        </td>

                        <td>
                            <?php if (!empty($exam['creatorName'])): ?>
                                <span>
                                    <?= htmlspecialchars($exam['creatorName']) ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>

                        <!-- ACTION -->
                        <td>
                            <div class="admin-actions">
                                <a
                                    href="/admin/exams/edit/<?= $exam['examId'] ?>"
                                    class="action-btn btn-edit">
                                    ✏
                                </a>

                                <button
                                    class="action-btn btn-delete"
                                    onclick="openDeleteExam(<?= $exam['examId'] ?>)">
                                    🗑
                                </button>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">
                        Không có dữ liệu
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>

    </table>


    <!-- PAGINATION -->
    <?php if (!empty($tabCount) && $tabCount > 1): ?>

        <?php $baseUrl = '/admin/exams'; ?>

        <div class="tab-pagination-wrapper" id="examPagination">

            <div class="tab-pagination">

                <?php for ($i = 1; $i <= $tabCount; $i++): ?>

                    <?php

                        $params = $_GET;

                        $params['tab'] = $i;

                        unset($params['success']);
                        unset($params['error']);

                        $url = $baseUrl . '?' . http_build_query($params);

                    ?>

                    <a
                        href="<?= $url ?>"
                        class="tab-btn <?= $i === $currentTab ? 'active' : '' ?>">

                        Tab <?= $i ?>
                    </a>

                <?php endfor; ?>

            </div>

        </div>

    <?php endif; ?>

</div>



<!-- MODAL XÓA -->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-box">
            <p class="delete-confirm-text">
                Bạn chắc chắn muốn xóa đề thi này?
            </p>

            <div class="text-center mt-3">
                <a
                    href="#"
                    id="deleteLink"
                    class="admin-btn btn-danger">
                    Xóa
                </a>

                <button
                    type="button"
                    class="admin-btn btn-add ml-2"
                    data-dismiss="modal">
                    Hủy
                </button>
            </div>
        </div>
    </div>
</div>