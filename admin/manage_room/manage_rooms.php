<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db_connect.php';
include '../../partials/header.php';

// Lấy danh sách tin đăng
$sql = "SELECT * FROM rooms ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>🏠 Quản lý tin đăng</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Giá thuê</th>
                <th>Chủ trọ</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td>
                        <?php
                        // Lấy ảnh đầu tiên của phòng
                        $room_id = $row["id"];
                        $sql_img = "SELECT image FROM room_images WHERE room_id = ? LIMIT 1";
                        $stmt_img = $conn->prepare($sql_img);
                        $stmt_img->bind_param("i", $room_id);
                        $stmt_img->execute();
                        $result_img = $stmt_img->get_result();
                        $image = ($result_img->num_rows > 0) ? $result_img->fetch_assoc()["image"] : "no-image.png";
                        ?>
                        <img src="<?= htmlspecialchars($image) ?>" width="100">
                    </td>
                    <td><?= htmlspecialchars($row["title"]) ?></td>
                    <td><?= number_format($row["price"], 0, ',', '.') . " VNĐ" ?></td>
                    <td>
                        <?php
                        // Lấy thông tin chủ trọ
                        $owner_id = $row["owner_id"];
                        $owner = $conn->query("SELECT name FROM users WHERE id = $owner_id")->fetch_assoc();
                        echo htmlspecialchars($owner["name"]);
                        ?>
                    </td>
                    <td>
    <?php
    if ($row["status"] == "pending") {
        echo '<span class="badge bg-warning">Chờ duyệt</span>';
    } elseif ($row["status"] == "approved") {
        echo '<span class="badge bg-success">Đã duyệt</span>';
    } elseif ($row["status"] == "waiting_for_payment") {
        echo '<span class="badge bg-primary">Chờ thanh toán</span>';
        
        // Kiểm tra chứng từ thanh toán
        if (!empty($row["payment_proof"])) {
            echo '<a href="' . $row["payment_proof"] . '" target="_blank" class="btn btn-sm btn-info">Xem chứng từ</a>';
            echo '<a href="approve_room.php?id=' . $row["id"] . '&status=payment_approved" class="btn btn-sm btn-success">✔️ Duyệt thanh toán</a>';
        } else {
            echo '<span>Chưa có chứng từ</span>';
        }
    } else {
        echo '<span class="badge bg-danger">Bị từ chối</span>';
    }
    ?>
</td>

<td>
    <?php if ($row["status"] == "pending") { ?>
        <a href="approve_room.php?id=<?= $row["id"] ?>&status=approved" class="btn btn-sm btn-success">✔️ Duyệt</a>
        <a href="approve_room.php?id=<?= $row["id"] ?>&status=rejected" class="btn btn-sm btn-danger">❌ Từ chối</a>
    <?php } elseif ($row["status"] == "waiting_for_payment") { ?>
        <a href="upload_payment.php?id=<?= $row["id"] ?>" class="btn btn-sm btn-primary">Tải chứng từ thanh toán</a>
    <?php } else { ?>
        <span>Đã xử lý</span>
    <?php } ?>
</td>

                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../../partials/footer.php'; ?>
