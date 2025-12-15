<?php
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
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
    $stmt->execute([$id]);
    
    header("location: news.php?status=deleted");
    exit;
}

// Xử lý THÊM/SỬA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $summary = $_POST['summary'];
    $content = $_POST['content'];
    $image_url = $_POST['image_url'] ?? null; // Có thể dùng link ngoài

    // Logic xử lý URL nếu người dùng chỉ dán link Youtube hoặc link ảnh
    if (empty($image_url) && !empty($_POST['url_link'])) {
        $image_url = $_POST['url_link'];
    }

    if (empty($id)) {
        // THÊM MỚI (CREATE)
        $sql = "INSERT INTO news (title, summary, content, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $summary, $content, $image_url]);
        $status = 'added';
    } else {
        // SỬA (UPDATE)
        $sql = "UPDATE news SET title = ?, summary = ?, content = ?, image_url = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $summary, $content, $image_url, $id]);
        $status = 'updated';
    }

    header("location: news.php?status=$status");
    exit;
}

header("location: news.php");
exit;
?>