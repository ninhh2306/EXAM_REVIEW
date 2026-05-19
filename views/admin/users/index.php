<?php

/** @var array $tabCount */
/** @var array $currentTab */

?>

<div class="admin-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Người dùng</span>
    </div>

    <!-- Title -->
    <div class="admin-title text-center">
        Danh sách Người dùng
    </div>

    <?php
        $successMessages = [
            'created' => 'Thêm người dùng thành công!',
            'updated' => 'Cập nhật người dùng thành công!',
            'deleted' => 'Xóa người dùng thành công!'
        ];

        $errorMessages = [
            'exists' => 'Email đã tồn tại!',
            'password' => 'Mật khẩu phải có ít nhất 6 ký tự!',
            'delete_failed' => 'Xóa người dùng thất bại!'
        ];
    ?>

    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $successMessages[$_GET['success']] ?>
        </div>
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
    

    <!-- Toolbar -->
    <div class="admin-toolbar admin-toolbar-right">
        <input type="text"
                id="userSearch"
                class="admin-search"
                placeholder="Tìm kiếm người dùng...">

        <a href="/admin/users/create"
        class="admin-btn btn-add no-underline">
            + Thêm
        </a>

    </div>

    <!-- TABLE -->
    <table class="admin-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Người dùng</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody id="userTableBody">

        <?php if (!empty($users)): ?>

            <?php foreach ($users as $u): ?>

                <tr>
                    <td><?= $u['userId'] ?></td>

                    <td>
                        <div class="user-info">
                            <?php if (!empty($u['avatar'])): ?>
                                <img src="<?= $u['avatar'] ?>"
                                    alt="avatar"
                                    class="user-avatar">
                            <?php else: ?>

                                <div class="user-avatar user-avatar-default avatar-fallback">
                                    <?= strtoupper(
                                        mb_substr(
                                            trim($u['fullName']),
                                            0,
                                            1,
                                            'UTF-8'
                                        )
                                    ) ?>

                                </div>

                            <?php endif; ?>

                            <span class="user-name">
                                <?= htmlspecialchars($u['fullName']) ?>
                            </span>

                        </div>
                    </td>

                    <td>
                        <?= htmlspecialchars($u['email']) ?>
                    </td>

                    <td>

                        <?php if ($u['role'] === 'admin'): ?>
                            <span class="badge-role badge-admin">
                                Admin
                            </span>
                        <?php else: ?>
                            <span class="badge-role badge-user">
                                User
                            </span>
                        <?php endif; ?>

                    </td>

                    <td>
                        <?php if ((int)$u['status'] === 1): ?>

                            <span class="badge-status badge-active">
                                Hoạt động
                            </span>

                        <?php else: ?>

                            <span class="badge-status badge-inactive">
                                Đã khóa
                            </span>

                        <?php endif; ?>
                    </td>

                    <td>
                        <?= date('d/m/Y', strtotime($u['createdAt'])) ?>
                    </td>

                    <td>
                        <div class="admin-actions">
                            <a href="/admin/users/edit/<?= $u['userId'] ?>"
                            class="action-btn btn-edit">
                                ✏
                            </a>

                            <button class="action-btn btn-delete"
                                    onclick="openDeleteUser(<?= $u['userId'] ?>)">
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

        <div id="emptySearchResult"
            class="admin-empty-search"
            style="display:none;">
            Không tìm thấy người dùng
        </div>

    </table>

    <!-- Tab Pagination -->
    <?php if ($tabCount > 1): ?>

    <?php
        $baseUrl = '/admin/users';
    ?>

    <div class="tab-pagination-wrapper" id="userPagination">

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

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content modal-box">

            <p class="delete-confirm-text">
                Bạn chắc chắn muốn xóa người dùng này?
            </p>

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

