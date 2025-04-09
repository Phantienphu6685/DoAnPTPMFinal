<?php
include '../config/db_connect.php';
include '../partials/header.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Không tìm thấy phòng!</div>";
    include '../partials/footer.php';
    exit;
}

$room_id = $_GET['id'];
$sql = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Phòng không tồn tại!</div>";
    include '../partials/footer.php';
    exit;
}

$room = $result->fetch_assoc();

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
    <h2><i class="fas fa-home"></i> <?= htmlspecialchars($room['title']) ?></h2>

    <div class="row mt-4">
        <!-- Hình ảnh -->
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
        </div>

        <!-- Thông tin -->
        <div class="col-md-6">
            <p><strong><i class="fas fa-map-marker-alt"></i> Vị trí:</strong> <?= htmlspecialchars($room['location']) ?>, <?= htmlspecialchars($room['ward']) ?>, <?= htmlspecialchars($room['district']) ?></p>
            <p><strong><i class="fas fa-align-left"></i> Mô tả:</strong> <?= nl2br(htmlspecialchars($room['description'])) ?></p>

            <?php if (!empty($room['discount_price']) && $room['discount_price'] > 0): ?>
                <p><strong>Giá KM:</strong> <span class="text-danger"><?= number_format($room['discount_price']) ?> VND</span></p>
                <p><strong>Giá gốc:</strong> <del><?= number_format($room['price']) ?> VND</del></p>
            <?php else: ?>
                <p><strong>Giá:</strong> <?= number_format($room['price']) ?> VND</p>
            <?php endif; ?>

            <!-- Nút xem bản đồ -->
            <?php if (!empty($room['latitude']) && !empty($room['longitude'])): ?>
                <a href="https://www.google.com/maps?q=<?= $room['latitude'] ?>,<?= $room['longitude'] ?>" target="_blank" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-map"></i> Xem trên Google Maps
                </a>
            <?php endif; ?>

            <!-- Nút thuê phòng -->
            <a href="booking.php?id=<?= $room['id'] ?>" class="btn btn-success mt-3">
                <i class="fas fa-calendar-check"></i> Thuê phòng
            </a>
        </div>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
