<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';
include '../partials/header.php';

$owner_id = $_SESSION["user_id"];

// Lấy danh sách phòng của chủ trọ
$sql = "SELECT * FROM rooms WHERE owner_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container mt-4">
    <h2>Danh sách phòng đã đăng</h2>
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
                        <div class="room-images">
                            <?php
                            // Lấy danh sách ảnh từ bảng room_images
                            $room_id = $row["id"];
                            $sql_images = "SELECT image FROM room_images WHERE room_id = ?";
                            $stmt_images = $conn->prepare($sql_images);
                            $stmt_images->bind_param("i", $room_id);
                            $stmt_images->execute();
                            $result_images = $stmt_images->get_result();

                            if ($result_images->num_rows > 0) {
                                while ($img = $result_images->fetch_assoc()) {
                                    $imagePath = $img["image"];

                                    // Nếu đường dẫn đã có "/ckfinder/" thì giữ nguyên, ngược lại thì thêm assets/images
                                    if (strpos($imagePath, '/ckfinder/') === 0) {
                                        echo '<img src="' . htmlspecialchars($imagePath) . '" width="80" class="img-thumbnail">';
                                    } else {
                                        echo '<img src="../assets/images/' . htmlspecialchars($imagePath) . '" width="80" class="img-thumbnail">';
                                    }
                                    
                                }
                            } else {
                                echo '<span>Không có ảnh</span>';
                            }
                            ?>
                        </div>
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
                        } else {
                            echo '<span class="badge bg-danger">Bị từ chối</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="edit_room.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-primary">Sửa</a>
                        <a href="delete_room.php?id=<?php echo $row["id"]; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include '../partials/footer.php'; ?>
