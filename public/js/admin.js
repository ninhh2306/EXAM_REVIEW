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


// ============== SEARCH =================


(function () {

    const searchInput = document.getElementById("gradeSearch");
    const tbody       = document.getElementById("gradeTableBody");
    const pagination  = document.getElementById("gradePagination");

    if (!searchInput || !tbody) return;

    searchInput.addEventListener("input", async function () {

        const keyword = this.value.trim();

        if (keyword !== '') {
            pagination.style.display = 'none';
        } else {
            pagination.style.display = 'flex';
        }

        const response = await fetch(
            `/admin/grades/ajax-search?keyword=` + encodeURIComponent(keyword)
        );

        const grades = await response.json();

        let html = '';

        // KHÔNG CÓ KẾT QUẢ
        if (!grades.length) {

            html = `
                <tr>
                    <td colspan="4" class="text-center">
                        Không tìm thấy khối lớp
                    </td>
                </tr>
            `;

            tbody.innerHTML = html;
            return;
        }

        // RENDER DATA
        grades.forEach(g => {

            html += `
                <tr>
                    <td>${g.gradeId}</td>
                    <td>${g.gradeName}</td>
                    <td>${g.slug}</td>

                    <td>
                        <div class="admin-actions">
                            <button class="action-btn btn-edit"
                                data-id="${g.gradeId}"
                                data-name="${g.gradeName}"
                                data-slug="${g.slug}"
                                onclick="openEditGrade(this)">
                                ✏
                            </button>

                            <button class="action-btn btn-delete"
                                onclick="openDeleteGrade(${g.gradeId})">
                                🗑
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;

    });

})();



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


// ====== SUBJECT AJAX SEARCH ======
(function () {

    const searchInput = document.getElementById("subjectSearch");
    const tbody       = document.getElementById("subjectTableBody");
    const pagination  = document.getElementById("subjectPagination");

    if (!searchInput || !tbody) return;

    let timeout = null;

    searchInput.addEventListener("input", function () {

        clearTimeout(timeout);

        timeout = setTimeout(async () => {

            const keyword = this.value.trim();

            // Ẩn pagination khi search
            if (pagination) {
                pagination.style.display =
                    keyword !== '' ? 'none' : 'flex';
            }

            // Nếu rỗng → reload page
            if (keyword === '') {
                location.reload();
                return;
            }

            try {

                const response = await fetch(
                    '/admin/ajax/search-subjects?keyword='
                    + encodeURIComponent(keyword)
                );

                const subjects = await response.json();

                let html = '';

                // Không tìm thấy
                if (!subjects.length) {

                    html = `
                        <tr id="subjectNotFoundRow">
                            <td colspan="5" class="text-center">
                                Không tìm thấy môn học
                            </td>
                        </tr>
                    `;

                    tbody.innerHTML = html;

                    return;
                }

                // Render table
                subjects.forEach(item => {

                    html += `
                        <tr>

                            <td>
                                ${item.subjectId}
                            </td>

                            <td>
                                ${item.gradeName}
                            </td>

                            <td>
                                ${item.subjectName}
                            </td>

                            <td>
                                ${item.slug}
                            </td>

                            <td>
                                <div class="admin-actions">

                                    <a href="/admin/subjects/edit/${item.subjectId}"
                                       class="action-btn btn-edit">
                                        ✏
                                    </a>

                                    <button
                                        class="action-btn btn-delete"
                                        onclick="openDeleteSubject(${item.subjectId})">
                                        🗑
                                    </button>

                                </div>
                            </td>

                        </tr>
                    `;
                });

                tbody.innerHTML = html;

            } catch (error) {

                console.error(error);
            }

        }, 300);

    });

})();



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

    document.getElementById("chapterModalTitle").innerText =
        "Cập nhật chương học";

    document.getElementById("id").value = id;
    document.getElementById("name").value = nameVal;
    document.getElementById("slug").value = slugVal;

    const sortInput = document.getElementById("sortOrder");

    if (sortInput) {
        sortInput.value = sortOrder;
    }

    document.getElementById("gradeSelect").value = gradeId;

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
        $('#subjectSelect').val(subjectId);

        loadChapterPositions('after-' + id);

        $('#formModal').modal('show');
    });
}


function resetChapterModal() {

    document.getElementById("id").value = '';
    document.getElementById("name").value = '';
    document.getElementById("slug").value = '';

    const sortOrder = document.getElementById("sortOrder");

    if (sortOrder) {
        sortOrder.value = '';
    }

    document.getElementById("gradeSelect").value = '';

    $('#subjectSelect').html(`
        <option value="">Chọn môn học</option>
    `);

    $('#positionSelect').html(`
        <option value="last">Hiển thị cuối cùng</option>
        <option value="first">Hiển thị đầu tiên</option>
    `);

    document.getElementById("chapterModalTitle").innerText =
        "Thêm chương học";

    document.querySelector('#formModal form').action =
        '/admin/chapters/store';

    document.querySelector('#formModal .alert-error')?.remove();
}


function openDeleteChapter(id) {
    document.getElementById("deleteLink").href = "/admin/chapters/delete/" + id;
    $('#deleteModal').modal('show');
}


// ======= CHAPTER AJAX SEARCH ========
(function () {

    const searchInput = document.getElementById("chapterSearch");
    const tbody       = document.getElementById("chapterTableBody");
    const pagination  = document.getElementById("chapterPagination");

    if (!searchInput || !tbody) return;

    searchInput.addEventListener("input", async function () {

        const keyword = this.value.trim();

        // Ẩn pagination khi search
        if (pagination) {
            pagination.style.display =
                keyword !== '' ? 'none' : 'flex';
        }

        // Nếu rỗng → reload page
        if (keyword === '') {
            location.reload();
            return;
        }

        try {

            const response = await fetch(
                '/admin/ajax/search-chapters?keyword='
                + encodeURIComponent(keyword)
            );

            const chapters = await response.json();

            let html = '';

            // Không có dữ liệu
            if (!chapters.length) {

                html = `
                    <tr>
                        <td colspan="6" class="text-center">
                            Không tìm thấy chương học
                        </td>
                    </tr>
                `;

                tbody.innerHTML = html;

                return;
            }

            chapters.forEach(item => {

                html += `
                    <tr>

                        <td>${item.chapterId}</td>

                        <td>${item.gradeName}</td>

                        <td>${item.subjectName}</td>

                        <td>${item.chapterName}</td>

                        <td>${item.slug}</td>

                        <td>
                            <div class="admin-actions">

                                <button
                                    class="action-btn btn-edit"

                                    data-id="${item.chapterId}"

                                    data-subject-id="${item.subjectId}"

                                    data-grade-id="${item.gradeId}"

                                    data-name="${item.chapterName}"

                                    data-slug="${item.slug}"

                                    data-sort-order="${item.sortOrder}"

                                    onclick="openEditChapter(this)">
                                    ✏
                                </button>

                                <button
                                    class="action-btn btn-delete"
                                    onclick="openDeleteChapter(${item.chapterId})">
                                    🗑
                                </button>

                            </div>
                        </td>

                    </tr>
                `;
            });

            tbody.innerHTML = html;

        } catch (error) {

            console.error(error);
        }

    });

})();



function injectFlashError(errorCode, page) {
    document.querySelector('#formModal .alert-error')?.remove();

    const messages = {
        category: {
            exists: 'Tên danh mục hoặc slug đã tồn tại!',
            empty: 'Tên danh mục không được để trống!',
            short: 'Tên danh mục quá ngắn!',
            long: 'Tên danh mục quá dài!',
        },

        grade: {
            name_exists: 'Tên khối lớp đã tồn tại!',
            slug_exists: 'Slug khối lớp đã tồn tại!'
        },

        chapter: {
            name_exists: 'Tên chương học đã tồn tại trong môn học này!',
            slug_exists: 'Slug chương học đã tồn tại trong môn học này!',
            sort_exists: 'Số thứ tự chương đã tồn tại trong môn học này!',
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



// =========================================================
// IMAGE PREVIEW
// =========================================================
(function () {
    const imgInput = document.getElementById('imageInput');
    if (!imgInput) return;

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
})();



// =========================================================
// LOAD SUBJECT THEO GRADE (chapter page)
// =========================================================
$(document).ready(function () {

    const isChapterPage = !!document.getElementById("chapterModalTitle");

    $('#gradeSelect').on('change', function () {

        let gradeId = $(this).val();

        $('#subjectSelect').html('<option>Đang tải...</option>');

        $.get('/admin/ajax/subjects?grade_id=' + gradeId, function (data) {

            let html = '<option value="">Chọn môn học</option>';

            data.forEach(item => {
                html += `<option value="${item.subjectId}">${item.subjectName}</option>`;
            });

            $('#subjectSelect').html(html);

            if (!isChapterPage) {
                $('#chapterSelect').html('<option value="">Chọn chương học</option>');
            }

            // Reset position khi đổi grade (chapter page)
            if (isChapterPage) {
                $('#positionSelect').html(`
                    <option value="last">Hiển thị cuối cùng</option>
                    <option value="first">Hiển thị đầu tiên</option>
                `);
            }
        });
    });

    if (isChapterPage) {
        $('#subjectSelect').on('change', function () {
            loadChapterPositions('last');
        });
    }
});


// =========================================================
// LOAD CHAPTER THEO SUBJECT
// =========================================================
$('#subjectSelect').change(function () {

    if (document.querySelector('.exam-create-card')) return;

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


// =========================================================
// QUESTION — restore cascade select
// =========================================================
(function restoreQuestionFlash() {

    const gradeSelect   = document.getElementById("gradeSelect");
    const subjectSelect = document.getElementById("subjectSelect");
    const chapterSelect = document.getElementById("chapterSelect");
    const lessonSelect  = document.getElementById("lessonSelect");

    // Chỉ chạy ở question create
    if (!document.querySelector('.question-create-card')) return;

    if (!gradeSelect || !subjectSelect || !chapterSelect) return;

    const savedGradeId   = gradeSelect.dataset.saved || '';
    const savedSubjectId = subjectSelect.dataset.saved || '';
    const savedChapterId = chapterSelect.dataset.saved || '';
    const savedLessonId  = lessonSelect?.dataset.saved || '';

    if (!savedGradeId) return;

    gradeSelect.value = savedGradeId;

    $.get('/admin/ajax/subjects?grade_id=' + savedGradeId, function (subjects) {

        let html = '<option value=""> Chọn môn học  </option>';

        subjects.forEach(s => {
            html += `
                <option value="${s.subjectId}">
                    ${s.subjectName}
                </option>
            `;
        });

        subjectSelect.innerHTML = html;

        if (savedSubjectId) {
            subjectSelect.value = savedSubjectId;
        }

        if (!savedSubjectId) return;

        $.get('/admin/ajax/chapters?subject_id=' + savedSubjectId, function (chapters) {

            let html2 = '<option value=""> Chọn chương học</option>';

            chapters.forEach(c => {
                html2 += `
                    <option value="${c.chapterId}">
                        ${c.chapterName}
                    </option>
                `;
            });

            chapterSelect.innerHTML = html2;

            if (savedChapterId) {
                chapterSelect.value = savedChapterId;
            }

            if (!savedChapterId || !lessonSelect) return;

            $.get('/admin/ajax/lessons?chapter_id=' + savedChapterId, function (lessons) {

                let html3 = '<option value="">  Chọn bài học  </option>';

                lessons.forEach(l => {
                    html3 += `
                        <option value="${l.lessonId}">
                            ${l.lessonName}
                        </option>
                    `;
                });

                lessonSelect.innerHTML = html3;

                if (savedLessonId) {
                    lessonSelect.value = savedLessonId;
                }
            });
        });
    });

})();



// ================= LESSON AJAX SEARCH =================
(function () {

    const searchInput = document.getElementById("lessonSearch");
    const tbody       = document.getElementById("lessonTableBody");
    const pagination  = document.getElementById("lessonPagination");

    if (!searchInput || !tbody) return;

    let timeout = null;

    searchInput.addEventListener("input", function () {

        clearTimeout(timeout);

        const keyword = this.value.trim();

        timeout = setTimeout(() => {

            // SEARCH RỖNG
            if (keyword === "") {

                if (pagination) {
                    pagination.style.display = "flex";
                }

                location.reload();
                return;
            }

            // Ẩn pagination
            if (pagination) {
                pagination.style.display = "none";
            }

            fetch(`/admin/ajax/search-lessons?keyword=${encodeURIComponent(keyword)}`)

                .then(res => res.json())

                .then(data => {

                    tbody.innerHTML = "";

                    // KHÔNG CÓ DATA
                    if (data.length === 0) {

                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center">
                                    Không tìm thấy bài học
                                </td>
                            </tr>
                        `;

                        return;
                    }

                    // RENDER DATA
                    data.forEach(l => {

                        tbody.innerHTML += `
                            <tr>

                                <td>${l.lessonId}</td>

                                <td>
                                    ${l.gradeName ?? ""}
                                </td>

                                <td>
                                    ${l.subjectName ?? ""}
                                </td>

                                <td>
                                    ${
                                        l.chapterName
                                        ? `Chương ${l.sortOrder ?? ""}: ${l.chapterName}`
                                        : `<span class="text-muted">Không có</span>`
                                    }
                                </td>

                                <td>
                                    Bài ${l.sortOrder ?? ""}: ${l.lessonName}
                                </td>

                                <td>
                                    ${l.slug ?? ""}
                                </td>

                                <td>
                                    ${
                                        Number(l.isActive) === 1
                                        ? `
                                            <span class="badge-status badge-active">
                                                Hiển thị
                                            </span>
                                        `
                                        : `
                                            <span class="badge-status badge-inactive">
                                                Ẩn
                                            </span>
                                        `
                                    }
                                </td>

                                <td>

                                    <div class="admin-actions">

                                        <a href="/admin/lessons/edit/${l.lessonId}"
                                           class="action-btn btn-edit">
                                            ✏
                                        </a>

                                        <button class="action-btn btn-delete"
                                                onclick="openDeleteLesson(${l.lessonId})">
                                            🗑
                                        </button>

                                    </div>

                                </td>

                            </tr>
                        `;
                    });

                })

                .catch(err => {
                    console.log(err);
                });

        }, 300);

    });

})();




