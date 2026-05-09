<footer class="sticky-footer bg-white">
  <div class="container my-auto">
    <div class="copyright text-center my-auto">
      <span>Copyright &copy; <script>document.write(new Date().getFullYear());</script> - Developed by <b>Exam Review</b></span>
    </div>
  </div>
</footer>

<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="/js/ruang-admin.min.js"></script>

<!-- JS CUSTOM -->
<script src="/js/admin.js"></script>

<!-- CKEDITOR -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // hỗ trợ nhiều editor dùng chung
    const editors = [
        '#lessonEditor',
        '#postEditor'
    ];

    editors.forEach(selector => {

        const element = document.querySelector(selector);

        if (element) {
            ClassicEditor
                .create(element)
                .catch(error => {
                    console.error(error);
                });
        }

    });

});
</script>
