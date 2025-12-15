<?php 
require_once '../templates/admin_header.php'; 

$team_id = $_GET['team_id'] ?? '';
$round = $_GET['round'] ?? '';
$status = $_GET['status'] ?? null;

// Data dropdowns
$all_teams = $pdo->query("SELECT id, name FROM teams ORDER BY name ASC")->fetchAll();
$all_rounds = $pdo->query("SELECT DISTINCT round FROM matches ORDER BY round ASC")->fetchAll();
?>

<h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
    Quản lý Trận Đấu
</h3>

<?php if ($status == 'added'): ?><div class="alert alert-success alert-dismissible fade show">Thêm Lịch thi đấu thành công!</div>
<?php elseif ($status == 'updated_basic'): ?><div class="alert alert-success alert-dismissible fade show">Sửa thông tin lịch đấu thành công!</div>
<?php elseif ($status == 'deleted'): ?><div class="alert alert-danger alert-dismissible fade show">Xóa trận đấu thành công!</div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        
        <form method="GET" action="" class="row g-3 align-items-center mb-4">
            <div class="col-md-4">
                <select name="team_id" class="form-select">
                    <option value="">-- Lọc theo CLB --</option>
                    <?php foreach ($all_teams as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php if($team_id == $t['id']) echo 'selected'; ?>><?php echo $t['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="round" class="form-select">
                    <option value="">-- Lọc theo Vòng --</option>
                    <?php foreach ($all_rounds as $r): ?>
                        <option value="<?php echo $r['round']; ?>" <?php if($round == $r['round']) echo 'selected'; ?>>Vòng <?php echo $r['round']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter"></i> Lọc</button>
            </div>
             <div class="col-md-3">
                <button type="button" class="btn btn-success float-end w-100" data-bs-toggle="modal" data-bs-target="#matchModal">
                    <i class="fa-solid fa-plus"></i> Thêm Lịch Mới
                </button>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead>
                    <tr class="table-light">
                        <th>Vòng</th>
                        <th>Trận đấu (Chủ vs Khách)</th>
                        <th>Thời gian</th>
                        <th>Tỷ số</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT m.*, t1.name as h_name, t2.name as a_name
                            FROM matches m 
                            JOIN teams t1 ON m.home_team_id = t1.id
                            JOIN teams t2 ON m.away_team_id = t2.id
                            WHERE 1=1";
                    $params = [];
                    if (!empty($team_id)) { $sql .= " AND (m.home_team_id = ? OR m.away_team_id = ?)"; $params[] = $team_id; $params[] = $team_id; }
                    if (!empty($round)) { $sql .= " AND m.round = ?"; $params[] = $round; }
                    $sql .= " ORDER BY m.finished ASC, m.match_date DESC";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $matches = $stmt->fetchAll();
                    
                    foreach ($matches as $m):
                        $status_txt = $m['finished'] ? 'Đã xong' : 'Sắp đấu';
                        $status_cls = $m['finished'] ? 'bg-secondary' : 'bg-warning text-dark';
                        $score_display = $m['finished'] ? "{$m['home_score']} - {$m['away_score']}" : "--";
                        // Format date cho input datetime-local của HTML5 (Y-m-d\TH:i)
                        $date_for_input = date("Y-m-d\TH:i", strtotime($m['match_date'])); 
                    ?>
                    <tr>
                        <td><?php echo $m['round']; ?></td>
                        <td class="fw-bold text-primary">
                            <?php echo $m['h_name']; ?> <span class="text-muted small">vs</span> <?php echo $m['a_name']; ?>
                        </td>
                        <td><?php echo date("H:i d/m", strtotime($m['match_date'])); ?></td>
                        <td class="fw-bold fs-5"><?php echo $score_display; ?></td>
                        <td><span class="badge <?php echo $status_cls; ?>"><?php echo $status_txt; ?></span></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary me-1" data-bs-toggle="modal" data-bs-target="#matchModal"
                                data-id="<?php echo $m['id']; ?>"
                                data-round="<?php echo $m['round']; ?>"
                                data-home="<?php echo $m['home_team_id']; ?>"
                                data-away="<?php echo $m['away_team_id']; ?>"
                                data-date="<?php echo $date_for_input; ?>">
                                <i class="fa-solid fa-calendar-days"></i> Sửa Lịch
                            </button>
                            
                            <a href="match_detail_edit.php?id=<?php echo $m['id']; ?>" class="btn btn-sm btn-primary me-1">
                                <i class="fa-solid fa-futbol"></i> Tỷ số
                            </a>
                            
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Xóa trận này?')) { document.getElementById('del-m-<?php echo $m['id']; ?>').submit(); }">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <form id="del-m-<?php echo $m['id']; ?>" action="process_match.php" method="POST" style="display:none;">
                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="matchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lên Lịch Thi Đấu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_match.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id" id="m-id">
                    <input type="hidden" name="action" value="save_basic"> <div class="mb-3">
                        <label class="form-label">Vòng đấu</label>
                        <input type="number" name="round" id="m-round" class="form-control" required min="1" max="26">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đội Nhà</label>
                            <select name="home_team_id" id="m-home" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <?php foreach ($all_teams as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đội Khách</label>
                            <select name="away_team_id" id="m-away" class="form-select" required>
                                <option value="">-- Chọn --</option>
                                <?php foreach ($all_teams as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày giờ đá</label>
                        <input type="datetime-local" name="match_date" id="m-date" class="form-control" required>
                    </div>
                    
                    <div class="alert alert-info small">
                        <i class="fa-solid fa-circle-info"></i> Sau khi tạo lịch, hãy dùng nút <b>"Tỷ số"</b> ở ngoài danh sách để cập nhật kết quả, thẻ phạt và highlight.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Lưu Lịch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var matchModal = document.getElementById('matchModal');
        matchModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var modal = this;
            
            modal.querySelector('form').reset();
            modal.querySelector('#m-id').value = '';
            modal.querySelector('.modal-title').textContent = 'Thêm Lịch Thi Đấu';

            if (button.dataset.id) {
                modal.querySelector('.modal-title').textContent = 'Sửa Lịch Đấu';
                modal.querySelector('#m-id').value = button.dataset.id;
                modal.querySelector('#m-round').value = button.dataset.round;
                modal.querySelector('#m-home').value = button.dataset.home;
                modal.querySelector('#m-away').value = button.dataset.away;
                modal.querySelector('#m-date').value = button.dataset.date;
            }
        });
    });
</script>
<?php echo '</div></body></html>'; ?>