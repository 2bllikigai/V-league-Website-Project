<?php
// Bắt buộc phải khởi động session và kiểm tra đăng nhập
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("location: ../login.php");
    exit;
}

// Xử lý XÓA
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = $_POST['id'];
    
    try {
        // Sử dụng DELETE để xóa (CASCADE sẽ tự xóa cầu thủ liên quan)
        $stmt = $pdo->prepare("DELETE FROM teams WHERE id = ?");
        $stmt->execute([$id]);
        
        // Quay về trang quản lý CLB với thông báo thành công
        header("location: clubs.php?status=deleted");
        exit;
    } catch (PDOException $e) {
        // Nếu bị lỗi khóa ngoại (do ràng buộc chưa được xử lý triệt để)
        header("location: clubs.php?status=error_fk");
        exit;
    }
}

// Xử lý THÊM/SỬA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $stadium = $_POST['stadium'];
    $coach = $_POST['coach'] ?? null;
    $founded_year = $_POST['founded_year'] ?? null;

    // Logic xử lý file upload (Rất đơn giản, không đảm bảo bảo mật)
    $logo_path = null;
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] == 0) {
        // Giả sử file được upload vào thư mục ../assets/img/logos/
        $file_name = uniqid() . '_' . basename($_FILES['logo_file']['name']);
        $target_dir = '../assets/img/logos/';
        
        if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $target_dir . $file_name)) {
            $logo_path = 'assets/img/logos/' . $file_name; // Lưu đường dẫn tương đối vào DB
        }
    }

    if (empty($id)) {
        // THÊM MỚI (CREATE)
        $sql = "INSERT INTO teams (name, stadium, coach, founded_year, logo) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $stadium, $coach, $founded_year, $logo_path]);
        $status = 'added';
    } else {
        // SỬA (UPDATE)
        $updates = ["name = ?", "stadium = ?", "coach = ?", "founded_year = ?"];
        $params = [$name, $stadium, $coach, $founded_year];
        
        if ($logo_path) {
            $updates[] = "logo = ?";
            $params[] = $logo_path;
        }

        $sql = "UPDATE teams SET " . implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $status = 'updated';
    }

    header("location: clubs.php?status=$status");
    exit;
}

header("location: clubs.php");
exit;
?>