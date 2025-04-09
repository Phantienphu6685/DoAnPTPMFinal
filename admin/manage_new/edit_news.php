<?php
session_start();
if (!isset($_SESSION["user_role"]) || $_SESSION["user_role"] !== "admin") {
    header("Location: ../../auth/login.php");
    exit;
}

include '../../config/db_connect.php';
include '../../partials/header.php';

// Kiểm tra ID tin tức cần sửa
if (!isset($_GET["id"])) {
    header("Location: manage_news.php");
    exit;
}

$news_id = $_GET["id"];

// Lấy dữ liệu tin tức từ database
$sql = "SELECT title, content, image, status FROM news WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $news_id);
$stmt->execute();
$result = $stmt->get_result();
$news = $result->fetch_assoc();

if (!$news) {
    echo "<script>alert('Tin tức không tồn tại!'); window.location.href = 'manage_news.php';</script>";
    exit;
}

// Xử lý cập nhật thông tin tin tức
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $image = $_POST["image"];
    $status = $_POST["status"];

    $sql_update = "UPDATE news SET title = ?, content = ?, image = ?, status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssi", $title, $content, $image, $status, $news_id);

    if ($stmt_update->execute()) {
        echo "<script>alert('Cập nhật thành công!'); window.location.href = 'manage_news.php';</script>";
    } else {
        echo "<script>alert('Có lỗi xảy ra!');</script>";
    }
}
?>

<div class="container mt-4">
    <h2>✏️ Chỉnh sửa tin tức</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($news['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ảnh đại diện</label>
                    <div class="input-group">
                        <input type="text" id="image" name="image" class="form-control" value="<?= htmlspecialchars($news['image']) ?>" required>
                        <button type="button" class="btn btn-secondary" onclick="selectImage()">📷 Chọn ảnh</button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung</label>
                    <textarea name="content" id="editor" class="form-control"><?= htmlspecialchars($news['content']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="active" <?= ($news['status'] == "active") ? "selected" : "" ?>>Hiển thị</option>
                        <option value="hidden" <?= ($news['status'] == "hidden") ? "selected" : "" ?>>Ẩn</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
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
            }
        });
    }
</script>

<script src="../../assets/ckfinder/ckfinder.js"></script>

<?php include '../../partials/footer.php'; ?>
