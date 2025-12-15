<?php require_once 'config.php'; require_once 'templates/header.php'; 

// --- XỬ LÝ BỘ LỌC ---
$search   = isset($_GET['search']) ? trim($_GET['search']) : '';
$team_id  = isset($_GET['team_id']) ? $_GET['team_id'] : '';
$position = isset($_GET['position']) ? $_GET['position'] : '';

// Lấy danh sách đội cho Dropdown
$all_teams = $pdo->query("SELECT * FROM teams ORDER BY name ASC")->fetchAll();
?>

<div class="container mb-5">
    <div class="text-center mb-4">
        <h2 class="page-title">Danh Sách Cầu Thủ</h2>
    </div>

    <div class="card border-0 shadow-sm mb-4 bg-light">
        <div class="card-body p-4">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control border-0 shadow-sm" placeholder="Nhập tên cầu thủ..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-3">
                    <select name="team_id" class="form-select border-0 shadow-sm">
                        <option value="">-- Tất cả CLB --</option>
                        <?php foreach ($all_teams as $t): ?>
                            <option value="<?php echo $t['id']; ?>" <?php if($team_id == $t['id']) echo 'selected'; ?>>
                                <?php echo $t['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="position" class="form-select border-0 shadow-sm">
                        <option value="">-- Tất cả Vị trí --</option>
                        <option value="Thủ môn" <?php if($position == 'Thủ môn') echo 'selected'; ?>>Thủ môn</option>
                        <option value="Hậu vệ" <?php if($position == 'Hậu vệ') echo 'selected'; ?>>Hậu vệ</option>
                        <option value="Tiền vệ" <?php if($position == 'Tiền vệ') echo 'selected'; ?>>Tiền vệ</option>
                        <option value="Tiền đạo" <?php if($position == 'Tiền đạo') echo 'selected'; ?>>Tiền đạo</option>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fa-solid fa-magnifying-glass me-2"></i> Tìm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3">
        <?php
        // Xây dựng câu lệnh SQL động
        $sql = "SELECT p.*, t.name as team_name, t.logo as team_logo 
                FROM players p 
                JOIN teams t ON p.team_id = t.id 
                WHERE 1=1"; // Kỹ thuật 1=1 để dễ nối chuỗi AND

        $params = [];

        if (!empty($search)) {
            $sql .= " AND p.name LIKE ?";
            $params[] = "%$search%";
        }

        if (!empty($team_id)) {
            $sql .= " AND p.team_id = ?";
            $params[] = $team_id;
        }

        if (!empty($position)) {
            $sql .= " AND p.position LIKE ?"; // Dùng LIKE để 'Tiền vệ' khớp cả 'Tiền vệ cánh'...
            $params[] = "%$position%";
        }

        $sql .= " ORDER BY t.name ASC, p.number ASC"; // Sắp xếp theo đội rồi đến số áo

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $players = $stmt->fetchAll();

        // HIỂN THỊ
        if (count($players) == 0) {
            echo "<div class='col-12'><div class='alert alert-warning text-center'>Không tìm thấy cầu thủ nào phù hợp.</div></div>";
        }

        foreach ($players as $p):
            $img = !empty($p['image']) ? $p['image'] : 'assets/img/default_player.png';
            $t_img = !empty($p['team_logo']) ? $p['team_logo'] : 'assets/img/default.png';
        ?>
        <div class="col-md-6 col-lg-4">
            <a href="player_detail.php?id=<?php echo $p['id']; ?>" class="text-decoration-none">
                <div class="player-compact-card d-flex align-items-center bg-white p-2 rounded shadow-sm border">
                    <div class="player-thumb-box rounded-3 overflow-hidden me-3 bg-light border">
                        <img src="<?php echo $img; ?>" alt="<?php echo $p['name']; ?>">
                    </div>
                    
                    <div class="player-info flex-grow-1">
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;"><?php echo $p['name']; ?></h6>
                        <div class="text-muted small d-flex align-items-center flex-wrap">
                            <span class="fw-bold text-white bg-primary rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:22px; height:22px; font-size: 10px;">
                                <?php echo $p['number']; ?>
                            </span>
                            <span class="text-uppercase text-secondary fw-bold me-2" style="font-size: 0.75rem;"><?php echo $p['position']; ?></span>
                        </div>
                    </div>

                    <div class="ms-2 text-center" style="width: 40px;">
                        <img src="<?php echo $t_img; ?>" style="width: 30px; height: 30px; object-fit: contain;" title="<?php echo $p['team_name']; ?>">
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .player-compact-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
    .player-compact-card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; border-color: var(--pl-green) !important; }
    .player-thumb-box { width: 60px; height: 60px; flex-shrink: 0; display: flex; align-items: flex-end; justify-content: center; background-color: #fff; padding-bottom: 0; }
    .player-thumb-box img { max-width: 100%; max-height: 100%; object-fit: contain; object-position: bottom center; }
</style>

</body></html>