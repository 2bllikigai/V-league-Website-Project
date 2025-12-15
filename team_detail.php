<?php 
require_once 'config.php'; require_once 'templates/header.php'; 

// 1. LẤY ID ĐỘI BÓNG TỪ URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 2. TRUY VẤN THÔNG TIN ĐỘI
$stmt = $pdo->prepare("SELECT * FROM teams WHERE id = ?");
$stmt->execute([$id]);
$team = $stmt->fetch();

if (!$team) {
    echo "<div class='alert alert-danger text-center my-5'>Không tìm thấy đội bóng!</div>";
    require_once 'templates/footer.php'; exit;
}

$team_logo = !empty($team['logo']) ? $team['logo'] : 'assets/img/default.png';

// 3. TRUY VẤN TRẬN SẮP TỚI
$sql_next = "SELECT m.*, t1.name as h_name, t1.logo as h_logo, t2.name as a_name, t2.logo as a_logo
             FROM matches m JOIN teams t1 ON m.home_team_id = t1.id JOIN teams t2 ON m.away_team_id = t2.id
             WHERE (m.home_team_id = $id OR m.away_team_id = $id) AND m.finished = 0
             ORDER BY m.match_date ASC LIMIT 1";
$next_match = $pdo->query($sql_next)->fetch();

// 4. TRUY VẤN LỊCH SỬ ĐẤU
$sql_hist = "SELECT m.*, t1.name as h_name, t1.logo as h_logo, t2.name as a_name, t2.logo as a_logo
             FROM matches m JOIN teams t1 ON m.home_team_id = t1.id JOIN teams t2 ON m.away_team_id = t2.id
             WHERE (m.home_team_id = $id OR m.away_team_id = $id) AND m.finished = 1
             ORDER BY m.match_date DESC LIMIT 5";
$history = $pdo->query($sql_hist)->fetchAll();

// 5. TRUY VẤN CẦU THỦ & PHÂN LOẠI
$players = $pdo->query("SELECT * FROM players WHERE team_id = $id ORDER BY number ASC")->fetchAll();

$gk = []; $df = []; $mf = []; $fw = [];
foreach ($players as $p) {
    $pos = mb_strtolower($p['position']);
    // Phân loại dựa trên chuỗi ký tự
    if (strpos($pos, 'thủ môn') !== false) $gk[] = $p;
    elseif (strpos($pos, 'hậu vệ') !== false) $df[] = $p;
    elseif (strpos($pos, 'tiền vệ') !== false) $mf[] = $p;
    else $fw[] = $p; // Tiền đạo và các vị trí khác
}

