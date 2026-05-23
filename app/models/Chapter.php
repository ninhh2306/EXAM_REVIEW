<?php

require_once ROOT . "/app/core/Model.php";

class Chapter extends Model
{

    public function toSlug($str)
    {
        $str = mb_strtolower(trim($str), 'UTF-8');

        $str = preg_replace('/[áàảãạăắằẳẵặâấầẩẫậ]/u', 'a', $str);
        $str = preg_replace('/[éèẻẽẹêếềểễệ]/u', 'e', $str);
        $str = preg_replace('/[íìỉĩị]/u', 'i', $str);
        $str = preg_replace('/[óòỏõọôốồổỗộơớờởỡợ]/u', 'o', $str);
        $str = preg_replace('/[úùủũụưứừửữự]/u', 'u', $str);
        $str = preg_replace('/[ýỳỷỹỵ]/u', 'y', $str);
        $str = preg_replace('/đ/u', 'd', $str);

        $str = preg_replace('/[^a-z0-9\s-]/u', '', $str);

        $str = preg_replace('/[\s-]+/', '-', $str);

        return trim($str, '-');
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
        $sql = "
            SELECT *
            FROM chapters
            WHERE subjectId = ?
            ORDER BY sortOrder ASC
        ";

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
        $sql = "
            SELECT 
                c.*, 
                s.subjectName, 
                g.gradeId, 
                g.gradeName

            FROM chapters c

            JOIN subjects s 
                ON c.subjectId = s.subjectId

            JOIN grades g 
                ON s.gradeId = g.gradeId

            ORDER BY c.chapterId DESC
        ";

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

    

    // CHECK NAME
    public function existsName($name, $subjectId, $ignoreId = null)
    {
        $sql = "SELECT 1 FROM chapters
                WHERE subjectId = ?
                AND LOWER(chapterName) = LOWER(?)";

        $params = [$subjectId, $name];

        if ($ignoreId) {
            $sql .= " AND chapterId != ?";
            $params[] = $ignoreId;
        }

        $sql .= " LIMIT 1";

        return !empty($this->fetch($sql, $params));
    }


    public function getMaxSortOrder($subjectId)
    {
        $sql = "
            SELECT MAX(sortOrder)
            FROM chapters
            WHERE subjectId = ?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$subjectId]);

        return (int)$stmt->fetchColumn();
    }


    public function getSortOrder($chapterId, $subjectId)
    {
        $sql = "
            SELECT sortOrder
            FROM chapters
            WHERE chapterId = ?
            AND subjectId = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $chapterId,
            $subjectId
        ]);

        return (int)$stmt->fetchColumn();
    }


    public function increaseSortOrders($subjectId, $fromSort, $excludeId = null)
    {
        // Bước 1: Tăng lên một offset lớn để tránh duplicate tạm thời
        $sql1 = "
            UPDATE chapters
            SET sortOrder = sortOrder + 10000
            WHERE subjectId = ?
            AND sortOrder >= ?
        ";
        $params1 = [$subjectId, $fromSort];
        if ($excludeId) {
            $sql1 .= " AND chapterId != ?";
            $params1[] = $excludeId;
        }
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute($params1);

        // Bước 2: Trả về giá trị thực (offset - 10000 + 1)
        $sql2 = "
            UPDATE chapters
            SET sortOrder = sortOrder - 9999
            WHERE subjectId = ?
            AND sortOrder >= ?
        ";
        $params2 = [$subjectId, $fromSort + 10000];
        if ($excludeId) {
            $sql2 .= " AND chapterId != ?";
            $params2[] = $excludeId;
        }
        $stmt2 = $this->db->prepare($sql2);
        return $stmt2->execute($params2);
    }


    public function decreaseSortOrders($subjectId, $fromSort)
    {
        // Bước 1: Tăng lên offset lớn trước
        $sql1 = "
            UPDATE chapters
            SET sortOrder = sortOrder + 10000
            WHERE subjectId = ?
            AND sortOrder > ?
            ORDER BY sortOrder ASC
        ";
        $stmt1 = $this->db->prepare($sql1);
        $stmt1->execute([$subjectId, $fromSort]);

        // Bước 2: Trừ đi (offset + 1) để giảm 1 so với giá trị gốc
        $sql2 = "
            UPDATE chapters
            SET sortOrder = sortOrder - 10001
            WHERE subjectId = ?
            AND sortOrder > ?
            ORDER BY sortOrder ASC
        ";
        $stmt2 = $this->db->prepare($sql2);
        return $stmt2->execute([$subjectId, $fromSort + 10000]);
    }


    public function getPrevChapter($subjectId, $sortOrder)
    {
        $sql = "
            SELECT chapterId
            FROM chapters
            WHERE subjectId = ?
            AND sortOrder < ?
            ORDER BY sortOrder DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $subjectId,
            $sortOrder
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function reorderSortOrders($subjectId)
    {
        // Lấy tất cả chapters theo thứ tự hiện tại
        $sql = "
            SELECT chapterId 
            FROM chapters 
            WHERE subjectId = ? 
            ORDER BY sortOrder ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$subjectId]);
        $chapters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Gán lại sortOrder = 1, 2, 3...
        $order = 1;
        $update = $this->db->prepare(
            "UPDATE chapters SET sortOrder = ? WHERE chapterId = ?"
        );

        foreach ($chapters as $chapter) {
            $update->execute([$order, $chapter['chapterId']]);
            $order++;
        }
    }


    public function hasLessons($chapterId)
    {
        $sql = "
            SELECT 1
            FROM lessons
            WHERE chapterId = ?
            LIMIT 1
        ";

        return !empty(
            $this->fetch($sql, [$chapterId])
        );
    }



    public function searchAdminChapters($keyword)
    {
        $sql = "
            SELECT 
                c.*,

                s.subjectName,
                s.subjectId,

                g.gradeId,
                g.gradeName

            FROM chapters c

            JOIN subjects s
                ON c.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            WHERE
                c.chapterName LIKE ?
                OR c.slug LIKE ?
                OR s.subjectName LIKE ?
                OR g.gradeName LIKE ?

            ORDER BY c.chapterId DESC

            LIMIT 50
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }

  

}