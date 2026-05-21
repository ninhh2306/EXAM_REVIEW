<?php

/** @var array $grades */
/** @var string|null $flashError */

$old = $flashOld ?? [];

?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/questions">
            Câu hỏi
        </a>
        <span>›</span>
        <span>Thêm mới</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Thêm Câu hỏi
    </div>

    <div class="card subject-card question-create-card">

        <?php if ($flashError): ?>
            <div class="alert-error" id="autoAlert">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($old['excelUploaded'])): ?>
            <div class="alert-warning mt-2">
                File Excel cần chọn lại sau khi có lỗi validate
            </div>
        <?php endif; ?>


        <form id="questionForm"
              method="POST"
              action="/admin/questions/store"
              enctype="multipart/form-data">

            <!-- =========================
                TOP GRID
            ========================== -->

            <div class="question-top-grid">

                <!-- TYPE -->
                <div class="form-group">

                    <label>Loại câu hỏi</label>

                    <select id="questionType" name="questionType">

                        <option value="lesson"
                            <?= ($old['questionType'] ?? '') === 'lesson'
                                ? 'selected'
                                : '' ?>>

                            Bài học

                        </option>

                        <option value="thpt"
                            <?= ($old['questionType'] ?? '') === 'thpt'
                                ? 'selected'
                                : '' ?>>

                            THPT

                        </option>

                    </select>

                </div>


                <!-- GRADE -->
                <div class="form-group" id="gradeWrap">
                    <label>Khối lớp</label>

                    <select name="gradeId"
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

                    <select name="subjectId"
                            id="subjectSelect"
                            data-saved="<?= $old['subjectId'] ?? '' ?>">

                        <option value="">
                            Chọn môn học
                        </option>

                    </select>
                </div>

                <!-- CHAPTER -->
                <div class="form-group" id="chapterWrap">
                    <label>Chương học</label>

                    <select name="chapterId"
                            id="chapterSelect"
                            data-saved="<?= $old['chapterId'] ?? '' ?>">

                        <option value="">
                            Chọn chương học
                        </option>

                    </select>
                </div>

                <!-- LESSON -->
                <div class="form-group" id="lessonWrap">
                    <label>Bài học</label>

                    <select name="lessonId"
                            id="lessonSelect"
                            data-saved="<?= $old['lessonId'] ?? '' ?>">

                        <option value="">Chọn bài học</option>

                    </select>
                </div>

                <!-- LEVEL -->
                <div class="form-group" id="levelWrap">

                    <label>Mức độ</label>

                    <select name="level" id="levelSelect">

                        <option value="">
                            Chọn mức độ
                        </option>

                        <option value="knowledge"
                            <?= ($old['level'] ?? '') === 'knowledge'
                                ? 'selected'
                                : '' ?>>

                            Nhận biết

                        </option>

                        <option value="comprehension"
                            <?= ($old['level'] ?? '') === 'comprehension'
                                ? 'selected'
                                : '' ?>>

                            Thông hiểu

                        </option>

                        <option value="application"
                            <?= ($old['level'] ?? '') === 'application'
                                ? 'selected'
                                : '' ?>>

                            Vận dụng

                        </option>

                    </select>

                </div>

                

            </div>

            <!-- IMPORT EXCEL -->
            <div class="question-import-box mb-4">

                <label class="admin-btn btn-save import-btn">

                    <i class="fa-solid fa-file-excel"></i>
                    Chọn file Excel

                    <input
                        type="file"
                        id="excelInput"
                        name="excelFile"
                        accept=".xlsx,.xls"
                        hidden>
                </label>

                <div
                    id="excelFileName"
                    class="question-import-note">
                    Chưa chọn file Excel
                </div>

                <div class="question-import-note mt-2">
                    Format:
                    question | A | B | C | D | correct | level
                </div>

            </div>


            <div id="manualQuestionBox">

            <!-- =========================
                CONTENT
            ========================== -->

            <div class="form-group mt-4">

                <label>Nội dung câu hỏi</label>

                <textarea
                    id="questionContent"
                    name="content"
                    class="question-big-textarea"
                    rows="2"><?= htmlspecialchars($old['content'] ?? '') ?></textarea>

            </div>

            <!-- =========================
                ANSWERS
            ========================== -->

            <div class="question-answer-list">

                <?php
                    $letters = ['A', 'B', 'C', 'D'];
                ?>

                <?php foreach ($letters as $index => $letter): ?>

                    <div class="question-answer-box">

                        <div class="question-answer-head">

                            <label>
                                Đáp án <?= $letter ?>
                            </label>

                            <label class="question-correct-radio">

                                <input
                                    type="radio"
                                    name="correctAnswer"
                                    value="<?= $index ?>"
                                    <?= ($old['correctAnswer'] ?? '') == $index
                                        ? 'checked'
                                        : '' ?>>

                                Đáp án đúng

                            </label>

                        </div>

                        <textarea
                            id="answer<?= $letter ?>"
                            name="answer<?= $letter ?>"
                            rows="1"
                            class="question-answer-textarea"><?= htmlspecialchars($old['answer' . $letter] ?? '') ?></textarea>

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

            <!-- ACTION -->
            <div class="form-actions">

                <button class="admin-btn btn-save">
                    Lưu
                </button>

                <a href="/admin/questions"
                   class="admin-btn btn-cancel">

                    Hủy

                </a>

            </div>

        </form>

    </div>

