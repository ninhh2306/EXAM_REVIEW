<?php
/** @var array $grade */
/** @var array $subject */
/** @var array $exam */
/** @var array $questions */
?>

<!-- BREADCRUMB + HERO -->
<section class="exam-hero-section">
    <div class="container">

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="/">Trang chủ</a>
            <span>›</span>
            <a href="/<?= $grade['slug'] ?>"><?= $grade['gradeName'] ?></a>
            <span>›</span>
            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>"><?= $subject['subjectName'] ?></a>
            <span>›</span>
            <a href="/<?= $grade['slug'] ?>/<?= $subject['slug'] ?>/trac-nghiem">Ôn luyện trắc nghiệm</a>
            <span>›</span>
            <span><?= $exam['title'] ?></span>
        </div>

        <!-- Tiêu đề -->
        <h1 class="exam-hero__title"><?= $exam['title'] ?></h1>

        <!-- Meta info -->
        <div class="exam-hero__meta">
            <span style="display: flex; align-items: center; gap: 6px; color: var(--text-sub); font-size: 14px;">
                <i class="fa-solid fa-file-lines"></i>
                <?= $exam['totalQuestions'] ?> câu hỏi
            </span>

            <span style="display: flex; align-items: center; gap: 6px; color: var(--text-sub); font-size: 14px;">
                <i class="fa-solid fa-clock"></i>
                <?= $exam['duration'] ?> phút
            </span>
        </div>

    </div>
</section>



