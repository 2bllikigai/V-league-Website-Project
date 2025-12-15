<?php require_once 'config.php'; require_once 'templates/header.php'; 

// 1. LẤY TIN MỚI NHẤT (Cho thanh chạy chữ)
$latest_news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 1")->fetch();

// 2. LẤY DANH SÁCH TIN TỨC (Cho khối cuối trang)
$list_news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3")->fetchAll();
?>

<?php if ($latest_news): ?>
<div class="container-fluid breaking-news-bar">
    <div class="container d-flex align-items-center">
        <span class="news-label">TIN MỚI</span>
        <a href="news_detail.php?id=<?php echo $latest_news['id']; ?>" class="news-link text-truncate">
            <?php echo $latest_news['title']; ?> <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
    </div>
</div>
<?php endif; ?>

<div class="container pb-5">
    <div class="text-center mb-5">
        <h1 class="fw-black text-uppercase display-5" style="color: var(--pl-purple);">V.League Dashboard</h1>
        <p class="text-muted">Trung tâm dữ liệu bóng đá chuyên nghiệp Việt Nam</p>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                    <h5 class="fw-bold text-uppercase m-0" style="color: var(--pl-green);"><i class="fa-regular fa-calendar me-2"></i> Sắp diễn ra</h5>
                    <a href="next_matches.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Xem lịch thi đấu</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php
                        // Lấy thêm cột stadium và logo
                        $sql = "SELECT m.*, t1.name as h_name, t1.logo as h_logo, t1.stadium as h_stadium,
                                       t2.name as a_name, t2.logo as a_logo 
                                FROM matches m JOIN teams t1 ON m.home_team_id = t1.id JOIN teams t2 ON m.away_team_id = t2.id 
                                WHERE m.finished = 0 ORDER BY m.match_date ASC LIMIT 5";
                        $next_matches = $pdo->query($sql)->fetchAll();

                        if(count($next_matches) == 0) echo "<div class='p-3 text-center text-muted'>Chưa có lịch thi đấu.</div>";

                        foreach ($next_matches as $row):
                            $h_img = !empty($row['h_logo']) ? $row['h_logo'] : 'assets/img/default.png';
                            $a_img = !empty($row['a_logo']) ? $row['a_logo'] : 'assets/img/default.png';
                            $time = date("d/m H:i", strtotime($row['match_date']));
                        ?>
                        <div class="list-group-item py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center" style="width: 40%;">
                                    <a href="team_detail.php?id=<?php echo $row['home_team_id']; ?>" class="text-decoration-none text-dark d-flex align-items-center team-link-hover">
                                        <img src="<?php echo $h_img; ?>" width="30" height="30" style="object-fit: contain;" class="me-2">
                                        <span class="fw-bold small"><?php echo $row['h_name']; ?></span>
                                    </a>
                                </div>
                                
                                <div class="text-center bg-light rounded px-2 py-1 small fw-bold text-muted" style="width: 20%;">
                                    <?php echo $time; ?>
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-end" style="width: 40%;">
                                    <a href="team_detail.php?id=<?php echo $row['away_team_id']; ?>" class="text-decoration-none text-dark d-flex align-items-center justify-content-end team-link-hover">
                                        <span class="fw-bold small me-2"><?php echo $row['a_name']; ?></span>
                                        <img src="<?php echo $a_img; ?>" width="30" height="30" style="object-fit: contain;">
                                    </a>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted" style="font-size: 0.75rem;"><i class="fa-solid fa-location-dot me-1 text-danger"></i> Sân <?php echo $row['h_stadium']; ?></small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                    <h5 class="fw-bold text-uppercase m-0" style="color: var(--pl-purple);"><i class="fa-solid fa-check-circle me-2"></i> Vừa kết thúc</h5>
                    <a href="results.php" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Xem kết quả</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php
                        $sql = "SELECT m.*, t1.name as h_name, t1.logo as h_logo, t2.name as a_name, t2.logo as a_logo 
                                FROM matches m JOIN teams t1 ON m.home_team_id = t1.id JOIN teams t2 ON m.away_team_id = t2.id 
                                WHERE m.finished = 1 ORDER BY m.match_date DESC LIMIT 5";
                        $last_results = $pdo->query($sql)->fetchAll();

                        if(count($last_results) == 0) echo "<div class='p-3 text-center text-muted'>Chưa có kết quả.</div>";

                        foreach ($last_results as $row):
                            $h_img = !empty($row['h_logo']) ? $row['h_logo'] : 'assets/img/default.png';
                            $a_img = !empty($row['a_logo']) ? $row['a_logo'] : 'assets/img/default.png';
                        ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                            <div class="d-flex align-items-center" style="width: 40%;">
                                <a href="team_detail.php?id=<?php echo $row['home_team_id']; ?>" class="text-decoration-none text-dark d-flex align-items-center team-link-hover">
                                    <img src="<?php echo $h_img; ?>" width="30" height="30" style="object-fit: contain;" class="me-2">
                                    <span class="fw-bold small"><?php echo $row['h_name']; ?></span>
                                </a>
                            </div>
                            
                            <div class="text-center fw-black text-white bg-dark rounded-pill px-3 py-1" style="font-size: 0.9rem; min-width: 60px;">
                                <?php echo $row['home_score']; ?> - <?php echo $row['away_score']; ?>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-end" style="width: 40%;">
                                <a href="team_detail.php?id=<?php echo $row['away_team_id']; ?>" class="text-decoration-none text-dark d-flex align-items-center justify-content-end team-link-hover">
                                    <span class="fw-bold small me-2"><?php echo $row['a_name']; ?></span>
                                    <img src="<?php echo $a_img; ?>" width="30" height="30" style="object-fit: contain;">
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                    <h5 class="fw-bold text-uppercase m-0"><i class="fa-solid fa-list-ol me-2"></i> Bảng Xếp Hạng</h5>
                    <a href="standings.php" class="btn btn-sm btn-primary rounded-pill px-3">Xem chi tiết</a>
                </div>
                <div class="card-body p-0">
                    <?php
                    // Logic tính điểm nhanh
                    $teams = [];
                    foreach ($pdo->query("SELECT * FROM teams") as $t) {
                        $teams[$t['id']] = ['info' => $t, 'p' => 0, 'gf' => 0, 'ga' => 0, 'pts' => 0];
                    }
                    $matches = $pdo->query("SELECT * FROM matches WHERE finished = 1");
                    foreach ($matches as $m) {
                        $h = $m['home_team_id']; $a = $m['away_team_id'];
                        if (!isset($teams[$h]) || !isset($teams[$a])) continue;
                        $teams[$h]['p']++; $teams[$a]['p']++;
                        $teams[$h]['gf'] += $m['home_score']; $teams[$h]['ga'] += $m['away_score'];
                        $teams[$a]['gf'] += $m['away_score']; $teams[$a]['ga'] += $m['home_score'];
                        if ($m['home_score'] > $m['away_score']) { $teams[$h]['pts'] += 3; }
                        elseif ($m['home_score'] < $m['away_score']) { $teams[$a]['pts'] += 3; }
                        else { $teams[$h]['pts'] += 1; $teams[$a]['pts'] += 1; }
                    }
                    usort($teams, function($a, $b) {
                        if ($a['pts'] != $b['pts']) return $b['pts'] - $a['pts'];
                        return ($b['gf'] - $b['ga']) - ($a['gf'] - $a['ga']);
                    });
                    ?>
                    <table class="table table-hover table-sm mb-0 text-center align-middle">
                        <thead class="table-light"><tr><th>#</th><th class="text-start">CLB</th><th>Trận</th><th>HS</th><th>Điểm</th></tr></thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach (array_slice($teams, 0, 5) as $data): // Top 5
                                $img = !empty($data['info']['logo']) ? $data['info']['logo'] : 'assets/img/default.png';
                            ?>
                            <tr>
                                <td><?php echo $rank; ?></td>
                                <td class="text-start fw-bold">
                                    <a href="team_detail.php?id=<?php echo $data['info']['id']; ?>" class="text-decoration-none text-dark team-link-hover">
                                        <img src="<?php echo $img; ?>" width="20" class="me-2">
                                        <?php echo $data['info']['name']; ?>
                                    </a>
                                </td>
                                <td><?php echo $data['p']; ?></td>
                                <td><?php echo $data['gf'] - $data['ga']; ?></td>
                                <td class="fw-bold text-primary"><?php echo $data['pts']; ?></td>
                            </tr>
                            <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                    <h5 class="fw-bold text-uppercase m-0 text-danger"><i class="fa-solid fa-bullseye me-2"></i> Vua Phá Lưới</h5>
                    <a href="stats.php" class="btn btn-sm btn-outline-danger rounded-pill px-3">Xem thêm</a>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php
                        $sql = "SELECT p.name, t.logo, COUNT(g.id) as goals 
                                FROM goals g JOIN players p ON g.scored_by_id = p.id 
                                JOIN teams t ON p.team_id = t.id 
                                GROUP BY p.id ORDER BY goals DESC LIMIT 5";
                        $scorers = $pdo->query($sql)->fetchAll();
                        $i = 1;
                        foreach ($scorers as $s):
                            $img = !empty($s['logo']) ? $s['logo'] : 'assets/img/default.png';
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-light text-dark me-3 border"><?php echo $i++; ?></span>
                                <div>
                                    <div class="fw-bold small"><?php echo $s['name']; ?></div>
                                    <img src="<?php echo $img; ?>" width="15">
                                </div>
                            </div>
                            <span class="badge bg-danger rounded-pill"><?php echo $s['goals']; ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

    </div> <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <h3 class="fw-black text-uppercase m-0" style="color: var(--pl-purple);">Tin Tức Mới</h3>
        </div>

        <div class="row g-4">
            <?php foreach ($list_news as $news): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <img src="<?php echo $news['image_url']; ?>" class="card-img-top news-card-img" alt="News" style="height: 180px; object-fit: cover;">
                    <div class="card-body">
                        <div class="small text-muted mb-2"><i class="fa-regular fa-clock"></i> <?php echo date("d/m/Y", strtotime($news['created_at'])); ?></div>
                        <h5 class="card-title fw-bold" style="color: var(--pl-purple); font-size: 1.1rem;"><?php echo $news['title']; ?></h5>
                        <p class="card-text text-secondary small"><?php echo $news['summary']; ?></p>
                        <a href="news_detail.php?id=<?php echo $news['id']; ?>" class="text-decoration-none fw-bold text-primary">Đọc tiếp &rarr;</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

