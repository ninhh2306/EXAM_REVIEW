<?php
/** @var array $lesson */
/** @var array $grades */
/** @var array $subjects */
/** @var array $chapters */
/** @var string|null $flashError */
/** @var array $flashOld */

$errorMessages = [
    'sort_exists' => 'Số thứ tự này đã có bài học, vui lòng chọn lại!',
    'slug_exists' => 'Slug đã tồn tại trong chương học này!',
    'name_exists' => 'Tên bài học đã tồn tại trong chương học này!',
];

// Ưu tiên flash old nếu vừa submit lỗi, không thì dùng data từ DB
$d = !empty($flashOld) ? $flashOld : $lesson;
?>

<div class="admin-page">

    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/lessons">Bài học</a>
        <span>›</span>
        <span>Cập nhật</span>
    </div>

    <div class="admin-title text-center">
        Cập nhật bài học
    </div>

    <div class="card lesson-card">

        <?php if ($flashError): ?>
            <div class="alert-error mb-3">
                <?= $errorMessages[$flashError] ?? 'Có lỗi xảy ra!' ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/lessons/update">

            <input type="hidden" name="id"      value="<?= $lesson['lessonId'] ?>">
            <input type="hidden" name="gradeId" value="<?= $d['gradeId'] ?? $lesson['gradeId'] ?>">

            <div class="lesson-row-4">

                <div class="lesson-group">
                    <label>Khối lớp</label>
                    <select name="gradeId"
                            id="gradeSelect"
                            required>
                        <option value="">Chọn khối lớp</option>
                        <?php foreach ($grades as $g): ?>
                            <option value="<?= $g['gradeId'] ?>"
                                <?= $g['gradeId'] == ($d['gradeId'] ?? $lesson['gradeId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['gradeName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Môn học</label>
                    <select name="subjectId"
                            id="subjectSelect"
                            required>
                        <option value="">Chọn môn học</option>
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?= $s['subjectId'] ?>"
                                <?= $s['subjectId'] == ($d['subjectId'] ?? $lesson['subjectId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['subjectName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Chương học</label>
                    <select name="chapterId"
                            id="chapterSelect"
                            required>
                        <option value="">Chọn chương học</option>
                        <?php foreach ($chapters as $c): ?>
                            <option value="<?= $c['chapterId'] ?>"
                                <?= $c['chapterId'] == ($d['chapterId'] ?? $lesson['chapterId']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['chapterName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Số thứ tự <small style="color:#999">(để sắp xếp)</small></label>
                    <input type="number"
                           name="sortOrder"
                           id="lesson_sort"
                           min="1"
                           value="<?= htmlspecialchars($d['sortOrder'] ?? $lesson['sortOrder']) ?>"
                           required>
                </div>

            </div>

            <div class="lesson-row-name">

                <div class="lesson-group lesson-name">
                    <label>Tên bài học</label>
                    <input type="text"
                           name="lessonName"
                           id="lesson_name"
                           value="<?= htmlspecialchars($d['lessonName'] ?? $lesson['lessonName']) ?>"
                           required>
                </div>

                <div class="lesson-group lesson-slug">
                    <label>Slug <small style="color:#999">(có thể sửa — cẩn thận làm hỏng link cũ)</small></label>
                    <input type="text"
                           name="slug"
                           id="lesson_slug"
                           value="<?= htmlspecialchars($d['slug'] ?? $lesson['slug']) ?>">
                </div>

            </div>

            <div class="lesson-group lesson-editor">
                <label>Nội dung bài học</label>
                <textarea name="content"
                          id="lessonEditor"><?= htmlspecialchars($d['content'] ?? $lesson['content']) ?></textarea>
            </div>

            <div class="lesson-actions">
                <button type="submit" class="admin-btn btn-save">Cập nhật</button>
                <a href="/admin/lessons" class="admin-btn btn-cancel">Hủy</a>
            </div>

        </form>

    </div>

</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>