<?php

/** @var array $grades */

$old = $flashOld ?? [];

?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">

        <a href="/admin/dashboard">Dashboard</a>

        <span>›</span>

        <a href="/admin/exams">
            Đề ôn luyện
        </a>

        <span>›</span>

        <span>Thêm mới</span>

    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Thêm đề thi
    </div>

    <div class="card exam-create-card">

        <?php if (!empty($flashError)): ?>
            <div class="alert-error" id="autoAlert">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form
            method="POST"
            action="/admin/exams/store"
            id="examForm">

            <!-- ===================================================== -->
            <!-- TOP -->
            <!-- ===================================================== -->

            <div class="question-top-grid">

                <!-- TYPE -->
                <div class="form-group">
                    <label>Loại đề</label>

                    <select  name="examType" id="examType">
                        <option
                            value="lesson"
                            <?= ($old['examType'] ?? 'lesson') === 'lesson'
                                ? 'selected'
                                : '' ?>>
                            Đề bài học
                        </option>

                        <option
                            value="thpt"
                            <?= ($old['examType'] ?? '') === 'thpt'
                                ? 'selected'
                                : '' ?>>
                            Đề THPT
                        </option>
                    </select>
                </div>

                <!-- GENERATION -->
                <div class="form-group">

                    <label>Cách tạo đề</label>

                    <select name="generationType" id="generationType">
                        <option
                            value="manual"
                            <?= ($old['generationType'] ?? 'manual') === 'manual'
                                ? 'selected'
                                : '' ?>>
                            Thủ công
                        </option>

                        <option
                            value="auto"
                            <?= ($old['generationType'] ?? '') === 'auto'
                                ? 'selected'
                                : '' ?>>
                            Tự động
                        </option>

                    </select>

                </div>

                <!-- TITLE -->
                <div class="form-group">
                    <label>Tên đề thi</label>

                    <input
                        type="text"
                        name="title"
                        id="name"
                        required
                        value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                </div>

                <!-- SLUG -->
                <div class="form-group">
                    <label>Slug</label>

                    <input
                        type="text"
                        name="slug"
                        id="slug"
                        required
                        value="<?= htmlspecialchars($old['slug'] ?? '') ?>">
                </div>

                <!-- DURATION -->
                <div class="form-group">
                    <label>Thời gian (phút)</label>

                    <input
                        type="number"
                        name="duration"
                        required
                        value="<?= htmlspecialchars($old['duration'] ?? '') ?>">
                </div>

                <!-- TOTAL -->
                <div class="form-group">
                    <label>Tổng số câu</label>

                    <input
                        type="number"
                        name="totalQuestions"
                        id="totalQuestions"
                        min="1"
                        max="200"
                        required
                        value="<?= htmlspecialchars($old['totalQuestions'] ?? '') ?>">
                </div>

            </div>

            <!-- ===================================================== -->
            <!-- FILTER -->
            <!-- ===================================================== -->

            <div class="question-top-grid mt-4">

                <!-- GRADE -->
                <div class="form-group" id="gradeWrap">

                    <label>Khối lớp</label>

                    <select
                        name="gradeId"
                        id="gradeSelect"
                        data-saved="<?= $old['gradeId'] ?? '' ?>">

                        <option value="">
                            Chọn khối lớp
                        </option>

                        <?php foreach ($grades as $g): ?>

                            <option
                                value="<?= $g['gradeId'] ?>"
                                <?= ($old['gradeId'] ?? '') == $g['gradeId']
                                    ? 'selected'
                                    : '' ?>>

                                <?= htmlspecialchars($g['gradeName']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- SUBJECT -->
                <div class="form-group">

                    <label>Môn học</label>

                    <select
                        name="subjectId"
                        id="subjectSelect"
                        data-saved="<?= $old['subjectId'] ?? '' ?>">

                        <option value="">
                            Chọn môn học
                        </option>

                    </select>

                </div>

                <!-- CHAPTER -->
                <div
                    class="form-group"
                    id="chapterWrap">

                    <label>Chương học</label>

                    <select
                        name="chapterId"
                        id="chapterSelect"
                        data-saved="<?= $old['chapterId'] ?? '' ?>">

                        <option value="">
                            Chọn chương
                        </option>

                    </select>

                </div>

                <!-- LESSON -->
                <div
                    class="form-group"
                    id="lessonWrap">

                    <label>Bài học</label>

                    <select
                        name="lessonId"
                        id="lessonSelect"
                        data-saved="<?= $old['lessonId'] ?? '' ?>">

                        <option value="">
                            Chọn bài học
                        </option>

                    </select>

                </div>


                <!-- SORT ORDER -->
                <div class="form-group" id="sortOrderWrap">
                    <label>Vị trí hiển thị</label>

                    <select name="positionValue" id="positionType">
                        <option value="last">
                            Hiển thị cuối cùng
                        </option>

                        <option value="first">
                            Hiển thị đầu tiên
                        </option>
                    </select>
                </div>


                <!-- QUESTION ORDER -->
                <div class="form-group">

                    <label>Thứ tự câu hỏi</label>

                    <select name="questionOrder">
                        <option
                            value="manual"
                            <?= ($old['questionOrder'] ?? '') === 'manual'
                                ? 'selected'
                                : '' ?>>
                            Theo thứ tự
                        </option>

                        <option
                            value="random"
                            <?= ($old['questionOrder'] ?? '') === 'random'
                                ? 'selected'
                                : '' ?>>
                            Random
                        </option>
                    </select>

                </div>

                <!-- STATUS -->
                <div class="post-field lesson-status">
                    <label>Trạng thái</label>

                    <label class="switch">
                        <input
                            type="checkbox"
                            name="isActive"
                            value="1"
                            <?= ($old['isActive'] ?? 1) ? 'checked' : '' ?>>

                        <span class="slider round"></span>
                    </label>
                </div>

            </div>

            <!-- ===================================================== -->
            <!-- AUTO -->
            <!-- ===================================================== -->

            <div
                id="autoBox"
                class="mt-4"
                style="display:none;">

                <div class="form-group">

                    <label>
                        Mẫu đề
                        <small class="text-muted d-block mt-1">
                            (Nhận biết - Thông hiểu - Vận dụng)
                        </small>
                    </label>

                    <div class="preset-radio-group">

                        <label>
                            <input
                                type="radio"
                                name="presetLevel"
                                value="basic"
                                checked>
                            Cơ bản: 50 - 30 - 20
                        </label>

                        <label>
                            <input
                                type="radio"
                                name="presetLevel"
                                value="standard">

                            Tiêu chuẩn: 40 - 30 - 30
                        </label>

                        <label>
                            <input
                                type="radio"
                                name="presetLevel"
                                value="advanced">

                            Nâng cao: 30 - 40 - 30
                        </label>

                    </div>

                </div>

                <!-- hidden -->
                <input
                    type="hidden"
                    name="knowledgePercent"
                    id="knowledgePercent">

                <input
                    type="hidden"
                    name="comprehensionPercent"
                    id="comprehensionPercent">

                <input
                    type="hidden"
                    name="applicationPercent"
                    id="applicationPercent">

            </div>

            <!-- ===================================================== -->
            <!-- QUESTION AREA -->
            <!-- ===================================================== -->

            <div
                class="exam-question-layout mt-4"
                id="manualBox">

                <!-- LEFT -->
                <div class="exam-question-list">
                    <div class="exam-box-head">

                        <h3>
                            Danh sách câu hỏi
                        </h3>

                        <div class="exam-filter-bar">

                            <input
                                type="text"
                                id="questionSearch"
                                placeholder="Tìm câu hỏi...">

                            <select id="levelFilter">

                                <option value="">
                                    Tất cả mức độ
                                </option>

                                <option value="knowledge">
                                    Nhận biết
                                </option>

                                <option value="comprehension">
                                    Thông hiểu
                                </option>

                                <option value="application">
                                    Vận dụng
                                </option>

                            </select>

                        </div>

                    </div>

                    <div id="questionList">
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="exam-selected-list">
                    <h3>
                        Đã chọn
                        (<span id="selectedCount">0</span>)
                    </h3>

                    <div id="selectedQuestions">

                        <?php
                        if (!empty($old['questionIds'])):

                            foreach ($old['questionIds'] as $qid):
                        ?>
                            <input
                                type="hidden"
                                name="questionIds[]"
                                value="<?= $qid ?>">

                        <?php
                            endforeach;

                        endif;
                        ?>
                    </div>
                </div>
            </div>

            <!-- ACTION -->
            <div class="form-actions">
                <button class="admin-btn btn-save">
                    Lưu đề
                </button>

                <a
                    href="/admin/exams"
                    class="admin-btn btn-cancel">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>



<?php ob_start(); ?>
<script>

let selectedQuestions = [];


// =========================================================
// RESET SELECTED QUESTIONS
// =========================================================

function resetSelectedQuestions() {
    selectedQuestions = [];
    $('#selectedCount').text(0);
    $('#selectedQuestions').html('');
}


// =========================================================
// EXAM TYPE
// =========================================================

(function () {

    const examType      = document.getElementById('examType');
    const gradeWrap     = document.getElementById('gradeWrap');
    const chapterWrap   = document.getElementById('chapterWrap');
    const lessonWrap    = document.getElementById('lessonWrap');
    const sortOrderWrap = document.getElementById('sortOrderWrap');

    // Lưu grade bài học trước khi chuyển THPT
    let lessonGradeBackup =
        $('#gradeSelect').val() || '';

    function toggleExamType() {

        // =========================================
        // THPT
        // =========================================

        if (examType.value === 'thpt') {

            // backup grade hiện tại
            if ($('#gradeSelect').val() !== '3') {
                lessonGradeBackup =
                    $('#gradeSelect').val();
            }

            gradeWrap.style.display     = 'none';
            chapterWrap.style.display   = 'none';
            lessonWrap.style.display    = 'none';
            sortOrderWrap.style.display = 'none';

            // THPT luôn là lớp 12
            $('#gradeSelect').val('3');

            // load subjects lớp 12
            $.get('/admin/ajax/subjects?grade_id=3', function (data) {

                let html = '<option value="">Chọn môn học</option>';

                $.each(data, function (i, s) {

                    html += `
                        <option value="${s.subjectId}">
                            ${s.subjectName}
                        </option>
                    `;
                });

                $('#subjectSelect').html(html);

                // restore old subject nếu có
                const savedSubject =
                    $('#subjectSelect').data('saved');

                if (savedSubject) {
                    $('#subjectSelect').val(savedSubject);
                }
            });
        }

        // =========================================
        // LESSON
        // =========================================

        else {

            gradeWrap.style.display     = '';
            chapterWrap.style.display   = '';
            lessonWrap.style.display    = '';
            sortOrderWrap.style.display = '';

            // restore grade cũ
            if (lessonGradeBackup) {

                $('#gradeSelect').val(lessonGradeBackup);

                // load lại subjects theo grade cũ
                $.get('/admin/ajax/subjects?grade_id=' + lessonGradeBackup, function (data) {

                    let html = '<option value="">Chọn môn học</option>';

                    $.each(data, function (i, s) {

                        html += `
                            <option value="${s.subjectId}">
                                ${s.subjectName}
                            </option>
                        `;
                    });

                    $('#subjectSelect').html(html);

                    // restore old subject
                    const savedSubject =
                        $('#subjectSelect').data('saved');

                    if (savedSubject) {
                        $('#subjectSelect').val(savedSubject);
                    }
                });
            }
        }
    }

    toggleExamType();

    examType.addEventListener('change', function () {

        toggleExamType();

    });

})();


// =========================================================
// GENERATION TYPE
// =========================================================

(function () {

    const generationType = document.getElementById('generationType');
    const autoBox        = document.getElementById('autoBox');
    const manualBox      = document.getElementById('manualBox');

    function toggleGeneration() {
        if (generationType.value === 'auto') {
            if (autoBox) autoBox.style.display = '';
            manualBox.style.display = 'none';
        } else {
            if (autoBox) autoBox.style.display = 'none';
            manualBox.style.display = '';
        }
    }

    toggleGeneration();
    generationType.addEventListener('change', toggleGeneration);

})();


// =========================================================
// LOAD POSITION OPTIONS — GLOBAL
// =========================================================

function loadPositionOptions() {

    const lessonId = $('#lessonSelect').val();

    if (!lessonId) return;

    $.get('/admin/ajax/exams-by-lesson?lesson_id=' + lessonId, function (data) {

        let html = `
            <option value="last">Hiển thị cuối cùng</option>
            <option value="first">Hiển thị đầu tiên</option>
        `;

        if (Array.isArray(data) && data.length > 0) {
            html += `<optgroup label="Hiển thị sau...">`;
            $.each(data, function (i, exam) {
                html += `<option value="after-${exam.examId}">${exam.title}</option>`;
            });
            html += `</optgroup>`;
        }

        $('#positionType').html(html);

        // Restore giá trị cũ nếu có flash old
        const savedPosition = '<?= htmlspecialchars($old['positionValue'] ?? '') ?>';
        if (savedPosition) {
            $('#positionType').val(savedPosition);
        }
    });
}


// =========================================================
// LOAD QUESTIONS — GLOBAL
// =========================================================

function loadQuestions() {

    const examType = $('#examType').val();
    let url = '';

    if (examType === 'thpt') {
        const gradeId   = $('#gradeSelect').val();
        const subjectId = $('#subjectSelect').val();
        if (!gradeId || !subjectId) return;
        url = '/admin/ajax/thpt-questions?grade_id=' + gradeId + '&subject_id=' + subjectId;
    } else {
        const lessonId = $('#lessonSelect').val();
        if (!lessonId) return;
        url = '/admin/ajax/questions?lesson_id=' + lessonId;
    }

    $.get(url, function (questions) {
        renderQuestions(questions);
    });
}


// =========================================================
// RENDER QUESTIONS — GLOBAL
// =========================================================

function renderQuestions(questions) {

    if (!questions.length) {
        $('#questionList').html('<div class="empty-data">Chưa có câu hỏi</div>');
        return;
    }

    let html = '';

    questions.forEach(function (q) {

        const questionId = String(q.questionId);
        const disabled   = selectedQuestions.includes(questionId);

        if (disabled) {
            $('#selected-' + questionId + ' .selected-content').html(q.content);
        }

        let levelColorClass = 'level-knowledge';
        let levelTextVi     = 'Nhận biết';
        const dbLevel       = q.level ? q.level.toLowerCase() : '';

        if (dbLevel.includes('comprehension') || dbLevel.includes('thông hiểu')) {
            levelColorClass = 'level-comprehension';
            levelTextVi     = 'Thông hiểu';
        } else if (dbLevel.includes('application') || dbLevel.includes('vận dụng')) {
            levelColorClass = 'level-application';
            levelTextVi     = 'Vận dụng';
        }

        html += `
            <div
                class="exam-question-item ${disabled ? 'selected' : ''}"
                id="question-${questionId}"
                data-level="${dbLevel}">
                <div class="exam-question-top">
                    <div class="exam-question-level ${levelColorClass}">
                        ${levelTextVi}
                    </div>
                </div>
                <div class="exam-question-content">
                    ${q.content}
                </div>
                <div class="exam-question-action">
                    <button
                        type="button"
                        ${disabled ? 'disabled' : ''}
                        onclick="addQuestion('${questionId}', \`${q.content.replace(/`/g, '\\`')}\`)">
                        Chọn
                    </button>
                </div>
            </div>
        `;
    });

    $('#questionList').html(html);
}


