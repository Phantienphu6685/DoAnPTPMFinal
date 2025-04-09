<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//require '../vendor/autoload.php';  // Nếu dùng Composer
require '../includes/PHPMailer-master/src/PHPMailer.php';
require '../includes/PHPMailer-master/src/Exception.php';
require '../includes/PHPMailer-master/src/SMTP.php';

function sendMail($toEmail, $subject, $body) {
    $mail = new PHPMailer(true); // Bật chế độ Exception nếu có lỗi

// Bật chế độ debug
$mail->SMTPDebug = 2; // 0 = Tắt debug, 1 = Thông báo cơ bản, 2 = Chi tiết, 3 = Rất chi tiết
$mail->Debugoutput = 'html'; // Hiển thị debug dưới dạng HTML


    try {
        // Cấu hình SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'phantienphu16012002@gmail.com'; // 🔥 Nhập Gmail của bạn
        $mail->Password = 'myei qusn nkwn ahol';   // 🔥 Nhập mật khẩu ứng dụng
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Cấu hình gửi
        $mail->setFrom('your-email@gmail.com', 'Mail Khach hang thue phong');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Gửi email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Lỗi khi gửi email: " . $mail->ErrorInfo;
    }
}
?>
