<?php
/** @var array $lessons */
/** @var array $tabCount */
/** @var array $currentTab */
?>

<div class="admin-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Bài học</span>
    </div>

    <!-- Title -->
    <div class="admin-title text-center">
        Danh sách Bài học
    </div>

    <!-- ALERT -->
    <?php
        $successMessages = [
            'created' => 'Thêm bài học thành công!',
            'updated' => 'Cập nhật bài học thành công!',
            'deleted' => 'Xóa bài học thành công!'
        ];
    ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_exams'): ?>
        <div class="alert-error" id="autoAlert">
            Không thể xóa bài học đang có đề thi!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $successMessages[$_GET['success']] ?>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="admin-toolbar admin-toolbar-right">

        <input type="text"
                id="lessonSearch"
                class="admin-search"
                placeholder="Tìm kiếm bài học...">

        <a href="/admin/lessons/create" class="admin-btn btn-add mt-2">
            + Thêm
        </a>

    </div>

    <!-- TABLE -->
    <table class="admin-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Khối lớp</th>
                <th>Môn học</th>
                <th>Chương học</th>
                <th>Bài học</th>
                <th>Slug</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody id="lessonTableBody">

        <?php if (!empty($lessons)): ?>

            <?php foreach ($lessons as $l): ?>

                <tr>

                    <!-- ID -->
                    <td><?= $l['lessonId'] ?></td>

                    <!-- Grade -->
                    <td>
                        <?= htmlspecialchars($l['gradeName']) ?>
                    </td>

                    <!-- Subject -->
                    <td>
                        <?= htmlspecialchars($l['subjectName']) ?>
                    </td>

                    <!-- Chapter -->
                    <td>
                        <?php if (!empty($l['chapterName'])): ?>
                            Chương <?= $l['chapterSortOrder'] ?? '' ?>:
                            <?= htmlspecialchars($l['chapterName']) ?>
                        <?php else: ?>
                            <span class="text-muted">Không có</span>
                        <?php endif; ?>
                    </td>

                    <!-- Lesson -->
                    <td>
                        Bài <?= $l['sortOrder'] ?? '' ?>:
                        <?= htmlspecialchars($l['lessonName']) ?>
                    </td>

                    <!-- Slug -->
                    <td><?= htmlspecialchars($l['slug']) ?></td>

                    <!-- STATUS -->
                    <td>

                        <?php if ($l['isActive']): ?>
                            <span class="badge-status badge-active">
                                Hiển thị
                            </span>

                        <?php else: ?>
                            <span class="badge-status badge-inactive">
                                Ẩn
                            </span>

                        <?php endif; ?>
                    </td>

                    <!-- ACTION -->
                    <td>

                        <div class="admin-actions">

                            <a href="/admin/lessons/edit/<?= $l['lessonId'] ?>"
                            class="action-btn btn-edit">
                                ✏
                            </a>

                            <button class="action-btn btn-delete"
                                    onclick="openDeleteLesson(<?= $l['lessonId'] ?>)">
                                🗑
                            </button>

                        </div>

                    </td>

                </tr>

            <?php endforeach; ?>

        <?php else: ?>

            <tr>
                <td colspan="8" class="text-center">
                    Không có dữ liệu
                </td>
            </tr>

        <?php endif; ?>

        </tbody>

    </table>

    <!-- Tab Pagination -->
    <?php if ($tabCount > 1): ?>
    <?php
        $baseUrl = '/admin/lessons'; 
    ?>
    <div class="tab-pagination-wrapper" id="lessonPagination">
        <div class="tab-pagination">
            <?php for ($i = 1; $i <= $tabCount; $i++): ?>
                <?php
                    $params = $_GET;
                    $params['tab'] = $i;
                    unset($params['success'], $params['error']);
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

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content modal-box">

            <p class="delete-confirm-text">Bạn chắc chắn muốn xóa bài học này?</p>

            <div class="text-center mt-3">

                <a href="#"
                   id="deleteLink"
                   class="admin-btn btn-danger">
                    Xóa
                </a>

                <button type="button"
                        class="admin-btn btn-add ml-2"
                        data-dismiss="modal">

                    Hủy

                </button>

            </div>

        </div>

    </div>

</div>

