<?php

require_once ROOT . "/app/core/Model.php";

class Exam extends Model
{
    protected $table = 'exams';

    // Lấy danh sách đề thi theo môn (lọc đúng gradeId, loại trừ đề thpt)
    public function getBySubjectSlug($subjectSlug, $gradeId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.*,
                COUNT(eq.questionId) AS realTotalQuestions

            FROM exams e

            JOIN subjects s 
                ON e.subjectId = s.subjectId

            LEFT JOIN exam_questions eq
                ON eq.examId = e.examId

            WHERE s.slug = ?
            AND s.gradeId = ?
            AND e.examType = 'lesson'
            AND e.isActive = 1

            GROUP BY e.examId

            ORDER BY e.sortOrder ASC
        ");

        $stmt->execute([$subjectSlug, $gradeId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy tất cả đề thi theo môn học
    public function getBySubject($subjectId)
    {
        $stmt = $this->db->prepare("
            SELECT e.*
            FROM exams e
            LEFT JOIN lessons l ON e.lessonId = l.lessonId
            LEFT JOIN chapters c ON l.chapterId = c.chapterId
            WHERE e.subjectId = ?
            AND e.isActive = 1
            AND e.examType != 'thpt'
            AND e.examType != 'random'
            AND e.isTemporary = 0
            ORDER BY 
                c.sortOrder ASC,
                l.sortOrder ASC,
                e.sortOrder ASC
        ");

        $stmt->execute([$subjectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đề thi theo slug và môn học
    public function getBySlug($slug, $subjectId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.*,
                COUNT(eq.questionId) AS realTotalQuestions

            FROM exams e

            LEFT JOIN exam_questions eq
                ON eq.examId = e.examId

            WHERE e.slug = ?
            AND e.subjectId = ?
            AND e.isActive = 1

            GROUP BY e.examId

            LIMIT 1
        ");

        $stmt->execute([$slug, $subjectId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // Lấy đề thi theo chương
    public function getByChapter($chapterId)
    {
        $stmt = $this->db->prepare(
            "SELECT e.*, c.slug AS chapterSlug
            FROM exams e
            JOIN chapters c ON e.chapterId = c.chapterId
            WHERE e.chapterId = ? AND e.isActive = 1 
            ORDER BY e.sortOrder ASC"
        );
        $stmt->execute([$chapterId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đề thi theo loại (lesson, chapter, practice, midterm...)
    public function getByType($subjectId, $examType)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM exams 
             WHERE subjectId = ? AND examType = ? AND isActive = 1 
             ORDER BY sortOrder ASC"
        );
        $stmt->execute([$subjectId, $examType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đề thi theo bài học
    public function getByLesson($lessonId)
    {
        $sql = "
            SELECT *
            FROM exams
            WHERE lessonId = ?
            ORDER BY sortOrder ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$lessonId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy số lượt làm bài
    public function increaseViewCount($examId)
    {
        $stmt = $this->db->prepare("
            UPDATE exams
            SET viewCount = viewCount + 1
            WHERE examId = ?
        ");
        $stmt->execute([$examId]);
    }


    // Lấy đề thi gợi ý cùng môn, loại trừ đề hiện tại
    // Dùng trong ResultController::show() sau khi nộp bài
    public function getSuggested($subjectId, $excludeExamId, $limit = 3)
    {
        $limit = (int)$limit; // ép kiểu an toàn

        $stmt = $this->db->prepare(
            "SELECT * FROM exams
            WHERE subjectId = ? AND examId != ? AND isActive = 1
            ORDER BY viewCount DESC, createdAt DESC
            LIMIT $limit" 
        );

        $stmt->execute([$subjectId, $excludeExamId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchExams($keyword)
    {
        $sql = "
            SELECT 
                e.*,

                s.subjectName,
                s.slug as subjectSlug,

                g.gradeName,
                g.slug as gradeSlug

            FROM exams e

            JOIN subjects s
                ON e.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            WHERE
                e.isActive = 1
                AND e.isPublic = 1
                AND e.isTemporary = 0

                AND (
                    e.title LIKE ?
                    OR e.slug LIKE ?
                )

            ORDER BY e.examId DESC
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }



    // =====================================================
    // ADMIN - GET ALL
    // =====================================================
    public function getAllWithSubject()
    {
        $stmt = $this->db->prepare("
            SELECT
                e.*,

                s.subjectName,
                s.slug AS subjectSlug,

                g.gradeName

            FROM exams e

            JOIN subjects s
                ON e.subjectId = s.subjectId

            JOIN grades g
                ON e.gradeId = g.gradeId

            ORDER BY e.examId DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =====================================================
    // ADMIN - PAGINATION
    // =====================================================
    public function getAdminExamsPaginated($limit, $offset)
    {
        $stmt = $this->db->prepare("
            SELECT
                e.*,
                s.subjectName,
                s.slug AS subjectSlug,
                g.gradeName,
                l.lessonName,
                l.sortOrder AS lessonSortOrder,
                c.chapterName,
                c.sortOrder AS chapterSortOrder,
                COUNT(eq.questionId) AS realTotalQuestions,
                u.fullName AS creatorName
            FROM exams e
            JOIN subjects s ON e.subjectId = s.subjectId
            JOIN grades g ON e.gradeId = g.gradeId
            LEFT JOIN lessons l ON e.lessonId = l.lessonId
            LEFT JOIN chapters c ON l.chapterId = c.chapterId
            LEFT JOIN exam_questions eq ON eq.examId = e.examId
            LEFT JOIN users u ON e.createdBy = u.userId
            GROUP BY e.examId
            ORDER BY e.examId DESC
            LIMIT ?
            OFFSET ?
        ");

        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =====================================================
    // COUNT ALL EXAMS
    // =====================================================
    public function countAllExams()
    {
        $stmt = $this->db->query("
            SELECT COUNT(*)
            FROM exams
        ");

        return (int)$stmt->fetchColumn();
    }


    // =====================================================
    // CREATE EXAM
    // =====================================================
    public function createExam($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO exams (
                gradeId,
                subjectId,
                chapterId,
                lessonId,
                sortOrder,

                title,
                slug,

                examType,
                generationType,

                totalQuestions,
                duration,

                questionOrder,

                isActive,
                createdBy
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['gradeId'],
            $data['subjectId'],
            $data['chapterId'],
            $data['lessonId'],
            $data['sortOrder'],

            $data['title'],
            $data['slug'],

            $data['examType'],
            $data['generationType'],

            $data['totalQuestions'],
            $data['duration'],

            $data['questionOrder'],

            $data['isActive'],
            $data['createdBy']
        ]);

        return $this->db->lastInsertId();
    }


    public function titleExistsInScope($title, $examType, $lessonId = null, $subjectId = null, $excludeId = null)
    {
        if ($examType === 'lesson') {

            // Không được trùng tên trong cùng chương học
            $excludeSql = $excludeId
                ? "AND e.examId != " . (int)$excludeId
                : "";

            $sql = "
                SELECT COUNT(*)
                FROM exams e
                JOIN lessons l  ON e.lessonId = l.lessonId
                JOIN lessons l2 ON l2.lessonId = ?
                WHERE e.title      = ?
                AND   l.chapterId  = l2.chapterId
                AND   e.examType   = 'lesson'
                {$excludeSql}
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$lessonId, $title]);

        } else {

            // THPT: không được trùng tên trong cùng môn học
            $excludeSql = $excludeId
                ? "AND examId != " . (int)$excludeId
                : "";

            $sql = "
                SELECT COUNT(*)
                FROM exams
                WHERE title    = ?
                AND   subjectId = ?
                AND   examType  = 'thpt'
                {$excludeSql}
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$title, $subjectId]);
        }

        return (int)$stmt->fetchColumn() > 0;
    }

    // =====================================================
    // GET BY ID (Admin - không lọc isActive)
    // =====================================================
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.*,

                s.subjectName,
                s.slug AS subjectSlug,

                g.gradeName,

                l.lessonName,
                l.sortOrder AS lessonSortOrder,

                c.chapterName,
                c.sortOrder AS chapterSortOrder,

                COUNT(eq.questionId) AS realTotalQuestions

            FROM exams e

            JOIN subjects s
                ON e.subjectId = s.subjectId

            JOIN grades g
                ON e.gradeId = g.gradeId

            LEFT JOIN lessons l
                ON e.lessonId = l.lessonId

            LEFT JOIN chapters c
                ON l.chapterId = c.chapterId

            LEFT JOIN exam_questions eq
                ON eq.examId = e.examId

            WHERE e.examId = ?

            GROUP BY e.examId

            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // UPDATE
    // =====================================================
    public function updateExam($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE exams
            SET
                gradeId        = ?,
                subjectId      = ?,
                chapterId      = ?,
                lessonId       = ?,
                sortOrder      = ?,

                title          = ?,
                slug           = ?,

                examType       = ?,
                generationType = ?,

                totalQuestions = ?,
                duration       = ?,
                questionOrder  = ?,
                isActive       = ?

            WHERE examId = ?
        ");

        return $stmt->execute([
            $data['gradeId'],
            $data['subjectId'],
            $data['chapterId'],
            $data['lessonId'],
            $data['sortOrder'],

            $data['title'],
            $data['slug'],

            $data['examType'],
            $data['generationType'],

            $data['totalQuestions'],
            $data['duration'],
            $data['questionOrder'],
            $data['isActive'],

            $id
        ]);
    }

    // =====================================================
    // DELETE
    // =====================================================
    public function deleteExam($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM exams
            WHERE examId = ?
        ");

        return $stmt->execute([$id]);
    }



    // =====================================================
    // GET MAX SORT ORDER BY LESSON
    // =====================================================
    public function getMaxSortOrderByLesson($lessonId)
    {
        $stmt = $this->db->prepare("
            SELECT MAX(sortOrder)
            FROM exams
            WHERE lessonId = ?
        ");

        $stmt->execute([$lessonId]);

        return (int)$stmt->fetchColumn();
    }


    // =====================================================
    // GET SORT ORDER
    // =====================================================
    public function getSortOrder($examId, $lessonId)
    {
        $stmt = $this->db->prepare("
            SELECT sortOrder
            FROM exams
            WHERE examId = ?
            AND lessonId = ?
            LIMIT 1
        ");

        $stmt->execute([
            $examId,
            $lessonId
        ]);

        return (int)$stmt->fetchColumn();
    }

    // =====================================================
    // INCREASE SORT ORDER FROM
    // =====================================================
    public function increaseSortOrders($lessonId, $fromSort)
    {
        $stmt = $this->db->prepare("
            UPDATE exams
            SET sortOrder = sortOrder + 1
            WHERE lessonId = ?
            AND sortOrder >= ?
        ");

        return $stmt->execute([
            $lessonId,
            $fromSort
        ]);
    }


    public function getPrevExam($lessonId, $sortOrder)
    {
        $stmt = $this->db->prepare("
            SELECT examId FROM exams
            WHERE lessonId = ? AND sortOrder < ?
            ORDER BY sortOrder DESC
            LIMIT 1
        ");
        $stmt->execute([$lessonId, $sortOrder]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function searchAdminExams($keyword)
    {
        $sql = "
            SELECT
                e.*,
                s.subjectName,
                g.gradeName,
                l.lessonName,
                l.sortOrder AS lessonSortOrder,
                c.chapterName,
                c.sortOrder AS chapterSortOrder,
                COUNT(eq.questionId) AS realTotalQuestions,
                u.fullName AS creatorName
            FROM exams e
            JOIN subjects s ON e.subjectId = s.subjectId
            JOIN grades g ON s.gradeId = g.gradeId
            LEFT JOIN lessons l ON e.lessonId = l.lessonId
            LEFT JOIN chapters c ON l.chapterId = c.chapterId
            LEFT JOIN exam_questions eq ON eq.examId = e.examId
            LEFT JOIN users u ON e.createdBy = u.userId
            WHERE (
                e.title LIKE ?
                OR e.slug LIKE ?
            )
            GROUP BY e.examId
            ORDER BY e.examId DESC
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }


    // =====================================================
    // THPT - ALL
    // =====================================================

    public function getThptExams()
    {
        $stmt = $this->db->prepare("
            SELECT
                e.*,

                s.subjectName,
                s.slug AS subjectSlug,

                g.slug AS gradeSlug,

                COUNT(eq.questionId) AS realTotalQuestions

            FROM exams e

            JOIN subjects s
                ON e.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            LEFT JOIN exam_questions eq
                ON eq.examId = e.examId

            WHERE
                e.examType = 'thpt'
                AND e.isActive = 1

            GROUP BY e.examId

            ORDER BY
                s.subjectName ASC,
                e.createdAt DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // =====================================================
    // THPT - BY SUBJECT
    // =====================================================

    public function getThptExamsBySubject($subjectId)
    {
        $stmt = $this->db->prepare("
            SELECT
                e.*,

                s.subjectName,
                s.slug AS subjectSlug,

                g.slug AS gradeSlug,

                COUNT(eq.questionId) AS realTotalQuestions

            FROM exams e

            JOIN subjects s
                ON e.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            LEFT JOIN exam_questions eq
                ON eq.examId = e.examId

            WHERE
                e.subjectId = ?
                AND e.examType = 'thpt'
                AND e.isActive = 1

            GROUP BY e.examId

            ORDER BY e.createdAt DESC
        ");

        $stmt->execute([$subjectId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy đề thi theo môn, loại trừ đề thpt
    public function getBySubjectExcludeThpt($subjectId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM exams 
            WHERE subjectId = ? 
            AND isActive = 1
            AND examType != 'thpt'
            ORDER BY sortOrder ASC"
        );
        $stmt->execute([$subjectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO exams
            (
                gradeId, subjectId, chapterId, lessonId,
                title, slug,
                examType, generationType,
                totalQuestions, duration,
                knowledgePercent, comprehensionPercent, applicationPercent,
                questionOrder,
                isPublic, isTemporary, isActive,   /* ← thêm isActive */
                createdBy
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");                                         /* ← thêm 1 dấu ? */

        $stmt->execute([
            $data['gradeId'],
            $data['subjectId'],
            $data['chapterId'] ?? null,
            $data['lessonId']  ?? null,
            $data['title'],
            $data['slug'],
            $data['examType'],
            $data['generationType'],
            $data['totalQuestions'],
            $data['duration'],
            $data['knowledgePercent'],
            $data['comprehensionPercent'],
            $data['applicationPercent'],
            $data['questionOrder'],
            $data['isPublic'],
            $data['isTemporary'],
            $data['isActive'] ?? 0,   // ← thêm dòng này
            $data['createdBy']
        ]);

        return $this->db->lastInsertId();
    }

    public function insertExamQuestion($examId,$questionId,$order) 
    {
        $stmt = $this->db->prepare("
            INSERT INTO exam_questions
            (
                examId,
                questionId,
                questionOrder
            )
            VALUES (?, ?, ?)
        ");

        return $stmt->execute([
            $examId,
            $questionId,
            $order
        ]);
    }


    public function increasePlayCount($examId)
    {
        $stmt = $this->db->prepare("
            UPDATE exams
            SET playCount = playCount + 1
            WHERE examId = ?
        ");

        return $stmt->execute([$examId]);
    }


    public function activateExam($examId)
    {
        $stmt = $this->db->prepare("
            UPDATE exams
            SET isActive = 1
            WHERE examId = ?
        ");
        return $stmt->execute([$examId]);
    }

    public function deleteExpiredTemporaryExams()
    {
        // Xóa exam_questions của các đề rác
        $stmt = $this->db->prepare("
            DELETE FROM exam_questions
            WHERE examId IN (
                SELECT examId FROM exams
                WHERE examType = 'random'
                AND isTemporary = 1
                AND playCount = 0
                AND createdAt < NOW() - INTERVAL 1 DAY
            )
        ");
        $stmt->execute();

        // Xóa đề rác
        $stmt = $this->db->prepare("
            DELETE FROM exams
            WHERE examType = 'random'
            AND isTemporary = 1
            AND playCount = 0
            AND createdAt < NOW() - INTERVAL 1 DAY
        ");

        return $stmt->execute();
    }


    public function getRandomExamBySlug($slug, $userId)
    {
        $stmt = $this->db->prepare("
            SELECT
                e.*,
                COUNT(eq.questionId) AS realTotalQuestions

            FROM exams e

            LEFT JOIN exam_questions eq
                ON eq.examId = e.examId

            WHERE
                e.slug = ?
                AND e.createdBy = ?

            GROUP BY e.examId

            LIMIT 1
        ");

        $stmt->execute([
            $slug,
            $userId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}