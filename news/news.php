<?php
include '../config/db_connect.php';
include '../partials/header.php';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin Tức Phòng Trọ</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    
    <style>
        .card-img-top {
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }
        .hover-shadow:hover {
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.3s;
        }
        .card-title {
            font-weight: bold;
            color: #007bff;
        }
        .card-text {
            font-size: 14px;
            color: #333;
        }
        .card-footer {
            background-color: #f8f9fa;
        }
        .btn-outline-primary {
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }
        .card {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
        }
    </style>
</head>
<body>

<!-- Nội dung -->
<div class="container mt-4">
    <h2 class="text-center mb-4 text-primary fw-bold animate__animated animate__fadeInDown">Danh Sách Tin Tức Phòng Trọ</h2>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $sql = "SELECT * FROM news WHERE status = 'active' ORDER BY id DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $imagePath = !empty($row['image']) ? 'http://localhost' . $row['image'] : 'default.jpg';
                ?>
                <div class="col animate__animated animate__zoomIn">
                    <div class="card h-100 shadow-lg hover-shadow">
                        <img src="<?= htmlspecialchars($imagePath) ?>" class="card-img-top" alt="Hình ảnh tin tức">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                            <p class="card-text"><?php echo substr($row['content'], 0, 800); ?>...</p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <small class="text-muted">🗓 Ngày đăng: <?= $row['created_at'] ?></small>
                            <a href="news_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center text-danger animate__animated animate__fadeIn'>Không có tin tức nào.</p>";
        }
        ?>
    </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php include '../partials/footer.php'; ?>