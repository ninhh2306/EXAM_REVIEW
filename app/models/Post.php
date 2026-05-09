<?php

require_once ROOT . "/app/core/Model.php";

class Post extends Model
{
    // ================= USER =================

    // Lấy danh sách bài theo category slug
    public function getByCategorySlug($slug)
    {
        $sql = "SELECT p.*, c.categoryName, c.slug as categorySlug
                FROM posts p
                JOIN category c ON p.categoryId = c.categoryId
                WHERE c.slug = ? 
                AND p.isActive = 1
                ORDER BY p.createdAt DESC";

        return $this->fetchAll($sql, [$slug]);
    }

    // Lấy tất cả bài (dùng cho /tin-tuc)
    public function getAll()
    {
        $sql = "SELECT p.*, c.categoryName, c.slug as categorySlug
                FROM posts p
                JOIN category c ON p.categoryId = c.categoryId
                WHERE p.isActive = 1
                ORDER BY p.createdAt DESC";

        return $this->fetchAll($sql);
    }

    // Lấy chi tiết bài
    public function getBySlug($slug)
    {
        $sql = "SELECT p.*, c.categoryName, c.slug as categorySlug
                FROM posts p
                JOIN category c ON p.categoryId = c.categoryId
                WHERE p.slug = ? AND p.isActive = 1";

        return $this->fetch($sql, [$slug]);
    }


    // Lấy bài viết liên quan cùng danh mục (trừ bài hiện tại)
    public function getRelated($categoryId, $excludePostId, $limit = 4)
    {
        $limit = (int)$limit;

        $sql = "SELECT p.*, c.categoryName, c.slug as categorySlug
                FROM posts p
                JOIN category c ON p.categoryId = c.categoryId
                WHERE p.categoryId = ?
                AND p.postId != ?
                AND p.isActive = 1
                ORDER BY p.createdAt DESC
                LIMIT $limit";

        return $this->fetchAll($sql, [$categoryId, $excludePostId]);
    }



    // ================= ADMIN =================

    public function getPaginate($limit, $offset)
    {
        $sql = "
            SELECT p.*, c.categoryName
            FROM posts p
            LEFT JOIN category c ON p.categoryId = c.categoryId
            ORDER BY p.postId DESC
            LIMIT $limit OFFSET $offset
        ";

        return $this->fetchAll($sql);
    }

    // count
    public function countAllAdmin()
    {
        $sql = "SELECT COUNT(*) as total FROM posts";

        $result = $this->fetch($sql);

        return $result['total'];
    }

    // find by id
    public function find($id)
    {
        $sql = "SELECT * FROM posts WHERE postId = ?";

        return $this->fetch($sql, [$id]);
    }

    // check exists
    public function exists($title, $slug, $id = null)
    {
        $sql = "
            SELECT * FROM posts
            WHERE (title = ? OR slug = ?)
        ";

        $params = [$title, $slug];

        if ($id) {
            $sql .= " AND postId != ?";
            $params[] = $id;
        }

        return $this->fetch($sql, $params);
    }

    // create
    public function createPost($data)
    {
        $sql = "
            INSERT INTO posts (
                categoryId,
                title,
                slug,
                thumbnail,
                excerpt,
                content,
                createdBy,
                isActive
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        return $this->execute($sql, [
            $data['categoryId'],
            $data['title'],
            $data['slug'],
            $data['thumbnail'],
            $data['excerpt'],
            $data['content'],
            $data['createdBy'],
            1
        ]);
    }

    // update
    public function updatePost($id, $data)
    {
        $sql = "
            UPDATE posts
            SET categoryId = ?,
                title = ?,
                slug = ?,
                thumbnail = ?,
                excerpt = ?,
                content = ?
            WHERE postId = ?
        ";

        return $this->execute($sql, [
            $data['categoryId'],
            $data['title'],
            $data['slug'],
            $data['thumbnail'],
            $data['excerpt'],
            $data['content'],
            $id
        ]);
    }

    // delete
    public function deletePost($id)
    {
        $sql = "DELETE FROM posts WHERE postId = ?";

        return $this->execute($sql, [$id]);
    }

}