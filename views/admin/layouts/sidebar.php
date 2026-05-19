<ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= $base ?>/">
        <div class="sidebar-brand-icon">
            <img src="<?= $base ?>/images/logo.png">
        </div>
    </a>

    <!-- Lấy URL hiện tại để kiểm tra active -->
    <?php $current_uri = $_SERVER['REQUEST_URI']; ?>

    <hr class="sidebar-divider my-0">
    <!-- Dashboard -->
    <li class="nav-item <?= ($current_uri == '/admin/dashboard') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link home-link" href="/admin/dashboard">
            <i class="fas fa-fw fa-home"></i><span>Trang chủ</span>
        </a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading custom-sidebar-heading">Nội dung đào tạo</div>

    <!-- Quản lý Danh mục -->
    <li class="nav-item <?= ($current_uri == '/admin/categories') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/categories"><span>Quản lý Danh mục</span></a>
    </li>
      
    <!-- Quản lý Khối lớp -->
    <li class="nav-item <?= ($current_uri == '/admin/grades') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/grades"><span>Quản lý Khối lớp</span></a>
    </li>

    <!-- Quản lý Môn học -->
    <li class="nav-item <?= ($current_uri == '/admin/subjects') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/subjects"><span>Quản lý Môn học</span></a>
    </li>

    <!-- Quản lý Chương học -->
    <li class="nav-item <?= ($current_uri == '/admin/chapters') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/chapters"><span>Quản lý Chương học</span></a>
    </li>

    <!-- Quản lý Bài học -->
    <li class="nav-item <?= ($current_uri == '/admin/lessons') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/lessons"><span>Quản lý Bài học</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading custom-sidebar-heading">Quản lý ôn luyện</div>

    <!-- Ngân hàng Câu hỏi -->
    <li class="nav-item <?= ($current_uri == '/admin/questions') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/questions"><span>Ngân hàng Câu hỏi</span></a>
    </li>

    <!-- Quản lý Đề thi -->
    <li class="nav-item <?= ($current_uri == '/admin/exams') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/exams"><span>Quản lý Đề thi</span></a>
    </li>

    <!-- Quản lý Kết quả -->
    <li class="nav-item <?= ($current_uri == '/admin/results') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/results"><span>Quản lý Kết quả</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading custom-sidebar-heading">Tin tức & Hướng dẫn</div>

    <!-- Quản lý Bài viết -->
    <li class="nav-item <?= ($current_uri == '/admin/posts') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/posts"><span>Quản lý Bài viết</span></a>
    </li>

    <hr class="sidebar-divider">
    <div class="sidebar-heading custom-sidebar-heading">Hệ thống</div>

    <!-- Quản lý Người dùng -->
    <li class="nav-item <?= ($current_uri == '/admin/users') ? 'active' : '' ?>">
        <a class="nav-link custom-nav-link" href="/admin/users"><span>Quản lý Người dùng</span></a>
    </li>
</ul>
