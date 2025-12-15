<?php 
require_once '../templates/admin_header.php'; 

// --- XỬ LÝ LỌC VÀ TÌM KIẾM ---
$search = $_GET['search'] ?? '';
$team_id = $_GET['team_id'] ?? '';
$status = $_GET['status'] ?? null;

// Lấy danh sách đội cho dropdown
$all_teams = $pdo->query("SELECT id, name FROM teams ORDER BY name ASC")->fetchAll();
?>

<h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
    Quản lý Cầu Thủ
</h3>

<?php if ($status == 'added'): ?><div class="alert alert-success alert-dismissible fade show">Thêm Cầu thủ thành công!</div>
<?php elseif ($status == 'deleted'): ?><div class="alert alert-danger alert-dismissible fade show">Đã xóa Cầu thủ thành công!</div>
<?php elseif ($status == 'updated'): ?><div class="alert alert-success alert-dismissible fade show">Cập nhật Cầu thủ thành công!</div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        
        <form method="GET" action="" class="row g-3 align-items-center mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo Tên cầu thủ..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-4">
                <select name="team_id" class="form-select">
                    <option value="">-- Tất cả CLB --</option>
                    <?php foreach ($all_teams as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php if($team_id == $t['id']) echo 'selected'; ?>>
                            <?php echo $t['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass"></i> Tìm</button>
            </div>
             <div class="col-md-2">
                <button type="button" class="btn btn-success float-end w-100" data-bs-toggle="modal" data-bs-target="#playerModal">
                    <i class="fa-solid fa-plus"></i> Thêm
                </button>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="table-light">
                        <th>Số áo</th>
                        <th>Ảnh</th>
                        <th>Tên Cầu thủ</th>
                        <th>Vị trí</th>
                        <th>CLB</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT p.*, t.name as team_name, t.logo as team_logo
                            FROM players p JOIN teams t ON p.team_id = t.id 
                            WHERE 1=1";
                    $params = [];
                    if (!empty($search)) { $sql .= " AND p.name LIKE ?"; $params[] = "%$search%"; }
                    if (!empty($team_id)) { $sql .= " AND p.team_id = ?"; $params[] = $team_id; }
                    $sql .= " ORDER BY p.team_id, p.number ASC";
                    
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);
                    $players = $stmt->fetchAll();
                    
                    foreach ($players as $p):
                        $img = !empty($p['image']) ? $p['image'] : 'assets/img/default_player.png';
                        $t_logo = !empty($p['team_logo']) ? $p['team_logo'] : 'assets/img/default.png';
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $p['number']; ?></td>
                        <td><img src="../<?php echo $img; ?>" width="40" height="40" style="object-fit: cover; border-radius: 50%;"></td>
                        <td class="fw-bold"><?php echo $p['name']; ?></td>
                        <td><?php echo $p['position']; ?></td>
                        <td><img src="../<?php echo $t_logo; ?>" width="20" class="me-2"><?php echo $p['team_name']; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#playerModal" 
                                data-id="<?php echo $p['id']; ?>" 
                                data-name="<?php echo $p['name']; ?>" 
                                data-number="<?php echo $p['number']; ?>"
                                data-position="<?php echo $p['position']; ?>"
                                data-team-id="<?php echo $p['team_id']; ?>">
                                <i class="fa-solid fa-edit"></i> Sửa
                            </button>
                            
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Bạn có chắc chắn muốn xóa <?php echo $p['name']; ?>?')) { document.getElementById('delete-form-p-<?php echo $p['id']; ?>').submit(); }">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </button>
                            <form id="delete-form-p-<?php echo $p['id']; ?>" action="process_player.php" method="POST" style="display:none;">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
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

<div class="modal fade" id="playerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="playerModalLabel">Thêm Cầu Thủ Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_player.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="p-id"> <div class="mb-3">
                        <label class="form-label">Tên Cầu thủ</label>
                        <input type="text" name="name" id="p-name" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số áo</label>
                            <input type="number" name="number" id="p-number" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vị trí</label>
                            <select name="position" id="p-position" class="form-select" required>
                                <option value="GK">GK (Thủ môn)</option>
                                <option value="DF">DF (Hậu vệ)</option>
                                <option value="MF">MF (Tiền vệ)</option>
                                <option value="FW">FW (Tiền đạo)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Câu lạc bộ</label>
                        <select name="team_id" id="p-team" class="form-select" required>
                            <option value="">-- Chọn CLB --</option>
                            <?php foreach ($all_teams as $t): ?>
                                <option value="<?php echo $t['id']; ?>"><?php echo $t['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ảnh đại diện</label>
                        <input type="file" name="image_file" class="form-control">
                        <small class="text-muted">Chỉ chọn nếu muốn thay đổi ảnh.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Lưu dữ liệu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JS xử lý đổ dữ liệu vào Modal
    document.addEventListener('DOMContentLoaded', function() {
        var playerModal = document.getElementById('playerModal');
        playerModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var modal = this;
            
            // Reset form mặc định
            modal.querySelector('form').reset();
            modal.querySelector('#p-id').value = '';
            modal.querySelector('.modal-title').textContent = 'Thêm Cầu Thủ Mới';

            // Nếu nút bấm có data-id => Là hành động Sửa
            if (button.dataset.id) {
                modal.querySelector('.modal-title').textContent = 'Sửa Thông Tin Cầu Thủ';
                modal.querySelector('#p-id').value = button.dataset.id;
                modal.querySelector('#p-name').value = button.dataset.name;
                modal.querySelector('#p-number').value = button.dataset.number;
                modal.querySelector('#p-position').value = button.dataset.position;
                modal.querySelector('#p-team').value = button.dataset.teamId;
            }
        });
    });
</script>
<?php echo '</div></body></html>'; ?>