<?php
/** @var array $grades */
/** @var string|null $flashError */
/** @var array $flashOld */

$errorMessages = [
    'sort_exists' => 'Số thứ tự này đã có bài học, vui lòng chọn lại!',
    'slug_exists' => 'Slug đã tồn tại trong chương học này!',
    'name_exists' => 'Tên bài học đã tồn tại trong chương học này!',
];

$old = $flashOld ?? [];
?>

<div class="admin-page">

    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/lessons">Bài học</a>
        <span>›</span>
        <span>Thêm mới</span>
    </div>

    <div class="admin-title text-center">
        Thêm bài học
    </div>

    <div class="card lesson-card">

        <?php if ($flashError): ?>
            <div class="alert-error mb-3">
                <?= $errorMessages[$flashError] ?? 'Có lỗi xảy ra!' ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/lessons/store">

            <div class="lesson-row-4">

                <div class="lesson-group">
                    <label>Khối lớp</label>
                    <select name="gradeId"
                            id="gradeSelect"
                            data-saved="<?= $old['gradeId'] ?? '' ?>"
                            required>
                        <option value="">Chọn khối lớp</option>
                        <?php foreach ($grades as $g): ?>
                            <option value="<?= $g['gradeId'] ?>"
                                <?= ($old['gradeId'] ?? '') == $g['gradeId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['gradeName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Môn học</label>
                    <select name="subjectId"
                            id="subjectSelect"
                            data-saved="<?= $old['subjectId'] ?? '' ?>"
                            required>
                        <option value="">Chọn môn học</option>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Chương học</label>
                    <select name="chapterId"
                            id="chapterSelect"
                            data-saved="<?= $old['chapterId'] ?? '' ?>"
                            required>
                        <option value="">Chọn chương học</option>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Số thứ tự bài học</label>
                    <input type="number"
                           name="sortOrder"
                           id="lesson_sort"
                           min="1"
                           value="<?= htmlspecialchars($old['sortOrder'] ?? '') ?>"
                           required>
                </div>

            </div>

            <div class="lesson-row-name">

                <div class="lesson-group lesson-name">
                    <label>Tên bài học</label>
                    <input type="text"
                           name="lessonName"
                           id="lesson_name"
                           value="<?= htmlspecialchars($old['lessonName'] ?? '') ?>"
                           required>
                </div>

                <div class="lesson-group lesson-slug">
                    <label>Slug</label>
                    <input type="text"
                           name="slug"
                           id="lesson_slug"
                           value="<?= htmlspecialchars($old['slug'] ?? '') ?>">
                </div>

            </div>

            <div class="lesson-group lesson-editor">
                <label>Nội dung bài học</label>
                <textarea name="content"
                          id="lessonEditor"><?= htmlspecialchars($old['content'] ?? '') ?></textarea>
            </div>

            <div class="lesson-actions">
                <button type="submit" class="admin-btn btn-save">Lưu</button>
                <a href="/admin/lessons" class="admin-btn btn-cancel">Hủy</a>
            </div>

        </form>

    </div>

</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>