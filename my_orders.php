<?php
session_start();
require_once 'db.php';

// 1. CHẶN KHÁCH CHƯA ĐĂNG NHẬP
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user']['id'];

// 2. LẤY DANH SÁCH ĐƠN HÀNG (Mới nhất lên đầu)
$sql = "SELECT * FROM orders WHERE user_id = '$uid' ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đơn hàng - KThanhfStore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <header style="background: white; padding: 10px 0; border-bottom: 1px solid #ddd; position: sticky; top: 0; z-index: 99;">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <a href="index.php" style="font-weight: 800; font-size: 22px; color: #008848; text-transform: uppercase; text-decoration: none;">
                <i class="fa-solid fa-store"></i> KThanhfStore
            </a>
            <a href="index.php" class="btn-back">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
        </div>
    </header>

    <div class="history-container">
        
        <div class="page-title"><i class="fa-solid fa-receipt"></i> Lịch sử đơn hàng</div>

        <?php if ($result->num_rows > 0): ?>
            <?php while($order = $result->fetch_assoc()): ?>
                <div class="order-card">
                    
                    <div class="card-header">
                        <div>
                            <span class="order-id">Đơn hàng #<?php echo $order['id']; ?></span>
                            <span class="order-date"><?php echo date('H:i - d/m/Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        
                        <?php if($order['status']=='Chờ xử lý'): ?>
                            <div class="status-badge st-pending"><i class="fa-regular fa-clock"></i> Chờ xác nhận</div>
                        <?php elseif($order['status']=='Đã giao hàng'): ?>
                            <div class="status-badge st-success"><i class="fa-solid fa-check-circle"></i> Giao thành công</div>
                        <?php else: ?>
                             <div class="status-badge st-shipping"><i class="fa-solid fa-truck"></i> <?php echo $order['status']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <?php 
                            $oid = $order['id'];
                            // 👇 CÂU LỆNH QUAN TRỌNG: Lấy ảnh sản phẩm từ bảng products
                            $sql_items = "SELECT od.*, p.name, p.image 
                                          FROM order_details od 
                                          JOIN products p ON od.product_id = p.id 
                                          WHERE od.order_id = $oid";
                            $res_items = $conn->query($sql_items);
                            
                            while($item = $res_items->fetch_assoc()):
                        ?>
                            <div class="item-row">
                                <img src="<?php echo $item['image']; ?>" class="item-img" onerror="this.src='https://via.placeholder.com/80'">
                                
                                <div class="item-info">
                                    <div class="item-name"><?php echo $item['name']; ?></div>
                                    <div class="item-qty">x<?php echo $item['quantity']; ?></div>
                                </div>
                                <div class="item-price">
                                    <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="card-footer">
                        <span class="total-label">Thành tiền:</span>
                        <span class="total-price"><?php echo number_format($order['total_money'], 0, ',', '.'); ?>đ</span>
                    </div>

                </div>
            <?php endwhile; ?>
            
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 8px;">
                <img src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/assets/5fafbb923393b712b96488590b8f781f.png" style="width: 120px; opacity: 0.7; margin-bottom: 20px;">
                <p style="color: #666; font-size: 16px;">Bạn chưa có đơn hàng nào cả.</p>
                <a href="index.php" style="background: #008848; color: white; padding: 12px 25px; border-radius: 5px; text-decoration: none; font-weight: bold; display: inline-block; margin-top: 15px; box-shadow: 0 4px 10px rgba(0,136,72,0.3);">
                    MUA SẮM NGAY
                </a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>