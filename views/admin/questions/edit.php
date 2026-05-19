<?php

/** @var array $grades */
/** @var array $question */
/** @var string|null $flashError */

$d = !empty($flashOld)
    ? $flashOld
    : $question;

?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">

        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/questions">Câu hỏi</a>
        <span>›</span>
        <span>Cập nhật</span>

    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Cập nhật Câu hỏi
    </div>

    <div class="card subject-card question-create-card">

        <?php if ($flashError): ?>

            <div class="alert-error" id="autoAlert">
                <?= htmlspecialchars($flashError) ?>
            </div>

        <?php endif; ?>

        <form method="POST"
              action="/admin/questions/update">

            <input
                type="hidden"
                name="questionId"
                value="<?= $question['questionId'] ?>">

            <!-- =========================
                TOP GRID
            ========================== -->

            <div class="question-top-grid">

                <!-- TYPE -->
                <div class="form-group">

                    <label>Loại câu hỏi</label>

                    <select id="questionType"
                            name="questionType">

                        <option value="lesson"
                            <?= ($d['questionType'] ?? '') === 'lesson'
                                ? 'selected'
                                : '' ?>>

                            Bài học

                        </option>

                        <option value="thpt"
                            <?= ($d['questionType'] ?? '') === 'thpt'
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
                            data-saved="<?= $d['gradeId'] ?? '' ?>">

                        <option value="">
                            Chọn khối lớp
                        </option>

                        <?php foreach ($grades as $g): ?>

                        <option
                            value="<?= $g['gradeId'] ?>"
                            <?= ($d['gradeId'] ?? '') == $g['gradeId']
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
                            data-saved="<?= $d['subjectId'] ?? '' ?>">

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
                            data-saved="<?= $d['chapterId'] ?? '' ?>">

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
                            data-saved="<?= $d['lessonId'] ?? '' ?>">

                        <option value="">
                            Chọn bài học
                        </option>

                    </select>

                </div>

                <!-- LEVEL -->
                <div class="form-group" id="levelWrap">

                    <label>Mức độ</label>

                    <select name="level"
                            id="levelSelect">

                        <option value="">
                            Chọn mức độ
                        </option>

                        <option value="knowledge"
                            <?= ($d['level'] ?? '') === 'knowledge'
                                ? 'selected'
                                : '' ?>>

                            Nhận biết

                        </option>

                        <option value="comprehension"
                            <?= ($d['level'] ?? '') === 'comprehension'
                                ? 'selected'
                                : '' ?>>

                            Thông hiểu

                        </option>

                        <option value="application"
                            <?= ($d['level'] ?? '') === 'application'
                                ? 'selected'
                                : '' ?>>

                            Vận dụng

                        </option>

                    </select>

                </div>

            </div>

            <!-- =========================
                CONTENT
            ========================== -->

            <div class="form-group mt-4">

                <label>Nội dung câu hỏi</label>

                <textarea
                    id="questionContent"
                    name="content"
                    class="question-big-textarea"
                    rows="2"><?= htmlspecialchars($d['content'] ?? '') ?></textarea>

            </div>

            <!-- =========================
                ANSWERS
            ========================== -->

            <div class="question-answer-list">

                <?php
                    $letters = ['A', 'B', 'C', 'D'];

                    $answerMap = [];

                    foreach ($question['answers'] as $index => $a) {
                        $answerMap[$index] = $a;
                    }
                ?>

                <?php foreach ($letters as $index => $letter): ?>

                    <?php
                        $answer =
                            $answerMap[$index]['content'] ?? '';

                        $isCorrect =
                            $answerMap[$index]['isCorrect'] ?? 0;
                    ?>

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
                                    <?= $isCorrect
                                        ? 'checked'
                                        : '' ?>>

                                Đáp án đúng

                            </label>

                        </div>

                        <textarea
                            id="answer<?= $letter ?>"
                            name="answer<?= $letter ?>"
                            rows="1"
                            class="question-answer-textarea"><?= htmlspecialchars($answer) ?></textarea>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- ACTION -->
            <div class="form-actions">

                <button class="admin-btn btn-save">
                    Cập nhật
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

    const levelSelect   = document.getElementById('levelSelect');

    if (!typeSelect) return;

    // backup dữ liệu lesson
    let lessonData = {

        gradeId   : gradeSelect.value,
        subjectId : subjectSelect.dataset.saved || '',
        chapterId : chapterSelect.dataset.saved || '',
        lessonId  : lessonSelect.dataset.saved || '',
        level     : levelSelect.value || ''
    };

    function toggleQuestionType() {

        const type = typeSelect.value;

        // =====================================================
        // THPT
        // =====================================================

        if (type === 'thpt') {

            // backup lesson data
            if (gradeSelect.value !== '3') {

                lessonData = {

                    gradeId   : gradeSelect.value,
                    subjectId : subjectSelect.value,
                    chapterId : chapterSelect.value,
                    lessonId  : lessonSelect.value,
                    level     : levelSelect.value
                };
            }

            // hide
            gradeWrap.style.display   = 'none';
            chapterWrap.style.display = 'none';
            lessonWrap.style.display  = 'none';
            levelWrap.style.display   = 'none';

            // THPT = lớp 12
            gradeSelect.value = '3';

            // load subjects lớp 12
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

                // restore subject
                const savedSubject =
                    subjectSelect.dataset.saved;

                if (savedSubject) {

                    subjectSelect.value =
                        savedSubject;
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

                levelSelect.value =
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

    const gradeSelect   = document.getElementById('gradeSelect');
    const subjectSelect = document.getElementById('subjectSelect');

    const chapterSelect = document.getElementById('chapterSelect');
    const lessonSelect  = document.getElementById('lessonSelect');

    const typeSelect    = document.getElementById('questionType');

    function toggleRequired() {

        const type = typeSelect.value;

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

    typeSelect.addEventListener(
        'change',
        toggleRequired
    );

})();



// =========================================================
// RESTORE EDIT DATA
// =========================================================

$(function () {

    const savedGradeId =
        $('#gradeSelect').data('saved');

    const savedSubjectId =
        $('#subjectSelect').data('saved');

    const savedChapterId =
        $('#chapterSelect').data('saved');

    const savedLessonId =
        $('#lessonSelect').data('saved');

    const type =
        $('#questionType').val();

    // =====================================================
    // THPT
    // =====================================================

    if (type === 'thpt') {

        $('#gradeSelect').val('3');

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

            $('#subjectSelect').html(html);

            if (savedSubjectId) {

                $('#subjectSelect').val(savedSubjectId);
            }
        });

        return;
    }

    // =====================================================
    // LESSON
    // =====================================================

    if (!savedGradeId) return;

    $.get('/admin/ajax/subjects?grade_id=' + savedGradeId, function (subjects) {

        let html =
            '<option value="">Chọn môn học</option>';

        subjects.forEach(function (s) {

            html += `
                <option value="${s.subjectId}">
                    ${s.subjectName}
                </option>
            `;
        });

        $('#subjectSelect').html(html);

        $('#subjectSelect').val(savedSubjectId);

        // load chapters
        if (!savedSubjectId) return;

        $.get('/admin/ajax/chapters?subject_id=' + savedSubjectId, function (chapters) {

            let html2 =
                '<option value="">Chọn chương học</option>';

            chapters.forEach(function (c) {

                html2 += `
                    <option value="${c.chapterId}">
                        Chương ${c.sortOrder}: ${c.chapterName}
                    </option>
                `;
            });

            $('#chapterSelect').html(html2);

            $('#chapterSelect').val(savedChapterId);

            // load lessons
            if (!savedChapterId) return;

            $.get('/admin/ajax/lessons?chapter_id=' + savedChapterId, function (lessons) {

                let html3 =
                    '<option value="">Chọn bài học</option>';

                lessons.forEach(function (l) {

                    html3 += `
                        <option value="${l.lessonId}">
                            Bài ${l.sortOrder}: ${l.lessonName}
                        </option>
                    `;
                });

                $('#lessonSelect').html(html3);

                $('#lessonSelect').val(savedLessonId);
            });
        });
    });

});


</script>