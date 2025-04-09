<?php
session_start();
include '../config/db_connect.php';
include '../partials/header.php';

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? $_GET['search'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// Truy vấn danh sách phòng trọ
$sql = "SELECT * FROM rooms WHERE status = 'available'";

// Nếu có tìm kiếm
if (!empty($search)) {
    $sql .= " AND (title LIKE '%$search%' OR location LIKE '%$search%')";
}

// Nếu có lọc theo giá
if (!empty($min_price) && !empty($max_price)) {
    $sql .= " AND (price BETWEEN $min_price AND $max_price)";
}

$result = $conn->query($sql);
?>

<div class="container mt-4">
    <h2>🏠 Danh Sách Phòng Trọ</h2>

    <!-- Tìm kiếm và lọc -->
    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="🔍 Tìm kiếm theo tên, vị trí..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="min_price" class="form-control" placeholder="Giá thấp nhất" value="<?= htmlspecialchars($min_price) ?>">
        </div>
        <div class="col-md-2">
            <input type="number" name="max_price" class="form-control" placeholder="Giá cao nhất" value="<?= htmlspecialchars($max_price) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary">Lọc 🔎</button>
        </div>
    </form>

    <!-- Hiển thị danh sách phòng -->
    <div class="row">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <img src="../../assets/images/<?= $row['image'] ?>" class="card-img-top" alt="Phòng trọ">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text">📍 <?= htmlspecialchars($row['location']) ?></p>
                        <p class="card-text">💰 <?= number_format($row['price']) ?> VNĐ / tháng</p>
                        <a href="../room_details/index.php?id=<?= $row['id'] ?>" class="btn btn-success">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php include '../partials/footer.php'; ?>
