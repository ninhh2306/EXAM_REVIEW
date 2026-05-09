<?php

require_once ROOT . "/app/models/Post.php";
require_once ROOT . "/app/models/Category.php";

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

        // lấy bài viết
        $post = $postModel->getBySlug($postSlug);

        if (!$post) {
            die("Post not found");
        }

        // kiểm tra đúng category slug
        if ($post['categorySlug'] !== $categorySlug) {
            die("Category not match");
        }

        // lấy bài viết liên quan
        $relatedPosts = $postModel->getRelated(
            $post['categoryId'],
            $post['postId'],
            4
        );

        // author (nếu chưa có bảng users thì để tạm rỗng)
        $author = [
            'fullName' => 'PrepMaster',
            'avatar'   => null
        ];

        $this->view("posts/detail", [
            "post"         => $post,
            "relatedPosts" => $relatedPosts,
            "author"       => $author
        ]);
    }

    
}