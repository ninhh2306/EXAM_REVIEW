
<?php

/** @var array $questions */
/** @var int $tabCount */
/** @var int $currentTab */


?>

<div class="admin-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Câu hỏi</span>
    </div>

    <!-- Title -->
    <div class="admin-title text-center">
        Danh sách Câu hỏi
    </div>

    <?php
        $successMessages = [
            'created' => 'Thêm câu hỏi thành công!',
            'updated' => 'Cập nhật câu hỏi thành công!',
            'deleted' => 'Xóa câu hỏi thành công!',
        ];

        $errorMessages = [
            'create_failed' => 'Thêm câu hỏi thất bại!',
            'update_failed' => 'Cập nhật câu hỏi thất bại!',
            'delete_failed' => 'Xóa câu hỏi thất bại!',
        ];
    ?>

    <!-- ALERT -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert-error" id="autoAlert">
            <?= htmlspecialchars($_SESSION['flash_error']) ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
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
            id="questionSearch"
            class="admin-search"
            placeholder="Tìm kiếm câu hỏi...">

        <a href="/admin/questions/create"
           class="admin-btn btn-add no-underline">
            + Thêm
        </a>

    </div>

    <!-- TABLE -->
    <table class="admin-table question-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Câu hỏi</th>
                <th>Môn học</th>
                <th>Chương / Bài học</th>  
                <th>Mức độ</th>
                <th>Loại câu hỏi</th>    
                <th>Cách tạo</th>    
                <th>Ngày tạo</th>       
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody id="questionTableBody">

        <?php if (!empty($questions)): ?>

            <?php foreach ($questions as $q): ?>

                <tr>

                    <td>
                        <?= $q['questionId'] ?>
                    </td>

                    <td class="question-content-cell">

                        <?= htmlspecialchars(
                            mb_strimwidth(
                                strip_tags($q['content']),
                                0,
                                120,
                                '...'
                            )
                        ) ?>

                    </td>

                    <td>
                        <?php if (!empty($q['subjectName'])): ?>
                            <?= htmlspecialchars($q['subjectName']) ?>
                            <?php if (!empty($q['gradeName'])): ?>
                                <div class="text-muted small"><?= htmlspecialchars($q['gradeName']) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>

                    <!-- CHAPTER - LESSON -->
                    <td>
                        <?php
                        $hasChapter = !empty($q['chapterName']);
                        $hasLesson  = !empty($q['lessonName']);
                        ?>

                        <?php if ($hasChapter): ?>
                            <small class="text-muted" style="display: block;">Chương <?= $q['chapterSortOrder'] ?? '' ?>: <?= htmlspecialchars($q['chapterName']) ?></small>
                        <?php endif; ?>

                        <?php if ($hasLesson): ?>
                            <div>Bài <?= $q['lessonSortOrder'] ?? '' ?>: <?= htmlspecialchars($q['lessonName']) ?></div>
                        <?php endif; ?>

                        <?php if (!$hasChapter && !$hasLesson): ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>



                    <!-- LEVEL -->
                    <td>

                        <?php if ($q['level'] === 'knowledge'): ?>
                            <span class="level-badge level-knowledge">Nhận biết</span>
                        <?php elseif ($q['level'] === 'comprehension'): ?>
                            <span class="level-badge level-comprehension">Thông hiểu</span>
                        <?php elseif ($q['level'] === 'application'): ?>
                            <span class="level-badge level-application">Vận dụng</span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>

                    </td>

                    <!-- LOẠI CÂU HỎI (lesson / thpt) -->
                    <td>
                        <?php if ($q['questionType'] === 'thpt'): ?>
                            <span class="type-badge type-thpt">THPT</span>
                        <?php else: ?>
                            <span class="type-badge type-lesson">Bài học</span>
                        <?php endif; ?>
                    </td>

                    <!-- TYPE -->
                    <td>
                        <?php if ($q['questionType'] === 'manual'): ?>
                            <span class="type-badge type-manual">
                                Thủ công
                            </span>
                        <?php else: ?>
                            <span class="type-badge type-auto">
                                Tự động
                            </span>
                        <?php endif; ?>
                    </td>


                    <td>
                        <?= date('d/m/Y', strtotime($q['createdAt'])) ?>
                    </td>

                    <!-- ACTION -->
                    <td>
                        <div class="admin-actions">
                            <a href="/admin/questions/edit/<?= $q['questionId'] ?>"
                               class="action-btn btn-edit">
                                ✏
                            </a>

                            <button class="action-btn btn-delete"
                                    onclick="openDeleteQuestion(<?= $q['questionId'] ?>)">
                                🗑
                            </button>
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

        <?php $baseUrl = '/admin/questions'; ?>

        <div class="tab-pagination-wrapper"
             id="questionPagination">

            <div class="tab-pagination">

                <?php for ($i = 1; $i <= $tabCount; $i++): ?>

                    <?php

                        $params = $_GET;

                        $params['tab'] = $i;

                        unset($params['success']);
                        unset($params['error']);

                        $url = $baseUrl . '?' . http_build_query($params);

                    ?>

                    <a href="<?= $url ?>"
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

        <p class="delete-confirm-text">Bạn chắc chắn muốn xóa câu hỏi này?</p>

        <div class="text-center mt-3">
            <a href="#" id="deleteLink" class="admin-btn btn-danger">Xóa</a>
            <button type="button" class="admin-btn btn-add ml-2" data-dismiss="modal">Hủy</button>
        </div>

    </div>
  </div>
</div>


