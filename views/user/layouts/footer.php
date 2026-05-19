<!-- =========================================
     FOOTER
========================================= -->
<?php
    $base = rtrim(str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME']))), '/');
?>
<footer class="footer">
    <div class="container">
        <div class="footer__grid">

            <div class="footer__brand">
                <div class="footer__logo">
                    <div class="footer__logo-icon">📘</div>
                    Vui Luyện Thi
                </div>
                <p>Nền tảng ôn luyện thi trực tuyến hàng đầu Việt Nam, giúp học sinh phổ thông đạt kết quả cao trong mọi kỳ thi.</p>
                <div class="footer__social">
                    <a href="#">📘</a>
                    <a href="#">📧</a>
                    <a href="#">📷</a>
                </div>
            </div>

            <div class="footer__col">
                <h4>Khóa học</h4>
                <!-- Đã fix cứng theo yêu cầu của bạn -->
                <a href="<?= $base ?>/lop-10">Luyện thi Lớp 10</a>
                <a href="<?= $base ?>/lop-11">Luyện thi Lớp 11</a>
                <a href="<?= $base ?>/lop-12">Luyện thi Lớp 12</a>
                <a href="<?= $base ?>/thpt-quoc-gia">Luyện thi THPT Quốc Gia</a>
            </div>

            <div class="footer__col">
                <h4>Hỗ trợ</h4>
                <a href="#">Trung tâm hỗ trợ</a>
                <a href="#">Hướng dẫn sử dụng</a>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Điều khoản dịch vụ</a>
                <a href="#">Phản hồi &amp; Khiếu nại</a>
            </div>

            <div class="footer__col">
                <h4>Liên hệ</h4>
                <div class="footer__contact-item">
                    <div class="footer__contact-icon">📧</div>
                    <span>contact@vuiluyenthi.vn</span>
                </div>
                <div class="footer__contact-item">
                    <div class="footer__contact-icon">📞</div>
                    <span>0984 155 625</span>
                </div>
                <div class="footer__contact-item">
                    <div class="footer__contact-icon">📍</div>
                    <span>Trường Đại học Công nghệ Đông Á</span>
                </div>
            </div>

        </div>

        <div class="footer__bottom">
            <p>© 2026 Vui Luyện Thi. Tất cả quyền được bảo lưu.</p>
            <div class="footer__bottom-links">
                <a href="#">Sơ đồ trang</a>
                <a href="#">Bản quyền</a>
                <a href="#">Cookie</a>
            </div>
        </div>
    </div>
</footer>

<script src="/js/main.js"></script>

</body>
</html>