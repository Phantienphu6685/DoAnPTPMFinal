<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';
include '../../partials/header.php';

// Lấy danh sách tin tức
$sql = "SELECT * FROM news ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>📰 Quản Lý Tin Tức</h2>
    <a href="add_news.php" class="btn btn-success mb-3">➕ Thêm Tin</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Ảnh</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= htmlspecialchars($row["title"]) ?></td>
                    <td>
                        <?php if ($row["image"]) { ?>
                            <img src="<?= htmlspecialchars($row["image"]) ?>" width="80" class="img-thumbnail">
                        <?php } else { ?>
                            <span>Không có ảnh</span>
                        <?php } ?>
                    </td>
                    <td>
                        <span class="badge <?= $row["status"] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $row["status"] == 'active' ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td><?= $row["created_at"] ?></td>
                    <td>
                        <a href="edit_news.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                        <a href="delete_news.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa tin này?');">🗑️ Xóa</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../../partials/footer.php'; ?>
