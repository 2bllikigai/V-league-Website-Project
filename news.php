<?php require_once 'config.php'; require_once 'templates/header.php'; ?>

<div class="text-center mb-5">
    <h2 class="page-title">Tin Tức Bóng Đá</h2>
    <p class="text-muted">Cập nhật những thông tin mới nhất về V.League 1</p>
</div>

<div class="container">
    <div class="row g-4">
        <?php
        // Lấy tất cả tin tức, tin mới nhất lên đầu
        $sql = "SELECT * FROM news ORDER BY created_at DESC";
        $stmt = $pdo->query($sql);
        
        if ($stmt->rowCount() == 0) {
            echo "<div class='col-12'><div class='alert alert-info text-center'>Chưa có tin tức nào.</div></div>";
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Xử lý ảnh, nếu không có ảnh thì dùng ảnh mặc định
            $img = !empty($row['image_url']) ? $row['image_url'] : 'assets/img/default_news.jpg';
            $date = date("d/m/Y", strtotime($row['created_at']));
            ?>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm news-card-hover">
                    <div class="overflow-hidden rounded-top">
                        <a href="news_detail.php?id=<?php echo $row['id']; ?>">
                            <img src="<?php echo $img; ?>" class="card-img-top news-thumb" alt="<?php echo $row['title']; ?>">
                        </a>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="small text-muted mb-2">
                            <i class="fa-regular fa-calendar me-1"></i> <?php echo $date; ?>
                        </div>
                        <h5 class="card-title fw-bold">
                            <a href="news_detail.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-dark news-title-link">
                                <?php echo $row['title']; ?>
                            </a>
                        </h5>
                        <p class="card-text text-secondary small flex-grow-1">
                            <?php echo substr($row['summary'], 0, 100) . '...'; ?>
                        </p>
                        <div class="mt-3">
                            <a href="news_detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-pill">
                                Đọc tiếp <i class="fa-solid fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }
        ?>
    </div>
</div>

<style>
    .news-thumb {
        height: 200px;
        object-fit: cover;
        width: 100%;
        transition: transform 0.3s ease;
    }
    
    .news-card-hover:hover .news-thumb {
        transform: scale(1.05);
    }
    
    .news-title-link:hover {
        color: var(--pl-purple) !important;
        text-decoration: underline !important;
    }
</style>

</body></html>