</div>



<script>

// =========================================================
// EXCEL FILE
// =========================================================

document.getElementById('excelInput')
?.addEventListener('change', function () {

    const file = this.files[0];

    if (!file) return;

    const allowed = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    if (!allowed.includes(file.type)) {

        alert('Chỉ chấp nhận file Excel');

        this.value = '';

        return;
    }

    const fileName =
        document.getElementById('excelFileName');

    if (fileName) {

        fileName.innerText =
            'Đã chọn: ' + file.name;
    }

});



// =========================================================
// QUESTION TYPE TOGGLE
// =========================================================

(function () {

    const typeSelect = document.getElementById('questionType');

    const gradeWrap   = document.getElementById('gradeWrap');
    const chapterWrap = document.getElementById('chapterWrap');
    const lessonWrap  = document.getElementById('lessonWrap');
    const levelWrap   = document.getElementById('levelWrap');

    const gradeSelect   = document.getElementById('gradeSelect');
    const subjectSelect = document.getElementById('subjectSelect');

    const chapterSelect = document.getElementById('chapterSelect');
    const lessonSelect  = document.getElementById('lessonSelect');

    if (!typeSelect) return;

    // backup dữ liệu lesson
    let lessonData = {

        gradeId   : '',
        subjectId : '',
        chapterId : '',
        lessonId  : '',
        level     : ''
    };

    function toggleQuestionType() {

        const type = typeSelect.value;

        // =====================================================
        // THPT
        // =====================================================

        if (type === 'thpt') {

            // backup dữ liệu lesson
            lessonData = {

                gradeId   : gradeSelect.value,
                subjectId : subjectSelect.value,
                chapterId : chapterSelect.value,
                lessonId  : lessonSelect.value,
                level     : document.getElementById('levelSelect').value
            };

            // hide
            gradeWrap.style.display   = 'none';
            chapterWrap.style.display = 'none';
            lessonWrap.style.display  = 'none';
            levelWrap.style.display   = '';

            // THPT = lớp 12
            gradeSelect.value = '3';

            // load môn lớp 12
            $.get('/admin/ajax/subjects?grade_id=3', function (subjects) {

                let html =
                    '<option value="">Chọn môn học</option>';

                subjects.forEach(function (s) {

                    html += `
                        <option value="${s.subjectId}">
                            ${s.subjectName}
                        </option>
                    `;
                });

                subjectSelect.innerHTML = html;

                // restore subject cũ nếu có
                const savedSubject =
                    subjectSelect.dataset.saved;

                if (savedSubject) {
                    subjectSelect.value = savedSubject;
                }
            });
        }

        // =====================================================
        // LESSON
        // =====================================================

        else {

            // show
            gradeWrap.style.display   = '';
            chapterWrap.style.display = '';
            lessonWrap.style.display  = '';
            levelWrap.style.display   = '';

            // restore grade
            if (lessonData.gradeId) {

                gradeSelect.value =
                    lessonData.gradeId;

                // load subjects
                $.get('/admin/ajax/subjects?grade_id=' + lessonData.gradeId, function (subjects) {

                    let html =
                        '<option value="">Chọn môn học</option>';

                    subjects.forEach(function (s) {

                        html += `
                            <option value="${s.subjectId}">
                                ${s.subjectName}
                            </option>
                        `;
                    });

                    subjectSelect.innerHTML = html;

                    // restore subject
                    subjectSelect.value =
                        lessonData.subjectId;

                    // load chapters
                    if (lessonData.subjectId) {

                        $.get('/admin/ajax/chapters?subject_id=' + lessonData.subjectId, function (chapters) {

                            let html2 =
                                '<option value="">Chọn chương học</option>';

                            chapters.forEach(function (c) {

                                html2 += `
                                    <option value="${c.chapterId}">
                                        Chương ${c.sortOrder}: ${c.chapterName}
                                    </option>
                                `;
                            });

                            chapterSelect.innerHTML = html2;

                            chapterSelect.value =
                                lessonData.chapterId;

                            // load lessons
                            if (lessonData.chapterId) {

                                $.get('/admin/ajax/lessons?chapter_id=' + lessonData.chapterId, function (lessons) {

                                    let html3 =
                                        '<option value="">Chọn bài học</option>';

                                    lessons.forEach(function (l) {

                                        html3 += `
                                            <option value="${l.lessonId}">
                                                Bài ${l.sortOrder}: ${l.lessonName}
                                            </option>
                                        `;
                                    });

                                    lessonSelect.innerHTML = html3;

                                    lessonSelect.value =
                                        lessonData.lessonId;
                                });
                            }
                        });
                    }
                });
            }

            // restore level
            if (lessonData.level) {

                document.getElementById('levelSelect').value =
                    lessonData.level;
            }
        }
    }

    window.addEventListener('load', function () {

        setTimeout(function () {

            toggleQuestionType();

        }, 100);

    });

    typeSelect.addEventListener(
        'change',
        toggleQuestionType
    );

})();