// =========================================================
// ADD QUESTION — GLOBAL
// =========================================================

function addQuestion(id, content) {

    id = String(id);
    if (selectedQuestions.includes(id)) return;

    const total = parseInt($('#totalQuestions').val());

    if (selectedQuestions.length >= total) {
        alert('Đã đủ số lượng câu hỏi');
        return;
    }

    selectedQuestions.push(id);
    $('#selectedCount').text(selectedQuestions.length);

    $('#selectedQuestions').append(`
        <div class="selected-question-item" id="selected-${id}">
            <input type="hidden" name="questionIds[]" value="${id}">
            <div class="selected-question-body">
                <div class="selected-content">${content}</div>
                <button type="button" class="selected-question-remove" onclick="removeQuestion('${id}')">
                    Xóa
                </button>
            </div>
        </div>
    `);

    $('#question-' + id).addClass('selected');
    $('#question-' + id + ' button').prop('disabled', true);
}


// =========================================================
// REMOVE QUESTION — GLOBAL
// =========================================================

function removeQuestion(id) {

    id = String(id);
    selectedQuestions = selectedQuestions.filter(q => q !== id);
    $('#selectedCount').text(selectedQuestions.length);
    $('#selected-' + id).remove();
    $('#question-' + id).removeClass('selected');
    $('#question-' + id + ' button').prop('disabled', false);
}


