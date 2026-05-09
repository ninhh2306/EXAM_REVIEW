<?php

require_once ROOT . "/app/core/Model.php";

class Lesson extends Model
{
    // ================= SLUG =================
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

        $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
        $str = preg_replace('/\s+/', '-', trim($str));

        return $str;
    }


    // ================= SORT ORDER =================
    public function getNextSortOrder($chapterId)
    {
        $sql = "
            SELECT COALESCE(MAX(sortOrder), 0) + 1 AS nextOrder
            FROM lessons
            WHERE chapterId = ?
        ";

        $result = $this->fetch($sql, [$chapterId]);

        return $result['nextOrder'];
    }


    // ================= ADMIN =================

    public function getBySubject($subjectId)
    {
        $sql = "
            SELECT 
                l.*,
                c.chapterName,
                c.slug as chapterSlug
            FROM lessons l

            LEFT JOIN chapters c
                ON l.chapterId = c.chapterId

            WHERE l.subjectId = ?

            ORDER BY
                c.chapterId ASC,
                l.sortOrder ASC
        ";

        return $this->fetchAll($sql, [$subjectId]);
    }


    public function getByChapterAndSlug($chapterSlug, $lessonSlug)
    {
        $sql = "
            SELECT 
                l.*,
                c.chapterName,
                c.slug AS chapterSlug
            FROM lessons l

            JOIN chapters c
                ON l.chapterId = c.chapterId

            WHERE c.slug = ?
            AND l.slug = ?

            LIMIT 1
        ";

        return $this->fetch($sql, [
            $chapterSlug,
            $lessonSlug
        ]);
    }

    public function getPaginate($limit, $offset)
    {
        $sql = "
            SELECT l.*, 
                   s.subjectName,
                   c.chapterName,
                   g.gradeName
            FROM lessons l
            JOIN subjects s ON l.subjectId = s.subjectId
            JOIN grades g ON s.gradeId = g.gradeId
            LEFT JOIN chapters c ON l.chapterId = c.chapterId
            ORDER BY l.lessonId DESC
            LIMIT $limit OFFSET $offset
        ";

        return $this->fetchAll($sql);
    }

    public function countAll()
    {
        $sql = "SELECT COUNT(*) as total FROM lessons";
        return $this->fetch($sql)['total'];
    }

    public function getById($id)
    {
        $sql = "
            SELECT l.*,
                s.gradeId
            FROM lessons l
            JOIN subjects s
                ON l.subjectId = s.subjectId
            WHERE l.lessonId = ?
        ";

        return $this->fetch($sql, [$id]);
    }

    public function create($data)
    {
        $sql = "
            INSERT INTO lessons (
                subjectId,
                chapterId,
                lessonName,
                slug,
                content,
                sortOrder,
                createdBy
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        return $this->execute($sql, [
        $data['subjectId'],
        $data['chapterId'],
        $data['lessonName'],
        $data['slug'],
        $data['content'],
        $data['sortOrder'],
        $data['createdBy']
    ]);
    }

    public function updateLesson($id, $data)
    {
        $sql = "
            UPDATE lessons
            SET subjectId = ?,
                chapterId = ?,
                lessonName = ?,
                slug = ?,
                content = ?,
                sortOrder = ?,
                createdBy = ?
            WHERE lessonId = ?
        ";

        return $this->execute($sql, [
            $data['subjectId'],
            $data['chapterId'],
            $data['lessonName'],
            $data['slug'],
            $data['content'],
            $data['sortOrder'],
            $data['createdBy'],
            $id
        ]);
    }

    public function delete($id)
    {
        return $this->execute(
            "DELETE FROM lessons WHERE lessonId = ?",
            [$id]
        );
    }

    
     public function existsSortOrder($chapterId, $sortOrder, $excludeId = null)
    {
        $sql = "
            SELECT lessonId FROM lessons
            WHERE chapterId = ?
            AND sortOrder = ?
        ";
        $params = [$chapterId, $sortOrder];

        if ($excludeId) {
            $sql .= " AND lessonId != ?";
            $params[] = $excludeId;
        }

        return !empty($this->fetch($sql, $params));
    }

    
    public function slugExists($slug, $chapterId, $excludeId = null)
    {
        $sql = "SELECT lessonId FROM lessons WHERE slug = ? AND chapterId = ?";
        $params = [$slug, $chapterId];

        if ($excludeId) {
            $sql .= " AND lessonId != ?";
            $params[] = $excludeId;
        }

        return !empty($this->fetch($sql, $params));
    }

    public function nameExists($name, $chapterId, $excludeId = null)
    {
        $sql = "
            SELECT lessonId FROM lessons
            WHERE LOWER(lessonName) = LOWER(?)
            AND chapterId = ?
        ";
        $params = [$name, $chapterId];

        if ($excludeId) {
            $sql .= " AND lessonId != ?";
            $params[] = $excludeId;
        }

        return !empty($this->fetch($sql, $params));
    }

   



}