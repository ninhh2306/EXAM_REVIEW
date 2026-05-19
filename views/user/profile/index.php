<?php
/**
 * @var array       $user
 * @var string|null $flashSuccess
 * @var string|null $flashError
 * @var array       $profileErrors
 * @var array       $profileOld
 * @var string|null $openForm
 */

$base = $base ?? '';

$flashSuccess = $flashSuccess ?? null;
$flashError   = $flashError ?? null;

$profileErrors = $profileErrors ?? [];
$profileOld    = $profileOld ?? [];
$openForm      = $openForm ?? null;
$hasProfileError = $hasProfileError ?? false;

// Format ngày cập nhật
$updatedAt = $user['updatedAt'] ?? $user['createdAt'] ?? null;

$lastModified = $updatedAt
    ? (new DateTime($updatedAt))->format('d/m/Y \l\ú\c H:i')
    : '—';

// Avatar
$avatarSrc = !empty($user['avatar'])
    ? $base . $user['avatar']
    : null;

// Initial fallback
$initials = mb_strtoupper(
    mb_substr(
        trim($user['fullName'] ?? 'U'),
        0,
        1,
        'UTF-8'
    ),
    'UTF-8'
);
?>

<div class="container">
<div class="profile-page">

    <!-- FLASH -->
    <?php if (!empty($flashSuccess)): ?>
        <div class="profile-flash profile-flash--success" id="profileFlash">
            <i class="fa-solid fa-circle-check"></i>
            <?= htmlspecialchars($flashSuccess) ?>
        </div>

    <?php elseif (!empty($flashError)): ?>
        <div class="profile-flash profile-flash--error" id="profileFlash">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= htmlspecialchars($flashError) ?>
        </div>
    <?php endif; ?>

    

    <!-- HEADER -->
    <div class="profile-header">

        <h1 class="profile-header__title">
            Thông tin cá nhân
        </h1>

        <p class="profile-header__sub">
            Chào
            <strong>
                <?= htmlspecialchars($user['fullName']) ?>
            </strong>!

            Hãy kiểm tra lại các thông tin phía dưới để tối ưu trải nghiệm học tập
            <br>
            và luôn an tâm về bảo mật thông tin riêng tư của bạn.
        </p>

    </div>

    <div class="profile-card">

        <!-- ================= AVATAR ================= -->
        <div class="profile-avatar-section">

            <form
                method="POST"
                action="<?= $base ?>/profile/update-avatar"
                enctype="multipart/form-data"
                id="avatarForm"
            >

                <div class="profile-avatar-wrapper">

                    <?php if ($avatarSrc): ?>

                        <img
                            src="<?= htmlspecialchars($avatarSrc) ?>"
                            alt="Avatar"
                            class="profile-avatar"
                            id="avatarPreview"
                        >

                    <?php else: ?>

                        <div
                            class="profile-avatar default-avatar default-avatar--lg"
                            id="avatarPreview"
                        >
                            <?= $initials ?>
                        </div>

                    <?php endif; ?>

                    <!-- Nút edit -->
                    <label
                        for="avatarInput"
                        class="profile-avatar-edit-btn"
                        title="Đổi ảnh"
                    >
                        <i class="fa-solid fa-pen"></i>
                    </label>

                    <input
                        type="file"
                        name="avatar"
                        id="avatarInput"
                        accept="image/*"
                        hidden
                    >

                </div>

                <p class="profile-avatar-label">
                    Đổi ảnh đại diện
                </p>

                <button
                    type="submit"
                    class="profile-btn profile-btn--primary profile-avatar-submit"
                    id="avatarSubmitBtn"
                    style="display:none">
                    Lưu ảnh
                </button>

            </form>

        </div>

        <div class="profile-divider"></div>

        <!-- ================= HỌ TÊN ================= -->
        <div class="profile-field" id="fieldName">

            <div class="profile-field__info">

                <span class="profile-field__label">
                    HỌ TÊN
                </span>

                <span class="profile-field__value">
                    <?= htmlspecialchars($user['fullName'] ?? '') ?>
                </span>

            </div>

            <button
                class="profile-field__edit-btn"
                onclick="toggleEdit('name')"
                title="Chỉnh sửa">
                <i class="fa-regular fa-pen-to-square"></i>
            </button>

            <!-- Form -->
            <form
                method="POST"
                action="<?= $base ?>/profile/update-name"
                class="profile-inline-form"
                id="formName"
                style="<?= $openForm === 'name'
                    ? 'display:flex'
                    : 'display:none' ?>">

                <input
                    type="text"
                    name="fullName"
                    class="profile-input"
                    placeholder="Nhập họ và tên"
                    required
                    value="<?= htmlspecialchars(
                        $profileOld['fullName']
                        ?? $user['fullName']
                        ?? ''
                    ) ?>">

                <?php if (!empty($profileErrors['name'])): ?>

                    <div class="profile-form-error">
                        <?= htmlspecialchars($profileErrors['name']) ?>
                    </div>

                <?php endif; ?>

                <div class="profile-inline-actions">

                    <button
                        type="submit"
                        class="profile-btn profile-btn--primary profile-btn--sm">
                        Lưu
                    </button>

                    <button
                        type="button"
                        class="profile-btn profile-btn--ghost profile-btn--sm"
                        onclick="toggleEdit('name')">
                        Hủy
                    </button>

                </div>

            </form>

        </div>

        <div class="profile-divider"></div>

        <!-- ================= EMAIL ================= -->
        <div class="profile-field" id="fieldEmail">

            <div class="profile-field__info">

                <span class="profile-field__label">
                    EMAIL
                </span>

                <span class="profile-field__value">
                    <?= htmlspecialchars($user['email'] ?? '') ?>
                </span>

            </div>

            <button
                class="profile-field__edit-btn"
                onclick="toggleEdit('email')"
                title="Chỉnh sửa">
                <i class="fa-regular fa-pen-to-square"></i>
            </button>

            <form
                method="POST"
                action="<?= $base ?>/profile/update-email"
                class="profile-inline-form"
                id="formEmail"
                style="<?= $openForm === 'email'
                    ? 'display:flex'
                    : 'display:none' ?>">

                <input
                    type="email"
                    name="email"
                    class="profile-input"
                    placeholder="Nhập email"
                    required
                    value="<?= htmlspecialchars(
                        $profileOld['email']
                        ?? $user['email']
                        ?? ''
                    ) ?>">

                <?php if (!empty($profileErrors['email'])): ?>

                    <div class="profile-form-error">
                        <?= htmlspecialchars($profileErrors['email']) ?>
                    </div>

                <?php endif; ?>

                <div class="profile-inline-actions">

                    <button
                        type="submit"
                        class="profile-btn profile-btn--primary profile-btn--sm">
                        Lưu
                    </button>

                    <button
                        type="button"
                        class="profile-btn profile-btn--ghost profile-btn--sm"
                        onclick="toggleEdit('email')">
                        Hủy
                    </button>

                </div>

            </form>

        </div>

        <div class="profile-divider"></div>

        <!-- ================= PASSWORD ================= -->
        <div class="profile-action-row" id="fieldPassword">

            <div class="profile-action-row__left">

                <i class="fa fa-lock profile-action-row__icon"></i>

                <div>

                    <span class="profile-action-row__title">
                        Đổi mật khẩu
                    </span>

                    <span class="profile-action-row__sub">
                        Cập nhật mật khẩu đăng nhập
                    </span>

                </div>

            </div>

            <button
                class="profile-action-row__arrow"
                onclick="toggleEdit('password')">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- FORM PASSWORD -->
            <form
                method="POST"
                action="<?= $base ?>/profile/update-password"
                class="profile-password-form"
                id="formPassword"
                style="<?= $openForm === 'password'
                    ? 'display:flex'
                    : 'display:none' ?>">

                <div class="profile-input-wrapper">

                    <input
                        type="password"
                        name="current_password"
                        class="profile-input"
                        placeholder="Mật khẩu hiện tại"
                        required>

                    <button
                        type="button"
                        class="profile-eye-btn"
                        onclick="togglePassword(this)">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>

                </div>

                <div class="profile-input-wrapper">

                    <input
                        type="password"
                        name="new_password"
                        class="profile-input"
                        placeholder="Mật khẩu mới (tối thiểu 6 ký tự)"
                        minlength="6"
                        required>

                    <button
                        type="button"
                        class="profile-eye-btn"
                        onclick="togglePassword(this)">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>

                </div>

                <div class="profile-input-wrapper">

                    <input
                        type="password"
                        name="confirm_password"
                        class="profile-input"
                        placeholder="Xác nhận mật khẩu mới"
                        required>

                    <button
                        type="button"
                        class="profile-eye-btn"
                        onclick="togglePassword(this)">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>

                </div>

                <?php if (!empty($profileErrors['password'])): ?>

                    <div class="profile-form-error">
                        <?= htmlspecialchars($profileErrors['password']) ?>
                    </div>

                <?php endif; ?>

                <div class="profile-inline-actions">

                    <button
                        type="submit"
                        class="profile-btn profile-btn--primary profile-btn--sm">
                        Lưu
                    </button>

                    <button
                        type="button"
                        class="profile-btn profile-btn--ghost profile-btn--sm"
                        onclick="toggleEdit('password')">
                        Hủy
                    </button>

                </div>

            </form>

        </div>

        <div class="profile-divider"></div>


        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>

            <!-- ================= DELETE ACCOUNT ================= -->
            <div class="profile-action-row profile-action-row--danger">

                <div class="profile-action-row__left">

                    <i class="fa-regular fa-trash-can profile-action-row__icon"></i>
                    <div>
                        <span class="profile-action-row__title">
                            Xóa tài khoản
                        </span>

                        <span class="profile-action-row__sub">
                            Xóa vĩnh viễn toàn bộ dữ liệu
                        </span>
                    </div>
                </div>

                <button
                    class="profile-action-row__arrow"
                    onclick="document.getElementById('deleteModal').style.display='flex'">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>

            </div>

        <?php endif; ?>

        <!-- FOOTER -->
        <div class="profile-card-footer">

            <span class="profile-footer-meta">
                Cập nhật lần cuối: <?= $lastModified ?>
            </span>

        </div>

    </div>

