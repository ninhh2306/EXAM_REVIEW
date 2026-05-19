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


   
    // ================= VALIDATE NAME =================
    public function validateLessonName(&$name)
    {
        $name = trim($name);

        // Xóa khoảng trắng thừa
        $name = preg_replace('/\s+/u', ' ', $name);

        // Remove HTML
        $name = strip_tags($name);

        if ($name === '') {
            return 'Tên bài học không được để trống!';
        }

        // Tối thiểu 3 ký tự
        if (mb_strlen($name) < 3) {
            return 'Tên bài học quá ngắn!';
        }

        // Tối đa 200 ký tự
        if (mb_strlen($name) > 200) {
            return 'Tên bài học quá dài!';
        }

        // Ký tự cấm
        if (preg_match('/[<>{}\[\]\/\\\\|*#@$^~]/u', $name)) {
            return 'Tên bài học chứa ký tự không hợp lệ!';
        }

        // Chỉ cho phép:
        // chữ, số, khoảng trắng, - ( ) : ! ? . , &
        if (!preg_match('/^[\p{L}\p{N}\s\-\(\)\:\!\?\.\,\&]+$/u', $name)) {
            return 'Tên bài học chứa ký tự không hợp lệ!';
        }

        // Kiểm tra slug sau convert
        $slug = $this->toSlug($name);

        if (empty($slug)) {
            return 'Slug không hợp lệ';
        }

        return null;
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
            AND l.isActive = 1

            ORDER BY
                c.chapterId ASC,
                l.sortOrder ASC
        ";

        return $this->fetchAll($sql, [$subjectId]);
    }


    // ================= GET BY CHAPTER =================
    public function getByChapter($chapterId)
    {
        $sql = "
            SELECT *
            FROM lessons
            WHERE chapterId = ?
            ORDER BY sortOrder ASC, lessonId ASC
        ";

        return $this->fetchAll($sql, [$chapterId]);
    }



    // ================= GET ALL =================
    public function all()
    {
        $sql = "
            SELECT *
            FROM lessons
            ORDER BY lessonId DESC
        ";

        return $this->fetchAll($sql);
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
            AND l.isActive = 1

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
                c.sortOrder as chapterSortOrder,
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
                isActive,
                sortOrder,
                createdBy
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        return $this->execute($sql, [
            $data['subjectId'],
            $data['chapterId'],
            $data['lessonName'],
            $data['slug'],
            $data['content'],
            $data['isActive'],
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
                isActive = ?,
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
            $data['isActive'],
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

    public function hasExams($lessonId)
    {
        $sql = "SELECT 1 FROM exams WHERE lessonId = ? AND examType = 'lesson' LIMIT 1";
        return !empty($this->fetch($sql, [$lessonId]));
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


    public function searchLessons($keyword)
    {
        $sql = "
            SELECT 
                l.*,

                s.subjectName,
                s.slug as subjectSlug,

                g.gradeName,
                g.slug as gradeSlug,

                c.chapterName,
                c.slug as chapterSlug

            FROM lessons l

            JOIN subjects s
                ON l.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            LEFT JOIN chapters c
                ON l.chapterId = c.chapterId

            WHERE
                l.isActive = 1

                AND (
                    l.lessonName LIKE ?
                    OR l.slug LIKE ?
                )

            ORDER BY l.lessonId DESC
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }

    public function removeVietnamese($str)
    {
        $str = mb_strtolower($str);

        $unicode = [
            'a' => ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ'],
            'd' => ['đ'],
            'e' => ['é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ'],
            'i' => ['í','ì','ỉ','ĩ','ị'],
            'o' => ['ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ'],
            'u' => ['ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự'],
            'y' => ['ý','ỳ','ỷ','ỹ','ỵ']
        ];

        foreach ($unicode as $nonAccent => $accent) {
            $str = str_replace($accent, $nonAccent, $str);
        }

        return $str;
    }

    public function getMaxSortOrder($chapterId)
    {
        $sql = "SELECT MAX(sortOrder) FROM lessons WHERE chapterId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chapterId]);
        return (int)$stmt->fetchColumn();
    }

    public function getSortOrderById($lessonId, $chapterId)
    {
        $sql = "SELECT sortOrder FROM lessons WHERE lessonId = ? AND chapterId = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$lessonId, $chapterId]);
        return (int)$stmt->fetchColumn();
    }

    public function increaseSortOrders($chapterId, $fromSort)
    {
        $sql = "
            UPDATE lessons
            SET sortOrder = sortOrder + 1
            WHERE chapterId = ?
            AND sortOrder >= ?
            ORDER BY sortOrder DESC
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$chapterId, $fromSort]);
    }

    public function decreaseSortOrders($chapterId, $fromSort)
    {
        $sql = "
            UPDATE lessons
            SET sortOrder = sortOrder - 1
            WHERE chapterId = ?
            AND sortOrder > ?
            ORDER BY sortOrder ASC
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$chapterId, $fromSort]);
    }

    public function reorderSortOrders($chapterId)
    {
        $sql = "SELECT lessonId FROM lessons WHERE chapterId = ? ORDER BY sortOrder ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$chapterId]);
        $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $update = $this->db->prepare("UPDATE lessons SET sortOrder = ? WHERE lessonId = ?");
        $order = 1;
        foreach ($lessons as $lesson) {
            $update->execute([$order, $lesson['lessonId']]);
            $order++;
        }
    }

    public function updateSortOrderOnly($lessonId, $sortOrder)
    {
        $stmt = $this->db->prepare(
            "UPDATE lessons SET sortOrder = ? WHERE lessonId = ?"
        );
        return $stmt->execute([$sortOrder, $lessonId]);
    }


    public function rebuildSortOrdersByChapter($chapterId)
    {
        $stmt = $this->db->prepare("
            SELECT lessonId
            FROM lessons
            WHERE chapterId = ?
            ORDER BY sortOrder ASC, lessonId ASC
        ");

        $stmt->execute([$chapterId]);

        $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sort = 1;

        foreach ($lessons as $lesson) {

            $update = $this->db->prepare("
                UPDATE lessons
                SET sortOrder = ?
                WHERE lessonId = ?
            ");

            $update->execute([
                $sort,
                $lesson['lessonId']
            ]);

            $sort++;
        }
    }


    public function searchAdminLessons($keyword)
    {
        $sql = "
            SELECT 
                l.*,

                s.subjectName,
                g.gradeName,
                c.chapterName

            FROM lessons l

            JOIN subjects s
                ON l.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            LEFT JOIN chapters c
                ON l.chapterId = c.chapterId

            WHERE
                l.lessonName LIKE ?
                OR l.slug LIKE ?
                OR s.subjectName LIKE ?
                OR g.gradeName LIKE ?
                OR c.chapterName LIKE ?

            ORDER BY l.lessonId DESC

            LIMIT 100
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }


}