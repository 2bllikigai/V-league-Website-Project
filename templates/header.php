<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giải Vô địch Quốc Gia LPBank 2025/2026</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="assets/img/logos/vleague.png" alt="V.League Logo" class="me-2" style="height: 24px;">
            V.League
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>
            
            <li class="nav-item"><a class="nav-link" href="results.php">Kết quả</a></li>
            
            <li class="nav-item"><a class="nav-link" href="next_matches.php">Lịch thi đấu</a></li>
            
            <li class="nav-item"><a class="nav-link" href="standings.php">Bảng xếp hạng</a></li>
            <li class="nav-item"><a class="nav-link" href="clubs.php">Câu lạc bộ</a></li>
            <li class="nav-item"><a class="nav-link" href="players.php">Cầu thủ</a></li>
            <li class="nav-item"><a class="nav-link" href="stats.php">Thống kê</a></li>
            <li class="nav-item"><a class="nav-link" href="news.php">Tin tức</a></li>
        </ul>
                    <ul class="navbar-nav">
                <?php if(isset($_SESSION['admin_logged_in'])): ?>
                    <li class="nav-item"><a class="nav-link btn-login-custom" href="admin/index.php">Admin Panel</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Thoát</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link btn-login-custom" href="login.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container">