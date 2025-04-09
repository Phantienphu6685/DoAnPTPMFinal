<?php
// Thông tin database
$servername = "localhost"; // Hoặc 127.0.0.1
$username = "root"; // Tài khoản mặc định của XAMPP
$password = ""; // Mật khẩu mặc định của XAMPP thường để trống
$database = "thue_phong"; // Tên database đã tạo

// Kết nối MySQLi
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Thiết lập UTF-8 để tránh lỗi font tiếng Việt
$conn->set_charset("utf8");

?>
