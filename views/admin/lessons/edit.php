<?php
/** @var array $lesson */
/** @var array $grades */
/** @var array $subjects */
/** @var array $chapters */
/** @var string|null $flashError */
/** @var array $flashOld */

$errorMessages = [
    'slug_exists'    => 'Slug đã tồn tại trong chương học này!',
    'name_exists'    => 'Tên bài học đã tồn tại trong chương học này!',
    'empty_content'  => 'Nội dung bài học không được để trống!',
    'empty_fields'   => 'Vui lòng điền đầy đủ thông tin!',
];

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
                <?= htmlspecialchars($errorMessages[$flashError] ?? $flashError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/lessons/update">

            <input type="hidden" name="id" value="<?= $lesson['lessonId'] ?>">

            <div class="lesson-row-4">

                <div class="lesson-group">
                    <label>Khối lớp</label>
                    <select name="gradeId" id="gradeSelect" required>
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
                    <select name="subjectId" id="subjectSelect" required>
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
                    <select name="chapterId" id="chapterSelect" required
                            data-current-lesson-id="<?= (int)$lesson['lessonId'] ?>">
                        <option value="">Chọn chương học</option>
                        <?php foreach ($chapters as $c): ?>
                            <option value="<?= $c['chapterId'] ?>"
                                <?= $c['chapterId'] == ($d['chapterId'] ?? $lesson['chapterId']) ? 'selected' : '' ?>>
                                Chương <?= $c['sortOrder'] ?>: <?= htmlspecialchars($c['chapterName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="lesson-group">
                    <label>Vị trí hiển thị</label>
                    <select name="positionValue" id="lessonPositionSelect">
                        <option value="first">Hiển thị đầu tiên</option>
                        <option value="last">Hiển thị cuối cùng</option>
                    </select>
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
                    <label>Slug</label>
                    <input type="text"
                           name="slug"
                           id="lesson_slug"
                           value="<?= htmlspecialchars($d['slug'] ?? $lesson['slug']) ?>">
                </div>

            </div>

            <div class="lesson-group lesson-status">
                <label>Trạng thái</label>
                <label class="switch">
                    <input type="checkbox"
                           name="isActive"
                           value="1"
                           <?= (int)($d['isActive'] ?? $lesson['isActive']) === 1 ? 'checked' : '' ?>>
                    <span class="slider round"></span>
                </label>
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



<?php ob_start(); ?>
<script>
$(document).ready(function () {

    const currentId        = <?= (int)$lesson['lessonId'] ?>;
    const currentSortOrder = <?= (int)($d['sortOrder'] ?? $lesson['sortOrder']) ?>;
    const currentChapterId = <?= (int)($d['chapterId'] ?? $lesson['chapterId']) ?>;

    if (currentChapterId) {
        loadLessonPositionsForEdit(currentChapterId, currentId, currentSortOrder);
    }

    $('#chapterSelect').on('change', function () {
        const chapterId = $(this).val();
        if (chapterId) {
            loadLessonPositionsForEdit(chapterId, currentId, null);
        }
    });
});


function loadLessonPositionsForEdit(chapterId, currentId, currentSortOrder) {
    $.get('/admin/ajax/lessons-by-chapter?chapter_id=' + chapterId, function (data) {

        let html = `
            <option value="first">Hiển thị đầu tiên</option>
            <option value="last">Hiển thị cuối cùng</option> 
        `;

        let selectedVal = 'last';

        if (Array.isArray(data) && data.length > 0) {

            html += `<optgroup label="Hiển thị sau...">`;

            $.each(data, function (i, lesson) {

                if (lesson.lessonId == currentId) return;

                html += `
                    <option value="after-${lesson.lessonId}">
                        Bài ${lesson.sortOrder}: ${lesson.lessonName}
                    </option>
                `;

                if (
                    currentSortOrder !== null &&
                    parseInt(lesson.sortOrder) === parseInt(currentSortOrder) - 1
                ) {
                    selectedVal = 'after-' + lesson.lessonId;
                }

            });

            html += `</optgroup>`;

            // fallback
            if (currentSortOrder == 1) {
                selectedVal = 'first';
            }
        }

        $('#lessonPositionSelect').html(html);
        $('#lessonPositionSelect').val(selectedVal);
    });
}
</script>
<?php $pageScripts = ob_get_clean(); ?>