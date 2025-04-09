<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../auth/login.php");
    exit;
}

include '../../config/db_connect.php';

if (isset($_GET["id"])) {
    $user_id = $_GET["id"];

    // Xóa người dùng khỏi database
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Xóa người dùng thành công!'); window.location.href = 'manage_users.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!'); window.location.href = 'manage_users.php';</script>";
    }
} else {
    header("Location: manage_users.php");
    exit;
}
?>
