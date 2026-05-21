<?php
/** @var array $subjects */
/** @var array $exams */
/** @var array|null $currentSubject */
?>

<section class="exam-hero-section">
    <div class="container">

        <div class="breadcrumb">
            <a href="/">Trang chủ</a>
            <span>›</span>
            <span>Đề thi THPT Quốc Gia</span>
            <?php if (!empty($currentSubject)): ?>
                <span>›</span>
                <span><?= htmlspecialchars($currentSubject['subjectName']) ?></span>
            <?php endif; ?>
        </div>

        <h1 class="exam-hero__title">Đề thi THPT Quốc Gia</h1>

        <div class="exam-hero__meta">
            <span>
                <i class="fa-solid fa-graduation-cap"></i>
                Luyện thi THPT lớp 12
            </span>
            <span>
                <i class="fa-regular fa-file-lines"></i>
                <?= count($exams) ?> đề thi
            </span>
        </div>

    </div>
</section>


<section class="index-list-section">
<div class="container">

    <div class="thpt-layout">

        <!-- CỘT TRÁI: QUICK CREATE -->
        <div class="thpt-sidebar">

            <div class="thpt-sidebar-top">
                <span class="thpt-subject-btn thpt-subject-btn--quick" style="margin-bottom: 0; width: 100%; cursor: default;">
                    <i class="fa-solid fa-bolt"></i> Tạo đề nhanh
                </span>
            </div>

            <div class="quick-exam-card" id="quick-exam-form" style="margin-top: 24px;">
                <form id="quickExamForm">

                    <?php if (empty($currentSubject)): ?>
                    <div class="quick-form-group">
                        <label>Môn học</label>
                        <select name="subjectId" id="subjectSelect">
                            <option value="">Chọn môn học</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['subjectId'] ?>">
                                    <?= htmlspecialchars($subject['subjectName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="subjectId" value="<?= $currentSubject['subjectId'] ?>">
                    <?php endif; ?>

                    <div class="quick-form-group">
                        <label>Số lượng câu hỏi</label>
                        <select name="totalQuestions" id="questionCount">
                            <option value="20">20 câu</option>
                            <option value="40">40 câu</option>
                            <option value="50">50 câu</option>
                        </select>
                    </div>

                    <div class="quick-form-group">
                        <label>Thời gian</label>
                        <input type="text" id="durationInput" value="30 phút" readonly>
                    </div>

                    <div class="quick-form-group">
                        <div class="quick-level-header">
                            <label>Mẫu đề</label>
                            <span class="quick-note">Nhận biết – Thông hiểu – Vận dụng</span>
                        </div>
                        <div class="quick-radio-list">
                            <label class="quick-radio-item">
                                <input type="radio" name="template" value="basic" checked>
                                <span>Cơ bản: 50 - 30 - 20</span>
                            </label>
                            <label class="quick-radio-item">
                                <input type="radio" name="template" value="standard">
                                <span>Tiêu chuẩn: 40 - 30 - 30</span>
                            </label>
                            <label class="quick-radio-item">
                                <input type="radio" name="template" value="advanced">
                                <span>Nâng cao: 30 - 40 - 30</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="quick-create-btn">
                        <i class="fa-solid fa-bolt"></i>
                        Tạo đề
                    </button>

                </form>
            </div>

        </div>


        <!-- CỘT PHẢI: FILTER + BÀI THI -->
        <div class="thpt-content">

            <div class="thpt-topbar">
                <a href="/thpt-quoc-gia"
                class="thpt-subject-btn <?= empty($currentSubject) ? 'active' : '' ?>">
                    Tất cả
                </a>

                <?php foreach ($subjects as $subject): ?>
                    <a href="/thpt-quoc-gia/<?= $subject['slug'] ?>"
                       class="thpt-subject-btn <?= (!empty($currentSubject) && $currentSubject['subjectId'] == $subject['subjectId']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($subject['subjectName']) ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($exams)): ?>
                <p class="index-empty">Chưa có đề thi.</p>
            <?php else: ?>
                <div class="index-card-grid">
                    <?php foreach ($exams as $exam): ?>
                    <div class="index-card">
                        <div class="index-card__body">
                            <h3 class="index-card__title">
                                <?= htmlspecialchars($exam['title']) ?>
                            </h3>
                            <div class="index-card__meta">
                                <span><i class="fa-solid fa-book"></i> <?= htmlspecialchars($exam['subjectName']) ?></span>
                                <span><i class="fa-regular fa-circle-question"></i> <?= (int)$exam['realTotalQuestions'] ?> câu</span>
                                <span><i class="fa-regular fa-clock"></i> <?= (int)$exam['duration'] ?> phút</span>
                                <span><i class="fa-solid fa-eye"></i> <?= (int)$exam['viewCount'] ?> lượt làm</span>
                            </div>
                        </div>
                        <div class="index-card__footer">
                            <?php if (empty($_SESSION['user_id'])): ?>
                                <a href="javascript:void(0)"
                                   onclick="openLoginModal()"
                                   class="index-card__btn index-card__btn--green">
                                    <i class="fa-solid fa-pen-to-square"></i> Làm đề
                                </a>
                            <?php else: ?>
                                <?php
                                    $gradeSlug   = $exam['gradeSlug']   ?? '';
                                    $subjectSlug = $exam['subjectSlug'] ?? '';
                                    $examSlug    = $exam['slug']        ?? '';
                                    $examId      = $exam['examId']      ?? '';
                                    $examUrl     = "/{$gradeSlug}/{$subjectSlug}/trac-nghiem/{$examSlug}-{$examId}";
                                ?>
                                <a href="<?= htmlspecialchars($examUrl) ?>"
                                   class="index-card__btn index-card__btn--green">
                                    <i class="fa-solid fa-pen-to-square"></i> Làm đề
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>
</section>


<!-- Modal đăng nhập — dùng chung cho cả "Làm đề" và "Tạo đề nhanh" -->
<div id="loginModal" class="submit-modal" style="display:none;">
    <div class="submit-modal__overlay" onclick="closeLoginModal()"></div>
    <div class="submit-modal__box">
        <h3 class="submit-modal__title">Đăng nhập để ôn luyện</h3>
        <p class="submit-modal__msg">
            Bạn cần đăng nhập để làm bài thi, xem điểm và lưu kết quả học tập
        </p>
        <div class="submit-modal__actions">
            <button class="submit-modal__btn submit-modal__btn--cancel"
                    onclick="closeLoginModal()">Huỷ</button>
            <a href="/login"
               class="submit-modal__btn submit-modal__btn--confirm"
               style="text-decoration:none; text-align:center;">Đăng nhập</a>
        </div>
    </div>
</div>


<script>
// ── Đồng bộ thời gian theo số câu ──────────────────────────────
const questionCount = document.getElementById('questionCount');
const durationInput = document.getElementById('durationInput');

if (questionCount) {
    questionCount.addEventListener('change', function () {
        const map = { '20': '30 phút', '40': '50 phút', '50': '60 phút' };
        durationInput.value = map[this.value] || '30 phút';
    });
}

// ── Modal login ─────────────────────────────────────────────────
function openLoginModal() {
    document.getElementById('loginModal').style.display = 'flex';
}
function closeLoginModal() {
    document.getElementById('loginModal').style.display = 'none';
}

// ── Submit tạo đề nhanh ─────────────────────────────────────────
// Biến PHP truyền trạng thái đăng nhập sang JS
const isLoggedIn = <?= empty($_SESSION['user_id']) ? 'false' : 'true' ?>;

document.getElementById('quickExamForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    // Chưa đăng nhập → mở modal, không gửi request
    if (!isLoggedIn) {
        openLoginModal();
        return;
    }

    const form = e.target;
    const btn  = form.querySelector('.quick-create-btn');

    // Validate môn học nếu đang hiện select
    const subjectSelect = document.getElementById('subjectSelect');
    if (subjectSelect && !subjectSelect.value) {
        alert('Vui lòng chọn môn học!');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tạo...';

    try {
        const res  = await fetch('/thpt-quoc-gia/de-tao-nhanh', {
            method: 'POST',
            body: new FormData(form),
        });

        const data = await res.json();

        if (data.redirect) {
            // Giữ trạng thái loading đến khi chuyển trang
            window.location.href = data.redirect;
            return;
        }

        // Server báo lỗi
        alert(data.error || 'Có lỗi xảy ra, vui lòng thử lại!');

    } catch (err) {
        alert('Có lỗi xảy ra, vui lòng thử lại!');
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Tạo đề';
});
</script>