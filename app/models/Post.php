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


    public function getBySlugAndCategory($slug, $categorySlug)
    {
        $sql = "
            SELECT 
                p.*,
                c.categoryName,
                c.slug as categorySlug,
                u.fullName as authorName,
                u.avatar as authorAvatar
            FROM posts p
            JOIN category c ON p.categoryId = c.categoryId
            LEFT JOIN users u ON p.createdBy = u.userId
            WHERE p.slug = ?
            AND c.slug = ?
            AND p.isActive = 1
        ";

        return $this->fetch($sql, [$slug, $categorySlug]);
    }

    // Lấy chi tiết bài
    public function getBySlug($slug)
    {
        $sql = "
            SELECT 
                p.*,
                c.categoryName,
                c.slug as categorySlug,

                u.fullName as authorName,
                u.avatar as authorAvatar

            FROM posts p

            JOIN category c
                ON p.categoryId = c.categoryId

            LEFT JOIN users u
                ON p.createdBy = u.userId

            WHERE p.slug = ?
            AND p.isActive = 1
        ";

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
                ORDER BY p.createdAt DESC
                LIMIT $limit";

        return $this->fetchAll($sql, [$categoryId, $excludePostId]);
    }



    // ================= ADMIN =================

        public function toSlug($str)
    {
        $str = strtolower($str);

        $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
        $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
        $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
        $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
        $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
        $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
        $str = preg_replace('/đ/u', 'd', $str);

        // bỏ html
        $str = strip_tags($str);

        // bỏ ký tự đặc biệt
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);

        // space => -
        $str = preg_replace('/\s+/', '-', trim($str));

        // xóa --
        $str = preg_replace('/-+/', '-', $str);

        return trim($str, '-');
    }


    public function validateTitle(&$title)
    {
        $title = trim($title);

        $title = strip_tags($title);

        $title = preg_replace('/\s+/u', ' ', $title);

        if ($title === '') {
            return 'Tiêu đề bài viết không được để trống!';
        }

        if (mb_strlen($title) < 5) {
            return 'Tiêu đề bài viết quá ngắn!';
        }

        if (mb_strlen($title) > 255) {
            return 'Tiêu đề bài viết quá dài!';
        }

        return null;
    }


    public function validateContent($content)
    {
        $plain = strip_tags($content);

        $plain = html_entity_decode($plain);

        $plain = str_replace("\xC2\xA0", ' ', $plain);

        $plain = trim($plain);

        if ($plain === '') {
            return 'Nội dung bài viết không được để trống!';
        }

        if (mb_strlen($plain) < 20) {
            return 'Nội dung bài viết quá ngắn!';
        }

        return null;
    }

    public function getPaginate($limit, $offset)
    {
        $sql = "
            SELECT 
                p.*,
                c.categoryName,
                u.fullName as authorName

            FROM posts p

            LEFT JOIN category c
                ON p.categoryId = c.categoryId

            LEFT JOIN users u
                ON p.createdBy = u.userId

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
    public function exists($categoryId, $title, $slug, $id = null)
    {
        $sql = "
            SELECT *
            FROM posts
            WHERE categoryId = ?
            AND (
                title = ?
                OR slug = ?
            )
        ";

        $params = [$categoryId, $title, $slug];

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
            $data['isActive']
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
                content = ?,
                isActive = ?
            WHERE postId = ?
        ";

        return $this->execute($sql, [
            $data['categoryId'],
            $data['title'],
            $data['slug'],
            $data['thumbnail'],
            $data['excerpt'],
            $data['content'],
            $data['isActive'],
            $id
        ]);
    }

    // delete
    public function deletePost($id)
    {
        $sql = "DELETE FROM posts WHERE postId = ?";

        return $this->execute($sql, [$id]);
    }



    // ================= SEARCH =================
    public function searchPosts($keyword)
    {
        $sql = "
            SELECT 
                p.*,
                c.categoryName,
                c.slug as categorySlug,
                u.fullName as authorName

            FROM posts p

            LEFT JOIN category c
                ON p.categoryId = c.categoryId

            LEFT JOIN users u
                ON p.createdBy = u.userId

            WHERE
                p.isActive = 1

                AND (
                    p.title LIKE ?
                    OR p.slug LIKE ?
                    OR u.fullName LIKE ?
                )

            ORDER BY p.postId DESC
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }


    public function searchAdminPosts($keyword)
    {
        $sql = "
            SELECT 
                p.*,
                c.categoryName,
                u.fullName as authorName

            FROM posts p

            LEFT JOIN category c
                ON p.categoryId = c.categoryId

            LEFT JOIN users u
                ON p.createdBy = u.userId

            WHERE
                p.title LIKE ?
                OR p.slug LIKE ?
                OR c.categoryName LIKE ?
                OR u.fullName LIKE ?

            ORDER BY p.postId DESC

            LIMIT 100
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }
    
}