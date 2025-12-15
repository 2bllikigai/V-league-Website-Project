<?php 
require_once 'config.php'; 
require_once 'templates/header.php'; 

// Lấy Match ID từ URL
$match_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. TRUY VẤN THÔNG TIN TRẬN ĐẤU (Teams, Score, Highlight Link)
$sql_match = "SELECT m.*, 
                     t1.name as home_name, t1.logo as home_logo, 
                     t2.name as away_name, t2.logo as away_logo
              FROM matches m
              JOIN teams t1 ON m.home_team_id = t1.id
              JOIN teams t2 ON m.away_team_id = t2.id
              WHERE m.id = ?";
$stmt = $pdo->prepare($sql_match);
$stmt->execute([$match_id]);
$match = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$match || $match['finished'] == 0) {
    echo "<div class='alert alert-warning text-center my-5'>Trận đấu này chưa diễn ra hoặc không tồn tại!</div>";
    echo "<div class='text-center'><a href='index.php' class='btn btn-secondary'>Về trang chủ</a></div>";
    exit;
}

// 2. TRUY VẤN SỰ KIỆN (Goals và Cards)
// Lấy tất cả bàn thắng
$sql_goals = "SELECT g.minute, 'goal' as type, p.name as player_name, t.name as team_name
              FROM goals g
              JOIN players p ON g.scored_by_id = p.id
              JOIN teams t ON p.team_id = t.id
              WHERE g.match_id = ?";
$goals = $pdo->prepare($sql_goals);
$goals->execute([$match_id]);
$goal_events = $goals->fetchAll(PDO::FETCH_ASSOC);

// Lấy tất cả thẻ phạt
$sql_cards = "SELECT c.minute, c.card_type as type, p.name as player_name, t.name as team_name
              FROM cards c
              JOIN players p ON c.player_id = p.id
              JOIN teams t ON p.team_id = t.id
              WHERE c.match_id = ?";
$cards = $pdo->prepare($sql_cards);
$cards->execute([$match_id]);
$card_events = $cards->fetchAll(PDO::FETCH_ASSOC);

// Gộp sự kiện và sắp xếp theo thời gian
$timeline = array_merge($goal_events, $card_events);

// Hàm sắp xếp timeline theo phút
usort($timeline, function($a, $b) {
    return $a['minute'] - $b['minute'];
});

// Xử lý link Highlight (chuyển từ watch?v=... sang embed/...)
$video_url = '';
if (!empty($match['highlight_link'])) {
    $video_url = str_replace("watch?v=", "embed/", $match['highlight_link']);
    // Cắt thêm tham số thừa nếu có
    $video_url = explode('&', $video_url)[0];
}

?>

<div class="row mb-5">
    
    <div class="col-12 text-center mb-4">
        <h2 class="page-title text-uppercase mb-1">Vòng <?php echo $match['round']; ?> - Đã kết thúc</h2>
        <h1 class="display-3 fw-black" style="color: var(--pl-purple);">
            <?php echo $match['home_score']; ?> - <?php echo $match['away_score']; ?>
        </h1>
        <p class="text-muted small"><?php echo date("H:i, d/m/Y", strtotime($match['match_date'])); ?></p>
    </div>

    <div class="col-lg-7">
        <h4 class="fw-bold text-uppercase mb-3" style="color: var(--pl-purple);">
            <i class="fa-solid fa-video me-2"></i> Highlight Trận Đấu
        </h4>
        
        <?php if ($video_url): ?>
            <div class="ratio ratio-16x9 shadow-lg rounded-3">
                <iframe src="<?php echo $video_url; ?>" title="Highlight" frameborder="0" allowfullscreen></iframe>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center py-5">Chưa có video highlight chính thức.</div>
        <?php endif; ?>
    </div>

    <div class="col-lg-5">
        <h4 class="fw-bold text-uppercase mb-3" style="color: var(--pl-purple);">
            <i class="fa-solid fa-list-check me-2"></i> Diễn Biến Trận Đấu
        </h4>
        
        <div class="card shadow-sm border-0 h-100">
            <div class="list-group list-group-flush timeline-list">
                <?php if (empty($timeline)): ?>
                    <div class="p-4 text-center text-muted">Không có sự kiện chính thức nào được ghi nhận.</div>
                <?php else: ?>
                    <?php foreach ($timeline as $event):
                        $minute = $event['minute'];
                        $player_name = $event['player_name'];
                        $team_name = $event['team_name'];
                        $type = $event['type'];
                        
                        // Xử lý hiển thị theo loại sự kiện
                        if ($type == 'goal') {
                            $icon = '<i class="fa-solid fa-futbol text-success"></i>';
                            $text_class = 'text-success fw-bold';
                            $event_text = "$player_name ($team_name) đã ghi bàn!";
                        } elseif ($type == 'Thẻ vàng') {
                            $icon = '<i class="fa-solid fa-square text-warning"></i>';
                            $text_class = 'text-warning';
                            $event_text = "$player_name ($team_name) nhận Thẻ Vàng.";
                        } elseif ($type == 'Thẻ đỏ') {
                            $icon = '<i class="fa-solid fa-square text-danger"></i>';
                            $text_class = 'text-danger fw-bold';
                            $event_text = "$player_name ($team_name) nhận THẺ ĐỎ!";
                        }
                    ?>
                    <div class="list-group-item d-flex align-items-center py-3">
                        <span class="badge bg-dark me-3"><?php echo $minute; ?>'</span>
                        <span class="<?php echo $text_class; ?> me-2"><?php echo $icon; ?></span>
                        <span><?php echo $event_text; ?></span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="text-center mb-5">
    <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="fa-solid fa-arrow-left me-2"></i> Về trang kết quả
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php 
// (Nếu bạn có file footer.php thì cần include nó, nếu không thì đóng thủ công)
echo '</div></body></html>'; 
?>