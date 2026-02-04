<?php 
session_start();
// 👇 SỬA LỖI QUAN TRỌNG: Kết nối db.php thay vì data.php
require_once 'db.php'; 

// Xử lý xóa sản phẩm (Nếu bấm nút xóa)
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// Xử lý xóa nhiều
if (isset($_POST['delete_selected'])) {
    if (isset($_POST['remove']) && is_array($_POST['remove'])) {
        foreach ($_POST['remove'] as $remove_id) {
            unset($_SESSION['cart'][$remove_id]);
        }
    }
    header("Location: cart.php");
    exit();
}

$cart_items = [];
$total_money = 0;

// Lấy dữ liệu sản phẩm từ Database dựa trên Session Cart
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $cart_ids = array_keys($_SESSION['cart']);
    if (!empty($cart_ids)) {
        $ids_string = implode(',', $cart_ids);
        $sql = "SELECT * FROM products WHERE id IN ($ids_string)";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['id'];
                $quantity = $_SESSION['cart'][$product_id];
                $row['qty'] = $quantity;
                $row['total'] = $row['price'] * $quantity;
                $cart_items[] = $row;
                $total_money += $row['total'];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - KThanhf Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <header>
        <div class="container header-top">
            <div class="logo">
                <a href="index.php" style="text-decoration:none;">
                    <h2 style="color: var(--primary-color);"><i class="fa-solid fa-store"></i> KThanhfStore</h2>
                </a>
            </div>
            <a href="index.php" style="color: #333; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm</a>
        </div>
    </header>

    <div class="container" style="margin-top:30px; margin-bottom: 50px;">
        <h2 style="margin-bottom: 20px;"><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của bạn</h2>
        
        <?php if (empty($cart_items)): ?>
            <div style="text-align: center; padding: 50px; background: white; border-radius: 8px;">
                <i class="fa-solid fa-basket-shopping" style="font-size: 60px; color: #ddd; margin-bottom: 20px;"></i>
                <p>Giỏ hàng đang trống!</p>
                <a href="index.php" class="btn-add" style="display:inline-block; width:auto; padding: 10px 30px;">Quay lại mua sắm</a>
            </div>
        <?php else: ?>

            <form method="POST" action="">
                <table class="cart-table" style="width: 100%; border-collapse: collapse; background: white;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #eee;">
                            <th style="padding: 15px; text-align: center; width: 50px;">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th style="padding: 15px; text-align: left;">Sản phẩm</th>
                            <th style="padding: 15px;">Đơn giá</th>
                            <th style="padding: 15px;">Số lượng</th>
                            <th style="padding: 15px;">Thành tiền</th>
                            <th style="padding: 15px;">Xóa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="text-align: center;">
                                <input type="checkbox" name="remove[]" value="<?php echo $item['id']; ?>" class="item-checkbox">
                            </td>
                            <td style="padding: 15px; display: flex; align-items: center; gap: 15px;">
                                <img src="<?php echo $item['image']; ?>" style="width: 60px; height: 60px; object-fit: contain; border: 1px solid #eee; border-radius: 5px;">
                                <div>
                                    <div style="font-weight: bold;"><?php echo $item['name']; ?></div>
                                    <small style="color: #888;"><?php echo $item['category']; ?></small>
                                </div>
                            </td>
                            <td style="padding: 15px; text-align: center;"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</td>
                            <td style="padding: 15px; text-align: center;">
                                <span style="background: #f0f0f0; padding: 5px 15px; border-radius: 5px; font-weight: bold;"><?php echo $item['qty']; ?></span>
                            </td>
                            <td style="padding: 15px; text-align: center; color: var(--red); font-weight: bold;">
                                <?php echo number_format($item['total'], 0, ',', '.'); ?>đ
                            </td>
                            <td style="padding: 15px; text-align: center;">
                                <a href="cart.php?action=delete&id=<?php echo $item['id']; ?>" style="color: #999;" onclick="return confirm('Xóa sản phẩm này?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <button type="submit" name="delete_selected" style="background: white; border: 1px solid #ff424e; color: #ff424e; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;" onclick="return confirm('Xóa các mục đã chọn?');">
                        <i class="fa-solid fa-trash-can"></i> Xóa đã chọn
                    </button>

                    <div style="text-align: right;">
                        <p style="font-size: 18px;">Tổng thanh toán: <strong style="color: var(--red); font-size: 24px;"><?php echo number_format($total_money, 0, ',', '.'); ?>đ</strong></p>
                        <a href="checkout.php" class="btn-add" style="display: inline-block; width: auto; padding: 12px 40px; margin-top: 10px; text-decoration: none;">TIẾN HÀNH ĐẶT HÀNG</a>
                    </div>
                </div>
            </form>

        <?php endif; ?>
    </div>

    <script>
        function toggleSelectAll() {
            var selectAll = document.getElementById('selectAll');
            var boxes = document.getElementsByClassName('item-checkbox');
            for (var i = 0; i < boxes.length; i++) {
                boxes[i].checked = selectAll.checked;
            }
        }
    </script>
</body>
</html>