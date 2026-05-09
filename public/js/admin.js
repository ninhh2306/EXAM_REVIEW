// ================== SLUG ==================
function toSlug(str) {
    return str
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/đ/g, "d")
        .replace(/[^a-z0-9\s-]/g, "")
        .trim()
        .replace(/\s+/g, "-");
}

function bindSlug(nameId, slugId) {

    const name = document.getElementById(nameId);
    const slug = document.getElementById(slugId);

    if (!name || !slug) return;

    let edited = false;

    name.addEventListener("input", () => {

        // chapter dùng riêng
        if (document.getElementById("chapterModalTitle")) {
            return;
        }

        if (!edited) {
            slug.value = toSlug(name.value);
        }
    });
}


// ================== INIT ==================
document.addEventListener("DOMContentLoaded", function () {

    bindSlug("name", "slug");
    bindSlug("subject_name", "subject_slug");

    setTimeout(() => {
        const alert = document.getElementById("autoAlert");
        if (alert) {
            alert.style.opacity = "0";
            setTimeout(() => alert.remove(), 300);
        }
    }, 2000);

    // Flash error — inject data + mở modal đúng trang
    const flashMeta = document.getElementById("flashMeta");
    if (flashMeta && flashMeta.dataset.error) {

        // CATEGORY
        if (document.getElementById("categoryModalTitle")) {
            document.getElementById("id").value          = flashMeta.dataset.oldId   || '';
            document.getElementById("name").value        = flashMeta.dataset.oldName || '';
            document.getElementById("description").value = flashMeta.dataset.oldDescription || '';
            document.getElementById("slug").value        = flashMeta.dataset.oldSlug || '';

            const isUpdate = flashMeta.dataset.oldId !== '';
            document.getElementById("categoryModalTitle").innerText =
                isUpdate ? "Cập nhật danh mục" : "Thêm danh mục";
            document.querySelector('#formModal form').action =
                isUpdate ? '/admin/categories/update' : '/admin/categories/store';

            injectFlashError(flashMeta.dataset.error, "category");
            $('#formModal').modal('show');
        }

        // GRADE
        if (document.getElementById("gradeModalTitle")) {
            document.getElementById("id").value   = flashMeta.dataset.oldId   || '';
            document.getElementById("name").value = flashMeta.dataset.oldName || '';
            document.getElementById("slug").value = flashMeta.dataset.oldSlug || '';

            const isUpdate = flashMeta.dataset.oldId !== '';
            document.getElementById("gradeModalTitle").innerText =
                isUpdate ? "Cập nhật khối lớp" : "Thêm khối lớp";
            document.querySelector('#formModal form').action =
                isUpdate ? '/admin/grades/update' : '/admin/grades/store';

            injectFlashError(flashMeta.dataset.error, "grade");
            $('#formModal').modal('show');
        }

        // CHAPTER — phải load subjects async trước khi mở modal
        if (document.getElementById("chapterModalTitle")) {
            const gradeId   = flashMeta.dataset.oldGradeId   || '';
            const subjectId = flashMeta.dataset.oldSubjectId || '';

            document.getElementById("id").value        = flashMeta.dataset.oldId        || '';
            document.getElementById("name").value      = flashMeta.dataset.oldName      || '';
            document.getElementById("sortOrder").value = flashMeta.dataset.oldSortOrder || '';
            document.getElementById("slug").value      = flashMeta.dataset.oldSlug      || '';

            const isUpdate = flashMeta.dataset.oldId !== '';
            document.getElementById("chapterModalTitle").innerText =
                isUpdate ? "Cập nhật chương học" : "Thêm chương học";

            injectFlashError(flashMeta.dataset.error, "chapter");

            // Restore grade select
            if (gradeId) {
                document.getElementById("gradeSelect").value = gradeId;

                // Load subjects rồi mới mở modal
                $.get('/admin/ajax/subjects?grade_id=' + gradeId, function (data) {
                    let html = '<option value="">Chọn môn học</option>';
                    data.forEach(item => {
                        html += `<option value="${item.subjectId}">${item.subjectName}</option>`;
                    });
                    $('#subjectSelect').html(html);

                    if (subjectId) {
                        $('#subjectSelect').val(subjectId);
                    }

                    $('#formModal').modal('show');
                });
            } else {
                $('#formModal').modal('show');
            }
        }
    }
});


