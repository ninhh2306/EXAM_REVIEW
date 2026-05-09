<?php

/** @var array $subjects */
/** @var array $tabCount */
/** @var array $currentTab */

?>

<div class="admin-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Môn học</span>
    </div>

    <!-- Title -->
    <div class="admin-title text-center">
        Danh sách Môn học
    </div>

    <!-- THÔNG BÁO -->
    <?php
        $successMessages = [
            'created' => 'Thêm môn học thành công!',
            'updated' => 'Cập nhật môn học thành công!',
            'deleted' => 'Xóa môn học thành công!'
        ];

        $errorMessages = [
            'exists' => 'Tên hoặc slug môn học đã tồn tại!',
            'delete_failed' => 'Xóa môn học thất bại!'
        ];
    ?>

    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $successMessages[$_GET['success']] ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-error" id="autoAlert">
            <?= $errorMessages[$_GET['error']] ?? 'Đã xảy ra lỗi!' ?>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="admin-toolbar admin-toolbar-right">
        <input type="text" class="admin-search" placeholder="Tìm kiếm...">

        <a href="/admin/subjects/create" class="admin-btn btn-add mt-2">
            + Thêm
        </a>
    </div>

    <!-- Table -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khối lớp</th>
                <th>Tên môn</th>
                <th>Slug</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($subjects)): ?>
                <?php foreach ($subjects as $s): ?>
                <tr>
                    <td><?= $s['subjectId'] ?></td>
                    <td><?= $s['gradeName'] ?></td>
                    <td><?= $s['subjectName'] ?></td>
                    <td><?= $s['slug'] ?></td>
                    <td>
                        <div class="admin-actions">
                            <a href="/admin/subjects/edit/<?= $s['subjectId'] ?>"
                               class="action-btn btn-edit">✏</a>

                            <button class="action-btn btn-delete"
                                    onclick="openDeleteSubject(<?= $s['subjectId'] ?>)">
                                🗑
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tab Pagination -->
    <?php if ($tabCount > 1): ?>
    <?php
        $baseUrl = '/admin/subjects'; 
    ?>
    <div class="tab-pagination-wrapper">
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

<!-- MODAL XÓA -->
<div class="modal fade" id="deleteModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modal-box">

        <p class="delete-confirm-text">Bạn chắc chắn muốn xóa môn học này?</p>

        <div class="text-center mt-3">
            <a href="#" id="deleteLink" class="admin-btn btn-danger">Xóa</a>
            <button type="button" class="admin-btn btn-add ml-2" data-dismiss="modal">Hủy</button>
        </div>

    </div>
  </div>
</div>