// =========================================================
// jQuery READY 
// =========================================================

$(function () {

    // PRESET LEVEL
    $('input[name="presetLevel"]').on('change', function () {
        const value = $(this).val();
        if (value === 'basic') {
            $('#knowledgePercent').val(50);
            $('#comprehensionPercent').val(30);
            $('#applicationPercent').val(20);
        } else if (value === 'standard') {
            $('#knowledgePercent').val(40);
            $('#comprehensionPercent').val(30);
            $('#applicationPercent').val(30);
        } else if (value === 'advanced') {
            $('#knowledgePercent').val(30);
            $('#comprehensionPercent').val(40);
            $('#applicationPercent').val(30);
        }
    });
    $('input[name="presetLevel"]:checked').trigger('change');


    // GRADE → SUBJECT
    $('#gradeSelect').on('change', function () {

        const gradeId = $(this).val();

        $('#subjectSelect').html('<option value="">Chọn môn học</option>');
        $('#chapterSelect').html('<option value="">Chọn chương</option>');
        $('#lessonSelect').html('<option value="">Chọn bài học</option>');

        if (!gradeId) return;

        $.get('/admin/ajax/subjects?grade_id=' + gradeId, function (data) {

            let html = '<option value="">Chọn môn học</option>';

            $.each(data, function (i, s) {

                html += `
                    <option value="${s.subjectId}">
                        ${s.subjectName}
                    </option>
                `;
            });

            $('#subjectSelect').html(html);
        });

    });


    // SUBJECT → CHAPTER
    $('#subjectSelect').on('change', function () {

        const subjectId = $(this).val();

        $('#chapterSelect').html('<option value="">Chọn chương</option>');
        $('#lessonSelect').html('<option value="">Chọn bài học</option>');

        if (!subjectId) return;

        // THPT → load câu hỏi luôn
        if ($('#examType').val() === 'thpt') {

            loadQuestions();
            return;
        }

        $.get('/admin/ajax/chapters?subject_id=' + subjectId, function (data) {

            let html = '<option value="">Chọn chương</option>';

            $.each(data, function (i, c) {

                html += `
                    <option value="${c.chapterId}">
                        Chương ${c.sortOrder}: ${c.chapterName}
                    </option>
                `;
            });

            $('#chapterSelect').html(html);
        });

    });


    // CHAPTER → LESSON
    $('#chapterSelect').on('change', function () {

        const chapterId = $(this).val();

        $('#lessonSelect').html('<option value="">Chọn bài học</option>');

        if (!chapterId) return;

        $.get('/admin/ajax/lessons?chapter_id=' + chapterId, function (data) {

            let html = '<option value="">Chọn bài học</option>';

            $.each(data, function (i, l) {

                html += `
                    <option value="${l.lessonId}">
                        Bài ${l.sortOrder}: ${l.lessonName}
                    </option>
                `;
            });

            $('#lessonSelect').html(html);
        });

    });


    // LESSON → QUESTIONS + POSITION
    $('#lessonSelect').on('change', function () {

        loadQuestions();
        loadPositionOptions();

    });


    // SEARCH + FILTER
    $('#questionSearch, #levelFilter').on('input change', function () {
        const keyword = $('#questionSearch').val().toLowerCase();
        const level   = $('#levelFilter').val().toLowerCase();
        $('.exam-question-item').each(function () {
            const text      = $(this).text().toLowerCase();
            const itemLevel = ($(this).data('level') || '').toLowerCase();
            $(this).toggle(
                text.includes(keyword) && (!level || itemLevel.includes(level))
            );
        });
    });


    // VALIDATE SUBMIT
    $('#examForm').on('submit', function (e) {
        const total = parseInt($('#totalQuestions').val());
        if (total > 200) {
            e.preventDefault();
            alert('Không được vượt quá 200 câu hỏi');
            return;
        }
        if (total <= 0 || isNaN(total)) {
            e.preventDefault();
            alert('Tổng số câu phải lớn hơn 0');
            return;
        }
        if ($('#generationType').val() === 'manual' && selectedQuestions.length !== total) {
            e.preventDefault();
            alert(`Bạn cần chọn đúng ${total} câu hỏi`);
        }
    });

});


