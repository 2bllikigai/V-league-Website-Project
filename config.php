<?php
// Cấu hình kết nối CSDL

// BƯỚC SỬA LỖI: Kiểm tra session_status() trước khi gọi session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Hết phần sửa lỗi

try {
    $pdo = new PDO("mysql:host=localhost;dbname=vleague_final;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}
?>