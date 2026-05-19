<?php
/** @var array $grades */
?>

<div class="admin-page">

    <!-- BREADCRUMB -->
    <div class="breadcrumb">
        <a href="/admin/dashboard">Dashboard</a>
        <span>›</span>
        <a href="/admin/subjects">Môn học</a>
        <span>›</span>
        <span>Thêm mới</span>
    </div>

    <!-- TITLE -->
    <div class="admin-title text-center">
        Thêm môn học
    </div>

    <div class="card subject-card">

        <?php if (isset($_GET['success'])): ?>
            <div class="alert-success" id="autoAlert">Thêm môn học thành công!</div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="alert-error" id="autoAlert">
                <?= htmlspecialchars($flashError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/admin/subjects/store" enctype="multipart/form-data">

            <div class="form-grid-2">

                <!-- LEFT -->
                <div class="form-left">

                    <div class="form-group subject-field">
                        <label>Khối lớp</label>
                        <select name="gradeId" required>
                            <option value="">Chọn khối lớp</option>
                            <?php foreach ($grades as $g): ?>
                                <option value="<?= $g['gradeId'] ?>"
                                <?= ($flashOld['gradeId'] ?? '') == $g['gradeId']
                                    ? 'selected'
                                    : '' ?>>
                                    <?= $g['gradeName'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tên môn</label>
                        <input type="text"
                                id="name"
                                name="name"
                                value="<?= htmlspecialchars($flashOld['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text"
                                id="slug"
                                name="slug"
                                value="<?= htmlspecialchars($flashOld['slug'] ?? '') ?>">
                    </div>

                </div>

                <!-- RIGHT -->
                <div class="form-right">

                    <label class="form-label">Hình ảnh đại diện</label>

                    <div class="image-upload-box">

                        <div class="image-preview-wrapper">
                            <img id="preview" style="display:none;">

                            <div class="image-placeholder">
                                🖼️<br>
                                Chưa có ảnh
                            </div>
                        </div>

                        <label class="upload-btn">
                            Chọn ảnh
                            <input type="file" name="image" id="imageInput"
                                accept=".jpg,.jpeg,.png,.webp,.gif" hidden>
                        </label>

                        <small class="text-muted">
                            JPG, JPEG, PNG, WEBP, GIF (khuyến nghị ảnh vuông)
                        </small>

                    </div>

                </div>

            </div>

            <!-- DESCRIPTION -->
            <div class="form-group full">
                <label>Mô tả nhanh</label>
                <textarea name="description" rows="2" class="textarea-sm"><?=
                htmlspecialchars($flashOld['description'] ?? '')
                ?></textarea>
            </div>

            <div class="form-group full">
                <label>Mô tả chi tiết</label>
                <textarea name="detailDesc" rows="4" class="textarea-lg"><?=
                htmlspecialchars($flashOld['detailDesc'] ?? '')
                ?></textarea>
            </div>

            <!-- ACTION -->
            <div class="form-actions">
                <button class="admin-btn btn-save">Lưu</button>
                <a href="/admin/subjects" class="admin-btn btn-cancel">Hủy</a>
            </div>

        </form>
    </div>
</div>


<!-- PREVIEW IMAGE -->
<script>
const input = document.getElementById("imageInput");
const preview = document.getElementById("preview");
const placeholder = document.querySelector(".image-placeholder");

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
</script>