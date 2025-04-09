<?php
include '../config/db_connect.php';
include '../includes/send_mail.php'; // Import hàm sendMail()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = intval($_POST['room_id']);
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $created_at = date("Y-m-d H:i:s");

    // Lấy email chủ trọ
    $sql = "SELECT u.email FROM rooms r 
            JOIN users u ON r.owner_id = u.id 
            WHERE r.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $owner = $result->fetch_assoc();

    if (!$owner) {
        die("Lỗi: Không tìm thấy chủ trọ.");
    }

    $owner_email = $owner['email'];

    // Lưu vào database
    $sql = "INSERT INTO bookings (room_id, name, phone, email, message, created_at) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Lỗi SQL: " . $conn->error . " - Câu lệnh SQL: " . $sql);
    }
    
    
    $stmt->bind_param("isssss", $room_id, $name, $phone, $email, $message, $created_at);
    $stmt->execute();

    // Nội dung email
    $subject = "Yeu cau thue phong tu $name";
    $body = "<h3>Thông tin khách hàng:</h3>
             <p><strong>Tên:</strong> $name</p>
             <p><strong>Số điện thoại:</strong> $phone</p>
             <p><strong>Email:</strong> $email</p>
             <p><strong>Lời nhắn:</strong> $message</p>";

    // Gửi email cho chủ trọ
    $sendStatus = sendMail($owner_email, $subject, $body);

    if ($sendStatus === true) {
        echo "<script>alert('Đã gửi thông tin đặt phòng. Chủ trọ sẽ liên hệ bạn!'); window.location.href='booking.php?id=$room_id';</script>";
    } else {
        echo "<script>alert('Có lỗi khi gửi email: $sendStatus');</script>";
    }
}
?>
