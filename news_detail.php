<?php 
require_once 'config.php'; require_once 'templates/header.php'; 
$id = $_GET['id'] ?? 0;
$news = $pdo->query("SELECT * FROM news WHERE id = $id")->fetch();
if (!$news) die("Tin không tồn tại");
?>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-3"><?php echo $news['title']; ?></h1>
            <p class="text-muted mb-4"><i class="fa-regular fa-clock"></i> <?php echo date("H:i d/m/Y", strtotime($news['created_at'])); ?></p>
            <img src="<?php echo $news['image_url']; ?>" class="img-fluid rounded mb-4 w-100">
            <div class="content fs-5">
                <?php echo nl2br($news['content']); ?>
            </div>
            <div class="mt-5">
                <a href="index.php" class="btn btn-secondary">&larr; Quay lại trang chủ</a>
            </div>
        </div>
    </div>
</div>
</body></html>