// Helper inject alert lỗi vào modal
function injectFlashError(errorCode, page) {
    // Xóa cái cũ nếu có
    document.querySelector('#formModal .alert-error')?.remove();

    const messages = {
        category: { exists: 'Tên hoặc slug danh mục đã tồn tại!' },
        grade:    { exists: 'Tên hoặc slug khối lớp đã tồn tại!' },
    };

    const msg = messages[page]?.[errorCode] ?? 'Có lỗi xảy ra!';

    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert-error mb-3';
    alertDiv.innerText = msg;

    // Chèn trước label đầu tiên trong form
    const firstLabel = document.querySelector('#formModal form label');
    if (firstLabel) {
        firstLabel.parentNode.insertBefore(alertDiv, firstLabel);
    }
}


// ================== MODAL RESET ==================
$('#formModal').on('hidden.bs.modal', function () {

    const form = this.querySelector("form");
    if (form) form.reset();

    // ===== CATEGORY =====
    if (document.getElementById("categoryModalTitle")) {
        resetCategoryModal();
        return;
    }

    // ===== GRADE =====
    if (document.getElementById("gradeModalTitle")) {
        resetGradeModal();
        return;
    }

    // ===== SUBJECT =====
    if (document.getElementById("modalTitle")) {
        document.getElementById("modalTitle").innerText = "Thêm môn học";
        form.action = '/admin/subjects/store';
    }

    // ===== CHAPTER =====
    if (document.getElementById("chapterModalTitle")) {
        resetChapterModal();
        return;
    }

    
});



// =========================================================
// CATEGORY
// =========================================================

// Mở modal THÊM — luôn reset sạch
function openAddCategory() {
    resetCategoryModal();
    $('#formModal').modal('show');
}

// Mở modal SỬA — điền data từ tham số
function openEditCategory(el) {
    const id             = el.dataset.id;
    const nameVal        = el.dataset.name;
    const descriptionVal = el.dataset.description;
    const slugVal        = el.dataset.slug;

    resetCategoryModal();

    document.getElementById("categoryModalTitle").innerText = "Cập nhật danh mục";
    document.getElementById("id").value          = id;
    document.getElementById("name").value        = nameVal;
    document.getElementById("description").value = descriptionVal;
    document.getElementById("slug").value        = slugVal;
    document.querySelector('#formModal form').action = '/admin/categories/update';

    $('#formModal').modal('show');
}

// Reset modal về trạng thái THÊM MỚI hoàn toàn
function resetCategoryModal() {
    const form = document.querySelector('#formModal form');
    
    // Reset từng field thủ công — KHÔNG dùng form.reset() vì nó trả về HTML default value
    document.getElementById("id").value          = '';
    document.getElementById("name").value        = '';
    document.getElementById("description").value = '';
    document.getElementById("slug").value        = '';

    document.getElementById("categoryModalTitle").innerText = "Thêm danh mục";
    form.action = '/admin/categories/store';

    // Xóa alert lỗi nếu có
    document.querySelector('#formModal .alert-error')?.remove();
}

function openDeleteCategory(id) {
    document.getElementById("deleteLink").href = "/admin/categories/delete/" + id;
    $('#deleteModal').modal('show');
}



// =========================================================
// GRADE
// =========================================================

function openAddGrade() {
    resetGradeModal();
    $('#formModal').modal('show');
}

function openEditGrade(el) {
    const id      = el.dataset.id;
    const nameVal = el.dataset.name;
    const slugVal = el.dataset.slug;

    resetGradeModal();

    document.getElementById("gradeModalTitle").innerText = "Cập nhật khối lớp";
    document.getElementById("id").value   = id;
    document.getElementById("name").value = nameVal;
    document.getElementById("slug").value = slugVal;
    document.querySelector('#formModal form').action = '/admin/grades/update';

    $('#formModal').modal('show');
}

function resetGradeModal() {
    // Reset thủ công từng field — KHÔNG dùng form.reset()
    document.getElementById("id").value   = '';
    document.getElementById("name").value = '';
    document.getElementById("slug").value = '';

    document.getElementById("gradeModalTitle").innerText = "Thêm khối lớp";
    document.querySelector('#formModal form').action = '/admin/grades/store';

    document.querySelector('#formModal .alert-error')?.remove();
}

