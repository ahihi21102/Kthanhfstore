<?php
session_start(); // Khởi động bộ nhớ (BẮT BUỘC)

// Lấy ID sản phẩm từ đường link
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Nếu giỏ hàng chưa có gì thì tạo mới
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Nếu sản phẩm đã có trong giỏ -> Tăng số lượng lên 1
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        // Nếu chưa có -> Thêm mới với số lượng 1
        $_SESSION['cart'][$id] = 1;
    }
}

// Quay lại trang trước đó (Trang chủ hoặc Chi tiết)
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();
?>