// =========================================================
// POST
// =========================================================


bindSlug("post_title", "post_slug");

function openDeletePost(id) {

    document.getElementById("deleteLink").href =
        "/admin/posts/delete/" + id;

    $('#deleteModal').modal('show');
}


// ===== POST AJAX SEARCH =====
(function () {

    const searchInput = document.getElementById("postSearch");
    const tbody       = document.getElementById("postTableBody");
    const pagination  = document.getElementById("postPagination");

    if (!searchInput || !tbody) return;

    let timeout = null;

    searchInput.addEventListener("input", function () {

        clearTimeout(timeout);

        timeout = setTimeout(() => {

            const keyword = this.value.trim();

            // Ẩn pagination khi search
            if (pagination) {
                pagination.style.display =
                    keyword !== "" ? "none" : "flex";
            }

            fetch(`/admin/ajax/search-posts?keyword=${encodeURIComponent(keyword)}`)
                .then(res => res.json())
                .then(posts => {

                    tbody.innerHTML = "";

                    if (!posts.length) {

                        tbody.innerHTML = `
                            <tr>
                                <td colspan="8" class="text-center">
                                    Không tìm thấy bài viết
                                </td>
                            </tr>
                        `;

                        return;
                    }

                    posts.forEach(p => {

                        const statusBadge = p.isActive == 1
                            ? `<span class="badge-status badge-active">
                                    Hiển thị
                               </span>`
                            : `<span class="badge-status badge-inactive">
                                    Ẩn
                               </span>`;

                        const createdAt = new Date(p.createdAt);

                        const date =
                            createdAt.getDate().toString().padStart(2, '0')
                            + '/'
                            + (createdAt.getMonth() + 1).toString().padStart(2, '0')
                            + '/'
                            + createdAt.getFullYear();

                        tbody.innerHTML += `
                            <tr>

                                <td>${p.postId}</td>

                                <td>${p.categoryName ?? ''}</td>

                                <td>${p.title}</td>

                                <td>${p.slug}</td>

                                <td>${statusBadge}</td>

                                <td>${date}</td>

                                <td>${p.authorName ?? 'Admin'}</td>

                                <td>
                                    <div class="admin-actions">

                                        <a href="/admin/posts/edit/${p.postId}"
                                           class="action-btn btn-edit">
                                            ✏
                                        </a>

                                        <button class="action-btn btn-delete"
                                                onclick="openDeletePost(${p.postId})">
                                            🗑
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        `;
                    });

                });

        }, 300);

    });

})();



