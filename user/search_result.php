<?php
include '../config/db_connect.php'; // Kết nối DB

if (!empty($_GET['user_lat']) && !empty($_GET['user_lng'])) {
    $userLat = $_GET['user_lat'];
    $userLng = $_GET['user_lng'];

    // Truy vấn lấy các phòng trọ trong bán kính 5km
    $sql = "SELECT *, 
            (6371 * ACOS(
                COS(RADIANS($userLat)) * COS(RADIANS(latitude)) *
                COS(RADIANS(longitude) - RADIANS($userLng)) +
                SIN(RADIANS($userLat)) * SIN(RADIANS(latitude))
            )) AS distance
            FROM rooms
            WHERE status = 'approved'
            HAVING distance < 5
            ORDER BY distance ASC";

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div>";
            echo "<h3>" . $row['title'] . "</h3>";
            echo "<p>Địa chỉ: " . $row['location'] . "</p>";
            echo "<p>Khoảng cách: " . round($row['distance'], 2) . " km</p>";
            echo "</div><hr>";
        }
    } else {
        echo "Không có phòng trọ nào gần bạn trong bán kính 5km.";
    }
} else {
    echo "Không lấy được vị trí của bạn.";
}
?>
