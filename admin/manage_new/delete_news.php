<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';

// Kiểm tra ID tin tức cần xóa
if (!isset($_GET["id"])) {
    header("Location: manage_news.php");
    exit;
}

$news_id = $_GET["id"];

// Lấy thông tin tin tức để xác nhận xóa
$sql = "SELECT title, image FROM news WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    echo "<script>alert('Tin tức không tồn tại!'); window.location.href = 'manage_news.php';</script>";
    exit;
}

// Nếu admin xác nhận xóa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql_delete = "DELETE FROM news WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $news_id);

    if ($stmt_delete->execute()) {
        echo "<script>alert('Đã xóa tin tức!'); window.location.href = 'manage_news.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi xóa!');</script>";
    }
}
?>

<?php include '../../partials/header.php'; ?>

<div class="container mt-4">
    <h2>🗑️ Xóa Tin Tức</h2>
    <div class="card">
        <div class="card-body">
            <h4>Bạn có chắc chắn muốn xóa tin tức?</h4>
            <p><strong>Tiêu đề:</strong> <?= htmlspecialchars($news['title']) ?></p>
            <p><strong>Ảnh đại diện:</strong></p>
            <img src="<?= htmlspecialchars($news['image']) ?>" alt="Ảnh tin tức" class="img-fluid" style="max-width: 200px;">

            <form method="POST" class="mt-3">
                <button type="submit" class="btn btn-danger">❌ Xác nhận xóa</button>
                <a href="manage_news.php" class="btn btn-secondary">⬅️ Quay lại</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../partials/footer.php'; ?>
