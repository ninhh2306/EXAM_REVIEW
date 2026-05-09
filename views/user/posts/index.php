
<!-- ========================= -->
<!--        HERO               -->
<!-- ========================= -->
<section class="post-hero">
    <div class="container">

        <!-- BREADCRUMB (nằm trong hero) -->
        <nav class="post-breadcrumb" aria-label="Breadcrumb">
            <a href="/">Trang chủ</a>
            <span>›</span>
            <span class="bc-current"><?= htmlspecialchars($title ?? '') ?></span>
        </nav>

        <h1><?= htmlspecialchars($title ?? '') ?></h1>

        <?php if (!empty($description)): ?>
            <p class="post-hero-desc"><?= htmlspecialchars($description) ?></p>
        <?php endif; ?>

    </div>
</section>


<!-- ========================= -->
<!--      DANH SÁCH BÀI       -->
<!-- ========================= -->
<section class="post-list-section">
    <div class="container">

        <div class="post-grid">

            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>

                    <div class="post-card">

                        <!-- ẢNH -->
                        <div class="post-card-img-wrap">
                            <img
                                src="<?= htmlspecialchars($post['thumbnail'] ?? '') ?>"
                                alt="<?= htmlspecialchars($post['title'] ?? '') ?>"
                                loading="lazy"
                            >
                        </div>

                        <!-- NỘI DUNG -->
                        <div class="post-card-body">
                            <h3>
                                <a href="/tin-tuc/<?= htmlspecialchars($post['categorySlug'] ?? '') ?>/<?= htmlspecialchars($post['slug'] ?? '') ?>">
                                    <?= htmlspecialchars($post['title'] ?? '') ?>
                                </a>
                            </h3>

                            <p><?= htmlspecialchars($post['excerpt'] ?? '') ?></p>

                            <a class="post-read-more"
                               href="/tin-tuc/<?= htmlspecialchars($post['categorySlug'] ?? '') ?>/<?= htmlspecialchars($post['slug'] ?? '') ?>">
                                Xem chi tiết →
                            </a>
                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>
                <p class="post-empty">Không có bài viết nào.</p>
            <?php endif; ?>

        </div>

    </div>
</section>
