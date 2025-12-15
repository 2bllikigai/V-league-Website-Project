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
    
    // Xóa tất cả các bản ghi liên quan trong goals và cards trước
    $pdo->prepare("DELETE FROM goals WHERE scored_by_id = ? OR assist_by_id = ?")->execute([$id, $id]);
    $pdo->prepare("DELETE FROM cards WHERE player_id = ?")->execute([$id]);
    
    // Xóa cầu thủ
    $stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
    $stmt->execute([$id]);
    
    header("location: players.php?status=deleted");
    exit;
}

// Xử lý THÊM/SỬA
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $number = $_POST['number'];
    $team_id = $_POST['team_id'];
    $position = $_POST['position'];

    // Xử lý file ảnh (Tương tự CLB)
    $image_path = null;
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $file_name = uniqid() . '_' . basename($_FILES['image_file']['name']);
        $target_dir = '../assets/img/player/misc/'; // Thư mục chung cho ảnh tải lên
        
        // Đảm bảo thư mục tồn tại
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_dir . $file_name)) {
            $image_path = 'assets/img/player/misc/' . $file_name;
        }
    }

    if (empty($id)) {
        // THÊM MỚI (CREATE)
        $sql = "INSERT INTO players (name, number, position, team_id, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $number, $position, $team_id, $image_path]);
        $status = 'added';
    } else {
        // SỬA (UPDATE)
        $updates = ["name = ?", "number = ?", "position = ?", "team_id = ?"];
        $params = [$name, $number, $position, $team_id];
        
        if ($image_path) {
            $updates[] = "image = ?";
            $params[] = $image_path;
        }

        $sql = "UPDATE players SET " . implode(', ', $updates) . " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $status = 'updated';
    }

    header("location: players.php?status=$status");
    exit;
}

header("location: players.php");
exit;
?>