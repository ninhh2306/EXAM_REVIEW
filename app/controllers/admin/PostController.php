<?php

require_once ROOT . '/app/models/Post.php';
require_once ROOT . '/app/models/Category.php';

class PostController extends Controller
{
    // ================= INDEX =================
    public function index()
    {
        $postModel = new Post();

        $perTab     = 5;
        $total      = $postModel->countAllAdmin();
        $tabCount   = max(1, (int) ceil($total / $perTab));
        $currentTab = max(1, min($tabCount, (int)($_GET['tab'] ?? 1)));
        $offset     = ($currentTab - 1) * $perTab;

        $posts = $postModel->getPaginate($perTab, $offset);

        $this->viewAdmin('posts/index', [
            'title'      => 'Danh sách Bài viết',
            'posts'      => $posts,
            'tabCount'   => $tabCount,
            'currentTab' => $currentTab,
        ]);
    }

    // ================= CREATE =================
    public function create()
    {
        $categoryModel = new Category();

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_old']
        );

        $this->viewAdmin('posts/create', [
            'title'       => 'Thêm bài viết',
            'categories'  => $categoryModel->getAll(),
            'flashError'  => $flashError,
            'flashOld'    => $flashOld
        ]);
    }

    // ================= STORE =================
    public function store()
    {
        $postModel = new Post();

        $title      = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['categoryId'] ?? 0);
        $content    = $_POST['content'] ?? '';
        $isActive   = isset($_POST['isActive']) ? 1 : 0;

        $slugInput = trim($_POST['slug'] ?? '');

        $titleError   = $postModel->validateTitle($title);
        $contentError = $postModel->validateContent($content);

        $slug = $slugInput
            ? $postModel->toSlug($slugInput)
            : $postModel->toSlug($title);

        $old = [
            'categoryId' => $categoryId,
            'title'      => $title,
            'slug'       => $slugInput,
            'content'    => $content,
            'isActive'   => $isActive,
        ];

        $_SESSION['flash_old'] = $old;

        // EMPTY
        if (
            empty($categoryId) ||
            empty($title)
        ) {

            $_SESSION['flash_error'] = 'empty_fields';

            header("Location: /admin/posts/create");
            exit;
        }

        // TITLE
        if ($titleError) {

            $_SESSION['flash_error'] = $titleError;

            header("Location: /admin/posts/create");
            exit;
        }

        // CONTENT
        if ($contentError) {

            $_SESSION['flash_error'] = $contentError;

            header("Location: /admin/posts/create");
            exit;
        }

        // EXISTS
        if ($postModel->exists($categoryId, $title, $slug)) { 

            $_SESSION['flash_error'] = 'exists';

            header("Location: /admin/posts/create");
            exit;
        }

        // upload image
        $thumbnail = null;

        if (!empty($_FILES['thumbnail']['name'])) {

            $fileName = time() . '_' . $_FILES['thumbnail']['name'];

            move_uploaded_file(
                $_FILES['thumbnail']['tmp_name'],
                ROOT . '/public/images/posts/' . $fileName
            );

            $thumbnail = '/images/posts/' . $fileName;
        }

        $plainText = strip_tags($content);

        $excerpt = mb_substr($plainText, 0, 160);

        $postModel->createPost([
            'categoryId' => $categoryId,
            'title'      => $title,
            'slug'       => $slug,
            'thumbnail'  => $thumbnail,
            'excerpt'    => $excerpt,
            'content'    => $content,
            'isActive'   => $isActive,
            'createdBy'  => $_SESSION['user']['userId'] ?? null,
        ]);

        unset($_SESSION['flash_old']);

        header("Location: /admin/posts?success=created");
        exit;
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $postModel = new Post();

        $categoryModel = new Category();

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashOld   = $_SESSION['flash_old'] ?? [];

        unset(
            $_SESSION['flash_error'],
            $_SESSION['flash_old']
        );

        $post = $postModel->find($id);

        $this->viewAdmin('posts/edit', [
            'title'       => 'Cập nhật bài viết',
            'post'        => $post,
            'categories'  => $categoryModel->getAll(),
            'flashError'  => $flashError,
            'flashOld'    => $flashOld
        ]);
    }

    // ================= UPDATE =================
    public function update()
    {
        $postModel = new Post();

        $id = (int)($_POST['id'] ?? 0);

        $title      = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['categoryId'] ?? 0);
        $content    = $_POST['content'] ?? '';
        $isActive   = isset($_POST['isActive']) ? 1 : 0;

        $slugInput = trim($_POST['slug'] ?? '');

        $slug = $slugInput
            ? $postModel->toSlug($slugInput)
            : $postModel->toSlug($title);

        // validate
        $titleError   = $postModel->validateTitle($title);
        $contentError = $postModel->validateContent($content);

        // old data
        $old = [
            'categoryId' => $categoryId,
            'title'      => $title,
            'slug'       => $slugInput,
            'content'    => $content,
            'isActive'   => $isActive
        ];

        $_SESSION['flash_old'] = $old;

        // EMPTY
        if (
            empty($categoryId) ||
            empty($title)
        ) {

            $_SESSION['flash_error'] = 'empty_fields';

            header("Location: /admin/posts/edit/$id");
            exit;
        }

        // TITLE
        if ($titleError) {

            $_SESSION['flash_error'] = $titleError;

            header("Location: /admin/posts/edit/$id");
            exit;
        }

        // CONTENT
        if ($contentError) {

            $_SESSION['flash_error'] = $contentError;

            header("Location: /admin/posts/edit/$id");
            exit;
        }

        // EXISTS
        if ($postModel->exists($categoryId, $title, $slug, $id)) {

            $_SESSION['flash_error'] = 'exists';

            header("Location: /admin/posts/edit/$id");
            exit;
        }

        $post = $postModel->find($id);

        $thumbnail = $post['thumbnail'];

        // upload new image
        if (!empty($_FILES['thumbnail']['name'])) {

            $fileName = time() . '_' . $_FILES['thumbnail']['name'];

            move_uploaded_file(
                $_FILES['thumbnail']['tmp_name'],
                ROOT . '/public/images/posts/' . $fileName
            );

            $thumbnail = '/images/posts/' . $fileName;
        }

        // excerpt
        $plainText = strip_tags($content);

        $excerpt = mb_substr(trim($plainText), 0, 160);

        $postModel->updatePost($id, [
            'categoryId' => $categoryId,
            'title'      => $title,
            'slug'       => $slug,
            'thumbnail'  => $thumbnail,
            'excerpt'    => $excerpt,
            'content'    => $content,
            'isActive'   => $isActive
        ]);

        unset($_SESSION['flash_old']);

        header("Location: /admin/posts?success=updated");
        exit;
    }


    // ================= DELETE =================
    public function delete($id)
    {
        $postModel = new Post();

        $post = $postModel->find($id);

        if (!$post) {

            header("Location: /admin/posts");
            exit;
        }

        // xóa ảnh nếu có
        if (!empty($post['thumbnail'])) {

            $imagePath = ROOT . '/public' . $post['thumbnail'];

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // xóa bài viết
        $postModel->deletePost($id);

        header("Location: /admin/posts?success=deleted");
        exit;
    }


    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');

        $postModel = new Post();

        if ($keyword === '') {

            $posts = $postModel->getPaginate(999, 0);

        } else {

            $posts = $postModel->searchPosts($keyword);
        }

        if (empty($posts)) {

            echo '
            <tr>
                <td colspan="7" class="text-center">
                    Không có bài viết nào
                </td>
            </tr>
            ';

            return;
        }

        foreach ($posts as $p) {
            ?>

            <tr>

                <td><?= $p['postId'] ?></td>

                <td><?= htmlspecialchars($p['categoryName']) ?></td>

                <td><?= htmlspecialchars($p['title']) ?></td>

                <td><?= htmlspecialchars($p['slug']) ?></td>

                <td>
                    <?= date('d/m/Y', strtotime($p['createdAt'])) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p['authorName'] ?? 'Admin') ?>
                </td>

                <td>

                    <div class="admin-actions">

                        <a href="/admin/posts/edit/<?= $p['postId'] ?>"
                        class="action-btn btn-edit">
                            ✏
                        </a>

                        <button class="action-btn btn-delete"
                                onclick="openDeletePost(<?= $p['postId'] ?>)">
                            🗑
                        </button>

                    </div>

                </td>

            </tr>

            <?php
        }
    }


}