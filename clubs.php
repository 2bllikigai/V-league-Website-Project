<?php require_once 'config.php'; require_once 'templates/header.php'; ?>

<div class="text-center mb-5">
    <h2 class="page-title">Các Câu Lạc Bộ</h2>
    <p class="text-muted">Danh sách 14 đội bóng tham dự V.League 1 - 2024/25</p>
</div>

<div class="row g-4">
    <?php
    // Lấy danh sách tất cả đội bóng
    $sql = "SELECT * FROM teams ORDER BY name ASC";
    $stmt = $pdo->query($sql);

    while ($team = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Xử lý ảnh logo
        $img = !empty($team['logo']) ? $team['logo'] : 'assets/img/default.png';
        ?>
        
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm border-0 club-card text-center p-4">
                <a href="team_detail.php?id=<?php echo $team['id']; ?>" class="text-decoration-none text-dark">
                    <div class="mb-3" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                        <img src="<?php echo $img; ?>" alt="<?php echo $team['name']; ?>" 
                             style="max-height: 100px; max-width: 100px; object-fit: contain; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.1));">
                    </div>
                    <h5 class="fw-bold text-uppercase mb-1" style="color: var(--pl-purple); font-size: 1rem;">
                        <?php echo $team['name']; ?>
                    </h5>
                    <div class="small text-muted">
                        <i class="fa-solid fa-location-dot me-1"></i> <?php echo $team['stadium']; ?>
                    </div>
                    
                    <div class="club-arrow mt-3 text-success opacity-0">
                        <i class="fa-solid fa-arrow-right"></i> Xem đội hình
                    </div>
                </a>
            </div>
        </div>

        <?php
    }
    ?>
</div>

<style>
    .club-card {
        transition: transform 0.3s, box-shadow 0.3s;
        background: #fff;
        border-radius: 15px;
    }
    .club-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-bottom: 5px solid var(--pl-green);
    }
    .club-card:hover .club-arrow {
        opacity: 1 !important;
        transform: translateY(-2px);
        transition: all 0.3s;
    }
</style>

</body></html>