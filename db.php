<?php
$servername = "localhost";
$username = "root"; // Mặc định XAMPP là root
$password = "";     // Mặc định XAMPP không có pass
$dbname = "Kthanhfstore_db";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Hàm hỗ trợ format tiền tệ
if (!function_exists('format_currency')) {
    function format_currency($n) {
        return number_format($n, 0, ',', '.') . 'đ';
    }
}
?>