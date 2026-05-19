<?php
$isLoggedIn = !empty($_SESSION['user_id']);
?>


<!-- =========================================
     HERO
========================================= -->
<section class="hero">
    <div class="container hero__inner">

        <div class="hero__content">
            <div class="hero__badge">
                <span class="hero__badge-dot"></span>
                ✨ HỌC TẬP THÔNG MINH HƠN
            </div>

            <h1 class="hero-title">
                <span class="t1">Học vui</span>
                <span class="t2">Vui</span>

                <span class="t3">Cùng</span>
                <span class="t4">Luyện</span>

                <span class="t5">Thi chất</span>
                <span class="t6">Thi</span>
            </h1>

            <p class="hero__desc">
                Nền tảng ôn luyện bám sát chương trình chuẩn, giúp bạn nắm vững kiến thức, 
                làm chủ kỹ năng và sẵn sàng tỏa sáng trên hành trình phía trước.
            </p>

            <div class="hero__actions">
                <a href="<?= $isLoggedIn ? $_SERVER['REQUEST_URI'] : $base . '/register' ?>"
                    class="btn btn-primary btn-lg">
                    Bắt đầu ngay
                </a>
                <a href="<?= $_SERVER['REQUEST_URI'] ?>" class="btn btn-outline btn-lg">
                    Xem lộ trình
                </a>
            </div>

            <div class="hero__social">
                <div class="hero__avatars">
                    <div class="hero__avatars-item">🧑</div>
                    <div class="hero__avatars-item">👩</div>
                    <div class="hero__avatars-item">🧑</div>
                </div>
                <p class="hero__social-text">
                    Hơn <strong><?= number_format(!empty($totalStudents) && $totalStudents > 0 ? $totalStudents : 50000) ?>+</strong> học sinh đang ôn luyện mỗi ngày
                </p>
            </div>
        </div>

        <!-- CỘT PHẢI: slider ảnh -->
        <div class="hero__visual">
            <div class="hero__slider">

                <!-- Slide 1 -->
                <div class="hero__slide hero__slide--active">
                    <img
                        src="<?= $base ?>/images/slide/slide1.jpg"
                        alt="Học sinh ôn luyện"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="hero__slide-fallback" style="display:none">
                        <span>🎓</span>
                        <p>Ảnh học sinh 1</p>
                    </div>
                    <div class="hero__slide-caption">Lộ trình học tập cá nhân hóa</div>
                </div>

                <!-- Slide 2 -->
                <div class="hero__slide">
                    <img
                        src="<?= $base ?>/images/slide/slide2.jpg"
                        alt="Ôn luyện trắc nghiệm"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="hero__slide-fallback" style="display:none">
                        <span>📝</span>
                        <p>Ảnh học sinh 2</p>
                    </div>
                    <div class="hero__slide-caption">Ngân hàng đề thi phong phú</div>
                </div>

                <!-- Slide 3 -->
                <div class="hero__slide">
                    <img
                        src="<?= $base ?>/images/slide/slide3.jpg"
                        alt="Kết quả thi cao"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                    >
                    <div class="hero__slide-fallback" style="display:none">
                        <span>🏆</span>
                        <p>Ảnh học sinh 3</p>
                    </div>
                </div>

                <!-- Dots điều hướng -->
                <div class="hero__dots">
                    <button class="hero__dot hero__dot--active" data-index="0"></button>
                    <button class="hero__dot" data-index="1"></button>
                    <button class="hero__dot" data-index="2"></button>
                </div>

                <!-- Badge nổi -->
                <!-- <div class="hero__float-badge">
                    <div class="hero__float-icon">📊</div>
                    <div>
                        <div class="hero__float-label">Tỷ lệ đỗ</div>
                        <div class="hero__float-value">98.5%</div>
                    </div>
                </div> -->

            </div>
        </div>

    </div>
</section>


<!-- =========================================
     TÍNH NĂNG NỔI BẬT
========================================= -->
<section class="features" id="features">
    <div class="container">
        <div class="section-head reveal">
            <h2 class="section-head__title">Vui Luyện Thi - Học nhẹ nhàng, thi điểm cao, kiến tạo tương lai</h2>
            <p class="section-head__sub">vuiluyenthi.vn là nền tảng luyện thi trắc nghiệm online hàng đầu dành cho học sinh THPT.
                Website cung cấp kho đề thi đa dạng, sát thực tế với lời giải chi tiết giúp bạn ôn tập hiệu quả.
                Bên cạnh đó, Vui Luyện Thi còn chia sẻ những bài giảng và bí kíp học tập độc đáo,
                giúp việc chinh phục kỳ thi Đại học trở nên nhẹ nhàng và đầy hứng khởi.</p>
        </div>

        <div class="features__grid">

            <div class="feature-card feature-card--blue reveal">
                <div class="feature-card__icon feature-card__icon--blue">
                    <i class="fa-solid fa-file-lines" style="color: rgb(30, 83, 178);"></i>
                </div>
                <h3 class="feature-card__title">Tài liệu</h3>
                <p class="feature-card__desc">
                    Kho tài liệu phong phú, bám sát sách giáo khoa và các chuyên đề
                    nâng cao từ các giáo viên uy tín.
                </p>
                <a href="<?= $_SERVER['REQUEST_URI'] ?>" 
                    class="feature-card__link feature-card__link--blue">
                    Khám phá ngay →
                </a>
            </div>

            <div class="feature-card feature-card--pink reveal" style="transition-delay:.1s">
                <div class="feature-card__icon feature-card__icon--pink">
                    <i class="fa-solid fa-square-check" style="color: rgb(28, 179, 48);"></i>
                </div>
                <h3 class="feature-card__title">Ôn luyện trắc nghiệm</h3>
                <p class="feature-card__desc">
                    Ngân hàng câu hỏi khổng lồ với lời giải chi tiết giúp bạn
                    rèn luyện kỹ năng và phản xạ làm bài.
                </p>
                <a href="<?= $_SERVER['REQUEST_URI'] ?>" 
                    class="feature-card__link feature-card__link--pink">
                    Luyện tập →
                </a>
            </div>

            <div class="feature-card feature-card--yellow reveal" style="transition-delay:.2s">
                <div class="feature-card__icon feature-card__icon--yellow">
                    <i class="fa-solid fa-bolt" style="color: rgb(227, 158, 48);"></i>
                </div>
                <h3 class="feature-card__title">Tạo đề nhanh</h3>
                <p class="feature-card__desc">
                    Tự tạo đề thi theo yêu cầu: chọn môn, chọn chương,
                    chọn mức độ khó chỉ trong vài giây.
                </p>
                <a href="<?= $_SERVER['REQUEST_URI'] ?>" 
                    class="feature-card__link feature-card__link--yellow">
                    Tạo đề thi →
                </a>
            </div>

        </div>
    </div>
