<?php
include '../config/db_connect.php';
include '../partials/header.php';

// Kiểm tra xem có ID phòng hay không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Lỗi: Thiếu ID phòng.");
}

$room_id = intval($_GET['id']); // Lấy ID phòng từ URL

// Lấy thông tin phòng và email chủ trọ
$sql = "SELECT r.*, u.email AS owner_email, u.phone AS owner_phone
        FROM rooms r 
        LEFT JOIN users u ON r.owner_id = u.id 
        WHERE r.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    die("Lỗi: Không tìm thấy phòng.");
}

// Lấy danh sách ảnh
$sql_images = "SELECT image FROM room_images WHERE room_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $room_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();

$images = [];
while ($img = $result_images->fetch_assoc()) {
    $images[] = (strpos($img['image'], '/ckfinder/') === 0)
        ? 'http://localhost' . $img['image']
        : '../assets/images/' . $img['image'];
}
?>

<div class="container mt-4">
    <div class="row">
        <!-- Hiển thị thông tin phòng bên trái -->
        <div class="col-md-6">
            <?php if (count($images) > 0): ?>
                <div id="carouselImages" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php foreach ($images as $index => $imgPath): ?>
                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                <img src="<?= htmlspecialchars($imgPath) ?>" class="d-block w-100" style="max-height: 400px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            <?php else: ?>
                <img src="../assets/images/default.jpg" class="img-fluid" alt="No image">
            <?php endif; ?>
                    
            <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($room['title']) ?></h5>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($room['location']) ?></p>
                    <p><strong>Giá:</strong> <?= number_format($room['price']) ?> VND</p>
                </div>

        </div>

        <!-- Form nhập thông tin đặt phòng bên phải -->
        <div class="col-md-6">
            <h4>Thông tin người thuê</h4>
            <form method="POST" action="process_booking.php">
                <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                <div class="mb-3">
                    <label>Họ và Tên:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Số điện thoại:</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Ghi chú:</label>
                    <textarea name="message" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Gửi thông tin đặt phòng</button>
            </form>

            <hr>
            <h5>Hoặc liên hệ trực tiếp</h5>
            <p><strong>Số điện thoại chủ trọ:</strong> <?= htmlspecialchars($room['owner_phone']) ?></p>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
