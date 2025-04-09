<?php
include '../config/db_connect.php';
include '../partials/header.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Truy vấn kiểm tra email
    $sql = "SELECT id, name, password, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu
        if (password_verify($password, $user["password"])) {
            // Lưu thông tin vào session
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_role"] = $user["role"];

            // Điều hướng theo vai trò
            if ($user["role"] == "admin") {
                header("Location: ../admin/dashboard.php");
            } elseif ($user["role"] == "owner") {
                header("Location: ../owner/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit;
        } else {
            echo "<script>alert('Mật khẩu không đúng!');</script>";
        }
    } else {
        echo "<script>alert('Email không tồn tại!');</script>";
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <h4>Đăng Nhập</h4>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-auth w-100">Đăng Nhập</button>
        </form>
        <div class="card-footer">
            <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
