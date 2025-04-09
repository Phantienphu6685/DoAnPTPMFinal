


<?php
// Bắt đầu session để lưu thông tin user đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Cho Thuê Phòng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/thue_phong/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="/thue_phong/index.php">🏠 Phòng Trọ Sài Gòn</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/thue_phong/index.php"><i class="fa fa-home"></i>  Trang Chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="/thue_phong/news.php">Tin tức</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/thue_phong/user/dashboard.php">👤 Tài Khoản</a></li>
                    <li class="nav-item"><a class="nav-link text-danger" href="/thue_phong/auth/logout.php">Đăng Xuất</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/thue_phong/auth/login.php">Đăng Nhập</a></li>
                    <li class="nav-item"><a class="nav-link" href="/thue_phong/auth/register.php">Đăng Ký</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <a href="/auth/logout.php" class="btn btn-danger">Đăng xuất</a>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
