<?php
session_start();
require_once 'db.php';

// Nếu đã là admin thì vào thẳng
if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin') {
    header("Location: admin.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Chỉ tìm user có role là 'admin'
    $sql = "SELECT * FROM users WHERE username = '$username' AND role = 'admin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password == $user['password']) {
            $_SESSION['user'] = $user;
            header("Location: admin.php");
            exit();
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Tài khoản không tồn tại hoặc không có quyền Admin!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập Quản Trị - KThanhfStore</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background-color: #f1f5f9; z-index: 9999;">
        
        <div style="background: white; padding: 40px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 380px; text-align: center;">
            
            <div style="margin-bottom: 20px;">
                <i class="fa-solid fa-user-shield" style="font-size: 50px; color: #008848;"></i>
                <h2 style="color: #333; margin-top: 15px; font-weight: 800; text-transform: uppercase; font-size: 20px;">Admin Panel</h2>
            </div>

            <?php if($error): ?>
                <div style="background: #fff0f1; color: #d70018; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; border: 1px solid #ffcdd2;">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 15px; text-align: left;">
                    <label style="font-weight: 600; font-size: 13px; color: #555; display: block; margin-bottom: 5px;">Tài khoản Admin</label>
                    <input type="text" name="username" required placeholder="Nhập username..." 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none;">
                </div>
                
                <div style="margin-bottom: 25px; text-align: left;">
                    <label style="font-weight: 600; font-size: 13px; color: #555; display: block; margin-bottom: 5px;">Mật khẩu</label>
                    <input type="password" name="password" required placeholder="Nhập mật khẩu..." 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none;">
                </div>
                
                <button type="submit" style="width: 100%; padding: 12px; background: #008848; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 16px; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0, 136, 72, 0.3);">
                    ĐĂNG NHẬP
                </button>
            </form>
            
            <div style="margin-top: 25px;">
                <a href="index.php" style="color: #666; text-decoration: none; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left"></i> Quay về trang bán hàng
                </a>
            </div>
        </div>
    </div>

</body>
</html>