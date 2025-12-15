<?php require_once 'config.php'; require_once 'templates/header.php'; 

// LẤY BỘ LỌC
$selected_round = isset($_GET['round']) ? $_GET['round'] : '';
$selected_team  = isset($_GET['team_id']) ? $_GET['team_id'] : '';
$all_teams = $pdo->query("SELECT id, name FROM teams ORDER BY name ASC")->fetchAll();
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h2 class="page-title m-0">Lịch Thi Đấu Sắp Tới</h2>
        
        <form method="GET" action="" class="d-flex gap-2">
            <select name="team_id" class="form-select form-select-sm fw-bold border-primary text-primary" style="width: 180px;" onchange="this.form.submit()">
                <option value="">-- Tất cả CLB --</option>
                <?php foreach ($all_teams as $t): ?>
                    <option value="<?php echo $t['id']; ?>" <?php if($selected_team == $t['id']) echo 'selected'; ?>><?php echo $t['name']; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="round" class="form-select form-select-sm fw-bold border-success text-success" style="width: 120px;" onchange="this.form.submit()">
                <option value="">-- Vòng --</option>
                <?php for($i=1; $i<=26; $i++): ?>
                    <option value="<?php echo $i; ?>" <?php if($selected_round == $i) echo 'selected'; ?>>Vòng <?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            
            <?php if($selected_round || $selected_team): ?>
                <a href="next_matches.php" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-xmark"></i></a>
            <?php endif; ?>
        </form>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <?php
            // --- SQL LỌC LỊCH THI ĐẤU ---
            $sql = "SELECT m.*, t1.name as home_name, t1.logo as home_logo, t1.stadium as home_stadium,
                           t2.name as away_name, t2.logo as away_logo
                    FROM matches m 
                    JOIN teams t1 ON m.home_team_id = t1.id
                    JOIN teams t2 ON m.away_team_id = t2.id
                    WHERE m.finished = 0"; // CHƯA ĐÁ
            
            if (!empty($selected_round)) $sql .= " AND m.round = $selected_round";
            if (!empty($selected_team))  $sql .= " AND (m.home_team_id = $selected_team OR m.away_team_id = $selected_team)";
            
            $sql .= " ORDER BY m.match_date ASC"; // Sắp xếp ngày gần nhất đá trước
            
            $stmt = $pdo->query($sql);

            if ($stmt->rowCount() == 0) {
                echo "<div class='alert alert-warning text-center'>Không tìm thấy lịch thi đấu nào.</div>";
            }

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $time = date("H:i", strtotime($row['match_date']));
                $date = date("d/m/Y", strtotime($row['match_date']));
                $diff = strtotime($row['match_date']) - time();
                $days_left = floor($diff / 86400);
                $status_text = ($days_left > 0) ? "Còn $days_left ngày" : "Sắp diễn ra";
                $badge_class = ($days_left > 0) ? "bg-secondary" : "bg-danger";
                
                $home_img = !empty($row['home_logo']) ? $row['home_logo'] : 'assets/img/default.png';
                $away_img = !empty($row['away_logo']) ? $row['away_logo'] : 'assets/img/default.png';
                ?>
                <div class="match-card live p-4">
                     <div class="position-absolute top-0 start-0 px-3 py-1 bg-success text-white rounded-bottom small fw-bold">
                        Vòng <?php echo $row['round']; ?>
                    </div>

                    <div class="row align-items-center mt-2">
                        <div class="col-4 d-flex justify-content-end align-items-center">
                            <a href="team_detail.php?id=<?php echo $row['home_team_id']; ?>" class="text-decoration-none text-dark d-flex align-items-center justify-content-end team-link-match">
                                <h5 class="fw-bold m-0 me-3 d-none d-md-block text-uppercase text-end"><?php echo $row['home_name']; ?></h5>
                                <img src="<?php echo $home_img; ?>" class="team-logo-match" style="width: 60px; height: 60px; object-fit: contain;">
                            </a>
                        </div>
                        
                        <div class="col-4 text-center">
                            <div class="fs-2 fw-black text-secondary" style="font-weight: 900; color: #ccc;">VS</div>
                            <div class="badge <?php echo $badge_class; ?> mt-1"><?php echo $status_text; ?></div>
                            <div class="fw-bold mt-2" style="color: var(--pl-purple);">
                                <?php echo $time; ?> <br> <small class="text-muted"><?php echo $date; ?></small>
                            </div>
                        </div>
                        
                        <div class="col-4 d-flex justify-content-start align-items-center">
                            <a href="team_detail.php?id=<?php echo $row['away_team_id']; ?>" class="text-decoration-none text-dark d-flex align-items-center justify-content-start team-link-match">
                                <img src="<?php echo $away_img; ?>" class="team-logo-match me-3" style="width: 60px; height: 60px; object-fit: contain;">
                                <h5 class="fw-bold m-0 d-none d-md-block text-uppercase"><?php echo $row['away_name']; ?></h5>
                            </a>
                        </div>
                    </div>
                    <div class="text-center mt-3 pt-3 border-top text-muted small">
                        <i class="fa-solid fa-location-dot me-1 text-danger"></i> Sân <?php echo $row['home_stadium']; ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<style>
    .team-link-match:hover h5 { color: var(--pl-purple); text-decoration: underline; }
    .team-link-match:hover img { transform: scale(1.1); transition: transform 0.2s; }
    .form-select-sm { border-radius: 20px; padding-left: 15px; cursor: pointer; }
</style>
</body></html>