<?php
session_start();
include '../config/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['owner_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $booking_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'confirm') {
        $status = 'confirmed';
    } elseif ($action == 'cancel') {
        $status = 'canceled';
    } else {
        die("Hành động không hợp lệ!");
    }

    // Cập nhật trạng thái đơn đặt phòng
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $booking_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Cập nhật đơn đặt phòng thành công!";
    } else {
        $_SESSION['error'] = "Lỗi khi cập nhật!";
    }

    header("Location: manage_bookings.php");
    exit();
}
?>