</div> <div class="container-fluid sponsors-section" style="background: #fff; border-top: 4px solid var(--pl-green); padding: 40px 0; margin-top: 50px;">
    <div class="container text-center">
        <h6 class="text-muted text-uppercase fw-bold mb-4" style="letter-spacing: 2px;">Nhà Tài Trợ Chính</h6>
        <div class="d-flex justify-content-center align-items-center flex-wrap gap-5">
            <img src="assets/img/founder/VPF.jpg" style="height: 60px; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s;" onmouseover="this.style.filter='none'; this.style.opacity='1'" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.7'">
            <img src="assets/img/founder/LPBank.png" style="height: 45px; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s;" onmouseover="this.style.filter='none'; this.style.opacity='1'" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.7'">
            <img src="assets/img/founder/FPT.png" style="height: 40px; filter: grayscale(100%); opacity: 0.7; transition: all 0.3s;" onmouseover="this.style.filter='none'; this.style.opacity='1'" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.7'">
        </div>
    </div>
</div>

<style>
    .team-link-hover:hover span { color: var(--pl-green) !important; text-decoration: underline; }
    .team-link-hover:hover img { transform: scale(1.1); transition: transform 0.2s; }
    .breaking-news-bar { background-color: #e90052; color: white; padding: 10px 0; font-size: 0.9rem; margin-bottom: 20px; }
    .news-label { background: white; color: #e90052; padding: 2px 8px; border-radius: 4px; font-weight: 800; font-size: 0.75rem; margin-right: 10px; }
    .news-link { color: white; text-decoration: none; font-weight: 600; }
    .news-link:hover { color: #ffd1e3; text-decoration: underline; }
</style>

</body></html>