<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';
include '../partials/header.php';

// Lấy số lượng tin đăng, người dùng, chủ trọ
$total_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms")->fetch_assoc()["count"];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'")->fetch_assoc()["count"];
$total_owners = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'owner'")->fetch_assoc()["count"];
$pending_rooms = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE status = 'pending'")->fetch_assoc()["count"];

?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky">
                <h4 class="text-center mt-3">Admin Panel</h4>
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">📊 Tổng quan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_room/manage_rooms.php">🏠 Quản lý tin đăng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_user/manage_users.php">👤 Quản lý người dùng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_owners.php">🧑‍💼 Quản lý chủ trọ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_new/manage_news.php">📰 Quản lý tin tức</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Nội dung chính -->
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2">
                <h2>📊 Tổng Quan</h2>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Tổng số tin đăng</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $total_rooms ?></h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Số lượng người dùng</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $total_users ?></h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Số lượng chủ trọ</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $total_owners ?></h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-header">Tin đang chờ duyệt</div>
                        <div class="card-body">
                            <h4 class="card-title"><?= $pending_rooms ?></h4>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
