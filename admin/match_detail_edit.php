<?php 
require_once '../templates/admin_header.php'; 

$match_id = $_GET['id'] ?? 0;
$status = $_GET['status'] ?? null;

// Lấy thông tin trận đấu
$sql_match = "SELECT m.*, t1.name as h_name, t1.logo as h_logo, t2.name as a_name, t2.logo as a_logo
              FROM matches m JOIN teams t1 ON m.home_team_id = t1.id JOIN teams t2 ON m.away_team_id = t2.id
              WHERE m.id = ?";
$stmt = $pdo->prepare($sql_match);
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match) {
    echo "<div class='alert alert-danger text-center my-5'>Không tìm thấy trận đấu này!</div>";
    echo "<div class='text-center'><a href='matches.php' class='btn btn-secondary'>Về trang quản lý</a></div>";
    echo '</div></body></html>'; exit;
}

// Lấy danh sách cầu thủ của 2 đội để dùng cho dropdown
$players = $pdo->query("SELECT id, name, team_id, position FROM players WHERE team_id IN ({$match['home_team_id']}, {$match['away_team_id']}) ORDER BY team_id, position DESC")->fetchAll();

// Lấy sự kiện timeline
$sql_goals = "SELECT g.id, g.minute, 'goal' as type, p.name as player_name, t.name as team_name, t.id as team_id 
              FROM goals g JOIN players p ON g.scored_by_id = p.id JOIN teams t ON p.team_id = t.id WHERE g.match_id = $match_id";
$sql_cards = "SELECT c.id, c.minute, c.card_type as type, p.name as player_name, t.name as team_name, t.id as team_id 
              FROM cards c JOIN players p ON c.player_id = p.id JOIN teams t ON p.team_id = t.id WHERE c.match_id = $match_id";

$goal_events = $pdo->query($sql_goals)->fetchAll(PDO::FETCH_ASSOC);
$card_events = $pdo->query($sql_cards)->fetchAll(PDO::FETCH_ASSOC);
$timeline = array_merge($goal_events, $card_events);

usort($timeline, function($a, $b) { return $a['minute'] - $b['minute']; });

?>

<h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
    Cập Nhật Chi Tiết Trận Đấu Vòng <?php echo $match['round']; ?>
</h3>
<p class="fs-5 fw-bold text-dark"><?php echo $match['h_name']; ?> vs <?php echo $match['a_name']; ?> (<?php echo date("d/m/Y H:i", strtotime($match['match_date'])); ?>)</p>

<?php if ($status): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        Cập nhật thành công! (Tỷ số/Highlight/Sự kiện)
    </div>
<?php endif; ?>