// HÀM HIỂN THỊ DANH SÁCH CẦU THỦ (DẠNG COMPACT CÓ LINK)
function renderCompactList($list, $title) {
    if (empty($list)) return;
    ?>
    <div class="mb-4">
        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2 text-uppercase" style="color: var(--pl-purple) !important; letter-spacing: 1px; font-size: 0.9rem;">
            <?php echo $title; ?>
        </h5>
        <div class="row g-3">
            <?php foreach ($list as $p): 
                $img = !empty($p['image']) ? $p['image'] : 'assets/img/default_player.png';
            ?>
            <div class="col-12">
                <a href="player_detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                    <div class="player-compact-card d-flex align-items-center bg-white p-2 rounded shadow-sm border">
                        <div class="player-thumb-box rounded-3 overflow-hidden me-3 bg-light border">
                            <img src="<?php echo $img; ?>" alt="<?php echo $p['name']; ?>">
                        </div>
                        
                        <div class="player-info flex-grow-1">
                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;"><?php echo $p['name']; ?></h6>
                            <div class="text-muted small d-flex align-items-center">
                                <span class="fw-bold text-white bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px; height:24px; font-size: 11px; margin-right: 8px;">
                                    <?php echo $p['number']; ?>
                                </span>
                                <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem;"><?php echo $p['position']; ?></span>
                            </div>
                        </div>
                        
                        <div class="me-2 text-muted opacity-25 arrow-icon">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
?>

<div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-top: 5px solid var(--pl-purple);">
    <div class="card-body p-0">
        <div class="row g-0">
            <div class="col-md-3 bg-light d-flex align-items-center justify-content-center p-4">
                <img src="<?php echo $team_logo; ?>" class="img-fluid" style="max-height: 140px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
            </div>
            
            <div class="col-md-9 p-4">
                <h1 class="fw-black text-uppercase display-6 mb-2" style="color: var(--pl-purple); font-weight: 900;">
                    <?php echo $team['name']; ?>
                </h1>
                <div class="text-muted mb-4"><?php echo isset($team['description']) ? $team['description'] : 'Câu lạc bộ chuyên nghiệp V.League 1'; ?></div>
                
                <div class="d-flex flex-wrap gap-4 text-secondary">
                    <div class="d-flex align-items-center">
                        <div class="icon-box me-2"><i class="fa-solid fa-location-dot text-danger"></i></div>
                        <div>
                            <div class="small fw-bold text-uppercase" style="font-size: 0.7rem;">Sân vận động</div>
                            <div class="fw-bold text-dark"><?php echo $team['stadium']; ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="icon-box me-2"><i class="fa-solid fa-user-tie text-primary"></i></div>
                        <div>
                            <div class="small fw-bold text-uppercase" style="font-size: 0.7rem;">Huấn luyện viên</div>
                            <div class="fw-bold text-dark"><?php echo !empty($team['coach']) ? $team['coach'] : '---'; ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="icon-box me-2"><i class="fa-regular fa-calendar text-success"></i></div>
                        <div>
                            <div class="small fw-bold text-uppercase" style="font-size: 0.7rem;">Thành lập</div>
                            <div class="fw-bold text-dark"><?php echo !empty($team['founded_year']) ? $team['founded_year'] : '---'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <h5 class="fw-bold text-uppercase mb-3" style="color: var(--pl-purple);">Trận đấu tiếp theo</h5>
        <?php if ($next_match): 
            $h_img = !empty($next_match['h_logo']) ? $next_match['h_logo'] : 'assets/img/default.png';
            $a_img = !empty($next_match['a_logo']) ? $next_match['a_logo'] : 'assets/img/default.png';
        ?>
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--pl-green);">
                <div class="card-body text-center d-flex flex-column justify-content-center py-4">
                    <div class="d-flex justify-content-around align-items-center mb-3">
                        <div class="text-center" style="width: 100px;">
                            <img src="<?php echo $h_img; ?>" style="width:60px; height:60px; object-fit:contain;">
                            <div class="fw-bold mt-2 small text-uppercase"><?php echo $next_match['h_name']; ?></div>
                        </div>
                        <div>
                            <div class="h2 fw-black text-secondary m-0">VS</div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success">Sắp diễn ra</span>
                        </div>
                        <div class="text-center" style="width: 100px;">
                            <img src="<?php echo $a_img; ?>" style="width:60px; height:60px; object-fit:contain;">
                            <div class="fw-bold mt-2 small text-uppercase"><?php echo $next_match['a_name']; ?></div>
                        </div>
                    </div>
                    <div class="text-muted small fw-bold border-top pt-3">
                        <i class="fa-regular fa-clock me-1"></i> <?php echo date("H:i", strtotime($next_match['match_date'])); ?> 
                        &nbsp;|&nbsp; 
                        <i class="fa-regular fa-calendar me-1"></i> <?php echo date("d/m/Y", strtotime($next_match['match_date'])); ?>
                    </div>
                </div>
            </div>
        <?php else: ?><div class="alert alert-light border text-center py-4">Chưa có lịch thi đấu.</div><?php endif; ?>
    </div>

    <div class="col-lg-6 mb-3">
        <h5 class="fw-bold text-uppercase mb-3" style="color: var(--pl-purple);">Phong độ gần đây</h5>
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-0">
                <?php if (count($history) > 0): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($history as $h): 
                            $is_home = ($h['home_team_id'] == $id);
                            $my_score = $is_home ? $h['home_score'] : $h['away_score'];
                            $opp_score = $is_home ? $h['away_score'] : $h['home_score'];
                            
                            // Badge W/D/L đẹp mắt
                            if ($my_score > $opp_score) { $badge = '<span class="badge bg-success" style="width:25px">W</span>'; }
                            elseif ($my_score < $opp_score) { $badge = '<span class="badge bg-danger" style="width:25px">L</span>'; }
                            else { $badge = '<span class="badge bg-secondary" style="width:25px">D</span>'; }

                            $opp_name = $is_home ? $h['a_name'] : $h['h_name'];
                            $opp_img = !empty($is_home ? $h['a_logo'] : $h['h_logo']) ? ($is_home ? $h['a_logo'] : $h['h_logo']) : 'assets/img/default.png';
                        ?>
                        <div class="list-group-item d-flex align-items-center justify-content-between py-2 px-3 border-bottom-0">
                            <div class="d-flex align-items-center">
                                <span class="text-muted small me-3" style="width:20px">vs</span>
                                <img src="<?php echo $opp_img; ?>" width="24" height="24" style="object-fit: contain;" class="me-2">
                                <span class="fw-bold small text-dark"><?php echo $opp_name; ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold me-3" style="font-family: monospace;"><?php echo $h['home_score']; ?> - <?php echo $h['away_score']; ?></span>
                                <?php echo $badge; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?><div class="p-4 text-center text-muted">Chưa có dữ liệu.</div><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-5">
    <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-3" style="border-color: var(--pl-green) !important;">
        <h3 class="fw-black text-uppercase m-0" style="color: var(--pl-purple);">Đội hình thi đấu</h3>
        <span class="ms-3 badge bg-light text-dark border"><?php echo count($players); ?> Cầu thủ</span>
    </div>

    <?php if(count($players) == 0): ?>
        <div class="alert alert-warning text-center py-5">Chưa cập nhật danh sách cầu thủ.</div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-6">
                <?php renderCompactList($gk, 'Thủ Môn (Goalkeepers)'); ?>
                <?php renderCompactList($df, 'Hậu Vệ (Defenders)'); ?>
            </div>

            <div class="col-lg-6">
                <?php renderCompactList($mf, 'Tiền Vệ (Midfielders)'); ?>
                <?php renderCompactList($fw, 'Tiền Đạo (Forwards)'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="my-5 text-center">
    <a href="clubs.php" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="fa-solid fa-arrow-left me-2"></i> Danh sách CLB
    </a>
</div>

<style>
    .player-compact-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .player-compact-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        border-color: var(--pl-green) !important;
    }

    .player-compact-card:hover .arrow-icon {
        opacity: 1;
        transform: translateX(3px);
        color: var(--pl-purple) !important;
        transition: all 0.2s;
    }

    .player-thumb-box {
        width: 65px;
        height: 65px;
        flex-shrink: 0;
        display: flex;
        align-items: flex-end; /* Đẩy ảnh xuống đáy */
        justify-content: center;
        background-color: #fff;
        padding-bottom: 0;
    }

    .player-thumb-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain; /* Ảnh hiển thị trọn vẹn */
        object-position: bottom center;
    }
    
    .icon-box {
        width: 30px; height: 30px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
    }
</style>

</body></html>