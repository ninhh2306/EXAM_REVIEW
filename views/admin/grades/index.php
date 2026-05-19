<?php
/** @var array $grades */
/** @var int $tabCount */
/** @var int $currentTab */
/** @var string|null $flashError */
/** @var array $flashOld */

$errorType = $flashError ?? null;
$oldId     = $flashOld['id']   ?? '';
$oldName   = $flashOld['name'] ?? '';
$oldSlug   = $flashOld['slug'] ?? '';
?>

<div class="admin-page">

    <!-- FLASH META -->
    <div id="flashMeta"
         data-error="<?= htmlspecialchars($errorType ?? '') ?>"
         data-old-id="<?= htmlspecialchars($oldId) ?>"
         data-old-name="<?= htmlspecialchars($oldName) ?>"
         data-old-slug="<?= htmlspecialchars($oldSlug) ?>"
         style="display:none">
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Khối lớp</span>
    </div>

    <div class="admin-title text-center">
        Danh sách Khối lớp
    </div>

    <!-- ALERT SUCCESS -->
    <?php if (isset($_GET['success'])): ?>
        <?php
            $msg = match($_GET['success']) {
                'created' => 'Thêm khối lớp thành công!',
                'updated' => 'Cập nhật khối lớp thành công!',
                'deleted' => 'Xóa khối lớp thành công!',
                default   => 'Thao tác thành công!'
            };
        ?>
        <div class="alert-success" id="autoAlert"><?= $msg ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <?php
            $gradeErrors = [
                'has_subjects' => 'Không thể xóa khối lớp đang có môn học!',
                'has_exams'    => 'Không thể xóa khối lớp đang có đề thi!',
            ];
        ?>
        <div class="alert-error">
            <?= $gradeErrors[$_GET['error']] ?? 'Không thể xóa!' ?>
        </div>
    <?php endif; ?>

    <!-- TOOLBAR -->
    <div class="admin-toolbar admin-toolbar-right">

        <input type="text"
            class="admin-search"
            id="gradeSearch"
            placeholder="Tìm kiếm khối lớp...">

        <!-- onclick thay vì data-toggle để kiểm soát qua JS -->
        <button class="admin-btn btn-add mt-2" onclick="openAddGrade()">
            + Thêm
        </button>
    </div>

    <!-- TABLE -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên khối lớp</th>
                <th>Slug</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody id="gradeTableBody">
            <?php if (!empty($grades)): ?>
                <?php foreach ($grades as $g): ?>
                <tr>
                    <td><?= $g['gradeId'] ?></td>
                    <td><?= htmlspecialchars($g['gradeName']) ?></td>
                    <td><?= htmlspecialchars($g['slug']) ?></td>
                    <td>
                        <div class="admin-actions">

                            <!-- data-* thay vì inline params để tránh lỗi ký tự đặc biệt -->
                            <button class="action-btn btn-edit"
                                data-id="<?= $g['gradeId'] ?>"
                                data-name="<?= htmlspecialchars($g['gradeName'], ENT_QUOTES) ?>"
                                data-slug="<?= htmlspecialchars($g['slug'], ENT_QUOTES) ?>"
                                onclick="openEditGrade(this)">
                                ✏
                            </button>

                            <button class="action-btn btn-delete"
                                onclick="openDeleteGrade(<?= $g['gradeId'] ?>)">
                                🗑
                            </button>

                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TAB PAGINATION -->
    <?php if ($tabCount > 1): ?>
    <?php $baseUrl = '/admin/grades'; ?>
    <div class="tab-pagination-wrapper" id="gradePagination">
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

<!-- MODAL THÊM / CẬP NHẬT -->
<div class="modal fade" id="formModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-box">

            <!-- form luôn rỗng, JS tự điền khi cần -->
            <form method="POST" action="/admin/grades/store">
                <input type="hidden" name="id" id="id" value="">

                <h4 id="gradeModalTitle" class="text-center mb-3">
                    Thêm khối lớp
                </h4>

                <label>Tên khối lớp</label>
                <input type="text" name="name" id="name" required value="">

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

<!-- MODAL XÓA -->
<div class="modal fade" id="deleteModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-box">

            <p class="delete-confirm-text">Bạn chắc chắn muốn xóa khối lớp này?</p>

            <div class="text-center mt-3">
                <a href="#" id="deleteLink" class="admin-btn btn-danger">Xóa</a>
                <button type="button" class="admin-btn btn-add ml-2"
                        data-dismiss="modal">Hủy</button>
            </div>

        </div>
    </div>
</div>