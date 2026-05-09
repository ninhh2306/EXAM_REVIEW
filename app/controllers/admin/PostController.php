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

        $categories = $categoryModel->getAll();

        $this->viewAdmin('posts/create', [
            'title' => 'Thêm bài viết',
            'categories' => $categories
        ]);
    }

    // ================= STORE =================
    public function store()
    {
        $postModel = new Post();

        $title = trim($_POST['title']);
        $slug = trim($_POST['slug']);

        if ($postModel->exists($title, $slug)) {

            header("Location: /admin/posts/create?error=exists");
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

        // excerpt auto
        $plainText = strip_tags($_POST['content']);

        $excerpt = mb_substr($plainText, 0, 160);

        $postModel->createPost([
            'categoryId' => $_POST['categoryId'],
            'title' => $title,
            'slug' => $slug,
            'thumbnail' => $thumbnail,
            'excerpt' => $excerpt,
            'content' => $_POST['content'],
            'createdBy' => $_SESSION['user']['userId']
        ]);

        header("Location: /admin/posts?success=created");
        exit;
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $postModel = new Post();

        $categoryModel = new Category();

        $post = $postModel->find($id);

        $categories = $categoryModel->getAll();

        $this->viewAdmin('posts/edit', [
            'title' => 'Cập nhật bài viết',
            'post' => $post,
            'categories' => $categories
        ]);
    }

    // ================= UPDATE =================
    public function update()
    {
        $postModel = new Post();

        $id = $_POST['id'];

        if ($postModel->exists(
            $_POST['title'],
            $_POST['slug'],
            $id
        )) {

            header("Location: /admin/posts/edit/$id?error=exists");
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

        $plainText = strip_tags($_POST['content']);

        $excerpt = mb_substr($plainText, 0, 160);

        $postModel->updatePost($id, [
            'categoryId' => $_POST['categoryId'],
            'title' => $_POST['title'],
            'slug' => $_POST['slug'],
            'thumbnail' => $thumbnail,
            'excerpt' => $excerpt,
            'content' => $_POST['content']
        ]);

        header("Location: /admin/posts?success=updated");
        exit;
    }

    // ================= DELETE =================
    public function delete($id)
    {
        $postModel = new Post();

        $postModel->deletePost($id);

        header("Location: /admin/posts?success=deleted");
        exit;
    }


    // ================= SHOW (chi tiết bài viết - user) =================
    public function show($categorySlug, $postSlug)
    {
        $postModel = new Post();

        // Lấy chi tiết bài viết kèm category
        $post = $postModel->getBySlug($postSlug);

        // 404 nếu không tìm thấy hoặc sai category
        if (!$post || $post['categorySlug'] !== $categorySlug) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Không tìm thấy bài viết']);
            return;
        }

        // Lấy thông tin tác giả
        require_once ROOT . '/app/models/User.php';
        $userModel = new User();
        $author = $userModel->find($post['createdBy']);

        // Lấy bài viết liên quan cùng danh mục
        $relatedPosts = $postModel->getRelated(
            $post['categoryId'],
            $post['postId'],
            4
        );


        $this->view('user/posts/show', [
            'title'        => $post['title'],
            'post'         => $post,
            'author'       => $author,
            'relatedPosts' => $relatedPosts,
        ]);
    }


}