<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';

if (!isset($_GET["id"])) {
    echo "<script>alert('Phòng không tồn tại!'); window.location.href = 'dashboard.php';</script>";
    exit;
}

$room_id = $_GET["id"];
$owner_id = $_SESSION["user_id"];

// Kiểm tra phòng có tồn tại không
$sql_check = "SELECT * FROM rooms WHERE id = ? AND owner_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $room_id, $owner_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
if ($result_check->num_rows == 0) {
    echo "<script>alert('Phòng không tồn tại hoặc bạn không có quyền xóa!'); window.location.href = 'dashboard.php';</script>";
    exit;
}

// Xóa ảnh trong thư mục
$sql_get_images = "SELECT image FROM room_images WHERE room_id = ?";
$stmt_get_images = $conn->prepare($sql_get_images);
$stmt_get_images->bind_param("i", $room_id);
$stmt_get_images->execute();
$result_get_images = $stmt_get_images->get_result();

while ($row = $result_get_images->fetch_assoc()) {
    $image_path = $row["image"];
    if (file_exists($image_path)) {
        unlink($image_path); // Xóa ảnh trong thư mục
    }
}

// Xóa ảnh khỏi database
$sql_delete_images = "DELETE FROM room_images WHERE room_id = ?";
$stmt_delete_images = $conn->prepare($sql_delete_images);
$stmt_delete_images->bind_param("i", $room_id);
$stmt_delete_images->execute();

// Xóa phòng
$sql_delete_room = "DELETE FROM rooms WHERE id = ? AND owner_id = ?";
$stmt_delete_room = $conn->prepare($sql_delete_room);
$stmt_delete_room->bind_param("ii", $room_id, $owner_id);
if ($stmt_delete_room->execute()) {
    echo "<script>alert('Xóa phòng thành công!'); window.location.href = 'dashboard.php';</script>";
} else {
    echo "<script>alert('Có lỗi xảy ra!'); window.location.href = 'dashboard.php';</script>";
}
?>
