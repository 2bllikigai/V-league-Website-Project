<?php require_once 'config.php'; require_once 'templates/header.php'; ?>

<div class="text-center mb-4">
    <h2 class="page-title">Trung Tâm Thống Kê</h2>
</div>

<ul class="nav nav-tabs nav-fill mb-4" id="statsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-uppercase" id="player-tab" data-bs-toggle="tab" data-bs-target="#player-stats" type="button" role="tab">
            <i class="fa-solid fa-user me-2"></i> Thống kê Cầu Thủ
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-uppercase" id="club-tab" data-bs-toggle="tab" data-bs-target="#club-stats" type="button" role="tab">
            <i class="fa-solid fa-shield-halved me-2"></i> Thống kê Câu Lạc Bộ
        </button>
    </li>
</ul>

<div class="tab-content" id="statsTabContent">

    <div class="tab-pane fade show active" id="player-stats" role="tabpanel">
        <div class="row">
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="text-danger fw-bold text-uppercase"><i class="fa-solid fa-futbol me-2"></i> Bàn Thắng</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>#</th><th>Cầu thủ</th><th class="text-center">SL</th></tr></thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.name, p.image, t.name as team_name, t.logo, COUNT(g.id) as count 
                                        FROM goals g JOIN players p ON g.scored_by_id = p.id 
                                        JOIN teams t ON p.team_id = t.id 
                                        GROUP BY p.id ORDER BY count DESC LIMIT 5";
                                $result = $pdo->query($sql);
                                if ($result->rowCount() == 0) echo "<tr><td colspan='3' class='text-center text-muted py-3'>Chưa có dữ liệu</td></tr>";
                                foreach ($result as $i => $row) {
                                    $rank = $i + 1;
                                    $img_player = !empty($row['image']) ? $row['image'] : 'assets/img/default_player.png';
                                    $img_team = !empty($row['logo']) ? $row['logo'] : 'assets/img/default.png';
                                    echo "<tr>
                                            <td><span class='badge bg-light text-dark border'>{$rank}</span></td>
                                            <td>
                                                <div class='d-flex align-items-center'>
                                                    <img src='{$img_player}' width='30' height='30' style='object-fit: cover; border-radius: 50%;' class='me-2'>
                                                    <div class='fw-bold'>{$row['name']}</div>
                                                </div>
                                                <div class='small text-muted mt-1 ms-4'><img src='{$img_team}' width='15'> {$row['team_name']}</div>
                                            </td>
                                            <td class='text-center fw-bold text-danger fs-5'>{$row['count']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="text-warning fw-bold text-uppercase"><i class="fa-solid fa-square text-warning me-2"></i> Thẻ Vàng</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>#</th><th>Cầu thủ</th><th class="text-center">SL</th></tr></thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.name, p.image, t.name as team_name, t.logo, COUNT(c.id) as count 
                                        FROM cards c JOIN players p ON c.player_id = p.id 
                                        JOIN teams t ON p.team_id = t.id 
                                        WHERE c.card_type = 'Thẻ vàng'
                                        GROUP BY p.id ORDER BY count DESC LIMIT 5";
                                $result = $pdo->query($sql);
                                if ($result->rowCount() == 0) echo "<tr><td colspan='3' class='text-center text-muted py-3'>Chưa có thẻ vàng nào</td></tr>";
                                foreach ($result as $i => $row) {
                                    $rank = $i + 1;
                                    $img_player = !empty($row['image']) ? $row['image'] : 'assets/img/default_player.png';
                                    $img_team = !empty($row['logo']) ? $row['logo'] : 'assets/img/default.png';
                                    echo "<tr>
                                            <td><span class='badge bg-light text-dark border'>{$rank}</span></td>
                                            <td>
                                                <div class='d-flex align-items-center'>
                                                    <img src='{$img_player}' width='30' height='30' style='object-fit: cover; border-radius: 50%;' class='me-2'>
                                                    <div class='fw-bold'>{$row['name']}</div>
                                                </div>
                                                <div class='small text-muted mt-1 ms-4'><img src='{$img_team}' width='15'> {$row['team_name']}</div>
                                            </td>
                                            <td class='text-center fw-bold text-warning'>{$row['count']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom-0">
                        <h5 class="text-danger fw-bold text-uppercase"><i class="fa-solid fa-square text-danger me-2"></i> Thẻ Đỏ</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light"><tr><th>#</th><th>Cầu thủ</th><th class="text-center">SL</th></tr></thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.name, p.image, t.name as team_name, t.logo, COUNT(c.id) as count 
                                        FROM cards c JOIN players p ON c.player_id = p.id 
                                        JOIN teams t ON p.team_id = t.id 
                                        WHERE c.card_type = 'Thẻ đỏ'
                                        GROUP BY p.id ORDER BY count DESC LIMIT 5";
                                $result = $pdo->query($sql);
                                if ($result->rowCount() == 0) echo "<tr><td colspan='3' class='text-center text-muted py-3'>Chưa có thẻ đỏ nào (Fair-play!)</td></tr>";
                                foreach ($result as $i => $row) {
                                    $rank = $i + 1;
                                    $img_player = !empty($row['image']) ? $row['image'] : 'assets/img/default_player.png';
                                    $img_team = !empty($row['logo']) ? $row['logo'] : 'assets/img/default.png';
                                    echo "<tr>
                                            <td><span class='badge bg-light text-dark border'>{$rank}</span></td>
                                            <td>
                                                <div class='d-flex align-items-center'>
                                                    <img src='{$img_player}' width='30' height='30' style='object-fit: cover; border-radius: 50%;' class='me-2'>
                                                    <div class='fw-bold'>{$row['name']}</div>
                                                </div>
                                                <div class='small text-muted mt-1 ms-4'><img src='{$img_team}' width='15'> {$row['team_name']}</div>
                                            </td>
                                            <td class='text-center fw-bold text-danger'>{$row['count']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="tab-pane fade" id="club-stats" role="tabpanel">
        <div class="row">
            
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="fw-bold text-uppercase mb-0"><i class="fa-solid fa-futbol me-2"></i> Ghi Bàn Nhiều Nhất</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <?php
                                $sql = "SELECT t.name, t.logo, COUNT(g.id) as count 
                                        FROM goals g JOIN players p ON g.scored_by_id = p.id 
                                        JOIN teams t ON p.team_id = t.id 
                                        GROUP BY t.id ORDER BY count DESC LIMIT 5";
                                foreach ($pdo->query($sql) as $i => $row) {
                                    $rank = $i + 1;
                                    $img = !empty($row['logo']) ? $row['logo'] : 'assets/img/default.png';
                                    echo "<tr>
                                            <td style='width:30px'>{$rank}</td>
                                            <td>
                                                <img src='{$img}' width='30' class='me-2'> 
                                                <span class='fw-bold'>{$row['name']}</span>
                                            </td>
                                            <td class='text-center fw-bold text-success'>{$row['count']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="fw-bold text-uppercase mb-0"><i class="fa-solid fa-square me-2"></i> Nhiều Thẻ Vàng</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <?php
                                $sql = "SELECT t.name, t.logo, COUNT(c.id) as count 
                                        FROM cards c JOIN players p ON c.player_id = p.id 
                                        JOIN teams t ON p.team_id = t.id 
                                        WHERE c.card_type = 'Thẻ vàng'
                                        GROUP BY t.id ORDER BY count DESC LIMIT 5";
                                foreach ($pdo->query($sql) as $i => $row) {
                                    $rank = $i + 1;
                                    $img = !empty($row['logo']) ? $row['logo'] : 'assets/img/default.png';
                                    echo "<tr>
                                            <td style='width:30px'>{$rank}</td>
                                            <td>
                                                <img src='{$img}' width='30' class='me-2'> 
                                                <span class='fw-bold'>{$row['name']}</span>
                                            </td>
                                            <td class='text-center fw-bold text-warning'>{$row['count']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="fw-bold text-uppercase mb-0"><i class="fa-solid fa-square me-2"></i> Nhiều Thẻ Đỏ</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <?php
                                $sql = "SELECT t.name, t.logo, COUNT(c.id) as count 
                                        FROM cards c JOIN players p ON c.player_id = p.id 
                                        JOIN teams t ON p.team_id = t.id 
                                        WHERE c.card_type = 'Thẻ đỏ'
                                        GROUP BY t.id ORDER BY count DESC LIMIT 5";
                                foreach ($pdo->query($sql) as $i => $row) {
                                    $rank = $i + 1;
                                    $img = !empty($row['logo']) ? $row['logo'] : 'assets/img/default.png';
                                    echo "<tr>
                                            <td style='width:30px'>{$rank}</td>
                                            <td>
                                                <img src='{$img}' width='30' class='me-2'> 
                                                <span class='fw-bold'>{$row['name']}</span>
                                            </td>
                                            <td class='text-center fw-bold text-danger'>{$row['count']}</td>
                                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Khởi tạo Tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#statsTab button'))
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl)
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault()
            tabTrigger.show()
        })
    })
</script>

</body></html>