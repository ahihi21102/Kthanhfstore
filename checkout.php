<?php
session_start();
require_once 'db.php';

// === 1. CHẶN NGƯỜI CHƯA ĐĂNG NHẬP ===
if (!isset($_SESSION['user'])) {
    // Lưu lại dấu vết: Khách đang muốn vào trang checkout
    $_SESSION['redirect_to'] = 'checkout.php';
    
    // Đá sang trang đăng nhập
    echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='login.php';</script>";
    exit();
}

// Lấy thông tin người dùng đang đăng nhập để điền sẵn vào form
$user = $_SESSION['user'];

// === 2. XỬ LÝ GIỎ HÀNG (Giữ nguyên logic cũ) ===
$cart_items = [];
$total = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    if ($ids) {
        $res = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
        while ($row = $res->fetch_assoc()) {
            $row['qty'] = $_SESSION['cart'][$row['id']];
            $row['total'] = $row['price'] * $row['qty'];
            $cart_items[] = $row;
            $total += $row['total'];
        }
    }
}

// === 3. XỬ LÝ ĐẶT HÀNG ===
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $user['id']; // Lấy ID từ session người dùng thật
    $name = $_POST['fullname'];
    $phone = $_POST['phone'];
    $addr = $_POST['address'];
    $pay = $_POST['payment'];
    $note = $_POST['note']; // Thêm ghi chú nếu thích

    // Lưu đơn hàng
    $sql = "INSERT INTO orders (user_id, customer_name, phone, address, payment_method, total_money, status, created_at) 
            VALUES ('$uid', '$name', '$phone', '$addr', '$pay', '$total', 'Chờ xử lý', NOW())";
            
    if ($conn->query($sql)) {
        $oid = $conn->insert_id;
        // Lưu chi tiết đơn hàng
        foreach ($cart_items as $item) {
            $conn->query("INSERT INTO order_details (order_id, product_id, price, quantity) 
                          VALUES ('$oid', '{$item['id']}', '{$item['price']}', '{$item['qty']}')");
        }
        
        unset($_SESSION['cart']); // Xóa giỏ hàng
        echo "<script>alert('Đặt hàng thành công! Cảm ơn bạn.'); window.location.href='index.php';</script>";
        exit;
    } else {
        echo "Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="checkout-container">
        <h2 style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Thanh toán</h2>
        
        <div style="display: flex; gap: 40px;">
            <div style="flex: 1;">
                <form method="POST">
                    <div class="form-group">
                        <label>Họ và tên người nhận</label>
                        <input type="text" name="fullname" required value="<?php echo $user['fullname']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" required value="<?php echo $user['phone']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Địa chỉ giao hàng</label>
                        <textarea name="address" required rows="3"><?php echo $user['address']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Ghi chú (Tùy chọn)</label>
                        <textarea name="note" rows="2"></textarea>
                    </div>

                    <h4 style="margin-top: 20px;">Phương thức thanh toán</h4>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="COD" checked> 
                        Thanh toán khi nhận hàng (COD)
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment" value="Banking"> 
                        Chuyển khoản ngân hàng
                    </label>

                    <button class="btn-submit-order">HOÀN TẤT ĐẶT HÀNG</button>
                    <a href="cart.php" style="display:block; text-align:center; margin-top:10px; color:#666;">Quay lại giỏ hàng</a>
                </form>
            </div>

            <div style="width: 350px;">
                <div class="order-summary">
                    <h3 style="margin-top: 0;">Đơn hàng của bạn</h3>
                    <hr style="border: 0; border-top: 1px solid #eee; margin: 10px 0;">
                    
                    <?php foreach($cart_items as $item): ?>
                    <div class="order-row">
                        <span style="font-weight: 600; font-size: 13px;">
                            <?php echo $item['qty']; ?>x <?php echo $item['name']; ?>
                        </span>
                        <span><?php echo number_format($item['total'], 0, ',', '.'); ?>đ</span>
                    </div>
                    <?php endforeach; ?>
                    
                    <div class="total-row">
                        <span>Tổng cộng:</span>
                        <span><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>