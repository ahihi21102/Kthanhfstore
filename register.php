<?php
session_start();
require_once 'db.php';
$error = '';
$success = '';

// Xử lý khi người dùng bấm nút Đăng ký
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']); 
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $address = $conn->real_escape_string($_POST['address']);

    // 1. Kiểm tra xem tên đăng nhập đã có chưa
    $check = $conn->query("SELECT * FROM users WHERE username = '$username'");
    if ($check->num_rows > 0) {
        $error = "Tên đăng nhập này đã có người dùng!";
    } else {
        // 2. Nếu chưa có thì thêm vào CSDL
        // Lưu ý: Cột 'role' để mặc định là 'customer'
        $sql = "INSERT INTO users (username, password, fullname, phone, address, role) 
                VALUES ('$username', '$password', '$fullname', '$phone', '$address', 'customer')";
        
        if ($conn->query($sql)) {
            $success = "Đăng ký thành công! Đang chuyển hướng...";
            // Tự động chuyển sang trang đăng nhập sau 2 giây
            echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
        } else {
            // Nếu lỗi SQL (ví dụ bảng chưa có cột phone) thì hiện ra để biết
            $error = "Lỗi hệ thống: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - KThanhfStore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: #f0f2f5; z-index: 9999; overflow-y: auto;">
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); width: 100%; max-width: 450px; text-align: center; margin: 20px auto;">
            
            <div style="margin-bottom: 15px;">
                <i class="fa-solid fa-store" style="font-size: 40px; color: #008848;"></i>
                <h2 style="color: #008848; margin-top: 5px; font-weight: 800; text-transform: uppercase;">Đăng Ký</h2>
            </div>

            <?php if($error): ?>
                <div style="background: #fff0f1; color: #d70018; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; border: 1px solid #ffcdd2;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div style="background: #e6fffa; color: #008848; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; border: 1px solid #b2f5ea;">
                    <i class="fa-solid fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left;">
                    <div style="margin-bottom: 15px;">
                        <label style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px;">Họ tên</label>
                        <input type="text" name="fullname" required placeholder="Nguyễn Văn A" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none;">
                    </div>
                    <div style="margin-bottom: 15px;">
                         <label style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px;">SĐT</label>
                        <input type="text" name="phone" required placeholder="09xxx..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none;">
                    </div>
                </div>

                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px;">Địa chỉ</label>
                    <input type="text" name="address" required placeholder="Số nhà, đường, quận/huyện..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none;">
                </div>

                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px;">Tên đăng nhập</label>
                    <input type="text" name="username" required placeholder="Viết liền không dấu" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none;">
                </div>

                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="font-weight: 600; font-size: 13px; display: block; margin-bottom: 5px;">Mật khẩu</label>
                    <input type="password" name="password" required placeholder="Tự đặt mật khẩu..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; outline: none;">
                </div>
                
                <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(to right, #008848, #00b35e); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0, 136, 72, 0.3);">
                    ĐĂNG KÝ TÀI KHOẢN
                </button>
            </form>
            
            <div style="margin-top: 15px; font-size: 14px; color: #666;">
                Đã có tài khoản? <a href="login.php" style="color: #008848; font-weight: bold; text-decoration: none;">Đăng nhập</a>
            </div>
            
             <a href="index.php" style="display: inline-block; margin-top: 15px; color: #888; text-decoration: none; font-size: 13px;">
                <i class="fa-solid fa-arrow-left"></i> Quay về trang chủ
            </a>
        </div>
    </div>
</body>
</html>