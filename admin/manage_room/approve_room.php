<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';

if (isset($_GET["id"]) && isset($_GET["status"])) {
    $room_id = $_GET["id"];
    $status = $_GET["status"];

    // Nếu trạng thái là "payment_approved", cập nhật trạng thái phòng thành "approved"
    if ($status == 'payment_approved') {
        $new_status = 'approved';  // Đã duyệt
    } else {
        $new_status = $status;  // Giữ nguyên trạng thái nếu không phải payment_approved
    }

    // Cập nhật trạng thái tin đăng
    $sql = "UPDATE rooms SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $room_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Duyệt thanh toán thành công!'); window.location.href = 'manage_rooms.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!'); window.location.href = 'manage_rooms.php';</script>";
    }
} else {
    header("Location: manage_rooms.php");
    exit;
}
?>
