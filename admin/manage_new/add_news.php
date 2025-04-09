<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';
include '../../partials/header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $image = $_POST["image"];
    $status = $_POST["status"];

    $sql = "INSERT INTO news (title, content, image, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $title, $content, $image, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Thêm tin tức thành công!'); window.location.href = 'manage_news.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!');</script>";
    }
}
?>

<div class="container mt-4">
    <h2>🆕 Thêm Tin Tức</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ảnh đại diện</label>
                    <div class="input-group">
                        <input type="text" id="image" name="image" class="form-control" required>
                        <button type="button" class="btn btn-secondary" onclick="selectImage()">📷 Chọn ảnh</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea name="content" id="editor" class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="active">Hiển thị</option>
                        <option value="hidden">Ẩn</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">💾 Lưu Tin</button>
                <a href="manage_news.php" class="btn btn-secondary">⬅️ Quay lại</a>
            </form>
        </div>
    </div>
</div>

<script src="../../assets/ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor', {
        filebrowserBrowseUrl: '../../assets/ckfinder/ckfinder.html',
        filebrowserUploadUrl: '../../assets/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
    });

    function selectImage() {
        CKFinder.popup({
            chooseFiles: true,
            onInit: function (finder) {
                finder.on('files:choose', function (evt) {
                    let file = evt.data.files.first();
                    document.getElementById('image').value = file.getUrl();
                });

                finder.on('file:choose:resizedImage', function (evt) {
                    document.getElementById('image').value = evt.data.resizedUrl;
                });
            }
        });
    }
</script>

<script src="../../assets/ckfinder/ckfinder.js"></script>
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

<?php include '../../partials/footer.php'; ?>
