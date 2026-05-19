<?php
/** @var array $categories */
/** @var array $post */
/** @var array $flashError */


$d = $flashOld ?? [];

?>

<div class="admin-page">

    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/posts">Bài viết</a>
        <span>›</span>
        <span>Cập nhật</span>
    </div>

    <div class="admin-title text-center">
        Cập nhật bài viết
    </div>

    <div class="card post-card-admin">

        <?php
        $errorMessages = [
            'empty_fields' => 'Vui lòng nhập đầy đủ thông tin!',
            'exists'       => 'Tiêu đề hoặc slug bài viết đã tồn tại!',
        ];

        $errorText = $errorMessages[$flashError] ?? $flashError;
        ?>

        <?php if (!empty($flashError)): ?>
            <div class="alert-error" id="autoAlert">
                <?= $errorText ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="/admin/posts/update"
              enctype="multipart/form-data">

            <input type="hidden"
                   name="id"
                   value="<?= $post['postId'] ?>">

            <div class="post-top-grid">

                <!-- LEFT -->
                <div class="post-form-left">

                    <div class="post-field">
                        <label>Danh mục tin tức</label>

                        <select name="categoryId" required>
                            <?php foreach($categories as $c): ?>
                                <option value="<?= $c['categoryId'] ?>"
                                    <?= (($d['categoryId'] ?? $post['categoryId']) == $c['categoryId'])
                                        ? 'selected'
                                        : '' ?>>

                                    <?= htmlspecialchars($c['categoryName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="post-field">
                        <label>Tiêu đề bài viết</label>

                        <input type="text"
                               name="title"
                               id="post_title"
                               value="<?= htmlspecialchars($d['title'] ?? $post['title']) ?>"
                               required>
                    </div>

                    <div class="post-field">
                        <label>Slug</label>

                        <input type="text"
                               name="slug"
                               id="post_slug"
                               value="<?= htmlspecialchars($d['slug'] ?? $post['slug']) ?>">
                    </div>

                    <div class="post-field lesson-status">

                        <label>Trạng thái</label>

                        <label class="switch">

                            <input type="checkbox"
                                name="isActive"
                                value="1"
                                <?= ($flashOld['isActive'] ?? $post['isActive']) ? 'checked' : '' ?>>

                            <span class="slider round"></span>

                        </label>

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="post-thumbnail-box">

                    <label class="font-weight-bold mb-2">
                        Ảnh Thumbnail
                    </label>

                    <div class="post-thumbnail-preview">

                        <img id="preview"
                            src="<?= !empty($post['thumbnail'])
                                    ? $post['thumbnail']
                                    : '' ?>"
                            style="<?= empty($post['thumbnail'])
                                        ? 'display:none'
                                        : '' ?>">

                        <div class="post-thumbnail-placeholder"
                            style="<?= !empty($post['thumbnail'])
                                        ? 'display:none'
                                        : '' ?>">
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

                <textarea name="content"
                          id="postEditor"><?= htmlspecialchars($d['content'] ?? $post['content']) ?>
                </textarea>

            </div>

            <!-- ACTION -->
            <div class="post-actions">

                <button class="admin-btn btn-save">
                    Cập nhật
                </button>

                <a href="/admin/posts"
                   class="admin-btn btn-cancel">
                    Hủy
                </a>

            </div>

        </form>

    </div>

</div>


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
            "image/jpg",
            "image/jpeg",
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


document.querySelector('form').addEventListener('submit', () => {
    if (window.editorInstance) {
        document.querySelector('#postEditor').value =
            window.editorInstance.getData();
    }
});

</script>