// =========================================================
// USER
// =========================================================
function openDeleteUser(id, role)
{
    document.getElementById("deleteLink").href =
        "/admin/users/delete/" + id;

    $('#deleteModal').modal('show');
}

// ======= USER SEARCH ========

(function () {

    const input = document.getElementById('userSearch');
    const tbody = document.getElementById('userTableBody');
    const pagination = document.getElementById('userPagination');

    if (!input || !tbody) return;

    let debounce;

    input.addEventListener('input', function () {

        clearTimeout(debounce);

        debounce = setTimeout(async () => {

            const keyword = this.value.trim();

            // HIỆN LẠI PAGINATION nếu rỗng
            if (keyword === '') {

                if (pagination) {
                    pagination.style.display = 'flex';
                }

                location.reload();
                return;
            }

            // ẨN PAGINATION
            if (pagination) {
                pagination.style.display = 'none';
            }

            try {

                const response = await fetch(
                    `/admin/ajax/search-users?keyword=${encodeURIComponent(keyword)}`
                );

                const users = await response.json();

                renderUsers(users);

            } catch (error) {

                console.error(error);
            }

        }, 250);

    });

    // =====================================================
    // RENDER USERS
    // =====================================================
    function renderUsers(users)
    {
        tbody.innerHTML = '';

        // EMPTY
        if (!users.length) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center">
                        Không tìm thấy người dùng
                    </td>
                </tr>
            `;

            return;
        }

        // LOOP
        users.forEach(u => {

            const avatar = u.avatar
                ? `
                    <img src="${u.avatar}"
                         alt="avatar"
                         class="user-avatar">
                `
                : `
                    <div class="user-avatar user-avatar-default avatar-fallback">
                        ${u.fullName.charAt(0).toUpperCase()}
                    </div>
                `;

            const roleBadge = u.role === 'admin'
                ? `
                    <span class="badge-role badge-admin">
                        Admin
                    </span>
                `
                : `
                    <span class="badge-role badge-user">
                        User
                    </span>
                `;

            const statusBadge = parseInt(u.status) === 1
                ? `
                    <span class="badge-status badge-active">
                        Hoạt động
                    </span>
                `
                : `
                    <span class="badge-status badge-inactive">
                        Đã khóa
                    </span>
                `;

            const createdAt = new Date(u.createdAt)
                .toLocaleDateString('vi-VN');

            tbody.innerHTML += `
                <tr>

                    <td>${u.userId}</td>

                    <td>
                        <div class="user-info">

                            ${avatar}

                            <span class="user-name">
                                ${escapeHtml(u.fullName)}
                            </span>

                        </div>
                    </td>

                    <td>
                        ${escapeHtml(u.email)}
                    </td>

                    <td>
                        ${roleBadge}
                    </td>

                    <td>
                        ${statusBadge}
                    </td>

                    <td>
                        ${createdAt}
                    </td>

                    <td>
                        <div class="admin-actions">

                            <a href="/admin/users/edit/${u.userId}"
                               class="action-btn btn-edit">
                                ✏
                            </a>

                            <button class="action-btn btn-delete"
                                    onclick="openDeleteUser(${u.userId}, '${u.role}')">
                                🗑
                            </button>

                        </div>
                    </td>

                </tr>
            `;
        });
    }

    // =====================================================
    // ESCAPE HTML
    // =====================================================
    function escapeHtml(text)
    {
        const div = document.createElement('div');

        div.innerText = text;

        return div.innerHTML;
    }

})();




// =========================================================
// LOAD SUBJECT / CHAPTER / LESSON cascade (các trang khác)
// =========================================================
$(document).ready(function () {

    if (document.getElementById("chapterModalTitle")) return;
    if (document.querySelector('.exam-create-card')) return;

    $('#gradeSelect').on('change', function () {
        let gradeId = $(this).val();
        $('#subjectSelect').html('<option>Đang tải...</option>');
        $.get('/admin/ajax/subjects?grade_id=' + gradeId, function (data) {
            let html = '<option value="">Chọn môn học</option>';
            data.forEach(item => {
                html += `<option value="${item.subjectId}">${item.subjectName}</option>`;
            });
            $('#subjectSelect').html(html);
            $('#chapterSelect').html('<option value="">Chọn chương học</option>');
        });
    });

    // LOAD CHAPTER THEO SUBJECT

    $('#subjectSelect').on('change', function () {
        let subjectId = $(this).val();
        $('#chapterSelect').html('<option>Đang tải...</option>');
        $.get('/admin/ajax/chapters?subject_id=' + subjectId, function (data) {
            let html = '<option value="">Chọn chương học</option>';
            data.forEach(item => {
                html += `<option value="${item.chapterId}">
                    Chương ${item.sortOrder}: ${item.chapterName}
                </option>`;
            });
            $('#chapterSelect').html(html);
            // Reset position khi đổi subject
            $('#lessonPositionSelect').html(`
                <option value="last">Hiển thị cuối cùng</option>
                <option value="first">Hiển thị đầu tiên</option>
            `);
        });
    });

    // LOAD LESSON THEO CHAPTER

    $('#chapterSelect').on('change', function () {
        if (document.getElementById('lessonPositionSelect')) {

            const chapterId = $(this).val();
            const currentLessonId = $(this).data('current-lesson-id');
            const isEditPage = currentLessonId !== undefined;

            if (chapterId) {
                if (isEditPage) {
                    // Trang EDIT → dùng hàm edit, lọc bài hiện tại ra
                    loadLessonPositionsForEdit(chapterId, currentLessonId, null);
                } else {
                    // Trang CREATE → dùng hàm create
                    loadLessonPositions('last');
                }
            }
            return;
        }

        // Các trang khác → load lessonSelect bình thường
        let chapterId = $(this).val();
        $('#lessonSelect').html('<option>Đang tải...</option>');
        $.get('/admin/ajax/lessons?chapter_id=' + chapterId, function (data) {
            let html = '<option value="">Chọn bài học</option>';
            data.forEach(item => {
                html += `<option value="${item.lessonId}">${item.lessonName}</option>`;
            });
            $('#lessonSelect').html(html);
        });
    });

});



// =========================================================
// LOAD EXAM THEO LESSON
// =========================================================
$('#lessonSelect').change(function () {

    let lessonId = $(this).val();

    $('#examSelect').html('<option>Đang tải...</option>');

    $.get('/admin/ajax/exams?lesson_id=' + lessonId, function (data) {

        let html = '<option value="">Chọn đề thi</option>';

        data.forEach(item => {

            html += `
                <option value="${item.examId}">
                    ${item.examTitle}
                </option>
            `;
        });

        $('#examSelect').html(html);

        // reset question
        $('#questionSelect').html(
            '<option value="">Chọn câu hỏi</option>'
        );
    });

});


// =========================================================
// LOAD QUESTION THEO EXAM
// =========================================================
$('#examSelect').change(function () {

    let examId = $(this).val();

    $('#questionSelect').html('<option>Đang tải...</option>');

    $.get('/admin/ajax/questions?exam_id=' + examId, function (data) {

        let html = '<option value="">Chọn câu hỏi</option>';

        data.forEach(item => {

            html += `
                <option value="${item.questionId}">
                    Câu ${item.questionId}
                </option>
            `;
        });

        $('#questionSelect').html(html);
    });

});




// =========================================================
// QUESTION
// =========================================================

function openDeleteQuestion(id) {
    document.getElementById("deleteLink").href =
        "/admin/questions/delete/" + id;
    $('#deleteModal').modal('show');
}

// =====================================================
// QUESTION SEARCH AJAX
// =====================================================

(function () {

    const input = document.getElementById('questionSearch');
    const tbody = document.getElementById('questionTableBody');
    const pagination = document.getElementById('questionPagination');

    if (!input || !tbody) return;

    let timeout = null;

    input.addEventListener('input', function () {

        clearTimeout(timeout);

        const keyword = this.value.trim();

        timeout = setTimeout(() => {

            // EMPTY
            if (keyword === '') {

                window.location.reload();

                return;
            }

            // HIDE PAGINATION
            if (pagination) {
                pagination.style.display = 'none';
            }

            fetch(`/admin/questions/search?keyword=${encodeURIComponent(keyword)}`)

                .then(res => res.json())

                .then(data => {

                    tbody.innerHTML = '';

                    // NOT FOUND
                    if (!data.length) {

                        tbody.innerHTML = `
                            <tr>
                                <td colspan="9" class="text-center">
                                    Không tìm thấy câu hỏi
                                </td>
                            </tr>
                        `;

                        return;
                    }

                    // RENDER
                    data.forEach(q => {

                        let levelHtml = '-';
                        if (q.level === 'knowledge') {
                            levelHtml = `<span class="level-badge level-knowledge">Nhận biết</span>`;
                        } else if (q.level === 'comprehension') {
                            levelHtml = `<span class="level-badge level-comprehension">Thông hiểu</span>`;
                        } else if (q.level === 'application') {
                            levelHtml = `<span class="level-badge level-application">Vận dụng</span>`;
                        }

                        let typeHtml = q.questionType === 'manual'
                            ? `<span class="type-badge type-manual">Thủ công</span>`
                            : `<span class="type-badge type-auto">Tự động</span>`;

                        // =========================
                        // CONTENT
                        // =========================

                        const cleanContent = stripHtml(q.content);

                        const excerpt = createExcerpt(
                            cleanContent,
                            keyword
                        );

                        const highlighted =
                            highlightKeyword(
                                excerpt,
                                keyword
                            );

                        tbody.innerHTML += `
                            <tr>

                                <td>${q.questionId}</td>

                                <td class="question-content-cell">
                                    ${highlighted}
                                </td>

                                <td>
                                    ${q.subjectName ?? '-'}
                                    ${q.gradeName ? `<div class="text-muted small">${q.gradeName}</div>` : ''}
                                </td>

                                <td>
                                    ${q.chapterName
                                        ? `<small class="text-muted">Chương ${q.chapterSortOrder ?? ''}: ${q.chapterName}</small>`
                                        : '-'}
                                </td>

                                <td>
                                    ${q.lessonName
                                        ? `Bài ${q.lessonSortOrder ?? ''}: ${q.lessonName}`
                                        : '-'}
                                </td>


                                <td>${levelHtml}</td>

                                <td>${typeHtml}</td>

                                <td>
                                    ${formatDate(q.createdAt)}
                                </td>

                                <td>
                                    <div class="admin-actions">

                                        <a href="/admin/questions/edit/${q.questionId}"
                                           class="action-btn btn-edit">
                                            ✏
                                        </a>

                                        <button
                                            class="action-btn btn-delete"
                                            onclick="openDeleteQuestion(${q.questionId})">
                                            🗑
                                        </button>

                                    </div>
                                </td>

                            </tr>
                        `;
                    });

                })

                .catch(err => {

                    console.error(
                        'Question search error:',
                        err
                    );
                });

        }, 300);

    });

    // =====================================================
    // REMOVE HTML
    // =====================================================

    function stripHtml(html) {

        const div = document.createElement('div');

        div.innerHTML = html;

        return div.textContent || div.innerText || '';
    }

    // =====================================================
    // FORMAT DATE
    // =====================================================

    function formatDate(dateString) {

        const date = new Date(dateString);

        return date.toLocaleDateString('vi-VN');
    }

    // =====================================================
    // CREATE EXCERPT
    // =====================================================

    function createExcerpt(text, keyword) {

        if (!text) return '';

        const clean = text.replace(/\s+/g, ' ').trim();

        const lowerText = clean.toLowerCase();
        const lowerKeyword = keyword.toLowerCase();

        const index = lowerText.indexOf(lowerKeyword);

        // Không tìm thấy
        if (index === -1) {

            return clean.length > 120
                ? clean.substring(0, 120) + '...'
                : clean;
        }

        // Lấy đoạn xung quanh keyword
        const radius = 45;

        const start = Math.max(0, index - radius);

        const end = Math.min(
            clean.length,
            index + keyword.length + radius
        );

        let excerpt = clean.substring(start, end);

        if (start > 0) {
            excerpt = '... ' + excerpt;
        }

        if (end < clean.length) {
            excerpt += ' ...';
        }

        return excerpt;
    }

    // =====================================================
    // HIGHLIGHT
    // =====================================================

    function highlightKeyword(text, keyword) {

        if (!keyword) return text;

        const regex = new RegExp(
            `(${escapeRegExp(keyword)})`,
            'gi'
        );

        return text.replace(
            regex,
            '<mark class="search-highlight">$1</mark>'
        );
    }

    // =====================================================
    // ESCAPE REGEX
    // =====================================================

    function escapeRegExp(string) {

        return string.replace(
            /[.*+?^${}()|[\]\\]/g,
            '\\$&'
        );
    }

})();



// =========================================================
// EXAM
// =========================================================

function openDeleteExam(id) {

    document.getElementById("deleteLink").href =
        "/admin/exams/delete/" + id;

    $('#deleteModal').modal('show');
}


// =============== EXAM SEARCH AJAX ======================
(function () {

    const searchInput = document.getElementById('examSearch');
    const tbody       = document.getElementById('examTableBody');
    const pagination  = document.getElementById('examPagination');

    if (!searchInput || !tbody) return;

    let timeout = null;

    searchInput.addEventListener('input', function () {

        clearTimeout(timeout);

        timeout = setTimeout(async () => {

            const keyword = this.value.trim();

            // =================================================
            // EMPTY => RELOAD PAGE CURRENT TAB
            // =================================================
            if (keyword === '') {

                if (pagination) {
                    pagination.style.display = 'block';
                }

                window.location.href = '/admin/exams';

                return;
            }

            try {

                const response = await fetch(
                    `/admin/ajax/search-exams?keyword=${encodeURIComponent(keyword)}`
                );

                if (!response.ok) {
                    throw new Error('HTTP error ' + response.status);
                }

                const exams = await response.json();

                console.log(exams);

                renderExamRows(exams);

                // Ẩn pagination khi search
                if (pagination) {
                    pagination.style.display = 'none';
                }

            } catch (error) {

                console.error(error);
            }

        }, 300);

    });

    // =====================================================
    // RENDER ROWS
    // =====================================================
    function renderExamRows(exams) {

        tbody.innerHTML = '';

        if (!exams.length) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="12" class="text-center">
                        Không tìm thấy đề thi
                    </td>
                </tr>
            `;

            return;
        }

        exams.forEach(exam => {

            let examType = `
                <span class="type-badge type-random">
                    Random
                </span>
            `;

            if (exam.examType === 'lesson') {

                examType = `
                    <span class="type-badge type-lesson">
                        Ôn luyện
                    </span>
                `;
            }

            if (exam.examType === 'thpt') {

                examType = `
                    <span class="type-badge type-thpt">
                        THPT
                    </span>
                `;
            }

            let generationType = exam.generationType === 'manual'
                ? `
                    <span class="badge-status badge-inactive">
                        Thủ công
                    </span>
                `
                : `
                    <span class="badge-status badge-active">
                        Tự động
                    </span>
                `;

            let status = exam.isActive == 1
                ? `
                    <span class="badge-status badge-active">
                        Hiển thị
                    </span>
                `
                : `
                    <span class="badge-status badge-inactive">
                        Ẩn
                    </span>
                `;

            tbody.innerHTML += `
                <tr>

                    <td>${exam.examId}</td>

                    <td>
                        <div class="font-weight-bold">
                            ${escapeHtml(exam.title)}
                        </div>

                        <div class="text-muted small">
                            ${escapeHtml(exam.slug)}
                        </div>
                    </td>

                    <td>
                        ${escapeHtml(exam.subjectName)}
                        <div class="text-muted small">${escapeHtml(exam.gradeName)}</div>
                    </td>

                    <td>
                        ${exam.lessonName
                            ? `<div class="text-muted small">Chương ${exam.chapterSortOrder ?? ''}: ${escapeHtml(exam.chapterName ?? '')}</div>
                            <div>Bài ${exam.lessonSortOrder ?? ''}: ${escapeHtml(exam.lessonName)}</div>`
                            : '<span class="text-muted">-</span>'
                        }
                    </td>

                    <td>${examType}</td>

                    <td>${generationType}</td>

                    <td>
                        ${exam.realTotalQuestions ?? exam.totalQuestions} câu
                    </td>

                    <td>${exam.duration} phút</td>

                    <td>${exam.viewCount || 0}</td>

                    <td>${status}</td>

                    <td>
                        ${exam.creatorName
                            ? `<span>${escapeHtml(exam.creatorName)}</span>`
                            : '<span class="text-muted">-</span>'}
                    </td>

                    <td>
                        <div class="admin-actions">

                            <a
                                href="/admin/exams/edit/${exam.examId}"
                                class="action-btn btn-edit">
                                ✏
                            </a>

                            <button
                                class="action-btn btn-delete"
                                onclick="openDeleteExam(${exam.examId})">
                                🗑
                            </button>

                        </div>
                    </td>

                </tr>
            `;
        });
    }

    // =====================================================
    // ESCAPE HTML
    // =====================================================
    function escapeHtml(text) {

        const div = document.createElement('div');

        div.textContent = text;

        return div.innerHTML;
    }

})();




