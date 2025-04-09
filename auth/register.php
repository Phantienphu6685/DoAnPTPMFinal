<?php
include '../config/db_connect.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Kiểm tra mật khẩu nhập lại
    if ($password !== $confirm_password) {
        echo "<script>alert('Mật khẩu nhập lại không khớp!');</script>";
    } else {
        // Mã hóa mật khẩu
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm tài khoản mới vào database
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Đăng ký thành công!'); window.location.href='login.php';</script>";
            exit;
        } else {
            echo "<script>alert('Lỗi đăng ký: " . $stmt->error . "');</script>";
        }
    }
}
?>

<?php include '../partials/header.php'; // Gọi footer dùng chung ?>
<div class="auth-container">
    <div class="auth-card">
        <h4>Đăng Ký</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Họ và Tên</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-auth w-100">Đăng Ký</button>
        </form>
        <div class="card-footer">
            <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
        </div>
    </div>
</div>


<?php include '../partials/footer.php'; // Gọi footer dùng chung ?>
