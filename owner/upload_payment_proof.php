<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';
include '../partials/header.php';

// Kiểm tra xem 'id' có tồn tại trong URL không và kiểm tra nó có phải là một số hợp lệ không
$room_id = null;

// Ưu tiên lấy từ POST khi submit
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["room_id"])) {
    $room_id = (int) $_POST["room_id"];
} elseif (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $room_id = (int) $_GET["id"];
}

if (!$room_id) {
    echo "<script>alert('Phòng không hợp lệ!'); window.location.href = 'dashboard.php';</script>";
    exit;
}


$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id); // Truyền số nguyên
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Không tìm thấy phòng này!'); window.location.href = 'dashboard.php';</script>";
    exit;
}

// Lấy thông tin phòng
$room = $result->fetch_assoc();

// Hiển thị thông tin tài khoản và mã QR (giả sử bạn sử dụng mã QR cho tài khoản ngân hàng)
$bank_account_info = "Tên chủ tài khoản: ABC\nSố tài khoản: 123456789\nNgân hàng: XYZ Bank";
$qr_code = "path/to/qr-code.png";  // Thay bằng đường dẫn đến ảnh mã QR của bạn
?>

<div class="container mt-4">
    <h2>Thông tin chuyển khoản cho phòng <?php echo htmlspecialchars($room['title']); ?></h2>

    <!-- Hiển thị mã QR và thông tin tài khoản -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h4>Thông tin tài khoản:</h4>
            <pre><?php echo nl2br(htmlspecialchars($bank_account_info)); ?></pre>
        </div>
        <div class="col-md-6">
            <h4>Mã QR:</h4>
            <img src="<?php echo $qr_code; ?>" alt="QR Code" class="img-fluid">
        </div>
    </div>

    <!-- Form tải ảnh chứng từ -->
    <form action="upload_payment_proof.php?id=<?php echo $room_id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
    <div class="mb-3">
        <label for="payment_proof" class="form-label">Tải ảnh chứng từ chuyển khoản:</label>
        <input type="file" class="form-control" name="payment_proof" id="payment_proof" required>
    </div>
    <button type="submit" class="btn btn-success">Tải lên</button>
</form>


    <?php
    // Xử lý upload chứng từ
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["payment_proof"], $_POST["room_id"])) {
        $room_id = (int) $_POST["room_id"];
        $file = $_FILES["payment_proof"];
        $file_name = $file["name"];
        $file_tmp = $file["tmp_name"];
        $file_error = $file["error"];
        $file_size = $file["size"];
    
        if ($file_error !== UPLOAD_ERR_OK) {
            echo "<script>alert('Có lỗi xảy ra khi tải lên.');</script>";
        } elseif ($file_size > 5 * 1024 * 1024) {
            echo "<script>alert('File quá lớn.');</script>";
        } else {
            $upload_dir = '../assets/payment_proofs/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = uniqid('proof_', true) . '.' . $ext;
            $file_path = $upload_dir . $new_file_name;
    
            if (move_uploaded_file($file_tmp, $file_path)) {
                $sql_update = "UPDATE rooms SET payment_proof = ?, status = 'waiting_for_payment' WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);
    
                if (!$stmt_update) {
                    die("Prepare failed: " . $conn->error);
                }
    
                $stmt_update->bind_param("si", $file_path, $room_id);
                if ($stmt_update->execute()) {
                    echo "<script>alert('Tải lên thành công!'); window.location.href = 'dashboard.php';</script>";
                } else {
                    echo "<script>alert('Lỗi khi lưu vào database: " . $stmt_update->error . "');</script>";
                }
            } else {
                echo "<script>alert('Không thể lưu file.');</script>";
            }
        }
    }
    
    ?>
</div>

<?php include '../partials/footer.php'; ?>
