<?php 
require_once '../templates/admin_header.php'; 

$status = $_GET['status'] ?? null;
?>

<h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
    Quản lý Câu Lạc Bộ
</h3>

<?php if ($status == 'added'): ?><div class="alert alert-success alert-dismissible fade show" role="alert">Thêm CLB thành công!</div>
<?php elseif ($status == 'updated'): ?><div class="alert alert-success alert-dismissible fade show" role="alert">Cập nhật CLB thành công!</div>
<?php elseif ($status == 'deleted'): ?><div class="alert alert-danger alert-dismissible fade show" role="alert">Đã xóa CLB thành công!</div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <a href="#" class="btn btn-success mb-3 float-end" data-bs-toggle="modal" data-bs-target="#clubModal">
            <i class="fa-solid fa-plus me-2"></i> Thêm CLB mới
        </a>
        <div class="clearfix"></div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="table-light">
                        <th>#</th>
                        <th>Logo</th>
                        <th>Tên CLB</th>
                        <th>Sân nhà</th>
                        <th>HLV</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $clubs = $pdo->query("SELECT * FROM teams ORDER BY name ASC")->fetchAll();
                    foreach ($clubs as $c):
                        $logo = !empty($c['logo']) ? $c['logo'] : 'assets/img/default.png';
                    ?>
                    <tr>
                        <td><?php echo $c['id']; ?></td>
                        <td><img src="../<?php echo $logo; ?>" width="35" height="35" style="object-fit: contain;"></td>
                        <td><?php echo $c['name']; ?></td>
                        <td><?php echo $c['stadium']; ?></td>
                        <td><?php echo !empty($c['coach']) ? $c['coach'] : '---'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2 btn-edit" data-bs-toggle="modal" data-bs-target="#clubModal" 
                                data-id="<?php echo $c['id']; ?>" 
                                data-name="<?php echo $c['name']; ?>" 
                                data-stadium="<?php echo $c['stadium']; ?>"
                                data-coach="<?php echo $c['coach']; ?>"
                                data-founded="<?php echo $c['founded_year']; ?>"
                                data-desc="<?php echo htmlspecialchars($c['description']); ?>">
                                <i class="fa-solid fa-edit"></i> Sửa
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('CẢNH BÁO: Xóa CLB sẽ xóa HẾT CẦU THỦ, TRẬN ĐẤU, BÀN THẮNG liên quan. Bạn có chắc chắn muốn xóa?')) { document.getElementById('delete-form-<?php echo $c['id']; ?>').submit(); }">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </button>
                            <form id="delete-form-<?php echo $c['id']; ?>" action="process_club.php" method="POST" style="display:none;">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
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

<div class="modal fade" id="clubModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clubModalLabel">Thêm/Sửa Câu Lạc Bộ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_club.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="club-id">
                    <div class="mb-3">
                        <label class="form-label">Tên CLB</label>
                        <input type="text" name="name" id="club-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sân nhà</label>
                        <input type="text" name="stadium" id="club-stadium" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">HLV Trưởng</label>
                        <input type="text" name="coach" id="club-coach" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Năm thành lập</label>
                        <input type="number" name="founded_year" id="club-founded" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mô tả/Giới thiệu</label>
                        <textarea name="description" id="club-desc" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo (Chọn file mới)</label>
                        <input type="file" name="logo_file" class="form-control">
                        <small class="text-muted">Chỉ cần chọn file nếu bạn muốn thay logo.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Xử lý điền dữ liệu vào Modal khi nhấn nút "Sửa"
    document.addEventListener('DOMContentLoaded', function() {
        var clubModal = document.getElementById('clubModal');
        clubModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var modal = this;

            modal.querySelector('form').reset();
            modal.querySelector('#club-id').value = '';
            modal.querySelector('.modal-title').textContent = 'Thêm CLB mới';

            if (button.dataset.id) {
                // Sửa
                modal.querySelector('.modal-title').textContent = 'Sửa thông tin CLB';
                modal.querySelector('#club-id').value = button.dataset.id;
                modal.querySelector('#club-name').value = button.dataset.name;
                modal.querySelector('#club-stadium').value = button.dataset.stadium;
                modal.querySelector('#club-coach').value = button.dataset.coach;
                modal.querySelector('#club-founded').value = button.dataset.founded;
                modal.querySelector('#club-desc').value = button.dataset.desc;
            }
        });
    });
</script>

<?php 
echo '</div></body></html>'; 
?>