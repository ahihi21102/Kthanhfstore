<?php
session_start();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Nếu giỏ hàng chưa có thì tạo mới
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Cộng dồn số lượng
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]++;
    } else {
        $_SESSION['cart'][$id] = 1;
    }
}

// KHÁC BIỆT: Chuyển hướng thẳng sang trang Thanh toán
header("Location: checkout.php");
exit();
?>