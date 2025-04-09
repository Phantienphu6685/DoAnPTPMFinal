<?php
include 'config/db_connect.php';
include 'partials/header.php';

// Lấy danh sách quận và phường
// Lấy danh sách quận từ database
$districts = [];
$sqlDistricts = "SELECT DISTINCT district FROM rooms ORDER BY district ASC";
$resultDistricts = $conn->query($sqlDistricts);
while ($row = $resultDistricts->fetch_assoc()) {
    $districts[] = $row['district'];
}

// Lấy danh sách phường từ database
$wards = [];
$sqlWards = "SELECT DISTINCT ward FROM rooms ORDER BY ward ASC";
$resultWards = $conn->query($sqlWards);
while ($row = $resultWards->fetch_assoc()) {
    $wards[] = $row['ward'];
}


// Xử lý tìm kiếm và lọc dữ liệu
$whereClauses = ["rooms.status = 'approved'"]; // Chỉ hiển thị phòng đã được duyệt
// Nếu có từ khóa tìm kiếm
if (!empty($_GET['user_lat']) && !empty($_GET['user_lng'])) {
    $userLat = $_GET['user_lat'];
    $userLng = $_GET['user_lng'];

    // Tìm trong bán kính 5km
    $distanceRadius = 5;

    $whereClauses[] = "(6371 * ACOS(COS(RADIANS($userLat)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS($userLng)) + SIN(RADIANS($userLat)) * SIN(RADIANS(latitude)))) <= $distanceRadius";
}


if (!empty($_GET['search'])) {
    $whereClauses[] = "(rooms.name LIKE ? OR rooms.location LIKE ?)";
}

if (!empty($_GET['district'])) {
    $whereClauses[] = "rooms.district = '" . $_GET['district'] . "'";
}
if (!empty($_GET['ward'])) {
    $whereClauses[] = "rooms.ward = '" . $_GET['ward'] . "'";
}
if (!empty($_GET['sort_price']) && $_GET['sort_price'] == 'asc') {
    $orderBy = "ORDER BY rooms.price ASC";
} else {
    $orderBy = "ORDER BY rooms.created_at DESC";
}

$whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
// Xử lý tìm kiếm và lọc dữ liệu
//$whereClauses = ["rooms.status = 'approved'"]; // Chỉ hiển thị phòng đã được duyệt

if (!empty($_GET['search'])) {
    $whereClauses[] = "(rooms.location LIKE ? OR rooms.location LIKE ?)";
}
if (!empty($_GET['district'])) {
    $whereClauses[] = "rooms.district = '" . $_GET['district'] . "'";
}
if (!empty($_GET['ward'])) {
    $whereClauses[] = "rooms.ward = '" . $_GET['ward'] . "'";
}
if (!empty($_GET['sort_price']) && $_GET['sort_price'] == 'asc') {
    $orderBy = "ORDER BY rooms.price ASC";
} else {
    $orderBy = "ORDER BY rooms.created_at DESC";
}

// Xây dựng câu SQL đầy đủ
$sql = "SELECT * FROM rooms WHERE " . implode(" AND ", $whereClauses) . " $orderBy";

// Chuẩn bị truy vấn
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Lỗi chuẩn bị truy vấn: " . $conn->error); // Kiểm tra lỗi nếu prepare thất bại
}

// Nếu có tìm kiếm, bind tham số
if (!empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $stmt->bind_param("ss", $search, $search);
}

// Thực thi truy vấn
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Lỗi thực thi truy vấn: " . $conn->error); // Kiểm tra lỗi nếu truy vấn thất bại
}

?>

<!-- Slide quảng cáo -->
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="assets/images/Web.png" class="d-block w-100" alt="Slide 1"
                style="max-height: max; object-fit: cover;">
        </div>

        <div class="carousel-item">
            <img src="assets/images/Web2.png" class="d-block w-100" alt="Slide 2">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<hr>

