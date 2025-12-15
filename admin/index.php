<?php 
require_once '../templates/admin_header.php'; 
// Code còn lại giữ nguyên
?>
<h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
    Dashboard - Tổng quan
</h3>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-shield-halved me-2"></i> CLB</h5>
                <p class="card-text fs-3 fw-bold"><?php echo $pdo->query("SELECT COUNT(*) FROM teams")->fetchColumn(); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-users me-2"></i> Cầu thủ</h5>
                <p class="card-text fs-3 fw-bold"><?php echo $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn(); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title"><i class="fa-solid fa-newspaper me-2"></i> Tin tức</h5>
                <p class="card-text fs-3 fw-bold"><?php echo $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn(); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <h4 class="fw-bold text-uppercase mb-3" style="color: var(--pl-purple);">Các hành động chính</h4>
    <div class="row g-3">
        <div class="col-md-4">
            <a href="matches.php" class="btn btn-lg btn-danger w-100 shadow-sm">
                <i class="fa-solid fa-chart-line me-2"></i> Cập nhật TỶ SỐ
            </a>
        </div>
        <div class="col-md-4">
            <a href="clubs.php" class="btn btn-lg btn-secondary w-100 shadow-sm">
                <i class="fa-solid fa-plus me-2"></i> Thêm CLB/Cầu thủ
            </a>
        </div>
        <div class="col-md-4">
            <a href="news.php" class="btn btn-lg btn-info w-100 text-white shadow-sm">
                <i class="fa-solid fa-upload me-2"></i> Đăng Tin mới
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php 
echo '</div></body></html>'; 
?>