// =========================================================
// RESULT AJAX SEARCH
// =========================================================

(function () {

    const input      = document.getElementById('resultSearch');
    const tbody      = document.getElementById('resultTableBody');
    const pagination = document.getElementById('resultPagination');

    if (!input || !tbody) return;

    let timeout = null;

    input.addEventListener('input', function () {

        clearTimeout(timeout);

        timeout = setTimeout(async () => {

            const keyword = input.value.trim();

            if (keyword === '') {
                location.reload();
                return;
            }

            try {

                const response = await fetch(
                    `/admin/results/search?keyword=${encodeURIComponent(keyword)}`
                );

                const data = await response.json();

                tbody.innerHTML = '';

                if (pagination) {
                    pagination.style.display = 'none';
                }

                if (!data.results || data.results.length === 0) {

                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="text-center">
                                Không tìm thấy kết quả
                            </td>
                        </tr>
                    `;

                    return;
                }

                data.results.forEach(r => {

                    // THỜI GIAN
                    const start = r.startTime ? new Date(r.startTime) : null;
                    const end   = r.endTime   ? new Date(r.endTime)   : null;

                    let timeText = '<span class="text-muted">Không có</span>';

                    if (start && end) {
                        const seconds       = Math.floor((end - start) / 1000);
                        const minutes       = Math.floor(seconds / 60);
                        const remainSeconds = seconds % 60;
                        timeText = `${minutes} phút ${remainSeconds} giây`;
                    }

                    // NGÀY LÀM
                    let dateText = '<span class="text-muted">Chưa hoàn thành</span>';

                    if (r.endTime) {
                        dateText = new Date(r.endTime).toLocaleString('vi-VN');
                    }

                    // MÔN HỌC
                    const subjectCell = r.subjectName
                        ? `${r.subjectName}
                           <div class="text-muted small">${r.gradeName ?? ''}</div>`
                        : '<span class="text-muted">-</span>';

                    // BÀI HỌC
                    const chapterPart = r.chapterName
                        ? `<div class="text-muted small">Chương ${r.chapterSortOrder}: ${r.chapterName}</div>`
                        : '';

                    const lessonCell = r.lessonName
                        ? `${chapterPart}Bài ${r.lessonSortOrder}: ${r.lessonName}`
                        : '<span class="text-muted">-</span>';

                    tbody.innerHTML += `
                        <tr>
                            <td>${r.resultId}</td>

                            <td>
                                ${r.fullName
                                    ? r.fullName
                                    : '<span class="text-muted">Ẩn danh</span>'}
                            </td>

                            <td>${subjectCell}</td>

                            <td>${lessonCell}</td>

                            <td>
                                ${r.examTitle
                                    ? r.examTitle
                                    : '<span class="text-muted">Không có đề</span>'}
                            </td>

                            <td>
                                <span class="score-badge">
                                    ${parseFloat(r.realScore ?? 0).toFixed(1)}/10
                                </span>
                            </td>

                            <td>
                                <span class="badge-result">
                                    ${r.totalCorrect}/${r.realTotalQuestions} đúng
                                </span>
                            </td>

                            <td>${timeText}</td>

                            <td>${dateText}</td>

                            <td>
                                <div class="admin-actions">
                                    <a href="/admin/results/show/${r.resultId}"
                                       class="action-btn btn-view"
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                });

            } catch (error) {

                console.error(error);
            }

        }, 400);

    });

})();