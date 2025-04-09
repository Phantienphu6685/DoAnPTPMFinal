<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';
include '../partials/header.php';

$owner_id = $_SESSION["user_id"];

// Lấy tổng số phòng đã đăng
$sql_total = "SELECT COUNT(*) as total FROM rooms WHERE owner_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $owner_id);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_rooms = $result_total->fetch_assoc()['total'];

// Lấy tổng số phòng đã duyệt
$sql_approved = "SELECT COUNT(*) as approved FROM rooms WHERE owner_id = ? AND status = 'approved'";
$stmt_approved = $conn->prepare($sql_approved);
$stmt_approved->bind_param("i", $owner_id);
$stmt_approved->execute();
$result_approved = $stmt_approved->get_result();
$approved_rooms = $result_approved->fetch_assoc()['approved'];

// Lấy tổng số phòng đang chờ duyệt
$sql_pending = "SELECT COUNT(*) as pending FROM rooms WHERE owner_id = ? AND status = 'pending'";
$stmt_pending = $conn->prepare($sql_pending);
$stmt_pending->bind_param("i", $owner_id);
$stmt_pending->execute();
$result_pending = $stmt_pending->get_result();
$pending_rooms = $result_pending->fetch_assoc()['pending'];

// Lấy danh sách phòng
$sql = "SELECT * FROM rooms WHERE owner_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Dashboard Chủ Trọ</h2>

    <!-- Thống kê nhanh -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Tổng số phòng</h5>
                    <p class="card-text fs-3"><?php echo $total_rooms; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Phòng đã duyệt</h5>
                    <p class="card-text fs-3"><?php echo $approved_rooms; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Phòng chờ duyệt</h5>
                    <p class="card-text fs-3"><?php echo $pending_rooms; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Nút đăng tin -->
    <a href="add_room.php" class="btn btn-success mb-3">+ Đăng tin mới</a>

    <!-- Danh sách phòng -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tiêu đề</th>
                <th>Giá thuê</th>
                <th>Địa chỉ</th>
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
                        $sql_image = "SELECT image FROM room_images WHERE room_id = ? LIMIT 1";
                        $stmt_image = $conn->prepare($sql_image);
                        $stmt_image->bind_param("i", $room_id);
                        $stmt_image->execute();
                        $result_image = $stmt_image->get_result();
                        $img = $result_image->fetch_assoc();
                        ?>

                        <?php if ($img && $img["image"]) { ?>
                            <img src="<?= htmlspecialchars($img["image"]) ?>" width="80" class="img-thumbnail">
                        <?php } else { ?>
                            <span>Không có ảnh</span>
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row["title"]); ?></td>
                    <td>
                        <?php 
                        if (!empty($row["discount_price"]) && $row["discount_price"] > 0) {
                            echo "<del>" . number_format($row["price"], 0, ',', '.') . " VNĐ</del><br>";
                            echo "<strong class='text-danger'>" . number_format($row["discount_price"], 0, ',', '.') . " VNĐ</strong>";
                        } else {
                            echo number_format($row["price"], 0, ',', '.') . " VNĐ";
                        }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row["location"]); ?></td>
                    <td>
                        <?php
                        if ($row["status"] == "pending") {
                            echo '<span class="badge bg-warning">Chờ duyệt</span>';
                        } elseif ($row["status"] == "approved") {
                            echo '<span class="badge bg-success">Đã duyệt</span>';
                        } elseif ($row["status"] == "waiting_for_payment") {
                            echo '<span class="badge bg-primary">Chờ thanh toán</span>';
                        } else {
                            echo '<span class="badge bg-danger">Bị từ chối</span>';
                        }
                        if ($row["status"] == "waiting_for_payment") {
                            echo '<a href="upload_payment_proof.php?id=' . $row["id"] . '" class="btn btn-warning">Tải ảnh chứng từ thanh toán</a>';
                        }
                        
                        ?>
                    </td>
                    <td>
                        <a href="edit_room.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-primary">Sửa</a>
                        <a href="delete_room.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                        <a href="room_detail.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-info">Xem</a>
                        <?php if ($row["status"] == "waiting_payment") { ?>
                            <a href="upload_payment_proof.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-info">Tải chứng từ thanh toán</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../partials/footer.php'; ?>
