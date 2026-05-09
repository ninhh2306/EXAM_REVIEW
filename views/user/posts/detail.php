<?php
/** @var array $post */

  // Xử lý sẵn ở trên cho gọn
$categoryUrl = "/tin-tuc/" . $post['categorySlug'];
$categoryName = $post['categoryName'];
$shortTitle = mb_strimwidth($post['title'], 0, 50, '...');

?>
<!-- HERO -->
<section class="hero-post">

    <div class="container">

        <div class="breadcrumb">
            <a href="/">Trang chủ</a>
            <span>›</span>

            <a href="/tin-tuc">
                Tin tức
            </a>
            <span>›</span>

            <a href="<?= $categoryUrl ?>"><?= $categoryName ?></a>
            <span>›</span>

            <span><?= $shortTitle ?></span>

        </div>

        <a href="/tin-tuc/<?= htmlspecialchars($post['categorySlug']) ?>"
           class="badge-post">
            <?= htmlspecialchars($post['categoryName']) ?>
        </a>

        <h1 class="hero-post__title">
            <?= htmlspecialchars($post['title']) ?>
        </h1>

    </div>

</section>


<!-- PAGE -->
<section class="post-detail-section">

    <div class="container">

        <div class="layout-post">

            <!-- MAIN -->
            <main class="content-post">

                <article class="article-post">

                    <div class="article-post__body">
                        <?= $post['content'] ?>
                    </div>

                </article>

                <!-- AUTHOR -->
                <div class="author-post">

                    <?php if (!empty($author['avatar'])): ?>

                        <img class="author-post__avatar"
                             src="<?= htmlspecialchars($author['avatar']) ?>"
                             alt="<?= htmlspecialchars($author['fullName'] ?? '') ?>">

                    <?php else: ?>

                        <div class="author-post__avatar author-post__avatar--initials">
                            <?= mb_strtoupper(mb_substr($author['fullName'] ?? 'A', 0, 1)) ?>
                        </div>

                    <?php endif; ?>

                    <div class="author-post__info">

                        <span class="author-post__name">
                            <?= htmlspecialchars($author['fullName'] ?? 'PrepMaster') ?>
                        </span>

                        <span class="author-post__date">

                            <svg width="13"
                                 height="13"
                                 viewBox="0 0 24 24"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2">

                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>

                            </svg>

                            <?= date('d/m/Y', strtotime($post['createdAt'])) ?>

                        </span>

                    </div>

                </div>

            </main>

            <!-- SIDEBAR -->
            <aside class="sidebar-post">

                <div class="sidebar-post__card">

                    <h2 class="sidebar-post__title">
                        Bài viết liên quan
                    </h2>

                    <?php if (!empty($relatedPosts)): ?>

                        <div class="related-post__list">

                            <?php foreach ($relatedPosts as $rp): ?>

                                <a href="/tin-tuc/<?= htmlspecialchars($rp['categorySlug']) ?>/<?= htmlspecialchars($rp['slug']) ?>"
                                   class="related-post__item">

                                    <?php if (!empty($rp['thumbnail'])): ?>

                                        <img src="<?= htmlspecialchars($rp['thumbnail']) ?>"
                                             alt="<?= htmlspecialchars($rp['title']) ?>"
                                             class="related-post__thumb">

                                    <?php else: ?>

                                        <div class="related-post__thumb related-post__thumb--empty">
                                            🖼️
                                        </div>

                                    <?php endif; ?>

                                    <div class="related-post__info">

                                        <span class="related-post__rtitle">
                                            <?= htmlspecialchars($rp['title']) ?>
                                        </span>

                                        <span class="related-post__rdate">
                                            <?= date('d/m/Y', strtotime($rp['createdAt'])) ?>
                                        </span>

                                    </div>

                                </a>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <p class="related-post__empty">
                            Không có bài viết liên quan.
                        </p>

                    <?php endif; ?>

                </div>

            </aside>

        </div>

    </div>

</section>