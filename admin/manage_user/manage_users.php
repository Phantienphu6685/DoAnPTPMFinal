<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';
include '../../partials/header.php';

// Lấy danh sách người dùng
$sql = "SELECT id, name, email, phone, role FROM users ORDER BY role, name";
$result = $conn->query($sql);
if (!$result) {
    die("Lỗi SQL: " . $conn->error);
}
?>

<div class="container mt-4">
    <h2>👥 Quản lý người dùng</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ và Tên</th>
                <th>Email</th>
                <th>Số điện thoại</th>
                <th>Vai trò</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row["id"] ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td>
                        <?php
                        if ($row["role"] == "admin") {
                            echo '<span class="badge bg-danger">Admin</span>';
                        } elseif ($row["role"] == "owner") {
                            echo '<span class="badge bg-primary">Chủ trọ</span>';
                        } else {
                            echo '<span class="badge bg-success">Người thuê</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row["role"] !== "admin") { ?>
                            <a href="edit_user.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-warning">✏️ Sửa</a>
                            <a href="delete_user.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">🗑️ Xóa</a>
                        <?php } else { ?>
                            <span>-</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../../partials/footer.php'; ?>
