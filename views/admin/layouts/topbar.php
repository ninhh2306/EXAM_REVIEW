<?php
$adminId     = $_SESSION['user_id'] ?? null;
$adminName   = $_SESSION['user_name'] ?? 'Admin';
$adminAvatar = $_SESSION['avatar']   ?? null;
?>

<nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">

    <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown no-arrow">

            <!-- Thêm class custom-user-dropdown để cấu hình CSS -->
            <a class="nav-link dropdown-toggle custom-user-dropdown"
               href="#" id="userDropdown" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                <?php if (!empty($adminAvatar)): ?>
                    <!-- Thay style inline bằng class avatar-img -->
                    <img class="img-profile rounded-circle avatar-img"
                         src="<?= htmlspecialchars($adminAvatar) ?>" 
                         alt="Avatar">
                <?php else: ?>
                    <!-- Thay style inline bằng class avatar-text-box -->
                    <div class="avatar-text-box">
                        <?= mb_substr($adminName, 0, 1, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <span class="ml-2 text-white small">
                    <?= htmlspecialchars($adminName) ?>
                </span>

                <i class="fas fa-caret-down ml-1"></i>
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                 aria-labelledby="userDropdown">

                <a class="dropdown-item" href="/profile">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Hồ sơ
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item" href="/logout">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Đăng xuất
                </a>

            </div>
        </li>
    </ul>

</nav>
