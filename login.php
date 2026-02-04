<?php
session_start();
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password == $row['password']) {
            $_SESSION['user'] = $row;
            if (isset($_SESSION['redirect_to'])) {
                $url = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: $url");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Mật khẩu không đúng!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - KThanhfStore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: #f0f2f5; z-index: 9999;">
        
        <div style="background: white; padding: 40px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); width: 100%; max-width: 400px; text-align: center;">
            
            <div style="margin-bottom: 20px;">
                <i class="fa-solid fa-store" style="font-size: 50px; color: #008848;"></i>
                <h2 style="color: #008848; margin-top: 10px; font-weight: 800; text-transform: uppercase;">Đăng Nhập</h2>
            </div>

            <?php if($error): ?>
                <div style="background: #fff0f1; color: #d70018; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; border: 1px solid #ffcdd2;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="font-weight: 600; color: #555; display: block; margin-bottom: 5px;">
                        <i class="fa-solid fa-user"></i> Tên đăng nhập
                    </label>
                    <input type="text" name="username" required placeholder="Nhập username..." 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 15px;">
                </div>
                
                <div style="margin-bottom: 20px; text-align: left;">
                    <label style="font-weight: 600; color: #555; display: block; margin-bottom: 5px;">
                        <i class="fa-solid fa-lock"></i> Mật khẩu
                    </label>
                    <input type="password" name="password" required placeholder="Nhập mật khẩu..." 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; font-size: 15px;">
                </div>
                
                <button type="submit" style="width: 100%; padding: 14px; background: linear-gradient(to right, #008848, #00b35e); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0, 136, 72, 0.3);">
                    ĐĂNG NHẬP NGAY
                </button>
            </form>
            
            <div style="margin-top: 20px; font-size: 14px; color: #666;">
                Chưa có tài khoản? <a href="register.php" style="color: #008848; font-weight: bold; text-decoration: none;">Đăng ký ngay</a>
            </div>
            
            <a href="index.php" style="display: inline-block; margin-top: 20px; color: #888; text-decoration: none; font-size: 13px;">
                <i class="fa-solid fa-arrow-left"></i> Quay về trang chủ
            </a>
        </div>
    </div>

</body>
</html>