<?php
include 'config/db_connect.php';

if ($conn) {
    echo "✅ Kết nối database thành công!";
} else {
    echo "❌ Kết nối thất bại!";
}
?>