</div>
</div>


<?php if (($user['role'] ?? 'user') !== 'admin'): ?>

<!-- ================= DELETE MODAL ================= -->
<div
    class="profile-modal-backdrop"
    id="deleteModal"
    style="display:none">

    <div class="profile-modal">

        <h3 class="profile-modal__title">
            Xác nhận xóa tài khoản
        </h3>

        <p class="profile-modal__desc">
            Hành động này <strong>không thể hoàn tác</strong>.
            Toàn bộ dữ liệu của bạn sẽ bị xóa vĩnh viễn.
            Nhập mật khẩu để xác nhận.
        </p>

        <form
            method="POST"
            action="<?= $base ?>/profile/delete-account">

            <input
                type="password"
                name="confirm_delete_password"
                class="profile-input"
                placeholder="Nhập mật khẩu của bạn"
                required>

            <div class="profile-modal__actions">
                <button
                    type="submit"
                    class="profile-btn profile-btn--danger">
                    Xóa tài khoản
                </button>

                <button
                    type="button"
                    class="profile-btn profile-btn--ghost"
                    onclick="document.getElementById('deleteModal').style.display='none'">
                    Hủy
                </button>
            </div>

        </form>

    </div>

</div>

<?php endif; ?>


<script>
// Toggle inline form
function toggleEdit(field)
{
    const forms = {
        name: 'formName',
        email: 'formEmail',
        password: 'formPassword',
    };

    const formId = forms[field];

    if (!formId) return;

    const form = document.getElementById(formId);

    const isHidden =
        form.style.display === 'none';

    // Đóng tất cả
    Object.values(forms).forEach(id => {

        const el = document.getElementById(id);

        if (el) {
            el.style.display = 'none';
        }
    });

    // Mở form được chọn
    if (isHidden) {

        form.style.display = 'flex';

        const firstInput =
            form.querySelector('input');

        if (firstInput) {
            firstInput.focus();
        }
    }
}

