<?php

require_once ROOT . "/app/core/Model.php";

class Chapter extends Model
{
    // ===== HELPER =====

    private function extractChapterNumber($name)
    {
        // bỏ dấu tiếng Việt trước khi check
        $name = strtolower($name);

        $name = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $name);
        $name = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $name);
        $name = preg_replace('/[íìỉĩị]/u', 'i', $name);
        $name = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $name);
        $name = preg_replace('/[úùủũụưứừửữự]/u', 'u', $name);
        $name = preg_replace('/[ýỳỷỹỵ]/u', 'y', $name);
        $name = preg_replace('/đ/u', 'd', $name);

        // ⭐ FIX: match cả "chuong 1" và "chuong-1"
        if (preg_match('/chuong[\s-]*(\d+)/i', $name, $match)) {
            return 'chuong-' . $match[1];
        }

        return null;
    }

    public function toSlug($str)
    {
        // ⭐ ƯU TIÊN lấy dạng "chuong-x"
        $short = $this->extractChapterNumber($str);
        if ($short) return $short;

        // ===== fallback nếu không có chữ "Chương" =====
        $str = strtolower($str);

        $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
        $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
        $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
        $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
        $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
        $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
        $str = preg_replace('/đ/u', 'd', $str);

        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = preg_replace('/\s+/', '-', trim($str));

        return $str;
    }

    public function getNextSortOrder($subjectId)
    {
        $sql = "
            SELECT COALESCE(MAX(sortOrder), 0) + 1 AS nextOrder
            FROM chapters
            WHERE subjectId = ?
        ";

        $result = $this->fetch($sql, [$subjectId]);

        return $result['nextOrder'];
    }

    // =========== USER ===============

    public function getBySubject($subjectId)
    {
        $sql = "SELECT * FROM chapters 
                WHERE subjectId = ?
                ORDER BY sortOrder ASC";

        return $this->fetchAll($sql, [$subjectId]);
    }

    public function getById($chapterId)
    {
        $sql = "SELECT * FROM chapters WHERE chapterId = ?";
        return $this->fetch($sql, [$chapterId]);
    }

    public function getBySlug($slug, $subjectId)
    {
        $sql = "SELECT * FROM chapters WHERE slug = ? AND subjectId = ?";
        return $this->fetch($sql, [$slug, $subjectId]);
    }

    // =============== ADMIN  =================

    public function getAll()
    {
        $sql = "SELECT c.*, s.subjectName, g.gradeId, g.gradeName
                FROM chapters c
                JOIN subjects s ON c.subjectId = s.subjectId
                JOIN grades g ON s.gradeId = g.gradeId
                ORDER BY c.chapterId DESC";

        return $this->fetchAll($sql);
    }

    public function create($name, $slug, $subjectId, $sortOrder)
    {
        $sql = "
            INSERT INTO chapters (
                chapterName,
                slug,
                subjectId,
                sortOrder
            )
            VALUES (?, ?, ?, ?)
        ";

        return $this->execute($sql, [
            $name,
            $slug,
            $subjectId,
            $sortOrder
        ]);
    }

    public function update($id, $name, $slug, $subjectId, $sortOrder)
    {
        $sql = "
            UPDATE chapters
            SET
                chapterName = ?,
                slug = ?,
                subjectId = ?,
                sortOrder = ?
            WHERE chapterId = ?
        ";

        return $this->execute($sql, [
            $name,
            $slug,
            $subjectId,
            $sortOrder,
            $id
        ]);
    }

    public function delete($id)
    {
        return $this->execute("DELETE FROM chapters WHERE chapterId = ?", [$id]);
    }

    // check trùng tên, slug
    public function exists($name, $slug, $subjectId)
    {
        $sql = "SELECT * FROM chapters 
                WHERE subjectId = ?
                AND (LOWER(chapterName) = LOWER(?) OR slug = ?)";

        return !empty($this->fetchAll($sql, [$subjectId, $name, $slug]));
    }


    // check trùng sortOrder
    public function existsSortOrder($subjectId, $sortOrder, $ignoreId = null)
    {
        $sql = "
            SELECT *
            FROM chapters
            WHERE subjectId = ?
            AND sortOrder = ?
        ";

        $params = [$subjectId, $sortOrder];

        if ($ignoreId) {

            $sql .= " AND chapterId != ?";
            $params[] = $ignoreId;
        }

        return !empty($this->fetch($sql, $params));
    }

}