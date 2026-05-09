<?php
/** @var array $categories */
?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/posts">Bài viết</a>
        <span>›</span>
        <span>Thêm mới</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Thêm bài viết
    </div>

    <div class="card post-card-admin">

        <?php if (isset($_GET['success']) && $_GET['success'] == 'created'): ?>
            <div class="alert-success" id="autoAlert">
                Thêm bài viết thành công!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'exists'): ?>
            <div class="alert-error" id="autoAlert">
                Tiêu đề hoặc slug bài viết đã tồn tại!
            </div>
        <?php endif; ?>

        <form method="POST"
              action="/admin/posts/store"
              enctype="multipart/form-data">

            <div class="post-top-grid">

                <!-- LEFT -->
                <div class="post-form-left">

                    <div class="post-field">
                        <label>Danh mục tin tức</label>

                        <select name="categoryId" required>
                            <option value="">Chọn danh mục</option>

                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['categoryId'] ?>">
                                    <?= $c['categoryName'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="post-field">
                        <label>Tiêu đề bài viết</label>

                        <input type="text"
                               name="title"
                               id="post_title"
                               required>
                    </div>

                    <div class="post-field">
                        <label>Slug</label>

                        <input type="text"
                               name="slug"
                               id="post_slug"
                               required>
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="post-thumbnail-box">

                    <label class="font-weight-bold mb-2">
                        Ảnh Thumbnail
                    </label>

                    <div class="post-thumbnail-preview">

                        <img id="preview" style="display:none;">

                        <div class="post-thumbnail-placeholder">
                            🖼️<br>
                            Chưa có ảnh
                        </div>

                    </div>

                    <div class="text-center">

                        <label class="post-upload-btn">
                            Chọn ảnh

                            <input type="file"
                                   name="thumbnail"
                                   id="imageInput"
                                   accept=".jpg,.jpeg,.png,.webp,.gif"
                                   hidden>
                        </label>

                    </div>

                    <div class="post-upload-note">
                        Sử dụng các ảnh JPG, JPEG, PNG, WEBP, GIF
                    </div>

                </div>

            </div>

            <!-- EDITOR -->
            <div class="post-editor">
                <label>Nội dung bài viết</label>

                <textarea id="postEditor" name="content"></textarea>
            </div>

            <!-- ACTION -->
            <div class="post-actions">

                <button class="admin-btn btn-save">
                    Lưu
                </button>

                <a href="/admin/posts"
                   class="admin-btn btn-cancel">
                    Hủy
                </a>

            </div>

        </form>

    </div>

</div>


<!-- CKEDITOR -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>




<!-- IMAGE -->
<script>

const input = document.getElementById("imageInput");
const preview = document.getElementById("preview");
const placeholder = document.querySelector(".post-thumbnail-placeholder");

if (input && preview) {

    input.addEventListener("change", function () {

        const file = this.files[0];

        if (!file) return;

        const validTypes = [
            "image/jpeg",
            "image/jpg",
            "image/png",
            "image/webp",
            "image/gif"
        ];

        if (!validTypes.includes(file.type)) {

            alert("Chỉ chấp nhận JPG, JPEG, PNG, WEBP, GIF");

            input.value = "";

            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {

            preview.src = e.target.result;

            preview.style.display = "block";

            if (placeholder) {
                placeholder.style.display = "none";
            }

        };

        reader.readAsDataURL(file);

    });

}


</script>