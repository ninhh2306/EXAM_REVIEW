<?php

/** @var string|null $flashError */
/** @var array $flashOld */

$old = $flashOld ?? [];

?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/users">Người dùng</a>
        <span>›</span>
        <span>Thêm mới</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Thêm Người dùng
    </div>

    <div class="card subject-card">

        <?php if ($flashError): ?>
            <div class="alert-error" id="autoAlert">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="/admin/users/store"
              enctype="multipart/form-data"
              autocomplete="off">

            <div class="form-grid-2">

                <!-- LEFT -->
                <div class="form-left">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text"
                               name="fullName"
                               value="<?= htmlspecialchars($old['fullName'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu</label>

                        <input type="password"
                                     name="password"
                                     autocomplete="new-password">
                    </div>

                    <!-- ROLE -->
                    <div class="form-group">
                        <label>Vai trò</label>
                        <div class="role-group">
                            <label class="role-item">
                                <input type="radio"
                                       name="role"
                                       value="user"
                                       <?= ($old['role'] ?? 'user') === 'user' ? 'checked' : '' ?>>
                                User
                            </label>

                            <label class="role-item">
                                <input type="radio"
                                       name="role"
                                       value="admin"
                                       <?= ($old['role'] ?? '') === 'admin' ? 'checked' : '' ?>>
                                Admin
                            </label>
                        </div>
                    </div>

                    <!-- STATUS -->
                    <div class="form-group">
                        <label>Trạng thái</label>
                        <label class="switch">
                            <input type="checkbox"
                                    name="status"
                                    value="1"
                                    <?= ($old['status'] ?? 1) == 1
                                            ? 'checked'
                                            : '' ?>>

                            <span class="slider round"></span>

                        </label>
                    </div>
                </div>

                <!-- RIGHT -->
                <!-- AVATAR BLOCK NEW -->
                <div class="admin-user-avatar-box">
                    <input
                        type="file"
                        name="avatar"
                        id="adminAvatarInput"
                        accept="image/*"
                        hidden>

                    <div
                        class="admin-user-avatar-preview"
                        id="adminAvatarPreview">

                        <div
                            class="admin-user-avatar-letter default-avatar default-avatar--lg"
                            id="adminAvatarLetter">
                            <?= strtoupper(substr($old['fullName'] ?? 'U', 0, 1)) ?>
                        </div>

                        <button
                            type="button"
                            class="admin-user-avatar-edit"
                            id="adminAvatarButton">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- ACTION -->
            <div class="form-actions">

                <button class="admin-btn btn-save">
                    Lưu
                </button>

                <a href="/admin/users"
                   class="admin-btn btn-cancel">
                    Hủy
                </a>
            </div>

        </form>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('adminAvatarInput');
    const preview = document.getElementById('adminAvatarPreview');
    const button = document.getElementById('adminAvatarButton');
    const nameInput = document.querySelector(
        'input[name="fullName"]'
    );

    // =========== CHECK VALIDATION ERROR FIRST ==========

    const hasValidationError =
        document.getElementById('autoAlert');


    if (!hasValidationError) {

        sessionStorage.removeItem(
            'admin_user_avatar_preview'
        );
    }

    // ============= CLICK OPEN FILE ====================

    button.addEventListener('click', () => {
        input.click();
    });

    // ============ UPDATE LETTER WHEN TYPE NAME =================

    function renderLetterAvatar() {

        const oldImg =
            document.getElementById('adminAvatarImage');

        if (oldImg) return;

        let oldLetter =
            document.getElementById('adminAvatarLetter');

        const val = nameInput.value.trim();

        const letter = val
            ? val.charAt(0).toUpperCase()
            : 'U';

        if (!oldLetter) {

            oldLetter = document.createElement('div');

            oldLetter.id = 'adminAvatarLetter';

            oldLetter.className =
                'admin-user-avatar-letter';

            preview.prepend(oldLetter);
        }

        oldLetter.innerText = letter;
    }

    renderLetterAvatar();

    nameInput.addEventListener(
        'input',
        renderLetterAvatar
    );

    // ========= PREVIEW IMAGE ==================

    input.addEventListener('change', function(e){

        const file = e.target.files[0];

        if (!file) return;

        const reader = new FileReader();

        reader.onload = function(event){

            let img =
                document.getElementById(
                    'adminAvatarImage'
                );

            if (!img) {

                img = document.createElement('img');

                img.id = 'adminAvatarImage';

                preview.prepend(img);
            }

            img.src = event.target.result;

            const letter =
                document.getElementById(
                    'adminAvatarLetter'
                );

            if (letter) {
                letter.remove();
            }

            // SAVE TEMP AVATAR

            sessionStorage.setItem(
                'admin_user_avatar_preview',
                event.target.result
            );
        };

        reader.readAsDataURL(file);
    });

    // =========== RESTORE PREVIEW AFTER VALIDATION ERROR ===============

    const savedPreview = sessionStorage.getItem(
        'admin_user_avatar_preview'
    );

    if (savedPreview) {

        let img =
            document.getElementById(
                'adminAvatarImage'
            );

        if (!img) {

            img = document.createElement('img');

            img.id = 'adminAvatarImage';

            preview.prepend(img);
        }

        img.src = savedPreview;

        const letter =
            document.getElementById(
                'adminAvatarLetter'
            );

        if (letter) {
            letter.remove();
        }
    }

});

</script>