<div class="row g-4">
    
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header fw-bold bg-light">
                Cập nhật Tỷ số Chung cuộc & Trạng thái
            </div>
            <div class="card-body">
                <form action="process_match_events.php" method="POST">
                    <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                    <input type="hidden" name="action" value="update_score">

                    <div class="row mb-3">
                        <div class="col-5">
                            <label class="form-label">Tỷ số Đội nhà (<?php echo $match['h_name']; ?>)</label>
                            <input type="number" name="home_score" class="form-control" value="<?php echo $match['home_score']; ?>" required>
                        </div>
                         <div class="col-5">
                            <label class="form-label">Tỷ số Đội khách (<?php echo $match['a_name']; ?>)</label>
                            <input type="number" name="away_score" class="form-control" value="<?php echo $match['away_score']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link Highlight (Youtube URL)</label>
                        <input type="url" name="highlight_link" class="form-control" value="<?php echo htmlspecialchars($match['highlight_link']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Trạng thái trận đấu</label>
                        <select name="finished" class="form-select">
                            <option value="1" <?php if($match['finished'] == 1) echo 'selected'; ?>>1 - Đã xong</option>
                            <option value="0" <?php if($match['finished'] == 0) echo 'selected'; ?>>0 - Sắp diễn ra</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Lưu Tỷ số & Trạng thái</button>
                </form>
            </div>
        </div>

        <button class="btn btn-sm btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addGoalModal">
            <i class="fa-solid fa-futbol me-2"></i> Thêm Bàn Thắng
        </button>
        <button class="btn btn-sm btn-warning w-100" data-bs-toggle="modal" data-bs-target="#addCardModal">
            <i class="fa-solid fa-square me-2"></i> Thêm Thẻ Phạt
        </button>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
             <div class="card-header fw-bold bg-light">
                Diễn biến (Goals, Cards)
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($timeline)): ?>
                    <div class="p-4 text-center text-muted">Chưa có sự kiện nào được ghi nhận.</div>
                <?php else: ?>
                    <?php foreach ($timeline as $event):
                        $event_id = $event['id'];
                        $minute = $event['minute'];
                        $player_name = $event['player_name'];
                        $team_name = $event['team_name'];
                        $type = $event['type'];
                        $is_goal = ($type == 'goal');
                        
                        // Icon và màu sắc
                        if ($is_goal) {
                            $icon = 'fa-futbol text-success'; $type_text = 'Bàn thắng'; $event_type = 'goal';
                        } elseif ($type == 'Thẻ vàng') {
                            $icon = 'fa-square text-warning'; $type_text = 'Thẻ vàng'; $event_type = 'card';
                        } else {
                            $icon = 'fa-square text-danger'; $type_text = 'THẺ ĐỎ'; $event_type = 'card';
                        }
                    ?>
                    <div class="list-group-item d-flex align-items-center justify-content-between py-3">
                        <div class="d-flex align-items-center">
                             <span class="badge bg-dark me-3"><?php echo $minute; ?>'</span>
                             <i class="fa-solid <?php echo $icon; ?> me-2"></i>
                            
                            <span class="fw-bold me-1 <?php echo $is_goal ? 'text-success' : 'text-danger'; ?>">
                                <?php echo $type_text; ?>:
                            </span>
                            
                            <span><?php echo $player_name; ?> (<?php echo $team_name; ?>)</span>
                        </div>

                        <form action="process_match_events.php" method="POST" style="display:inline;">
                            <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                            <input type="hidden" name="event_type" value="<?php echo $event_type; ?>">
                            <input type="hidden" name="action" value="delete_event">
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa sự kiện này?');">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-3 text-end">
             <a href="matches.php" class="btn btn-sm btn-outline-secondary">Về trang quản lý</a>
        </div>
    </div>
</div>

<div class="modal fade" id="addGoalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Bàn Thắng</h5>
            </div>
            <form action="process_match_events.php" method="POST">
                <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                <input type="hidden" name="action" value="add_goal">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cầu thủ Ghi bàn</label>
                        <select name="player_id" class="form-select" required>
                            <option value="">-- Chọn cầu thủ --</option>
                            <?php foreach ($players as $p): ?>
                                <option value="<?php echo $p['id']; ?>">[<?php echo $p['number']; ?>] <?php echo $p['name']; ?> (<?php echo ($p['team_id'] == $match['home_team_id']) ? $match['h_name'] : $match['a_name']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phút Ghi bàn</label>
                        <input type="number" name="minute" class="form-control" min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Lưu Bàn thắng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Thẻ Phạt</h5>
            </div>
            <form action="process_match_events.php" method="POST">
                <input type="hidden" name="match_id" value="<?php echo $match_id; ?>">
                <input type="hidden" name="action" value="add_card">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cầu thủ nhận Thẻ</label>
                        <select name="player_id" class="form-select" required>
                            <option value="">-- Chọn cầu thủ --</option>
                            <?php foreach ($players as $p): ?>
                                <option value="<?php echo $p['id']; ?>">[<?php echo $p['number']; ?>] <?php echo $p['name']; ?> (<?php echo ($p['team_id'] == $match['home_team_id']) ? $match['h_name'] : $match['a_name']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Loại Thẻ</label>
                        <select name="card_type" class="form-select" required>
                            <option value="Thẻ vàng">Thẻ vàng</option>
                            <option value="Thẻ đỏ">Thẻ đỏ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phút</label>
                        <input type="number" name="minute" class="form-control" min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Lưu Thẻ Phạt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php 
echo '</div></body></html>'; 
?>