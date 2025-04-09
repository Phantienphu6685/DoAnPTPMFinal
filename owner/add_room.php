<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "owner") {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/db_connect.php';
include '../partials/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $discount_price = !empty($_POST["discount_price"]) ? $_POST["discount_price"] : NULL;
    $location = $_POST["location"];
    $district = $_POST["district"];
    $ward = $_POST["ward"];
    $owner_id = $_SESSION["user_id"];
    $images = isset($_POST["images"]) ? explode(',', $_POST["images"]) : [];
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

    // Chèn vào bảng rooms
    $sql = "INSERT INTO rooms (owner_id, title, description, price, discount_price, location, district, ward, latitude, longitude) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Lỗi SQL: " . $conn->error);
}

$stmt->bind_param("issddsssdd", $owner_id, $title, $description, $price, $discount_price, $location, $district, $ward, $latitude, $longitude);

    if ($stmt->execute()) {
        $room_id = $stmt->insert_id; // Lấy ID của phòng vừa thêm

        // Nếu có ảnh, thêm vào bảng room_images
        if (!empty($images)) {
            $image_sql = "INSERT INTO room_images (room_id, image) VALUES (?, ?)";
            $image_stmt = $conn->prepare($image_sql);

            if ($image_stmt) {
                foreach ($images as $image) {
                    $image_stmt->bind_param("is", $room_id, $image);
                    $image_stmt->execute();
                }
            }
        }

        echo "<script>alert('Đăng tin thành công!'); window.location.href = 'dashboard.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra khi thêm phòng: " . $stmt->error . "');</script>";
    }
}
?>

<div class="container mt-4">
    <h2>Đăng Tin Cho Thuê Phòng</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá thuê (VNĐ)</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giá khuyến mãi (VNĐ) (nếu có)</label>
            <input type="number" name="discount_price" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <!-- Quận -->
        <div class="mb-3">
            <label class="form-label">Quận</label>
            <select id="district" name="district" class="form-control" onchange="updateWards()" required>
                <option value="">Chọn quận</option>
                <option value="Thủ Đức">Thủ Đức</option>
                <option value="Bình Thạnh">Bình Thạnh</option>
                <option value="Quận 9">Quận 9</option>
            </select>
        </div>

        <!-- Phường -->
        <div class="mb-3">
            <label class="form-label">Phường</label>
            <select id="ward" name="ward" class="form-control" disabled required>
                <option value="">Chọn phường</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Vĩ độ (latitude)</label>
            <input type="text" name="latitude" class="form-control" required placeholder="Ví dụ: 10.762622">
        </div>
        <div class="mb-3">
            <label class="form-label">Kinh độ (longitude)</label>
            <input type="text" name="longitude" class="form-control" required placeholder="Ví dụ: 106.660172">
        </div>

        <!-- Hình ảnh -->
        <div class="mb-3">
            <label class="form-label">Hình ảnh</label>
            <input type="text" id="image-path" name="images" class="form-control" required>
            <button type="button" class="btn btn-primary mt-2" onclick="selectImages()">Chọn ảnh</button>
        </div>

        <button type="submit" class="btn btn-primary">Đăng Tin</button>
    </form>
</div>

<?php include '../partials/footer.php'; ?>
<script src="../assets/ckfinder/ckfinder.js"></script>
<script src="../assets/js/district_ward.js"></script>
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