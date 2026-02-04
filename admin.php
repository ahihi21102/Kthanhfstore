<?php
session_start();
require_once 'db.php';

// === 1. BẢO MẬT ===
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: admin_login.php"); 
    exit();
}

// === 2. XỬ LÝ CÁC HÀNH ĐỘNG ===
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action == 'update') {
        $sql = "UPDATE orders SET status = 'Đã giao hàng' WHERE id = $id";
        $conn->query($sql);
    } 
    elseif ($action == 'delete') {
        $sql_details = "DELETE FROM order_details WHERE order_id = $id";
        $conn->query($sql_details);
        $sql_order = "DELETE FROM orders WHERE id = $id";
        $conn->query($sql_order);
    }
    header("Location: admin.php");
    exit();
}

// === 3. THỐNG KÊ ===
$sql_stats = "SELECT COUNT(*) as total_orders, SUM(total_money) as total_revenue FROM orders";
$stats = $conn->query($sql_stats)->fetch_assoc();

// === 4. LẤY DANH SÁCH ĐƠN HÀNG ===
$sql_orders = "SELECT * FROM orders ORDER BY id DESC";
$result_orders = $conn->query($sql_orders);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị - KThanhf Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>

    <header class="admin-header">
        <a href="admin.php" class="logo-admin"><i class="fa-solid fa-user-shield"></i> Admin Panel</a>
        <div>
            Chào, <span style="font-weight: bold;"><?php echo $_SESSION['user']['fullname']; ?></span> | 
            <a href="logout.php" style="color: var(--red); text-decoration: none; font-weight: bold;">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <div class="stat-info">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Tổng đơn hàng</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: #d4edda; color: #155724;"><i class="fa-solid fa-sack-dollar"></i></div>
                <div class="stat-info">
                    <h3><?php echo number_format($stats['total_revenue'], 0, ',', '.'); ?>đ</h3>
                    <p>Tổng doanh thu</p>
                </div>
            </div>
        </div>

        <div class="order-box">
            <h2 style="margin-top: 0; color: #333; border-bottom: 1px solid #eee; padding-bottom: 15px;">Quản lý đơn hàng</h2>
            <table class="table-admin">
                <thead>
                    <tr>
                        <th width="5%">Mã</th>
                        <th width="15%">Khách hàng</th>
                        <th width="30%">Sản phẩm đã mua</th> <th width="15%">Tổng tiền</th>
                        <th width="15%">Trạng thái</th>
                        <th width="20%">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_orders->num_rows > 0): ?>
                        <?php while($order = $result_orders->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td>
                                <strong><?php echo $order['customer_name']; ?></strong><br>
                                <small style="color: #666;"><?php echo $order['phone']; ?></small><br>
                                <small style="color: #888;"><?php echo date('d/m H:i', strtotime($order['created_at'])); ?></small>
                            </td>
                            
                            <td>
                                <?php 
                                    // Truy vấn lấy sản phẩm của đơn hàng này
                                    $oid = $order['id'];
                                    $sql_items = "SELECT p.name, p.image, od.quantity 
                                                  FROM order_details od 
                                                  JOIN products p ON od.product_id = p.id 
                                                  WHERE od.order_id = $oid";
                                    $res_items = $conn->query($sql_items);
                                    
                                    if($res_items->num_rows > 0) {
                                        while($item = $res_items->fetch_assoc()) {
                                            echo '<div class="admin-product-item">
                                                    <img src="'.$item['image'].'" class="admin-thumb">
                                                    <div>
                                                        <div class="admin-p-name">'.$item['name'].'</div>
                                                        <span class="admin-qty">x'.$item['quantity'].'</span>
                                                    </div>
                                                  </div>';
                                        }
                                    } else {
                                        echo '<span style="color:red; font-size:12px;">Sản phẩm đã bị xóa khỏi hệ thống</span>';
                                    }
                                ?>
                            </td>
                            <td style="color: var(--red); font-weight: bold;">
                                <?php echo number_format($order['total_money'], 0, ',', '.'); ?>đ<br>
                                <small style="font-weight: normal; color: #333; font-size: 12px;"><?php echo $order['payment_method']; ?></small>
                            </td>
                            
                            <td>
                                <?php if($order['status'] == 'Chờ xử lý'): ?>
                                    <span class="badge badge-pending">Chờ xử lý</span>
                                <?php else: ?>
                                    <span class="badge badge-success">Đã giao</span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <?php if($order['status'] == 'Chờ xử lý'): ?>
                                    <a href="admin.php?action=update&id=<?php echo $order['id']; ?>" class="btn-action btn-approve" onclick="return confirm('Xác nhận giao hàng?');">
                                        <i class="fa-solid fa-check"></i> Duyệt
                                    </a>
                                <?php endif; ?>
                                <a href="admin.php?action=delete&id=<?php echo $order['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Bạn chắc chắn muốn xóa đơn này?');">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 30px;">Chưa có đơn hàng nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>