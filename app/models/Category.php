<?php

require_once ROOT . "/app/core/Model.php";

class Category extends Model
{
    // ================= USER =================

    // Lấy tất cả category dùng cho menu
    public function getAll()
    {
        $sql = "SELECT * FROM category 
                WHERE isActive = 1
                ORDER BY categoryId DESC";

        return $this->fetchAll($sql);
    }

    // Lấy theo slug (dùng cho /tin-tuc/{slug})
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM category WHERE slug = ? AND isActive = 1";
        return $this->fetch($sql, [$slug]);
    }

    // ================= ADMIN =================

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
}