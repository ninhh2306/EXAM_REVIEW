<?php 
/** @var array $categories */ 
/** @var array $tabCount */
/** @var array $currentTab */

/** @var string|null $flashError */
/** @var array $flashOld */

$errorType      = $flashError ?? null;

$oldId          = $flashOld['id'] ?? '';
$oldName        = $flashOld['name'] ?? '';
$oldDescription = $flashOld['description'] ?? '';
$oldSlug        = $flashOld['slug'] ?? '';
?>

<div class="admin-page">

    <!-- FLASH META for JS -->
    <div id="flashMeta"
        data-error="<?= htmlspecialchars($errorType ?? '') ?>"
        data-old-id="<?= htmlspecialchars($oldId) ?>"
        data-old-name="<?= htmlspecialchars($oldName) ?>"
        data-old-description="<?= htmlspecialchars($oldDescription) ?>"
        data-old-slug="<?= htmlspecialchars($oldSlug) ?>"
        style="display:none">
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Danh mục</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Danh sách Danh mục
    </div>

    <!-- ALERT -->
    <?php if (isset($_GET['success'])): ?>
        <?php
            $msg = match($_GET['success']) {
                'created' => 'Thêm danh mục thành công!',
                'updated' => 'Cập nhật danh mục thành công!',
                'deleted' => 'Xóa danh mục thành công!',
                'default' => 'Thao tác thành công!'
            };
        ?>

        <div class="alert-success" id="autoAlert">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_posts'): ?>
        <div class="alert-error">
            Không thể xóa danh mục đang có bài viết!
        </div>
    <?php endif; ?>


    <!-- TOOLBAR -->
    <div class="admin-toolbar admin-toolbar-right">

        <input type="text"
                class="admin-search"
                id="categorySearch"
                placeholder="Tìm kiếm danh mục...">

        <button class="admin-btn btn-add mt-2" onclick="openAddCategory()">
            + Thêm
        </button>

    </div>

    <!-- TABLE -->
    <table class="admin-table">

        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Slug</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?= $c['categoryId'] ?></td>

                        <td>
                            <?= htmlspecialchars($c['categoryName']) ?>
                        </td>

                        <td class="category-desc">
                            <?= htmlspecialchars($c['description'] ?? '') ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($c['slug']) ?>
                        </td>

                        <td>
                            <div class="admin-actions">
                                <button class="action-btn btn-edit"
                                    data-id="<?= $c['categoryId'] ?>"
                                    data-name="<?= htmlspecialchars($c['categoryName'], ENT_QUOTES) ?>"
                                    data-description="<?= htmlspecialchars($c['description'] ?? '', ENT_QUOTES) ?>"
                                    data-slug="<?= htmlspecialchars($c['slug'], ENT_QUOTES) ?>"
                                    onclick="openEditCategory(this)">
                                    ✏
                                </button>

                                <button class="action-btn btn-delete"
                                    onclick="openDeleteCategory(<?= $c['categoryId'] ?>)">
                                    🗑
                                </button>
                            </div>
                        </td>
                    </tr>

                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">
                        Không có dữ liệu
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>

    </table>

    <!-- Tab Pagination -->
    <?php if ($tabCount > 1): ?>
    <?php
        $baseUrl = '/admin/categories'; // đổi theo từng trang
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

<!-- MODAL -->
<div class="modal fade" id="formModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-box">

            <form method="POST" action="/admin/categories/store">

                <input type="hidden" name="id" id="id" value="">

                <h4 id="categoryModalTitle" class="text-center mb-3">
                    Thêm danh mục
                </h4>

                <label>Tên danh mục</label>
                <input type="text" name="name" id="name" required value="">

                <label>Mô tả</label>
                <textarea name="description" id="description"
                          class="category-description-input"></textarea>

                <label>Slug</label>
                <input type="text" name="slug" id="slug" value="">

                <div class="text-center modal-footer-custom">
                    <button type="submit" class="admin-btn btn-save">Lưu</button>
                    <button type="button" class="admin-btn btn-cancel ml-2"
                            data-dismiss="modal">Hủy</button>
                </div>

            </form>
        </div>
    </div>
</div>


<!-- DELETE -->
<div class="modal fade" id="deleteModal">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-box">
            <p class="delete-confirm-text">Bạn chắc chắn muốn xóa danh mục này?</p>

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



<script>

const searchInput = document.querySelector('.admin-search');
const pagination  = document.querySelector('.tab-pagination-wrapper');

let timeout = null;

searchInput.addEventListener('input', function () {

    clearTimeout(timeout);

    timeout = setTimeout(() => {

        const keyword = this.value.trim();

        // ẨN pagination khi đang search
        if (keyword !== '') {
            pagination?.classList.add('d-none');
        } else {
            pagination?.classList.remove('d-none');
        }

        fetch(`/admin/categories/search?keyword=${encodeURIComponent(keyword)}`)
            .then(res => res.text())
            .then(html => {

                document.querySelector('.admin-table tbody').innerHTML = html;

            });

    }, 300);

});

</script>

