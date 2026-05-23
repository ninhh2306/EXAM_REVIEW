<?php

require_once ROOT . "/app/models/Post.php";
require_once ROOT . "/app/models/Category.php";
require_once ROOT . "/app/models/User.php";

class PostController extends Controller
{
    public function index()
    {
        $postModel = new Post();
        $posts = $postModel->getAll();

        $this->view("posts/index", [
            "posts" => $posts,
            "title" => "Tin tức",
            "description" => "Cập nhật tin tức mới nhất"
        ]);
    }

    public function category($categorySlug)
    {
        $postModel = new Post();
        $categoryModel = new Category();

        $category = $categoryModel->getBySlug($categorySlug);

        if (!$category) {
            die("Category not found");
        }

        $posts = $postModel->getByCategorySlug($categorySlug);

        $this->view("posts/index", [
            "posts"       => $posts,
            "title"       => $category['categoryName'],
            "description" => $category['description']
        ]);
    }

    public function show($categorySlug, $postSlug)
    {
        $postModel = new Post();

        // Lấy bài viết theo cả slug bài VÀ slug category
        $post = $postModel->getBySlugAndCategory($postSlug, $categorySlug);

        if (!$post) {
            $this->view('errors/404');
            return;
        }

        // Không cần check category nữa vì đã lọc trong query

        $relatedPosts = $postModel->getRelated($post['categoryId'], $post['postId'], 4);

        $author = [
            'fullName' => $post['authorName'] ?? 'Admin',
            'avatar'   => $post['authorAvatar'] ?? null
        ];

        $this->view("posts/detail", [
            "post"         => $post,
            "relatedPosts" => $relatedPosts,
            "author"       => $author
        ]);
    }


}