<?php
session_start(); // KHÔNG ĐƯỢC THIẾU
include '../config/db_connect.php';

if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}
$owner_id = $_SESSION["user_id"] ?? null; // Đổi thành user_id
if (!$owner_id) {
    die("Lỗi: Không tìm thấy ID chủ trọ! Hãy đăng nhập lại.");
}



// Lấy danh sách đơn đặt của phòng mà chủ trọ sở hữu
$sql = "SELECT b.id, b.name, b.phone, b.email, b.message, b.created_at, r.title 
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE r.owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();


// Nếu nhấn nút Cập nhật trạng thái
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE bookings SET status = ? WHERE id = ? AND room_id IN (SELECT id FROM rooms WHERE owner_id = ?)";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_status, $booking_id, $owner_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Cập nhật trạng thái thành công!";
    } else {
        $_SESSION['message'] = "Lỗi khi cập nhật!";
    }
}

// Lấy danh sách đơn đặt phòng của chủ trọ
$sql = "SELECT b.id, b.room_id, r.title, b.name, b.phone, b.email, b.message, b.created_at, b.status
        FROM bookings b
        JOIN rooms r ON b.room_id = r.id
        WHERE r.owner_id = ?
        ORDER BY b.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn đặt phòng</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <?php include '../partials/header.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Danh sách đơn đặt phòng</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Phòng</th>
                    <th>Người đặt</th>
                    <th>Số điện thoại</th>
                    <th>Email</th>
                    <th>Tin nhắn</th>
                    <th>Ngày đặt</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= $row['title']; ?></td>
                        <td><?= $row['name']; ?></td>
                        <td><?= $row['phone']; ?></td>
                        <td><?= $row['email']; ?></td>
                        <td><?= $row['message']; ?></td>
                        <td><?= $row['created_at']; ?></td>
                        <td>
                            <span class="badge <?= $row['status'] == 'Đã xử lý' ? 'bg-success' : 'bg-warning'; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="booking_id" value="<?= $row['id']; ?>">
                                <select name="status" class="form-select">
                                    <option value="Chưa xử lý" <?= $row['status'] == 'Chưa xử lý' ? 'selected' : ''; ?>>Chưa xử lý</option>
                                    <option value="Đã xử lý" <?= $row['status'] == 'Đã xử lý' ? 'selected' : ''; ?>>Đã xử lý</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm mt-1">Cập nhật</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php include '../partials/footer.php'; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
