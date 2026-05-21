<?php
/** @var array $chapters */
/** @var array $subjects */
/** @var array $grades */
/** @var int $tabCount */
/** @var int $currentTab */
/** @var string|null $flashError */
/** @var array $flashOld */

$errorType    = $flashError ?? null;
$oldId        = $flashOld['id']        ?? '';
$oldGradeId   = $flashOld['gradeId']   ?? '';
$oldName      = $flashOld['name']      ?? '';
$oldSubjectId = $flashOld['subjectId'] ?? '';
$oldSortOrder = $flashOld['sortOrder'] ?? '';
$oldSlug      = $flashOld['slug']      ?? '';
?>

<div class="admin-page">

    <!-- FLASH META -->
    <div id="flashMeta"
         data-error="<?= htmlspecialchars($errorType ?? '') ?>"
         data-old-id="<?= htmlspecialchars($oldId) ?>"
         data-old-grade-id="<?= htmlspecialchars($oldGradeId) ?>"
         data-old-name="<?= htmlspecialchars($oldName) ?>"
         data-old-subject-id="<?= htmlspecialchars($oldSubjectId) ?>"
         data-old-sort-order="<?= htmlspecialchars($oldSortOrder) ?>"
         data-old-slug="<?= htmlspecialchars($oldSlug) ?>"
         style="display:none">
    </div>

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <span>Chương học</span>
    </div>

    <div class="admin-title text-center">
        Danh sách Chương học
    </div>

    <!-- ALERT SUCCESS -->
    <?php if (isset($_GET['success'])): ?>
        <?php
            $msg = match($_GET['success']) {
                'created' => 'Thêm chương học thành công!',
                'updated' => 'Cập nhật chương học thành công!',
                'deleted' => 'Xóa chương học thành công!',
                default   => 'Thành công!'
            };
        ?>
        <div class="alert-success" id="autoAlert"><?= $msg ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_lessons'): ?>
        <div class="alert-error">
            Không thể xóa chương đang có bài học
        </div>
    <?php endif; ?>

    <!-- TOOLBAR -->
    <div class="admin-toolbar admin-toolbar-right">

        <input type="text"
            class="admin-search"
            id="chapterSearch"
            placeholder="Tìm kiếm chương học...">

        <button class="admin-btn btn-add mt-2" onclick="openAddChapter()">
            + Thêm
        </button>
    </div>

    <!-- TABLE -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Khối lớp</th>
                <th>Môn học</th>
                <th>Tên chương</th>
                <th>Slug</th>
                <th>Hành động</th>
            </tr>
        </thead>
        
        <tbody id="chapterTableBody">
            <?php foreach ($chapters as $c): ?>
            <tr>
                <td><?= $c['chapterId'] ?></td>
                <td><?= htmlspecialchars($c['gradeName']) ?></td>
                <td><?= htmlspecialchars($c['subjectName']) ?></td>
                <td>Chương <?= $c['sortOrder'] ?>: <?= htmlspecialchars($c['chapterName']) ?></td>
                <td><?= htmlspecialchars($c['slug']) ?></td>
                <td>
                    <div class="admin-actions">

                        <!-- data-* thay vì inline params -->
                        <button class="action-btn btn-edit"
                            data-id="<?= $c['chapterId'] ?>"
                            data-subject-id="<?= $c['subjectId'] ?>"
                            data-grade-id="<?= $c['gradeId'] ?>"
                            data-name="<?= htmlspecialchars($c['chapterName'], ENT_QUOTES) ?>"
                            data-slug="<?= htmlspecialchars($c['slug'], ENT_QUOTES) ?>"
                            data-sort-order="<?= $c['sortOrder'] ?>"
                            onclick="openEditChapter(this)">
                            ✏
                        </button>

                        <button class="action-btn btn-delete"
                            onclick="openDeleteChapter(<?= $c['chapterId'] ?>)">
                            🗑
                        </button>

                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TAB PAGINATION -->
    <?php if ($tabCount > 1): ?>
    <?php $baseUrl = '/admin/chapters'; ?>
    <div class="tab-pagination-wrapper" id="chapterPagination">
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

            <!-- form luôn rỗng, JS tự điền -->
            <form method="POST" action="/admin/chapters/store">

                <input type="hidden" name="id" id="id" value="">

                <input type="hidden" id="sortOrder" value="">

                <h4 id="chapterModalTitle" class="text-center mb-3">
                    Thêm chương học
                </h4>

                <!-- alert-error sẽ được JS inject -->

                <label>Khối lớp</label>
                <select id="gradeSelect" name="gradeId">
                    <option value="" disabled selected>Chọn khối lớp</option>
                    <?php foreach ($grades as $g): ?>
                        <option value="<?= $g['gradeId'] ?>">
                            <?= htmlspecialchars($g['gradeName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Môn học</label>
                <select name="subjectId" id="subjectSelect" required>
                    <option value="">Chọn môn học</option>
                </select>

                <label>Tên chương</label>
                <input type="text" name="name" id="name" required value="">

                <label>Vị trí hiển thị</label>
                <select name="positionValue" id="positionSelect">
                    <option value="first">Hiển thị đầu tiên</option>
                    <option value="last">Hiển thị cuối cùng</option>
                </select>

                <label>Slug</label>
                <input type="text" name="slug" id="slug" readonly value="">

                <div class="text-center modal-footer-custom">
                    <button type="submit" class="admin-btn btn-save">Lưu</button>
                    <button type="button" class="admin-btn btn-cancel"
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
            <p class="delete-confirm-text">Bạn chắc chắn muốn xóa chương học này?</p>
            <div class="text-center mt-3">
                <a href="#" id="deleteLink" class="admin-btn btn-danger">Xóa</a>
                <button type="button" class="admin-btn btn-add"
                        data-dismiss="modal">Hủy</button>
            </div>
        </div>
    </div>
</div>



<script>

function loadChapterPositions(selectedValue = 'last') {
    const subjectId = $('#subjectSelect').val();
    if (!subjectId) return;

    $.get('/admin/ajax/chapters-by-subject?subject_id=' + subjectId, function(data) {
        let html = `
            <option value="first">Hiển thị đầu tiên</option>
            <option value="last">Hiển thị cuối cùng</option>  
        `;
        if (Array.isArray(data) && data.length > 0) {
            html += `<optgroup label="Hiển thị sau...">`;
            $.each(data, function(i, chapter) {
                html += `
                    <option value="after-${chapter.chapterId}">
                        Chương ${chapter.sortOrder}: ${chapter.chapterName}
                    </option>
                `;
            });
            html += `</optgroup>`;
        }
        $('#positionSelect').html(html);
        $('#positionSelect').val(selectedValue);
    });
}

</script>