<?php

require_once ROOT . "/app/core/Model.php";

class Category extends Model
{
    // ================= USER =================

    // Lấy tất cả category dùng cho menu
    public function getAll()
    {
        $sql = "SELECT * FROM category
                ORDER BY categoryId ASC";

        return $this->fetchAll($sql);
    }

    // Lấy theo slug (dùng cho /tin-tuc/{slug})
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM category WHERE slug = ?";

        return $this->fetch($sql, [$slug]);
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

        $str = strip_tags($str);
        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = preg_replace('/\s+/', '-', trim($str));
        $str = preg_replace('/-+/', '-', $str);

        return trim($str, '-');
    }


    public function validateName(&$name)
    {
        $name = trim($name);
        $name = strip_tags($name);
        $name = preg_replace('/\s+/u', ' ', $name);

        if ($name === '') {
            return 'empty';
        }

        if (mb_strlen($name) < 2) {
            return 'short';
        }

        if (mb_strlen($name) > 100) {
            return 'long';
        }

        return null;
    }


    public function getPaginate($limit, $offset)
    {
        $sql = "
            SELECT *
            FROM category

            ORDER BY categoryId DESC

            LIMIT $limit OFFSET $offset
        ";

        return $this->fetchAll($sql);
    }


    public function countAllAdmin()
    {
        $sql = "SELECT COUNT(*) as total FROM category";
        $result = $this->fetch($sql);

        return $result['total'];
    }


    public function find($id)
    {
        $sql = "SELECT * FROM category WHERE categoryId = ?";
        return $this->fetch($sql, [$id]);
    }

    public function create($name, $slug, $description)
    {
        $sql = "INSERT INTO category (
                    categoryName,
                    slug,
                    description
                )
                VALUES (?, ?, ?)";

        return $this->execute($sql, [
            $name,
            $slug,
            $description
        ]);
    }

    public function update($id, $name, $slug, $description)
    {
        $sql = "UPDATE category 
                SET categoryName = ?,
                    slug = ?,
                    description = ?
                WHERE categoryId = ?";

        return $this->execute($sql, [
            $name,
            $slug,
            $description,
            $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM category WHERE categoryId = ?";
        return $this->execute($sql, [$id]);
    }

    public function hasPosts($categoryId)
    {
        $sql = "SELECT 1 FROM posts WHERE categoryId = ? LIMIT 1";
        return !empty($this->fetch($sql, [$categoryId]));
    }

    // check trùng khi thêm
    public function existsFull($name, $slug)
    {
        $sql = "SELECT 1 FROM category 
                WHERE LOWER(categoryName) = LOWER(?) OR slug = ?
                LIMIT 1";

        return !empty($this->fetch($sql, [$name, $slug]));
    }

    // check trùng khi update
    public function existsFullExcept($name, $slug, $id)
    {
        $sql = "SELECT 1 FROM category 
                WHERE (LOWER(categoryName) = LOWER(?) OR slug = ?)
                AND categoryId != ?
                LIMIT 1";

        return !empty($this->fetch($sql, [$name, $slug, $id]));
    }


    public function search($keyword)
    {
        $sql = "
            SELECT *
            FROM category
            WHERE
                categoryName LIKE ?
                OR slug LIKE ?
            ORDER BY categoryId DESC
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }


}