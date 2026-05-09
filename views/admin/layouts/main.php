<?php
$base = "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $title ?? 'Admin' ?></title>
  <link href="<?= $base ?>/images/logo.png" rel="icon">
  <link href="<?= $base ?>/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="<?= $base ?>/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="/css/ruang-admin.min.css" rel="stylesheet">
  <link href="/css/admin.css" rel="stylesheet">
  
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
    <?php include ROOT . "/views/admin/layouts/sidebar.php"; ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- Topbar -->
            <?php include ROOT . "/views/admin/layouts/topbar.php"; ?>

            <!-- Container chính tạo khoảng cách lề (Padding) -->
            <div class="container-fluid" id="container-wrapper">
                
                <!-- Nội dung view (Dashboard, List, v.v.) -->
                <?php if (!empty($viewPath) && file_exists($viewPath)): ?>
                    <?php require $viewPath; ?>
                <?php else: ?>
                    <p>View không tồn tại</p>
                <?php endif; ?>
                
            </div>
            <!---Container Fluid-->
        </div>

        <!-- Footer -->
        <?php include ROOT . "/views/admin/layouts/footer.php"; ?>
    </div>
</div>

<script src="<?= $base ?>/vendor/jquery/jquery.min.js"></script>
<script src="<?= $base ?>/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="<?= $base ?>/js/ruang-admin.min.js"></script>

<!-- Thư viện Chart.js (Nên load ở tất cả các trang nếu có dùng chung) -->
<script src="<?= $base ?>/vendor/chart.js/Chart.min.js"></script>

<!-- Chỉ load file điều khiển biểu đồ khi ở trang Dashboard -->
<?php 
    // Kiểm tra nếu URL có chứa chữ 'dashboard'
    if (strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false): 
?>
    <script src="<?= $base ?>/js/demo/chart-area-demo.js"></script>

<?php endif; ?>


<script src="<?= $base ?>/js/admin.js"></script>
</body>
</html>
