<?php

/** @var array $user */
/** @var string|null $flashError */
/** @var array $flashOld */

// Ưu tiên flashOld nếu submit lỗi
$d = !empty($flashOld) ? $flashOld : $user;

?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/users">Người dùng</a>
        <span>›</span>
        <span>Cập nhật</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Cập nhật thông tin Người dùng
    </div>

    <div class="card subject-card">

        <?php if ($flashError): ?>
            <div class="alert-error" id="autoAlert">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="/admin/users/update"
              enctype="multipart/form-data"
              autocomplete="off">

            <input type="hidden"
                   name="userId"
                   value="<?= $user['userId'] ?>">

            <div class="form-grid-2">

                <!-- LEFT -->
                <div class="form-left">
                    <div class="form-group">
                        <label>Họ tên</label>

                        <input type="text"
                               name="fullName"
                               value="<?= htmlspecialchars($d['fullName'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>

                        <input type="email"
                               name="email"
                               value="<?= htmlspecialchars($d['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu</label>

                        <input type="password"
                               name="password"
                               autocomplete="new-password"
                               placeholder="Để trống nếu không đổi mật khẩu">
                    </div>

                    <!-- ROLE -->
                    <div class="form-group">
                        <label>Vai trò</label>

                        <div class="role-group">
                            <label class="role-item">
                                <input type="radio"
                                       name="role"
                                       value="user"
                                       <?= ($d['role'] ?? '') === 'user' ? 'checked' : '' ?>>
                                User
                            </label>

                            <label class="role-item">
                                <input type="radio"
                                       name="role"
                                       value="admin"
                                       <?= ($d['role'] ?? '') === 'admin' ? 'checked' : '' ?>>
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
                                    <?= ($d['status'] ?? 1) == 1
                                            ? 'checked'
                                            : '' ?>>

                            <span class="slider round"></span>
                        </label>
                    </div>

                </div>

                <!-- RIGHT -->
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

                        <?php if (!empty($d['avatar'])): ?>
                            <img
                                src="<?= $d['avatar'] ?>"
                                alt="avatar"
                                id="adminAvatarImage"
                            >
                        <?php else: ?>
                            <div
                                class="admin-user-avatar-letter default-avatar default-avatar--lg"
                                id="adminAvatarLetter">
                                <?= strtoupper(substr($d['fullName'] ?? 'U', 0, 1)) ?>
                            </div>

                        <?php endif; ?>

                        <button
                            type="button"
                            class="admin-user-avatar-edit"
                            id="adminAvatarButton">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>

                </div>

            </div>

            <!-- META -->
            <div class="user-meta-info">
                <p>
                    <strong>Ngày tạo:</strong>
                    <?= date('d/m/Y H:i', strtotime($user['createdAt'])) ?>
                </p>

                <p>
                    <strong>Cập nhật lần cuối:</strong>
                    <?= date('d/m/Y H:i', strtotime($user['updatedAt'])) ?>
                </p>

            </div>

            <!-- DANGER -->
            <div class="danger-zone">
                <h3>Vùng nguy hiểm</h3>
                <p>
                    Xóa tài khoản này vĩnh viễn.
                    Hành động này không thể hoàn tác.
                </p>

                <button type="button"
                        class="admin-btn btn-danger"
                        onclick="openDeleteUserModal(<?= $user['userId'] ?>)">

                    Xóa tài khoản
                </button>

            </div>

            <!-- ACTION -->
            <div class="form-actions">

                <button class="admin-btn btn-save">
                    Cập nhật
                </button>

                <a href="/admin/users"
                   class="admin-btn btn-cancel">
                    Hủy
                </a>
            </div>

        </form>

    </div>

</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteUserModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-box">

            <p class="delete-confirm-text">
                Bạn chắc chắn muốn xóa tài khoản này?
            </p>

            <div class="text-center mt-3">
                <a href="#"
                   id="deleteUserLink"
                   class="admin-btn btn-danger">
                    Xóa
                </a>

                <button type="button"
                        class="admin-btn btn-cancel ml-2"
                        data-dismiss="modal">

                    Hủy
                </button>
            </div>

        </div>

    </div>

</div>

<script>

function openDeleteUserModal(id)
{
    document.getElementById('deleteUserLink').href =
        '/admin/users/delete/' + id;

    $('#deleteUserModal').modal('show');
}

document.addEventListener('DOMContentLoaded', () => {

    const input = document.getElementById('adminAvatarInput');
    const preview = document.getElementById('adminAvatarPreview');
    const button = document.getElementById('adminAvatarButton');
    const nameInput = document.querySelector(
        'input[name="fullName"]'
    );

    const STORAGE_KEY =
        'admin_user_avatar_preview_edit_' +
        <?= (int)$user['userId'] ?>;

    // =========== CHECK VALIDATION ERROR ===========

    const hasValidationError =
        document.getElementById('autoAlert');


    if (!hasValidationError) {

        sessionStorage.removeItem(STORAGE_KEY);
    }

    // ============ CLICK OPEN FILE ==============

    button.addEventListener('click', () => {

        input.click();

    });

    // ========== LETTER AVATAR ===============

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

    // ========== PREVIEW IMAGE ==============

    input.addEventListener('change', function(e){

        const file = e.target.files[0];

        if (!file) return;

        const validTypes = [
            'image/jpg',
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif'
        ];

        if (!validTypes.includes(file.type)) {

            alert(
                'Chỉ chấp nhận JPG, JPEG, PNG, WEBP, GIF'
            );

            input.value = '';

            return;
        }

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
                STORAGE_KEY,
                event.target.result
            );
        };

        reader.readAsDataURL(file);

    });

    // ============ RESTORE IMAGE AFTER VALIDATION ERROR ==================

    const savedPreview =
        sessionStorage.getItem(STORAGE_KEY);

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