// =========================================================
// REQUIRED TOGGLE
// =========================================================

(function () {

    const excelInput = document.getElementById('excelInput');

    const content = document.getElementById('questionContent');
    const manualQuestionBox = document.getElementById('manualQuestionBox');

    const answers = [
        document.getElementById('answerA'),
        document.getElementById('answerB'),
        document.getElementById('answerC'),
        document.getElementById('answerD'),
    ];

    const gradeSelect   = document.getElementById('gradeSelect');
    const subjectSelect = document.getElementById('subjectSelect');

    const chapterSelect = document.getElementById('chapterSelect');
    const lessonSelect  = document.getElementById('lessonSelect');

    const typeSelect = document.getElementById('questionType');

    function toggleRequired() {

        const hasExcel =
            excelInput.files.length > 0;

        const type =
            typeSelect.value;

        // ======================
        // IMPORT EXCEL
        // ======================
        if (hasExcel) {

            // ẨN nhập tay
            if (manualQuestionBox) {
                manualQuestionBox.style.display = 'none';
            }

            content.removeAttribute('required');

            answers.forEach(a => {

                a.removeAttribute('required');

            });
        }

        // ======================
        // NHẬP TAY
        // ======================
        else {

            // HIỆN nhập tay
            if (manualQuestionBox) {
                manualQuestionBox.style.display = '';
            }

            content.setAttribute(
                'required',
                true
            );
        }

        // ======================
        // LESSON
        // ======================
        if (type === 'lesson') {

            gradeSelect.required   = true;
            subjectSelect.required = true;

            chapterSelect.required = true;
            lessonSelect.required  = true;
        }

        // ======================
        // THPT
        // ======================
        else {

            gradeSelect.required   = false;
            subjectSelect.required = true;

            chapterSelect.required = false;
            lessonSelect.required  = false;
        }
    }
    
    window.addEventListener('load', function () {

        setTimeout(() => {

            toggleRequired();

        }, 100);

    });

    excelInput.addEventListener(
        'change',
        toggleRequired
    );

    typeSelect.addEventListener(
        'change',
        toggleRequired
    );

})();



</script>