<!-- KHU VỰC LÀM BÀI -->
<section class="exam-section">
    <div class="container">
        <form id="examForm" method="POST" action="/submit-exam">
            <input type="hidden" name="examId" value="<?= $exam['examId'] ?>">
            <input type="hidden" name="expiredAt" id="expiredAt">

            <div class="exam-layout">

                <!-- CỘT TRÁI: CÂU HỎI -->
                <div class="exam-questions">

                    <?php foreach ($questions as $index => $q): ?>
                    <?php $num = $index + 1; ?>

                    <div class="question-block" id="q<?= $num ?>">
                        <!-- 1. Số câu và nội dung câu hỏi -->
                        <div class="question-header">
                            <div class="question-number">
                                <span class="q-num-badge"><?= sprintf('%02d', $num) ?></span>
                                <p class="question-content"><?= $q['content'] ?></p>
                            </div>
                        </div>

                        <!-- 2. Danh sách câu trả lời -->
                        <div class="answer-list">
                            <?php
                            $labels = ['A', 'B', 'C', 'D'];
                            foreach ($q['answers'] as $ai => $ans):
                            ?>
                            <label class="answer-item" data-q="<?= $num ?>" data-answer-id="<?= $ans['answerId'] ?>">
                                <input
                                    type="radio"
                                    name="answer[<?= $q['questionId'] ?>]"
                                    value="<?= $ans['answerId'] ?>"
                                    onchange="selectAnswer(<?= $num ?>, this)"
                                    style="display:none"
                                >
                                <span class="answer-label"><?= $labels[$ai] ?>.</span>
                                <span class="answer-text"><?= $ans['content'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- 3. Nút kiểm tra lại -->
                        <div style="text-align: right; margin-top: 10px;">
                            <button 
                                type="button" 
                                class="mark-btn" 
                                id="mark-<?= $num ?>"
                                onclick="toggleMark(<?= $num ?>)"
                                title="Đánh dấu xem lại"
                            >
                                Cần kiểm tra lại
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>


                <!-- CỘT PHẢI: BẢNG ĐÁP ÁN + TIMER + NỘP BÀI -->
                <section class="exam-sidebar">
                    <div class="answer-sheet">

                        <!-- Header -->
                        <div class="answer-sheet__header">
                            <span class="sheet-header-icon"><i class="fa-solid fa-table-cells fa-xl"></i></span>
                            <span class="sheet-header-title">Câu hỏi</span>
                            <button type="button" class="sheet-toggle-btn" id="sheetToggleBtn" onclick="toggleAnswerSheet()">
                                <i class="fa-solid fa-minus" id="sheetToggleIcon"></i>
                            </button>
                        </div>

                        <!-- Phần nội dung có thể ẩn/hiện -->
                        <div class="answer-sheet__body" id="answerSheetBody">

                            <div class="answer-sheet__grid">
                                <?php for ($i = 1; $i <= count($questions); $i++): ?>
                                <button
                                    type="button"
                                    class="sheet-btn"
                                    id="sheet-<?= $i ?>"
                                    onclick="scrollToQuestion(<?= $i ?>)"
                                ><?= $i ?></button>
                                <?php endfor; ?>
                            </div>

                            <div class="answer-sheet__legend">
                                <span><span class="legend-dot legend-dot--done"></span> Đã chọn</span>
                                 <span><span class="legend-dot legend-dot--marked"></span> Cần kiểm tra lại</span>
                                <span><span class="legend-dot legend-dot--empty"></span> Chưa trả lời</span>
                            </div>

                            <div style="border-top: 1px solid var(--border-color, #e5e7eb); margin: 12px 0;"></div>

                            <div class="exam-stats-row">
                                <div class="exam-stat-box">
                                    <div class="exam-stat-label">Số câu đã làm</div>
                                    <div class="exam-stat-value" id="answeredCount">0/<?= count($questions) ?></div>
                                </div>
                                <div class="exam-stat-box">
                                    <div class="exam-stat-label">Thời gian còn lại</div>
                                    <div class="exam-stat-value" id="timerDisplay"><?= sprintf('%02d', $exam['duration']) ?>:00:00</div>
                                </div>
                            </div>

                            <button type="button" class="exam-submit-btn" onclick="submitExam()">
                                Nộp bài
                            </button>

                        </div>
                    </div>
                </section>

            </div>
        </form>
    </div>
</section>


<!-- MODAL XÁC NHẬN THOÁT TRANG -->
<div id="exitModal" class="submit-modal">
    <div class="submit-modal__overlay"></div>
    <div class="submit-modal__box">
        <h3 class="submit-modal__title">Bạn muốn thoát?</h3>
        <p class="submit-modal__msg">Nếu thoát, toàn bộ câu trả lời sẽ bị xóa.</p>
        <div class="submit-modal__actions">
            <button id="exitCancelBtn" class="submit-modal__btn submit-modal__btn--cancel">Tiếp tục ôn luyện</button>
            <button id="exitConfirmBtn" class="submit-modal__btn submit-modal__btn--confirm" style="background:#dc2626">Thoát</button>
        </div>
    </div>
</div>


<!-- MODAL XÁC NHẬN NỘP BÀI -->
<div id="submitModal" class="submit-modal">
    <div class="submit-modal__overlay" onclick="closeSubmitModal()"></div>

    <div class="submit-modal__box">
    
        <h3 id="modalTitle" class="submit-modal__title"></h3>
        <p  id="modalMsg"   class="submit-modal__msg"></p>

        <div class="submit-modal__actions">
            <button id="modalCancelBtn" class="submit-modal__btn submit-modal__btn--cancel" onclick="closeSubmitModal()">
                Huỷ
            </button>
            <button 
                id="modalConfirmBtn" 
                class="submit-modal__btn submit-modal__btn--confirm" 
                onclick="handleManualSubmit()">
                Nộp bài
            </button>
        </div>
    </div>
</div>


<script>
const totalQ    = <?= count($questions) ?>;
const examId    = <?= $exam['examId'] ?>;
const duration  = <?= $exam['duration'] ?>;

 
// Storage keys
const STORAGE_ANSWERS       = 'exam_answers_'       + examId;
const STORAGE_MARKED        = 'exam_marked_'        + examId;
const STORAGE_END_TIME      = 'exam_end_time_'      + examId;
const STORAGE_PENDING_CLEAR = 'exam_pending_clear_' + examId;
const SESSION_RELOAD        = 'exam_reload_'        + examId;
 
const answered     = new Set();
const marked       = new Set();
let currentQ       = 1;
let timerInterval;
let autoSubmitting = false;
let _isSubmitting  = false; // true khi nộp bài hoặc thoát có chủ đích
let _isAutoSubmit  = false;
// true nếu user đã từng tương tác (kể cả trước reload - kiểm tra qua localStorage)
let _userActedAfterLoad = (function() {
    const saved = localStorage.getItem(STORAGE_ANSWERS);
    if (!saved) return false;
    try { return Object.keys(JSON.parse(saved)).length > 0; } catch(e) { return false; }
})();
 
// ─── HÀM XÓA DATA ─────────────────────────────────────────────
function clearExamData() {
    localStorage.removeItem(STORAGE_ANSWERS);
    localStorage.removeItem(STORAGE_MARKED);
    localStorage.removeItem(STORAGE_END_TIME);
    localStorage.removeItem(STORAGE_PENDING_CLEAR);
}
 
// ─── RELOAD: xử lý flag pending clear ─────────────────────────
// Khi trang load:
//   - Nếu có STORAGE_PENDING_CLEAR trong localStorage → đây là lần load SAU KHI đóng tab
//     (vì nếu là reload, DOMContentLoaded này chính là "lần load lại" → ta xóa flag, giữ data)
//   - Logic: nếu sessionStorage có SESSION_RELOAD → là reload → xóa pendingClear, giữ examData
//             nếu không có SESSION_RELOAD → là load mới sau đóng tab → xóa examData
(function () {
    const pendingClear = localStorage.getItem(STORAGE_PENDING_CLEAR);
    if (pendingClear) {
        if (sessionStorage.getItem(SESSION_RELOAD)) {
            // Là RELOAD → giữ lại examData, chỉ xóa flag
            localStorage.removeItem(STORAGE_PENDING_CLEAR);
            sessionStorage.removeItem(SESSION_RELOAD);
        } else {
            // Là load mới sau đóng tab → xóa data
            clearExamData();
            localStorage.removeItem(STORAGE_PENDING_CLEAR);
        }
    } else {
        // Không có pendingClear: xóa SESSION_RELOAD nếu còn sót
        if (sessionStorage.getItem(SESSION_RELOAD)) {
            sessionStorage.removeItem(SESSION_RELOAD);
        }
    }
})();
 
// ─── LOCALSTORAGE HELPERS ─────────────────────────────────────
function saveAnswers() {
    const data = {};
    document.querySelectorAll('input[type=radio]:checked').forEach(input => {
        data[input.name] = input.value;
    });
    localStorage.setItem(STORAGE_ANSWERS, JSON.stringify(data));
}
 
function saveMarked() {
    localStorage.setItem(STORAGE_MARKED, JSON.stringify([...marked]));
}
 
function loadSavedState() {
    // 1. Khôi phục đáp án
    const savedAnswers = JSON.parse(localStorage.getItem(STORAGE_ANSWERS) || '{}');
    Object.entries(savedAnswers).forEach(([name, value]) => {
        const input = document.querySelector(`input[name="${name}"][value="${value}"]`);
        if (!input) return;
        input.checked = true;
        // Tìm số câu (qNum) từ label cha
        const label = input.closest('.answer-item');
        const qNum  = parseInt(label?.dataset.q);
        if (!qNum) return;
        document.getElementById('q' + qNum)?.querySelectorAll('.answer-item').forEach(el => el.classList.remove('selected'));
        label.classList.add('selected');
        answered.add(qNum);
        document.getElementById('sheet-' + qNum)?.classList.add('done');
    });
    updateAnsweredCount();

    // 2. Khôi phục đánh dấu xem lại
    const savedMarked = JSON.parse(localStorage.getItem(STORAGE_MARKED) || '[]');
    savedMarked.forEach(qNum => {
        marked.add(qNum);
        document.getElementById('mark-' + qNum)?.classList.add('marked');
        const sheetBtn = document.getElementById('sheet-' + qNum);
        if (sheetBtn) {
            sheetBtn.classList.remove('done');
            sheetBtn.classList.add('marked');
        }
    });
}
 


// ─── GET END TIME ─────────────────────────────────────────
function getEndTime() {
    const saved = localStorage.getItem(STORAGE_END_TIME);

    if (saved) {
        const t = parseInt(saved);
        if (!isNaN(t)) {
            return t; // ✅ LUÔN dùng lại, kể cả đã hết giờ
        }
    }

    const newTime = Date.now() + duration * 60 * 1000;
    localStorage.setItem(STORAGE_END_TIME, newTime);
    return newTime;
}

const endTime = getEndTime();


// ─── TIMER ────────────────────────────────────────────────

function updateTimer() {
    const remaining = Math.max(0, Math.floor((endTime - Date.now()) / 1000));

    const h = Math.floor(remaining / 3600);
    const m = Math.floor((remaining % 3600) / 60);
    const s = remaining % 60;

    const display = document.getElementById('timerDisplay');
    if (display) {
        display.textContent =
            String(h).padStart(2, '0') + ':' +
            String(m).padStart(2, '0') + ':' +
            String(s).padStart(2, '0');

        // đổi màu khi còn 5 phút
        if (remaining <= 300) {
            display.style.color = '#dc2626';
            display.style.fontWeight = '700';
        }
    }

    // hết giờ → auto submit
    if (remaining <= 0) {
        clearInterval(timerInterval);

        if (!autoSubmitting) {
            autoSubmitting = true;

            // ✅ LƯU THỜI ĐIỂM HẾT GIỜ
            window.examExpiredAt = new Date().toISOString();

            localStorage.removeItem(STORAGE_END_TIME);

            submitExam(true);
        }
    }
}

// ─── START TIMER ──────────────────────────────────────────
timerInterval = setInterval(updateTimer, 1000);
updateTimer();

// ─── KHI USER SUBMIT THỦ CÔNG ─────────────────────────────
function handleManualSubmit() {
    // Xoá thời gian của bài hiện tại
    localStorage.removeItem(STORAGE_END_TIME);

    // Gọi lại hàm submit cũ của bạn
    confirmSubmit();
}

 
// ─── ANSWER ACTIONS ───────────────────────────────────────────
function updateAnsweredCount() {
    document.getElementById('answeredCount').textContent = answered.size + '/' + totalQ;
}
 
function selectAnswer(qNum, input) {
    _userActedAfterLoad = true;
    const block = document.getElementById('q' + qNum);
    block.querySelectorAll('.answer-item').forEach(el => el.classList.remove('selected'));
    input.closest('.answer-item').classList.add('selected');
    answered.add(qNum);
    updateAnsweredCount();
    saveAnswers();
    const btn = document.getElementById('sheet-' + qNum);
    btn.classList.remove('current', 'marked');
    btn.classList.add('done');
}
 
function toggleMark(qNum) {
    _userActedAfterLoad = true;
    const markBtn  = document.getElementById('mark-' + qNum);
    const sheetBtn = document.getElementById('sheet-' + qNum);
    if (marked.has(qNum)) {
        marked.delete(qNum);
        markBtn.classList.remove('marked');
        if (answered.has(qNum)) {
            sheetBtn.classList.add('done');
            sheetBtn.classList.remove('marked');
        } else {
            sheetBtn.classList.remove('marked');
        }
    } else {
        marked.add(qNum);
        markBtn.classList.add('marked');
        sheetBtn.classList.remove('done');
        sheetBtn.classList.add('marked');
    }
    saveMarked();
}

// Scroll vị trí câu hỏi
function scrollToQuestion(num) {
    document.querySelectorAll('.sheet-btn').forEach(b => b.classList.remove('current'));
    const btn = document.getElementById('sheet-' + num);
    if (btn && !btn.classList.contains('done') && !btn.classList.contains('marked')) {
        btn.classList.add('current');
    }
    const el = document.getElementById('q' + num);
    if (el) {
        // Highlight câu hỏi
        document.querySelectorAll('.question-block').forEach(q => q.classList.remove('highlight-active'));
        el.scrollIntoView({ behavior: 'auto', block: 'center' });
        el.classList.add('highlight-active');
        setTimeout(() => el.classList.remove('highlight-active'), 2000);
        currentQ = num;
    }
}
 
// ─── MODAL NỘP BÀI ────────────────────────────────────────────
function submitExam(autoSubmit = false) {
     // ❗ CHẶN GỌI 2 LẦN
    if (_isSubmitting) return;

    _isAutoSubmit = autoSubmit;
    const modal     = document.getElementById('submitModal');
    const title     = document.getElementById('modalTitle');
    const msg       = document.getElementById('modalMsg');
    const cancelBtn = document.getElementById('modalCancelBtn');
    const confirmBtn = document.getElementById('modalConfirmBtn'); // Nút xác nhận nộp

    if (autoSubmit) {
        title.textContent = 'Hết giờ làm bài!';
        cancelBtn.style.display = 'none';

        let countdown = 5;

        // ⏱ HIỂN THỊ COUNTDOWN
        msg.textContent = `Bạn đã trả lời ${answered.size}/${totalQ} câu. Hệ thống sẽ tự động nộp bài sau ${countdown} giây...`;

        const interval = setInterval(() => {
            countdown--;

            msg.textContent = `Bạn đã trả lời ${answered.size}/${totalQ} câu. Hệ thống sẽ tự động nộp bài sau ${countdown} giây...`;

            if (countdown <= 0) {
                clearInterval(interval);
            }
        }, 1000);

        // ⏳ 5 giây sau thì submit
        setTimeout(() => {
            confirmSubmit();
        }, 5000);

    } else {
        title.textContent = 'Xác nhận nộp bài';
        msg.textContent   = `Bạn đã trả lời ${answered.size}/${totalQ} câu. Bạn có chắc chắn muốn nộp bài không?`;
        cancelBtn.style.display = '';
    }

    modal.classList.add('is-open');
}

function closeSubmitModal() {
    const modal = document.getElementById('submitModal');
    if (modal) modal.classList.remove('is-open');
    _isAutoSubmit = false;
}

function confirmSubmit() {
    if (_isSubmitting) return; // ❗ chặn double submit
    
    // Chặn các sự kiện popstate/beforeunload
    _isSubmitting = true;
    
    // Dừng đếm ngược
    if (typeof timerInterval !== 'undefined') {
        clearInterval(timerInterval);
    }

    // ✅ nếu là auto submit → dùng thời điểm hết giờ
    if (_isAutoSubmit && window.examExpiredAt) {
        document.getElementById('expiredAt').value = window.examExpiredAt;
    }
    
    // Xóa dữ liệu tạm (nếu có)
    clearExamData();
    
    // Nộp form
    document.getElementById('examForm').submit();
}

 
// ─── TOGGLE SIDEBAR ───────────────────────────────────────────
function toggleAnswerSheet() {
    const sheet = document.querySelector('.answer-sheet');
    const body  = document.getElementById('answerSheetBody');
    const icon  = document.getElementById('sheetToggleIcon');
    const isCollapsed = sheet.classList.contains('collapsed');
    if (isCollapsed) {
        sheet.classList.remove('collapsed');
        body.style.display = 'block';
        icon.className = 'fa-solid fa-minus';
    } else {
        sheet.classList.add('collapsed');
        body.style.display = 'none';
        icon.className = 'fa-solid fa-plus';
    }
}
 
// ─── KHỞI TẠO ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadSavedState);
history.scrollRestoration = 'manual';

// ─── CHẶN BACK/FORWARD → MODAL THOÁT ─────────────────────────
// 1. Hàm push state kèm theo vị trí scroll hiện tại
function pushGuardState() {
    // Lưu lại vị trí scroll hiện tại vào state để khi popstate xảy ra, 
    // chúng ta biết chính xác vị trí cần giữ nguyên.
    history.pushState({ 
        examGuard: true, 
        scrollY: window.scrollY 
    }, '');
}

// Khởi tạo lần đầu
pushGuardState();

let _modalOpen = false;

window.addEventListener('popstate', function (e) {
    if (_isSubmitting) return;

    // Lấy lại vị trí scroll từ state ngay khi nhấn back
    const targetScrollY = e.state?.scrollY ?? window.scrollY;

    // QUAN TRỌNG: Đẩy lại state ngay lập tức để "chặn" việc thoát trang
    pushGuardState();

    // Khôi phục lại vị trí scroll cũ ngay lập tức (instant) để tránh bị nhảy lên đầu trang
    requestAnimationFrame(() => {
        window.scrollTo(0, targetScrollY);
    });

    if (_modalOpen) return;

    // Xử lý logic thoát nhanh nếu chưa tương tác
    if (!_userActedAfterLoad) {
        clearExamData();
        sessionStorage.removeItem(SESSION_RELOAD);
        _isSubmitting = true; 
        history.go(-2); 
        return;
    }

    // Hiển thị Modal tại chỗ
    _modalOpen = true;
    const modal = document.getElementById('exitModal');
    if (modal) {
        modal.classList.add('is-open');
    }
});

// 2. CẬP NHẬT STATE KHI SCROLL
// Cập nhật lại state liên tục khi user scroll để nếu họ nhấn back, 
// vị trí lưu trữ luôn là vị trí mới nhất.
let scrollTimeout;
window.addEventListener('scroll', function() {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        if (!_modalOpen) {
            // Thay thế state hiện tại bằng tọa độ mới nhất mà không tạo thêm lịch sử
            history.replaceState({ examGuard: true, scrollY: window.scrollY }, '');
        }
    }, 100); // debounce 100ms để tránh ghi đè liên tục gây lag
});


