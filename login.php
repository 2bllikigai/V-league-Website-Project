<?php 
// Bắt đầu session ngay đầu file (quan trọng để lưu trạng thái đăng nhập)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

$error = ''; // Khởi tạo biến lỗi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Tìm user trong database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // 2. Kiểm tra: Có user VÀ Mật khẩu khớp (dùng password_verify)
    if ($user && password_verify($password, $user['password'])) {
        // Đăng nhập thành công
        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_id"] = $user['id'];
        $_SESSION["admin_name"] = $user['username'];
        
        // Chuyển hướng vào trang quản trị
        header("location: admin/index.php");
        exit;
    } else {
        // Đăng nhập thất bại
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}

require_once 'templates/header.php'; 
?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4 fw-bold" style="color: var(--pl-purple);">Admin Login</h3>
                    
                    <?php if(!empty($error)): ?>
                        <div class="alert alert-danger text-center p-2"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" name="username" class="form-control" required placeholder="Nhập tên đăng nhập">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Nhập mật khẩu">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold" style="background-color: var(--pl-purple); border: none;">
                            ĐĂNG NHẬP
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="index.php" class="text-decoration-none text-muted">&larr; Quay về trang chủ</a>
            </div>
        </div>
    </div>
</div>

</body></html>