function openDeleteGrade(id) {
    document.getElementById("deleteLink").href = "/admin/grades/delete/" + id;
    $('#deleteModal').modal('show');
}


// =========================================================
// SUBJECT
// =========================================================
function openEditSubject(id, gradeId, nameVal, slugVal) {

    $('#formModal').modal('show');

    document.getElementById("modalTitle").innerText =
        "Cập nhật môn học";

    document.getElementById("subject_id").value = id;
    document.getElementById("subject_name").value = nameVal;
    document.getElementById("subject_slug").value = slugVal;
    document.getElementById("gradeId").value = gradeId;

    document.querySelector('#formModal form').action =
        '/admin/subjects/update';
}

function openDeleteSubject(id) {

    document.getElementById("deleteLink").href =
        "/admin/subjects/delete/" + id;

    $('#deleteModal').modal('show');
}


// =========================================================
// CHAPTER
// =========================================================

function openAddChapter() {
    resetChapterModal();
    $('#formModal').modal('show');
}

function openEditChapter(el) {
    const id        = el.dataset.id;
    const subjectId = el.dataset.subjectId;
    const gradeId   = el.dataset.gradeId;
    const nameVal   = el.dataset.name;
    const slugVal   = el.dataset.slug;
    const sortOrder = el.dataset.sortOrder;

    resetChapterModal();

    document.getElementById("chapterModalTitle").innerText = "Cập nhật chương học";
    document.getElementById("id").value        = id;
    document.getElementById("name").value      = nameVal;
    document.getElementById("sortOrder").value = sortOrder;
    document.getElementById("slug").value      = slugVal;
    document.getElementById("gradeSelect").value = gradeId;

    // Load subjects theo grade, sau đó set subject đã chọn
    $('#subjectSelect').html('<option>Đang tải...</option>');

    $.get('/admin/ajax/subjects?grade_id=' + gradeId, function (data) {
        let html = '<option value="">Chọn môn học</option>';
        data.forEach(item => {
            html += `<option value="${item.subjectId}">${item.subjectName}</option>`;
        });
        $('#subjectSelect').html(html);
        $('#subjectSelect').val(subjectId);
    });

    $('#formModal').modal('show');
}

function resetChapterModal() {
    // Reset thủ công từng field
    document.getElementById("id").value        = '';
    document.getElementById("name").value      = '';
    document.getElementById("sortOrder").value = '';
    document.getElementById("slug").value      = '';
    document.getElementById("gradeSelect").value = '';
    $('#subjectSelect').html('<option value="">Chọn môn học</option>');

    document.getElementById("chapterModalTitle").innerText = "Thêm chương học";

    document.querySelector('#formModal .alert-error')?.remove();
}

function openDeleteChapter(id) {
    document.getElementById("deleteLink").href = "/admin/chapters/delete/" + id;
    $('#deleteModal').modal('show');
}


function injectFlashError(errorCode, page) {
    document.querySelector('#formModal .alert-error')?.remove();

    const messages = {
        category: {
            exists: 'Tên hoặc slug danh mục đã tồn tại!'
        },
        grade: {
            exists: 'Tên hoặc slug khối lớp đã tồn tại!'
        },
        chapter: {
            exists:       'Tên chương học đã tồn tại trong môn học này!',
            sort_exists:  'Số thứ tự chương đã tồn tại trong môn học này!',
        },
    };

    const msg = messages[page]?.[errorCode] ?? 'Có lỗi xảy ra!';

    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert-error mb-3';
    alertDiv.innerText = msg;

    const firstLabel = document.querySelector('#formModal form label');
    if (firstLabel) {
        firstLabel.parentNode.insertBefore(alertDiv, firstLabel);
    }
}


// ================== AUTO SLUG CHAPTER ==================
function generateChapterSlug(sortOrder) {

    if (!sortOrder) return '';

    return 'chuong-' + sortOrder;
}

//  Auto update slug theo sortOrder
const sortInput = document.getElementById("sortOrder");

if (sortInput) {

    sortInput.addEventListener("input", function () {

        document.getElementById("slug").value =
            generateChapterSlug(this.value);

    });

}



// =========================================================
// IMAGE PREVIEW
// =========================================================
const imgInput = document.getElementById('imageInput');