document.getElementById('exitCancelBtn').addEventListener('click', function () {
    document.getElementById('exitModal').classList.remove('is-open');
    _modalOpen = false;
});

document.getElementById('exitConfirmBtn').addEventListener('click', function () {
    clearExamData();
    sessionStorage.removeItem(SESSION_RELOAD);
    _isSubmitting = true;   // tắt beforeunload
    history.go(-2);         // -1 cho pushState giả + -1 để back thật
});

// ─── CHẶN ĐÓNG TAB / NAVIGATE ─────────────────────────────────
window.addEventListener('beforeunload', function (e) {
    if (_isSubmitting) return;
    sessionStorage.setItem(SESSION_RELOAD, '1');
    e.preventDefault();
    e.returnValue = '';
});

window.addEventListener('pagehide', function (e) {
    if (_isSubmitting || e.persisted) return;
    // Đánh dấu "cần xóa data" vào localStorage
    // Nếu trang load lại (reload) → DOMContentLoaded sẽ xóa flag này (giữ lại examData)
    // Nếu tab đóng thật → flag còn trong localStorage → lần sau vào trang sẽ xóa examData
    localStorage.setItem(STORAGE_PENDING_CLEAR, '1');
    if (typeof timerInterval !== 'undefined') clearInterval(timerInterval);
});
 
 
</script>