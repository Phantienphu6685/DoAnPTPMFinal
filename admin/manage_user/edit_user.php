<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';
include '../../partials/header.php';

// Kiểm tra ID người dùng cần sửa
if (!isset($_GET["id"])) {
    header("Location: manage_users.php");
    exit;
}

$user_id = $_GET["id"];

// Lấy thông tin người dùng
$sql = "SELECT name, email, phone, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<script>alert('Người dùng không tồn tại!'); window.location.href = 'manage_users.php';</script>";
    exit;
}

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $role = $_POST["role"];

    // Cập nhật thông tin người dùng
    $sql_update = "UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $name, $email, $phone, $role, $user_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href = 'manage_users.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!');</script>";
    }
}
?>

<div class="container mt-4">
    <h2>✏️ Sửa Thông Tin Người Dùng</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Họ và Tên</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-control">
                        <option value="admin" <?= ($user['role'] == "admin") ? "selected" : "" ?>>Admin</option>
                        <option value="owner" <?= ($user['role'] == "owner") ? "selected" : "" ?>>Chủ trọ</option>
                        <option value="user" <?= ($user['role'] == "user") ? "selected" : "" ?>>Người thuê</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">💾 Lưu thay đổi</button>
                <a href="manage_users.php" class="btn btn-secondary">⬅️ Quay lại</a>
            </form>
        </div>
    </div>
</div>

<?php include '../../partials/footer.php'; ?>