if (imgInput) {

    imgInput.addEventListener('change', function (e) {

        const file = e.target.files[0];

        if (!file) return;

        const reader = new FileReader();

        reader.onload = function (e) {

            const preview = document.getElementById('preview');

            if (preview) {
                preview.src = e.target.result;
            }
        };

        reader.readAsDataURL(file);
    });
}


// =========================================================
// LOAD SUBJECT THEO GRADE
// =========================================================
$(document).ready(function () {

    $('#gradeSelect').on('change', function () {

        let gradeId = $(this).val();

        $('#subjectSelect').html('<option>Đang tải...</option>');

        $.get('/admin/ajax/subjects?grade_id=' + gradeId, function (data) {

            let html = '<option value="">Chọn môn học</option>';

            data.forEach(item => {

                html += `
                    <option value="${item.subjectId}">
                        ${item.subjectName}
                    </option>
                `;
            });

            $('#subjectSelect').html(html);

            // reset chapter
            $('#chapterSelect').html(
                '<option value="">Chọn chương học</option>'
            );
        });

    });

});


// =========================================================
// LOAD CHAPTER THEO SUBJECT
// =========================================================
$('#subjectSelect').change(function () {

    let subjectId = $(this).val();

    $('#chapterSelect').html('<option>Đang tải...</option>');

    $.get('/admin/ajax/chapters?subject_id=' + subjectId, function (data) {

        let html = '<option value="">Chọn chương học</option>';

        data.forEach(item => {

            html += `
                <option value="${item.chapterId}">
                    ${item.chapterName}
                </option>
            `;
        });

        $('#chapterSelect').html(html);
    });

});


// =========================================================
// LESSON
// =========================================================

(function initLessonSlug() {

    const nameInput = document.getElementById("lesson_name");
    const slugInput = document.getElementById("lesson_slug");

    if (!nameInput || !slugInput) return;

    // Mở khóa slug để admin sửa được
    slugInput.removeAttribute("readonly");

    let slugManuallyEdited = false;

    // Khi admin tự sửa slug → đánh dấu, không auto-generate nữa
    slugInput.addEventListener("input", function () {
        slugManuallyEdited = this.value.trim() !== '';
    });

    // Khi nhập tên → tự generate slug nếu chưa sửa tay
    nameInput.addEventListener("input", function () {
        if (!slugManuallyEdited) {
            slugInput.value = toSlug(this.value);
        }
    });

})();


function openDeleteLesson(id) {
    document.getElementById("deleteLink").href = "/admin/lessons/delete/" + id;
    $('#deleteModal').modal('show');
}


// LESSON — restore subject + chapter khi có lỗi flash (trang create)
(function restoreLessonFlash() {

    const gradeSelect   = document.getElementById("gradeSelect");
    const subjectSelect = document.getElementById("subjectSelect");
    const chapterSelect = document.getElementById("chapterSelect");

    if (!gradeSelect || !subjectSelect || !chapterSelect) return;

    // Edit page đã có options từ PHP — bỏ qua
    if (subjectSelect.options.length > 1) return;

    const savedGradeId   = gradeSelect.dataset.saved  || gradeSelect.value || '';
    const savedSubjectId = subjectSelect.dataset.saved || '';
    const savedChapterId = chapterSelect.dataset.saved || '';

    if (!savedGradeId) return;

    $.get('/admin/ajax/subjects?grade_id=' + savedGradeId, function (subjects) {

        let html = '<option value="">Chọn môn học</option>';
        subjects.forEach(s => {
            html += `<option value="${s.subjectId}">${s.subjectName}</option>`;
        });
        subjectSelect.innerHTML = html;

        if (savedSubjectId) subjectSelect.value = savedSubjectId;
        if (!savedSubjectId || !savedChapterId) return;

        $.get('/admin/ajax/chapters?subject_id=' + savedSubjectId, function (chapters) {

            let html2 = '<option value="">Chọn chương học</option>';
            chapters.forEach(c => {
                html2 += `<option value="${c.chapterId}">${c.chapterName}</option>`;
            });
            chapterSelect.innerHTML = html2;

            if (savedChapterId) chapterSelect.value = savedChapterId;
        });
    });

})();




// ================== POST ==================

bindSlug("post_title", "post_slug");

function openDeletePost(id) {

    document.getElementById("deleteLink").href =
        "/admin/posts/delete/" + id;

    $('#deleteModal').modal('show');
}