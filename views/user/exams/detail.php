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
                <?= $exam['realTotalQuestions'] ?> câu hỏi
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
        <form id="examForm" method="POST" action="<?= APP_URL ?>/submit-exam">
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


<!-- BANNER MẤT KẾT NỐI -->
<div id="offlineBanner">
    <i class="fa-solid fa-wifi" style="margin-right:8px;"></i>
    Mất kết nối mạng. Vui lòng kiểm tra lại!
</div>


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
const totalQ   = <?= count($questions) ?>;
const examId   = <?= $exam['examId'] ?>;
const duration = <?= $exam['duration'] ?>;

const STORAGE_ANSWERS  = 'exam_answers_'  + examId;
const STORAGE_MARKED   = 'exam_marked_'   + examId;
const STORAGE_END_TIME = 'exam_end_time_' + examId;

const answered    = new Set();
const marked      = new Set();
let currentQ      = 1;
let timerInterval;
let autoSubmitting = false;
let _isSubmitting  = false;
let _isAutoSubmit  = false;

// ── KHỞI TẠO: phân biệt reload vs vào mới ────────────────────
(function initSession() {
    const navEntry = performance.getEntriesByType('navigation')[0];
    const isReload = navEntry && navEntry.type === 'reload';
    if (!isReload) {
        sessionStorage.removeItem(STORAGE_ANSWERS);
        sessionStorage.removeItem(STORAGE_MARKED);
        sessionStorage.removeItem(STORAGE_END_TIME);
    }
})();

// ── XÓA DATA ─────────────────────────────────────────────────
function clearExamData() {
    sessionStorage.removeItem(STORAGE_ANSWERS);
    sessionStorage.removeItem(STORAGE_MARKED);
    sessionStorage.removeItem(STORAGE_END_TIME);
}

// ── LƯU ──────────────────────────────────────────────────────
function saveAnswers() {
    const data = {};
    document.querySelectorAll('input[type=radio]:checked').forEach(input => {
        data[input.name] = input.value;
    });
    sessionStorage.setItem(STORAGE_ANSWERS, JSON.stringify(data));
}

function saveMarked() {
    sessionStorage.setItem(STORAGE_MARKED, JSON.stringify([...marked]));
}

// ── KHÔI PHỤC ────────────────────────────────────────────────
function loadSavedState() {
    const savedAnswers = JSON.parse(sessionStorage.getItem(STORAGE_ANSWERS) || '{}');
    Object.entries(savedAnswers).forEach(([name, value]) => {
        const input = document.querySelector(`input[name="${name}"][value="${value}"]`);
        if (!input) return;
        input.checked = true;
        const label = input.closest('.answer-item');
        const qNum  = parseInt(label?.dataset.q);
        if (!qNum) return;
        document.getElementById('q' + qNum)?.querySelectorAll('.answer-item').forEach(el => el.classList.remove('selected'));
        label.classList.add('selected');
        answered.add(qNum);
        document.getElementById('sheet-' + qNum)?.classList.add('done');
    });
    updateAnsweredCount();

    const savedMarked = JSON.parse(sessionStorage.getItem(STORAGE_MARKED) || '[]');
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

// ── TIMER ─────────────────────────────────────────────────────
function getEndTime() {
    const saved = sessionStorage.getItem(STORAGE_END_TIME);
    if (saved) {
        const t = parseInt(saved);
        if (!isNaN(t) && t > Date.now()) {
            return t;
        }
        sessionStorage.removeItem(STORAGE_END_TIME);
    }
    const newTime = Date.now() + duration * 60 * 1000;
    sessionStorage.setItem(STORAGE_END_TIME, newTime);
    return newTime;
}

const endTime = getEndTime();

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
        if (remaining <= 300) {
            display.style.color = '#dc2626';
            display.style.fontWeight = '700';
        }
    }

    if (remaining <= 0) {
        clearInterval(timerInterval);
        if (!autoSubmitting) {
            autoSubmitting = true;
            window.examExpiredAt = new Date().toISOString();
            sessionStorage.removeItem(STORAGE_END_TIME);
            submitExam(true);
        }
    }
}