// =========================================================
// RESTORE FLASH OLD
// =========================================================

(function restoreExamFlash() {

    const savedGradeId   = '<?= htmlspecialchars($old['gradeId']   ?? '') ?>';
    const savedSubjectId = '<?= htmlspecialchars($old['subjectId'] ?? '') ?>';
    const savedChapterId = '<?= htmlspecialchars($old['chapterId'] ?? '') ?>';
    const savedLessonId  = '<?= htmlspecialchars($old['lessonId']  ?? '') ?>';
    const examType       = '<?= htmlspecialchars($old['examType']  ?? 'lesson') ?>';

    // Restore câu hỏi đã chọn
    $('input[name="questionIds[]"]').each(function () {
        const id = String($(this).val());
        selectedQuestions.push(id);
        $('#selectedQuestions').append(`
            <div class="selected-question-item" id="selected-${id}">
                ${this.outerHTML}
                <div class="selected-question-body">
                    <div class="selected-content">Đang tải câu hỏi...</div>
                    <button type="button" class="selected-question-remove" onclick="removeQuestion('${id}')">
                        Xóa
                    </button>
                </div>
            </div>
        `);
        $(this).remove();
    });

    $('#selectedCount').text(selectedQuestions.length);

    if (!savedGradeId) return;

    $('#gradeSelect').val(savedGradeId);

    $.get('/admin/ajax/subjects?grade_id=' + savedGradeId, function (subjects) {

        let html = '<option value="">Chọn môn học</option>';
        subjects.forEach(s => {
            html += `<option value="${s.subjectId}">${s.subjectName}</option>`;
        });
        $('#subjectSelect').html(html);
        $('#subjectSelect').val(savedSubjectId);

        if (examType === 'thpt') {
            loadQuestions();
            return;
        }

        if (!savedSubjectId || !savedChapterId) return;

        $.get('/admin/ajax/chapters?subject_id=' + savedSubjectId, function (chapters) {

            let html2 = '<option value="">Chọn chương</option>';
            chapters.forEach(c => {
                html2 += `<option value="${c.chapterId}">Chương ${c.sortOrder}: ${c.chapterName}</option>`;
            });
            $('#chapterSelect').html(html2);
            $('#chapterSelect').val(savedChapterId);

            if (!savedLessonId) return;

            $.get('/admin/ajax/lessons?chapter_id=' + savedChapterId, function (lessons) {

                let html3 = '<option value="">Chọn bài học</option>';
                lessons.forEach(l => {
                    html3 += `<option value="${l.lessonId}">Bài ${l.sortOrder}: ${l.lessonName}</option>`;
                });
                $('#lessonSelect').html(html3);
                $('#lessonSelect').val(savedLessonId);

                loadQuestions();
                loadPositionOptions(); // ← thêm dòng này so với bản cũ
            });
        });
    });

})();

</script>

<?php $pageScripts = ob_get_clean(); ?>