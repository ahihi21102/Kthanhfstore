<?php 
// 1. Khởi động Session & Kết nối Database
session_start();
require_once 'db.php'; 

// 2. Lấy ID sản phẩm từ thanh địa chỉ
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 3. Truy vấn SQL để lấy thông tin sản phẩm thật
$sql = "SELECT * FROM products WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    // Nếu không tìm thấy ID trong database thì về trang chủ
    header("Location: index.php");
    exit();
}

// 4. Tính số lượng giỏ hàng & Kiểm tra đăng nhập
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$user_logged_in = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> KThanhfStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <div class="container header-top">
            <div class="logo">
                <a href="index.php" style="text-decoration: none;">
                    <h2 style="color: var(--primary-color);"><i class="fa-solid fa-store"></i> KThanhfStore</h2>
                </a>
            </div>
            
            <form action="index.php" method="GET" class="search-bar" style="display: flex;">
                <input type="text" name="keyword" placeholder="Bạn tìm gì hôm nay?">
                <button type="submit" style="background: none; border: none; cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #888;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>

            <div class="header-actions">
                <?php if ($user_logged_in): ?>
                    <div class="user-dropdown">
                        <div style="color: var(--primary-color); font-weight: bold;">
                            <i class="fa-solid fa-user-check"></i> Chào, <?php echo $user_logged_in['fullname']; ?>
                        </div>
                        <div class="user-menu">
    <a href="my_orders.php">Đơn hàng của tôi</a>
    <a href="logout.php" style="color: red;">Đăng xuất</a>
</div>
                    </div>
                <?php else: ?>
                    <a href="login.php" style="text-decoration: none; color: inherit;">
                        <i class="fa-regular fa-user"></i> Đăng nhập
                    </a>
                <?php endif; ?>
                
                <div>
                    <a href="cart.php" style="color: inherit; text-decoration: none;">
                        <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng (<?php echo $cart_count; ?>)
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container" style="margin-top: 20px; margin-bottom: 50px;">
        
        <div class="breadcrumb">
            <a href="index.php">Trang chủ</a> / 
            <a href="index.php?category=<?php echo $product['category']; ?>"><?php echo $product['category']; ?></a> / 
            <span><?php echo $product['name']; ?></span>
        </div>

        <div class="product-detail-container">
            <div class="pd-image">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
            </div>

            <div class="pd-info">
                <h1><?php echo $product['name']; ?></h1>
                
                <div class="pd-meta">
                    <span>Thương hiệu: Chính hãng</span> 
                    <span style="margin-left: 20px;">
                        <?php for($i=0; $i<5; $i++) echo '<i class="fa-solid fa-star text-warning" style="color:#ffc107"></i>'; ?>
                        (Đánh giá tốt)
                    </span>
                </div>

                <div class="pd-price-box">
                    <span class="pd-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</span>
                    <span class="pd-old-price"><?php echo number_format($product['old_price'], 0, ',', '.'); ?>đ</span>
                </div>

                <div class="pd-specs">
                    <h3>Thông số nổi bật:</h3>
                    <ul>
                        <?php 
                        // XỬ LÝ CHUỖI MÔ TẢ TỪ DATABASE
                        if (!empty($product['description'])) {
                            $specs = explode(',', $product['description']);
                            foreach ($specs as $spec) {
                                // Tách dấu hai chấm (:) để in đậm phần tiêu đề
                                $parts = explode(':', $spec);
                                if (count($parts) == 2) {
                                    echo "<li><strong>" . trim($parts[0]) . ":</strong> " . trim($parts[1]) . "</li>";
                                } else {
                                    echo "<li>" . trim($spec) . "</li>";
                                }
                            }
                        } else {
                            echo "<li>Đang cập nhật thông số...</li>";
                        }
                        ?>
                    </ul>
                </div>

                <div class="pd-actions">
                    <a href="buy_now.php?id=<?php echo $product['id']; ?>" class="btn-buy-now" style="text-decoration: none; display: flex; align-items: center; justify-content: center; text-align: center;">
                        MUA NGAY <br>
                        <span style="font-size:12px; font-weight:normal; text-transform: none;">Giao tận nơi hoặc nhận tại cửa hàng</span>
                    </a>
                    
                    <a href="add_cart.php?id=<?php echo $product['id']; ?>" class="btn-add-cart" style="text-decoration: none; display: flex; align-items: center; justify-content: center; text-align: center;">
                        <div>
                            <i class="fa-solid fa-cart-plus"></i> <br>
                            <span style="font-size:12px; font-weight:normal; text-transform: none;">Thêm vào giỏ</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Về chúng tôi</h3>
                    <ul>
                        <li><a href="#">Giới thiệu công ty</a></li>
                        <li><a href="#">Hệ thống cửa hàng</a></li>
                        <li><a href="#">Tuyển dụng nhân tài</a></li>
                        <li><a href="#">Tin tức công nghệ</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Hỗ trợ khách hàng</h3>
                    <ul>
                        <li><a href="#">Chính sách bảo hành</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Hướng dẫn mua hàng online</a></li>
                        <li><a href="#">Bảo mật thông tin</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Liên hệ</h3>
                    <ul>
                        <li><i class="fa-solid fa-location-dot" style="width: 20px;"></i> 123 Đường ABC, Thái Nguyên</li>
                        <li><i class="fa-solid fa-phone" style="width: 20px;"></i> 1900 1234 (8:00 - 22:00)</li>
                        <li><i class="fa-solid fa-envelope" style="width: 20px;"></i> hotro@kthanhfstore.com</li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Kết nối với chúng tôi</h3>
                    <div style="font-size: 24px; display: flex; gap: 15px; margin-top: 10px;">
                        <a href="#" style="color: white; transition: 0.3s;"><i class="fa-brands fa-facebook"></i></a>
                        <a href="#" style="color: white; transition: 0.3s;"><i class="fa-brands fa-youtube"></i></a>
                        <a href="#" style="color: white; transition: 0.3s;"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="#" style="color: white; transition: 0.3s;"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                &copy; 2026 Triệu Kim Thành đã đăng ký bản quyền.
            </div>
        </div>
    </footer>
    </body>
</html>