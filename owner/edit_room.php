<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';

if (!isset($_GET["id"])) {
    echo "<script>alert('Phòng không tồn tại!'); window.location.href = 'dashboard.php';</script>";
    exit;
}

$room_id = $_GET["id"];
$owner_id = $_SESSION["user_id"];

// Lấy thông tin phòng
$sql = "SELECT * FROM rooms WHERE id = ? AND owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $room_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "<script>alert('Phòng không tồn tại!'); window.location.href = 'dashboard.php';</script>";
    exit;
}
$room = $result->fetch_assoc();

// Lấy danh sách ảnh từ room_images
$sql_images = "SELECT image FROM room_images WHERE room_id = ?";
$stmt_images = $conn->prepare($sql_images);
$stmt_images->bind_param("i", $room_id);
$stmt_images->execute();
$result_images = $stmt_images->get_result();
$images = [];
while ($img = $result_images->fetch_assoc()) {
    $images[] = $img["image"];
}

// Xử lý cập nhật phòng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $discount_price = !empty($_POST["discount_price"]) ? $_POST["discount_price"] : NULL;
    $location = $_POST["location"];
    $image_paths = $_POST["images"]; // Ảnh từ CKFinder (dạng chuỗi URL cách nhau bởi dấu phẩy)
    $latitude = !empty($_POST["latitude"]) ? $_POST["latitude"] : NULL;
    $longitude = !empty($_POST["longitude"]) ? $_POST["longitude"] : NULL;

    // Cập nhật thông tin phòng
    $sql_update = "UPDATE rooms 
    SET title = ?, description = ?, price = ?, discount_price = ?, location = ?, latitude = ?, longitude = ?
    WHERE id = ? AND owner_id = ?";

$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("ssdssddii", $title, $description, $price, $discount_price, $location, $latitude, $longitude, $room_id, $owner_id);

    if ($stmt_update->execute()) {
        // Xóa ảnh cũ
        $sql_delete_images = "DELETE FROM room_images WHERE room_id = ?";
        $stmt_delete_images = $conn->prepare($sql_delete_images);
        $stmt_delete_images->bind_param("i", $room_id);
        $stmt_delete_images->execute();

        // Lưu ảnh mới từ CKFinder vào database
        if (!empty($image_paths)) {
            $images_array = explode(',', $image_paths);
            foreach ($images_array as $image_url) {
                $image_url = trim($image_url);
                $sql_image = "INSERT INTO room_images (room_id, image) VALUES (?, ?)";
                $stmt_image = $conn->prepare($sql_image);
                $stmt_image->bind_param("is", $room_id, $image_url);
                $stmt_image->execute();
            }
        }

        echo "<script>alert('Cập nhật thành công!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!');</script>";
    }
}
?>

<?php include '../partials/header.php'; ?>

<div class="container mt-4">
    <h2>Cập Nhật Phòng</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($room['title']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($room['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá thuê (VNĐ)</label>
            <input type="number" name="price" class="form-control" value="<?= $room['price'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá khuyến mãi (VNĐ) <small>(Bỏ trống nếu không có)</small></labe>
            <input type="number" name="discount_price" class="form-control" value="<?= $room['discount_price'] ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($room['location']) ?>" required>
        </div>
        <!-- Vĩ độ -->
<div class="mb-3">
    <label class="form-label">Vĩ độ (latitude)</label>
    <input type="text" name="latitude" class="form-control" value="<?= htmlspecialchars($room['latitude']) ?>">
</div>

<!-- Kinh độ -->
<div class="mb-3">
    <label class="form-label">Kinh độ (longitude)</label>
    <input type="text" name="longitude" class="form-control" value="<?= htmlspecialchars($room['longitude']) ?>">
</div>

        <!-- Hiển thị ảnh hiện tại -->
        <div class="mb-3">
            <label class="form-label">Ảnh hiện tại</label>
            <div>
                <?php if (!empty($images)) { ?>
                    <?php foreach ($images as $image) { ?>
                        <img src="<?= htmlspecialchars($image) ?>" width="80" class="img-thumbnail">
                    <?php } ?>
                <?php } else { ?>
                    <p>Không có ảnh</p>
                <?php } ?>
            </div>
        </div>

        <!-- Chọn ảnh mới bằng CKFinder -->
        <div class="mb-3">
            <label class="form-label">Chọn ảnh mới</label>
            <input type="text" id="image-path" name="images" class="form-control" value="<?= implode(',', $images) ?>">
            <button type="button" class="btn btn-primary mt-2" onclick="selectImages()">Chọn ảnh</button>
        </div>

        <button type="submit" class="btn btn-primary">Cập Nhật</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>

<!-- CKFinder -->
<script src="../assets/ckfinder/ckfinder.js"></script>
<script>
function selectImages() {
    CKFinder.popup({
        chooseFiles: true,
        onInit: function (finder) {
            finder.on('files:choose', function (evt) {
                let files = evt.data.files;
                let imagePaths = [];
                files.forEach(file => {
                    imagePaths.push(file.getUrl());
                });
                document.getElementById("image-path").value = imagePaths.join(',');
            });
        }
    });
}
</script>
