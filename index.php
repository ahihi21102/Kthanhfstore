<?php 
session_start(); 
require_once 'db.php'; 

// Xử lý logic lọc & sắp xếp
$current_category = isset($_GET['category']) ? $_GET['category'] : 'Tất cả';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; 

$sql = "SELECT * FROM products WHERE 1=1"; 
if ($current_category != 'Tất cả') {
    $cat_safe = $conn->real_escape_string($current_category);
    $sql .= " AND category = '$cat_safe'";
}
if ($keyword != '') {
    $kw_safe = $conn->real_escape_string($keyword);
    $sql .= " AND name LIKE '%$kw_safe%'";
}
switch ($sort) {
    case 'price_asc': $sql .= " ORDER BY price ASC"; break;
    case 'price_desc': $sql .= " ORDER BY price DESC"; break;
    default: $sql .= " ORDER BY id DESC"; break;
}
$result = $conn->query($sql);
$filtered_products = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) $filtered_products[] = $row;
}
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
$user_logged_in = isset($_SESSION['user']) ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KThanhf Store - Thế giới công nghệ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <div class="container header-top">
            <div class="logo">
                <a href="index.php" style="text-decoration:none;">
                    <h2><i class="fa-solid fa-store"></i> KThanhfStore</h2>
                </a>
            </div>
            
            <div class="search-box-container">
                <form action="index.php" method="GET" class="search-bar">
                    <input type="hidden" name="category" value="<?php echo $current_category; ?>">
                    <input type="hidden" name="sort" value="<?php echo $sort; ?>">
                    <input type="text" id="searchInput" name="keyword" placeholder="Bạn tìm gì hôm nay?" value="<?php echo $keyword; ?>" autocomplete="off" onkeyup="searchSuggest(this.value)">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <div id="suggestionBox" class="search-suggestions"></div>
            </div>

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

                <a href="cart.php" style="color: inherit; text-decoration: none;">
                    <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng (<?php echo $cart_count; ?>)
                </a>
            </div>
        </div>
    </header>

    <nav class="category-nav">
        <div class="container">
            <ul class="cat-list">
                <?php 
                    $cats = ['Tất cả', 'Điện thoại', 'Laptop', 'Tablet', 'Phụ kiện', 'Đồng hồ'];
                    $icons = ['border-all', 'mobile-screen', 'laptop', 'tablet', 'headphones', 'clock'];
                    
                    foreach($cats as $index => $cat): 
                        // 👇 Kiểm tra: Nếu đang xem danh mục này thì thêm class 'active'
                        $activeClass = ($current_category == $cat) ? 'active' : '';
                ?>
                <li class="cat-item">
                    <a href="index.php?category=<?php echo $cat; ?>&sort=<?php echo $sort; ?>" class="<?php echo $activeClass; ?>">
                        <i class="fa-<?php echo ($index == 5) ? 'regular' : 'solid'; ?> fa-<?php echo $icons[$index]; ?>"></i> 
                        <?php echo $cat; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <main class="container">
        <?php if($keyword == ''): ?>
        <div class="hero-banner">
            
            <img src="images/baner2.png">
        </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="margin: 0; font-size: 20px;">
                <?php echo ($keyword != '') ? "Kết quả tìm kiếm: \"$keyword\"" : $current_category; ?>
                <small style="font-weight: normal; font-size: 14px; color: #666; margin-left: 5px;">(<?php echo count($filtered_products); ?> sản phẩm)</small>
            </h2>
            
            <select onchange="sortProducts(this.value)" style="padding: 5px; border-radius: 5px; border: 1px solid #ddd;">
                <option value="newest" <?php if($sort == 'newest') echo 'selected'; ?>>Mới nhất</option>
                <option value="price_asc" <?php if($sort == 'price_asc') echo 'selected'; ?>>Giá thấp đến cao</option>
                <option value="price_desc" <?php if($sort == 'price_desc') echo 'selected'; ?>>Giá cao đến thấp</option>
            </select>
        </div>

        <div class="product-grid">
            <?php if(count($filtered_products) > 0): ?>
                <?php foreach ($filtered_products as $product): ?>
                <div class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>" style="text-decoration:none; color:inherit; flex:1; display: flex; flex-direction: column;">
                        <?php if($product['badge']): ?>
                            <span class="badge <?php echo $product['badge_color']; ?>"><?php echo $product['badge']; ?></span>
                        <?php endif; ?>
                        
                        <?php 
                            $percent = 0;
                            if($product['old_price'] > 0) { $percent = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100); }
                        ?>
                        <?php if($percent > 0): ?>
                            <span class="discount">-<?php echo $percent; ?>%</span>
                        <?php endif; ?>

                        <img src="<?php echo $product['image']; ?>" class="p-img">
                        <h3 class="p-name"><?php echo $product['name']; ?></h3>
                        
                        <div class="p-price">
                            <?php echo number_format($product['price'], 0, ',', '.'); ?>đ
                            <span class="p-old-price"><?php echo number_format($product['old_price'], 0, ',', '.'); ?>đ</span>
                        </div>
                    </a>
                    
                    <a href="add_cart.php?id=<?php echo $product['id']; ?>" class="btn-add">
                        <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: white;">
                    <p>Không tìm thấy sản phẩm nào.</p>
                    <a href="index.php" style="color: var(--primary-color); font-weight: bold;">Xóa bộ lọc</a>
                </div>
            <?php endif; ?>
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

    <script>
        let slideIndex = 0;
        const slides = document.querySelectorAll('.banner-slide');
        
        function showSlides() {
            if(slides.length === 0) return;
            slides[slideIndex].classList.remove('active');
            slideIndex = (slideIndex + 1) % slides.length;
            slides[slideIndex].classList.add('active');
        }
        if(slides.length > 0) setInterval(showSlides, 3000);

        function sortProducts(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', value);
            window.location.href = url.toString();
        }

        function searchSuggest(keyword) {
            let box = document.getElementById('suggestionBox');
            if (keyword.length === 0) { box.style.display = "none"; return; }
            let xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    box.innerHTML = this.responseText;
                    box.style.display = "block";
                }
            };
            xhr.open("GET", "search_ajax.php?key=" + keyword, true);
            xhr.send();
        }
        document.addEventListener('click', function(e) {
            if (!document.querySelector('.search-box-container').contains(e.target)) {
                document.getElementById('suggestionBox').style.display = 'none';
            }
        });
    </script>
</body>
</html>