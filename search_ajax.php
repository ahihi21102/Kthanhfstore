<?php
require_once 'db.php';

// Lấy từ khóa từ URL
$key = isset($_GET['key']) ? trim($_GET['key']) : '';

if (strlen($key) > 0) {
    // Tìm kiếm sản phẩm (Lấy tối đa 5 kết quả để hiển thị cho gọn)
    $sql = "SELECT * FROM products WHERE name LIKE '%$key%' LIMIT 5";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<ul class="suggest-list">';
        while ($row = $result->fetch_assoc()) {
            $price = number_format($row['price'], 0, ',', '.');
            // Link khi bấm vào sẽ sang trang chi tiết
            echo '<li>
                    <a href="product.php?id='.$row['id'].'">
                        <img src="'.$row['image'].'" alt="">
                        <div class="info">
                            <div class="name">'.$row['name'].'</div>
                            <div class="price">'.$price.'đ</div>
                        </div>
                    </a>
                  </li>';
        }
        echo '</ul>';
    } else {
        echo '<div style="padding:10px; color:#666;">Không tìm thấy sản phẩm nào...</div>';
    }
}
?>