timerInterval = setInterval(updateTimer, 1000);
updateTimer();

// ── ANSWER ACTIONS ────────────────────────────────────────────
function updateAnsweredCount() {
    document.getElementById('answeredCount').textContent = answered.size + '/' + totalQ;
}

function selectAnswer(qNum, input) {
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

function scrollToQuestion(num) {
    document.querySelectorAll('.sheet-btn').forEach(b => b.classList.remove('current'));
    const btn = document.getElementById('sheet-' + num);
    if (btn && !btn.classList.contains('done') && !btn.classList.contains('marked')) {
        btn.classList.add('current');
    }
    const el = document.getElementById('q' + num);
    if (el) {
        document.querySelectorAll('.question-block').forEach(q => q.classList.remove('highlight-active'));
        el.scrollIntoView({ behavior: 'auto', block: 'center' });
        el.classList.add('highlight-active');
        setTimeout(() => el.classList.remove('highlight-active'), 2000);
        currentQ = num;
    }
}

// ── MODAL NỘP BÀI ─────────────────────────────────────────────
function submitExam(autoSubmit = false) {
    if (_isSubmitting) return;
    _isAutoSubmit = autoSubmit;

    const modal     = document.getElementById('submitModal');
    const title     = document.getElementById('modalTitle');
    const msg       = document.getElementById('modalMsg');
    const cancelBtn = document.getElementById('modalCancelBtn');

    if (autoSubmit) {
        title.textContent = 'Hết giờ làm bài!';
        cancelBtn.style.display = 'none';

        let countdown = 5;
        msg.textContent = `Bạn đã trả lời ${answered.size}/${totalQ} câu. Hệ thống sẽ tự động nộp bài sau ${countdown} giây...`;

        const interval = setInterval(() => {
            countdown--;
            msg.textContent = `Bạn đã trả lời ${answered.size}/${totalQ} câu. Hệ thống sẽ tự động nộp bài sau ${countdown} giây...`;
            if (countdown <= 0) clearInterval(interval);
        }, 1000);

        setTimeout(() => confirmSubmit(), 5000);
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

function handleManualSubmit() {
    sessionStorage.removeItem(STORAGE_END_TIME);
    confirmSubmit();
}

function confirmSubmit() {
    if (_isSubmitting) return;
    _isSubmitting = true;
    if (typeof timerInterval !== 'undefined') clearInterval(timerInterval);
    if (_isAutoSubmit && window.examExpiredAt) {
        document.getElementById('expiredAt').value = window.examExpiredAt;
    }
    clearExamData();
    document.getElementById('examForm').submit();
}

// ── TOGGLE SIDEBAR ────────────────────────────────────────────
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

// ── KHỞI TẠO ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadSavedState);
history.scrollRestoration = 'manual';

// ── CHẶN BACK/FORWARD ────────────────────────────────────────
function pushGuardState() {
    history.pushState({ examGuard: true, scrollY: window.scrollY }, '');
}
pushGuardState();

let _modalOpen = false;

window.addEventListener('popstate', function (e) {
    if (_isSubmitting) return;

    const targetScrollY = e.state?.scrollY ?? window.scrollY;
    pushGuardState();
    requestAnimationFrame(() => window.scrollTo(0, targetScrollY));

    if (_modalOpen) return;

    // Đọc thẳng từ sessionStorage, không phụ thuộc DOMContentLoaded
    let hasAnswers = answered.size > 0 || marked.size > 0;
    if (!hasAnswers) {
        try {
            const sa = sessionStorage.getItem(STORAGE_ANSWERS);
            const sm = sessionStorage.getItem(STORAGE_MARKED);
            if (sa && Object.keys(JSON.parse(sa)).length > 0) hasAnswers = true;
            if (sm && JSON.parse(sm).length > 0) hasAnswers = true;
        } catch(e) {}
    }

    if (!hasAnswers) {
        // Chưa làm gì → thoát thẳng
        clearExamData();
        _isSubmitting = true;
        history.go(-2);
        return;
    }

    // Có đáp án → hỏi xác nhận
    _modalOpen = true;
    document.getElementById('exitModal')?.classList.add('is-open');
});

let scrollTimeout;
window.addEventListener('scroll', function () {
    clearTimeout(scrollTimeout);
    scrollTimeout = setTimeout(() => {
        if (!_modalOpen) {
            history.replaceState({ examGuard: true, scrollY: window.scrollY }, '');
        }
    }, 100);
});

document.getElementById('exitCancelBtn').addEventListener('click', function () {
    document.getElementById('exitModal').classList.remove('is-open');
    _modalOpen = false;
});

document.getElementById('exitConfirmBtn').addEventListener('click', function () {
    clearExamData();
    _isSubmitting = true;
    history.go(-2);
});

// ── CẢNH BÁO KHI THOÁT ───────────────────────────────────────
window.addEventListener('beforeunload', function (e) {
    if (_isSubmitting) return;
    e.preventDefault();
    e.returnValue = '';
});

// ── KIỂM TRA ĐỀ THI CÒN TỒN TẠI ─────────────────────────────
(function checkExamAlive() {
    const CHECK_INTERVAL = 10 * 1000;

    async function ping() {
        if (_isSubmitting) return;
        try {
            const res  = await fetch('/api/exam-check?examId=' + examId);
            const data = await res.json();
            if (!data.exists) {
                clearInterval(timerInterval);
                clearExamData();
                showExamDeletedModal();
            }
        } catch (e) {}
    }

    ping();
    setInterval(ping, CHECK_INTERVAL);
})();

function showExamDeletedModal() {
    const modal      = document.getElementById('submitModal');
    const title      = document.getElementById('modalTitle');
    const msg        = document.getElementById('modalMsg');
    const cancelBtn  = document.getElementById('modalCancelBtn');
    const confirmBtn = document.getElementById('modalConfirmBtn');

    title.textContent = 'Đề thi không còn tồn tại';
    msg.textContent   = 'Đề thi này không còn tồn tại trên hệ thống. Kết quả của bạn không được lưu!';
    cancelBtn.style.display = 'none';
    confirmBtn.textContent  = 'Về trang chủ';
    confirmBtn.onclick = function () {
        _isSubmitting = true;
        clearExamData();
        setTimeout(() => { window.location.href = '/'; }, 0);
    };

    modal.classList.add('is-open');
}


// ── OFFLINE DETECTION ─────────────────────────────────────────
let _isOffline = false;

async function checkConnection() {
    if (_isSubmitting) return;
    try {
        const res = await fetch('/api/exam-check?examId=' + examId + '&_=' + Date.now(), {
            method: 'GET',
            cache: 'no-store',
            signal: AbortSignal.timeout(3000) 
        });

        if (res.ok) {
            if (_isOffline) {
                _isOffline = false;
                hideOfflineBanner();
            }
        } else {
            throw new Error('Server error');
        }
    } catch (e) {
        if (!_isOffline) {
            _isOffline = true;
            showOfflineBanner();
        }
    }
}

function showOfflineBanner() {
    document.getElementById('offlineBanner').style.display = 'block';
}

function hideOfflineBanner() {
    document.getElementById('offlineBanner').style.display = 'none';
}

setInterval(checkConnection, 3000);

checkConnection();


window.addEventListener('offline', showOfflineBanner);
window.addEventListener('online', function() {
    checkConnection(); 
});

const _originalConfirmSubmit = confirmSubmit;
window.confirmSubmit = function () {
    if (_isOffline) {
        closeSubmitModal();
        showOfflineBanner();
        return;
    }
    _originalConfirmSubmit();
};


</script>