// Preview avatar
document
.getElementById('avatarInput')
.addEventListener('change', function (e)
{
    const file = e.target.files[0];

    if (!file) return;

    const reader = new FileReader();

    reader.onload = function (ev)
    {
        const preview =
            document.getElementById('avatarPreview');

        // fallback -> img
        if (preview.tagName === 'DIV')
        {
            const img = document.createElement('img');

            img.src = ev.target.result;
            img.id = 'avatarPreview';
            img.className = 'profile-avatar';
            img.alt = 'Avatar';

            preview.replaceWith(img);
        }
        else
        {
            preview.src = ev.target.result;
        }

        document
            .getElementById('avatarSubmitBtn')
            .style.display = 'inline-flex';
    };

    reader.readAsDataURL(file);
});

// Toggle password
function togglePassword(btn)
{
    const input =
        btn.closest('.profile-input-wrapper')
        .querySelector('input');

    const icon =
        btn.querySelector('i');

    if (input.type === 'password')
    {
        input.type = 'text';

        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
    else
    {
        input.type = 'password';

        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}

// Auto hide flash
(function ()
{
    const flash =
        document.getElementById('profileFlash');

    if (!flash) return;

    setTimeout(() =>
    {
        flash.style.opacity = '0';
        flash.style.transition = 'opacity .4s';

        setTimeout(() =>
        {
            flash.remove();
        }, 400);

    }, 4000);
})();

// Close modal click backdrop
document
.getElementById('deleteModal')
.addEventListener('click', function (e)
{
    if (e.target === this)
    {
        this.style.display = 'none';
    }
});



// Chỉ lưu scroll nếu submit form
document.querySelectorAll('form').forEach(form => {

    form.addEventListener('submit', () => {

        sessionStorage.setItem(
            'profileScrollY',
            window.scrollY
        );
    });

});

window.addEventListener('load', () => {

    // Chỉ restore scroll khi có lỗi
    const hasError = <?= $hasProfileError ? 'true' : 'false' ?>;

    if (!hasError) {

        // Success -> clear scroll cũ
        sessionStorage.removeItem('profileScrollY');

        // Scroll lên đầu
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

        return;
    }

    // Error -> restore vị trí cũ
    const scrollY = sessionStorage.getItem('profileScrollY');

    if (scrollY !== null) {

        window.scrollTo({
            top: parseInt(scrollY),
            behavior: 'instant'
        });
    }

});



</script>