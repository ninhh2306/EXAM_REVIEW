<?php

require_once "../app/core/Model.php";

class Exam extends Model
{
    protected $table = 'exams';

    // Lấy danh sách tất cả đề thi theo môn
    public function getBySubjectSlug($subjectSlug)
    {
        $stmt = $this->db->prepare("
            SELECT e.*
            FROM exams e
            JOIN subjects s ON e.subjectId = s.subjectId
            WHERE s.slug = ?
            AND e.isActive = 1
            ORDER BY e.sortOrder ASC
        ");
        $stmt->execute([$subjectSlug]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả đề thi theo môn học
    public function getBySubject($subjectId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM exams 
             WHERE subjectId = ? AND isActive = 1 
             ORDER BY createdAt ASC"
        );
        $stmt->execute([$subjectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đề thi theo slug và môn học
    public function getBySlug($slug, $subjectId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM exams 
             WHERE slug = ? AND subjectId = ? AND isActive = 1 
             LIMIT 1"
        );
        $stmt->execute([$slug, $subjectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy đề thi theo id
    public function getById($examId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM exams WHERE examId = ? LIMIT 1"
        );
        $stmt->execute([$examId]);
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
            ORDER BY e.createdAt ASC"
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
             ORDER BY createdAt ASC"
        );
        $stmt->execute([$subjectId, $examType]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đề thi theo bài học
    public function getByLesson($lessonId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM exams 
             WHERE lessonId = ? AND isActive = 1 
             ORDER BY createdAt ASC"
        );
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
            LIMIT $limit" // ✅ gắn trực tiếp
        );

        $stmt->execute([$subjectId, $excludeExamId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}