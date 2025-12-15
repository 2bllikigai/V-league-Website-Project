<?php 
require_once 'config.php'; require_once 'templates/header.php'; 

// 1. LẤY ID CẦU THỦ
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// 2. TRUY VẤN THÔNG TIN CẦU THỦ + ĐỘI BÓNG
$sql = "SELECT p.*, t.name as team_name, t.logo as team_logo, t.id as team_id
        FROM players p
        JOIN teams t ON p.team_id = t.id
        WHERE p.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    echo "<div class='alert alert-danger text-center my-5'>Không tìm thấy cầu thủ!</div>";
    require_once 'templates/footer.php'; exit;
}

// Xử lý ảnh
$p_img = !empty($p['image']) ? $p['image'] : 'assets/img/default_player.png';
$t_img = !empty($p['team_logo']) ? $p['team_logo'] : 'assets/img/default.png';

// 3. TRUY VẤN THỐNG KÊ CÁ NHÂN
// Đếm bàn thắng
$goals = $pdo->query("SELECT COUNT(*) FROM goals WHERE scored_by_id = $id")->fetchColumn();
// Đếm kiến tạo (Nếu có bảng assist, tạm thời để 0 hoặc query nếu bạn đã làm)
$assists = $pdo->query("SELECT COUNT(*) FROM goals WHERE assist_by_id = $id")->fetchColumn(); // (Cần bảng goals có cột assist_by_id, nếu chưa có thì nó trả về 0 hoặc lỗi, bạn có thể bỏ dòng này nếu chưa làm cột assist)
// Đếm thẻ
$yellow = $pdo->query("SELECT COUNT(*) FROM cards WHERE player_id = $id AND card_type = 'Thẻ vàng'")->fetchColumn();
$red = $pdo->query("SELECT COUNT(*) FROM cards WHERE player_id = $id AND card_type = 'Thẻ đỏ'")->fetchColumn();

// Lấy 5 trận gần nhất có tham gia (ghi bàn hoặc thẻ - Logic đơn giản hóa)
// Hoặc lấy các trận đấu của đội mình
?>

<div class="card border-0 shadow-sm mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, var(--pl-purple) 0%, #2a002e 100%); color: white;">
    <div class="position-absolute top-0 end-0 h-100 w-50 d-none d-md-block" style="background: url('<?php echo $t_img; ?>') no-repeat center right; background-size: contain; opacity: 0.05; margin-right: -50px;"></div>

    <div class="card-body p-0">
        <div class="container">
            <div class="row align-items-center">
                
                <div class="col-md-4 col-lg-3 text-center p-0">
                    <div class="player-profile-img-container">
                        <img src="<?php echo $p_img; ?>" alt="<?php echo $p['name']; ?>" class="player-profile-img">
                    </div>
                </div>

                <div class="col-md-8 col-lg-9 py-4 px-4 position-relative">
                    
                    <div class="display-1 fw-black text-white opacity-25 position-absolute top-0 end-0 me-4 d-none d-sm-block" style="font-family: 'Poppins'; letter-spacing: -5px;">
                        <?php echo $p['number']; ?>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <a href="team_detail.php?id=<?php echo $p['team_id']; ?>" class="text-decoration-none text-white d-flex align-items-center badge bg-white bg-opacity-25 border border-white border-opacity-25 px-3 py-2 rounded-pill">
                            <img src="<?php echo $t_img; ?>" width="20" class="me-2">
                            <?php echo $p['team_name']; ?>
                        </a>
                    </div>

                    <h1 class="display-5 fw-black text-uppercase mb-1"><?php echo $p['name']; ?></h1>
                    <h4 class="text-uppercase fw-light" style="color: var(--pl-green);"><?php echo $p['position']; ?></h4>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-5">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-4 card-stat">
            <div class="fs-1 fw-black text-danger mb-1"><?php echo $goals; ?></div>
            <div class="text-muted small text-uppercase fw-bold">Bàn thắng</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-4 card-stat">
            <div class="fs-1 fw-black text-warning mb-1"><?php echo $yellow; ?></div>
            <div class="text-muted small text-uppercase fw-bold">Thẻ vàng</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-4 card-stat">
            <div class="fs-1 fw-black text-danger mb-1"><?php echo $red; ?></div>
            <div class="text-muted small text-uppercase fw-bold">Thẻ đỏ</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 text-center py-4 card-stat">
            <div class="fs-1 fw-black text-primary mb-1"><?php echo $p['number']; ?></div>
            <div class="text-muted small text-uppercase fw-bold">Số áo</div>
        </div>
    </div>
</div>

<div class="text-center mb-5">
    <a href="team_detail.php?id=<?php echo $p['team_id']; ?>" class="btn btn-outline-secondary rounded-pill px-4">
        <i class="fa-solid fa-arrow-left me-2"></i> Trở về CLB <?php echo $p['team_name']; ?>
    </a>
</div>

<style>
    .player-profile-img-container {
        height: 300px;
        background: rgba(255,255,255,0.1); /* Nền mờ sau lưng cầu thủ */
        display: flex;
        align-items: flex-end;
        justify-content: center;
        overflow: hidden;
        border-bottom: 4px solid var(--pl-green);
    }

    .player-profile-img {
        height: 95%;
        width: auto;
        object-fit: contain;
        filter: drop-shadow(0 10px 10px rgba(0,0,0,0.5)); /* Bóng đổ cho người thật hơn */
    }

    .card-stat {
        transition: transform 0.2s;
    }
    .card-stat:hover {
        transform: translateY(-5px);
    }
    
    /* Mobile */
    @media (max-width: 768px) {
        .player-profile-img-container { height: 250px; }
        .display-5 { font-size: 2rem; }
    }
</style>

</body></html>