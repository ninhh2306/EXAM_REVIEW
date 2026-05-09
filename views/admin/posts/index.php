<?php
/** @var array $posts */
/** @var array $tabCount */
/** @var array $currentTab */
?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Bài viết</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Danh sách Bài viết
    </div>

    <!-- ALERT -->
    <?php
        $successMessages = [
            'created' => 'Thêm bài viết thành công!',
            'updated' => 'Cập nhật bài viết thành công!',
            'deleted' => 'Xóa bài viết thành công!'
        ];

    ?>

    <?php if (isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
        <div class="alert-success" id="autoAlert">
            <?= $successMessages[$_GET['success']] ?>
        </div>
    <?php endif; ?>


    <!-- TOOLBAR -->
    <div class="admin-toolbar admin-toolbar-right">

        <input type="text"
               class="admin-search"
               placeholder="Tìm kiếm...">

        <a href="/admin/posts/create"
           class="admin-btn btn-add no-underline">
            + Thêm
        </a>

    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="admin-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Danh mục</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th>Ngày tạo</th>
                    <th>Tác giả</th>
                    <th>Hành động</th>
                </tr>
            </thead>

            <tbody>

            <?php if (!empty($posts)): ?>

                <?php foreach ($posts as $p): ?>

                    <tr>

                        <td><?= $p['postId'] ?></td>

                        <td><?= htmlspecialchars($p['categoryName']) ?></td>

                        <td>
                            <?= htmlspecialchars($p['title']) ?>
                        </td>

                        <td><?= htmlspecialchars($p['slug']) ?></td>

                        <td>
                            <?= date('d/m/Y', strtotime($p['createdAt'])) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($p['authorName'] ?? 'Admin') ?>
                        </td>

                        <td>
                            <div class="admin-actions">

                                <a href="/admin/posts/edit/<?= $p['postId'] ?>"
                                class="action-btn btn-edit">
                                    ✏
                                </a>

                                <button class="action-btn btn-delete"
                                        onclick="openDeletePost(<?= $p['postId'] ?>)">
                                    🗑
                                </button>

                            </div>
                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="text-center">
                        Không có dữ liệu
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>
    </div>

    <!-- PAGINATION -->
    <?php if ($tabCount > 1): ?>
    <?php
        $baseUrl = '/admin/posts'; 
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

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content modal-box">

            <p class="delete-confirm-text">Bạn chắc chắn muốn xóa bài viết này?</p>

            <div class="text-center mt-3">

                <a href="#"
                   id="deleteLink"
                   class="admin-btn btn-danger">
                    Xóa
                </a>

                <button type="button"
                        class="admin-btn btn-cancel ml-2"
                        data-dismiss="modal">
                    Hủy
                </button>

            </div>

        </div>

    </div>

</div>