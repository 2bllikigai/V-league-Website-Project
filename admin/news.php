<?php 
require_once '../templates/admin_header.php'; 
$status = $_GET['status'] ?? null;
?>

<h3 class="fw-bold text-uppercase mb-4 pb-2 border-bottom" style="color: var(--pl-purple);">
    Quản lý Tin Tức
</h3>

<?php if ($status == 'added'): ?><div class="alert alert-success alert-dismissible fade show">Thêm Tin thành công!</div>
<?php elseif ($status == 'updated'): ?><div class="alert alert-success alert-dismissible fade show">Cập nhật Tin thành công!</div>
<?php elseif ($status == 'deleted'): ?><div class="alert alert-danger alert-dismissible fade show">Xóa Tin thành công!</div>
<?php endif; ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <button type="button" class="btn btn-success mb-3 float-end" data-bs-toggle="modal" data-bs-target="#newsModal">
            <i class="fa-solid fa-plus me-2"></i> Thêm Tin mới
        </button>
        <div class="clearfix"></div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="table-light">
                        <th>#</th>
                        <th>Hình ảnh</th>
                        <th>Tiêu đề</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $news = $pdo->query("SELECT * FROM news ORDER BY created_at DESC")->fetchAll();
                    foreach ($news as $n):
                    ?>
                    <tr>
                        <td><?php echo $n['id']; ?></td>
                        <td><img src="<?php echo $n['image_url']; ?>" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;"></td>
                        <td class="fw-bold" style="max-width: 350px;"><?php echo $n['title']; ?></td>
                        <td><?php echo date("d/m/Y", strtotime($n['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#newsModal" 
                                data-id="<?php echo $n['id']; ?>"
                                data-title="<?php echo htmlspecialchars($n['title']); ?>"
                                data-summary="<?php echo htmlspecialchars($n['summary']); ?>"
                                data-content="<?php echo htmlspecialchars($n['content']); ?>"
                                data-image="<?php echo $n['image_url']; ?>">
                                <i class="fa-solid fa-edit"></i> Sửa
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="if(confirm('Xóa tin này?')) { document.getElementById('del-news-<?php echo $n['id']; ?>').submit(); }">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </button>
                            <form id="del-news-<?php echo $n['id']; ?>" action="process_news.php" method="POST" style="display:none;">
                                <input type="hidden" name="id" value="<?php echo $n['id']; ?>">
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

<div class="modal fade" id="newsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Tin Tức Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_news.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="n-id">
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề</label>
                        <input type="text" name="title" id="n-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tóm tắt</label>
                        <textarea name="summary" id="n-summary" class="form-control" rows="2" required></textarea>
                    </div>
                     <div class="mb-3">
                        <label class="form-label">Nội dung chi tiết</label>
                        <textarea name="content" id="n-content" class="form-control" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Ảnh / URL</label>
                        <input type="text" name="image_url" id="n-image" class="form-control" placeholder="http://...">
                        <div class="mt-2 text-muted small">Hoặc upload file (chưa xử lý trong demo này, ưu tiên dùng link)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Lưu Tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var newsModal = document.getElementById('newsModal');
        newsModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var modal = this;
            
            modal.querySelector('form').reset();
            modal.querySelector('#n-id').value = '';
            modal.querySelector('.modal-title').textContent = 'Thêm Tin Tức Mới';

            if (button.dataset.id) {
                modal.querySelector('.modal-title').textContent = 'Sửa Tin Tức';
                modal.querySelector('#n-id').value = button.dataset.id;
                modal.querySelector('#n-title').value = button.dataset.title;
                modal.querySelector('#n-summary').value = button.dataset.summary;
                modal.querySelector('#n-content').value = button.dataset.content;
                modal.querySelector('#n-image').value = button.dataset.image;
            }
        });
    });
</script>
<?php echo '</div></body></html>'; ?>