<?php
// Bắt buộc phải khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php'; 

// KIỂM TRA ĐĂNG NHẬP ADMIN
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý VLeague</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top" style="background-color: var(--pl-purple) !important;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="fa-solid fa-screwdriver-wrench me-2" style="color: var(--pl-green);"></i>
            ADMIN PANEL
        </a>
        <div class="collapse navbar-collapse" id="navbarNavAdmin">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="matches.php">Trận đấu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="clubs.php">Câu lạc bộ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="players.php">Cầu thủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="news.php">Tin tức</a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link btn-login-custom" href="../logout.php" style="border-color: var(--pl-pink) !important; color: var(--pl-pink) !important;">
                        <i class="fa-solid fa-right-from-bracket me-1"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="text-end mb-4">
        <a href="../index.php" class="btn btn-sm btn-outline-secondary rounded-pill">
            <i class="fa-solid fa-globe me-2"></i> Xem trang khách
        </a>
    </div>

    <h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
        </h3>