</section>


<!-- =========================================
     LỢI ÍCH & NỀN TẢNG HIỆN ĐẠI
========================================= -->
<section class="benefits-wrap" id="benefits">
    <div class="container">

        <div class="benefits-wrap__head reveal">
            <h2 class="section-head__title">Cùng Vui Luyện Thi luyện đề cực chất, 
                bứt phá cực nhanh, tự tin chinh phục mọi đấu trường trắc nghiệm.</h2>
        </div>

        <div class="benefits-inner">

            <!-- Lợi ích -->
            <div class="benefits-left reveal">
                <h3 class="benefits__title">Lợi ích khi tham gia</h3>

                <div class="benefit-item">
                    <div class="benefit-item__icon benefit-item__icon--blue">🕐</div>
                    <div>
                        <h4 class="benefit-item__title">Học mọi lúc - Vui mọi nơi</h4>
                        <p class="benefit-item__desc">Làm chủ thời gian, bứt phá điểm số trên mọi thiết bị.
                            Luyện tập linh hoạt theo đúng nhịp sống của bạn.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-item__icon benefit-item__icon--pink">⏱️</div>
                    <div>
                        <h4 class="benefit-item__title">Tối ưu thời gian - Ôn thi có chiến thuật</h4>
                        <p class="benefit-item__desc">Lộ trình riêng biệt giúp bạn nhẹ nhàng lấp đầy những lỗ hổng kiến thức, 
                                                      nắm chắc điểm cao mà vẫn thong dong tận hưởng thời gian.</p>
                    </div>
                </div>

                <div class="benefit-item">
                    <div class="benefit-item__icon benefit-item__icon--yellow">📈</div>
                    <div>
                        <h4 class="benefit-item__title">Làm chủ lộ trình - Đo lường tiến bộ</h4>
                        <p class="benefit-item__desc">Theo dõi lịch sử luyện đề cực chi tiết.
                            Nhìn biểu đồ đi lên mỗi ngày chính là động lực lớn nhất để bạn tự tin "vượt vũ môn"</p>
                    </div>
                </div>
            </div>

            <!-- Nền tảng -->
            <div class="benefits-right" style="transition-delay:.15s">
                <h3 class="platform__title">Nền tảng chuẩn tương lai</h3>
                <p class="platform__sub">Ứng dụng công nghệ AI để tối ưu hóa việc học tập</p>

                <div class="platform__grid">
                    <div class="platform-item">
                        <span class="platform-item__icon">📋</span>
                        <span class="platform-item__label">Đề thi đa dạng</span>
                    </div>
                    <div class="platform-item">
                        <span class="platform-item__icon">✔️</span>
                        <span class="platform-item__label">Kiểm tra tự động</span>
                    </div>
                    <div class="platform-item">
                        <span class="platform-item__icon">🛠️</span>
                        <span class="platform-item__label">Công cụ hiện đại</span>
                    </div>
                    <div class="platform-item">
                        <span class="platform-item__icon">🖥️</span>
                        <span class="platform-item__label">Giao diện thân thiện</span>
                    </div>
                    <div class="platform-item platform-item--active">
                        <span class="platform-item__icon">🔄</span>
                        <span class="platform-item__label">Cập nhật liên tục</span>
                    </div>
                    <div class="platform-item platform-item--active">
                        <span class="platform-item__icon">🤖</span>
                        <span class="platform-item__label">Phân tích AI</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- =========================================
     CTA
========================================= -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box reveal">
            <h2 class="cta-box__title">Sẵn sàng để bứt phá điểm số?</h2>
            <p class="cta-box__sub">Tham gia cùng hàng ngàn học sinh khác đã thành công cùng Vui Luyện Thi ngay hôm nay.</p>
            <div class="cta-box__actions">
                <a href="<?= $isLoggedIn ? $_SERVER['REQUEST_URI'] : $base . '/register' ?>" 
                    class="btn btn-white">
                    Đăng ký miễn phí
                </a>
                <a href="<?= $_SERVER['REQUEST_URI'] ?>" 
                    class="btn btn-ghost-white">
                    Tìm hiểu thêm
                </a>
            </div>
        </div>
    </div>
</section>