<!-- Bộ lọc -->
<div class="container mt-3">
    <form method="GET" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, vị trí">
        </div>
        <div class="col-md-2">
            <select id="district" name="district" class="form-control" onchange="updateWards()">
                <option value="">Chọn quận</option>
                <option value="Thủ Đức">Quận Thủ Đức</option>
                <option value="Bình Thạnh">Quận Bình Thạnh</option>
                <option value="Quận 9">Quận 9</option>
            </select>
        </div>
        <div class="col-md-2">
            <select id="ward" name="ward" class="form-control" disabled>
                <option value="">Chọn phường</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="sort_price" class="form-control">
                <option value="">Sắp xếp theo giá</option>
                <option value="asc">Thấp đến cao</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Lọc</button>
        </div>

        <input type="hidden" id="user_lat" name="user_lat">
        <input type="hidden" id="user_lng" name="user_lng">
        <button type="button" class="btn btn-secondary" onclick="findNearby()">Tìm phòng trọ gần bạn</button>

    </form>

</div>






<!-- Danh sách phòng trọ -->
<div class="container mt-4">
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="card h-100 d-flex flex-column">
                    <div class="card-body d-flex flex-column">
                        <!-- Tiêu đề -->
                        <h5 class="card-title">
                            <i class="fas fa-home"></i> <?= htmlspecialchars($row['title']) ?>
                        </h5>
                        <!-- Vị trí -->
                        <p><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']) ?></p>
                        <!-- Mô tả -->
                        <p><i class="fas fa-align-left"></i> <?= htmlspecialchars($row['description']) ?></p>

                        <!-- Hình ảnh -->
                        <?php
                        // Lấy ảnh đầu tiên của phòng
                        $room_id = $row["id"];
                        $sql_images = "SELECT image FROM room_images WHERE room_id = ? LIMIT 1";
                        $stmt_images = $conn->prepare($sql_images);
                        $stmt_images->bind_param("i", $room_id);
                        $stmt_images->execute();
                        $result_images = $stmt_images->get_result();
                        $img = $result_images->fetch_assoc();

                        if ($img) {
                            $imagePath = $img["image"];
                            $finalImagePath = (strpos($imagePath, '/ckfinder/') === 0)
                                ? 'http://localhost' . $imagePath
                                : '../assets/images/' . $imagePath;
                        } else {
                            $finalImagePath = '../assets/images/default.jpg';
                        }
                        ?>
                        <img src="<?= htmlspecialchars($finalImagePath) ?>" class="card-img-top" alt="Hình ảnh phòng">

                        <!-- Giá -->
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <?php if (!empty($row['discount_price']) && $row['discount_price'] > 0): ?>
                                <p class="text-muted mb-0">
                                    Giá gốc: <del><?= number_format($row['price']) ?> VND</del>
                                </p>
                                <p class="text-danger mb-0">
                                    Giá KM: <?= number_format($row['discount_price']) ?> VND
                                </p>
                            <?php else: ?>
                                <p class="text mb-0">
                                   Giá phòng: <?= number_format($row['price']) ?> VND</p>
                            <?php endif; ?>
                        </div>

                        <!-- Button -->
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="user/room_details.php?id=<?= $row['id'] ?>"
                                class="btn btn-primary btn-block btn-xemchitiet">
                                <i class="fas fa-info-circle"></i> Xem chi tiết
                            </a>
                            <a href="user/booking.php?id=<?= $row['id'] ?>" class="btn btn-success btn-block btn-thuephong">
                                <i class="fas fa-calendar-check"></i> Thuê phòng
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>


<?php include 'partials/footer.php'; ?>


<script src="assets/js/district_ward.js"></script>
<script>
    function findNearby() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                document.getElementById('user_lat').value = position.coords.latitude;
                document.getElementById('user_lng').value = position.coords.longitude;
                document.querySelector('form').submit(); // Gửi form luôn
            }, function (error) {
                alert('Không thể lấy vị trí của bạn. Vui lòng cho phép truy cập vị trí!');
            });
        } else {
            alert('Trình duyệt không hỗ trợ định vị!');
        }
    }
</script>