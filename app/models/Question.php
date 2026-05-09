<?php

require_once "../app/core/Model.php";

class Question extends Model
{
    protected $table = 'questions';

    // Lấy câu hỏi + đáp án theo đề thi
   public function getByExamWithAnswers($examId)
    {
        // 1. Lấy danh sách câu hỏi (dùng eq.id để sắp xếp)
        $stmt = $this->db->prepare("
            SELECT q.*
            FROM questions q
            JOIN exam_questions eq ON q.questionId = eq.questionId
            WHERE eq.examId = ?
            ORDER BY eq.id ASC
        ");
        $stmt->execute([$examId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC); // Thêm dòng này để lấy dữ liệu

        // 2. Lấy đáp án cho từng câu
        foreach ($questions as &$q) {
            $stmt2 = $this->db->prepare("
                SELECT answerId, content, isCorrect
                FROM answers
                WHERE questionId = ?
                ORDER BY answerId ASC
            ");
            $stmt2->execute([$q['questionId']]);
            $q['answers'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $questions;
    }

    public function getByExam($examId)
    {
        $stmt = $this->db->prepare("
            SELECT q.questionId
            FROM questions q
            JOIN exam_questions eq ON q.questionId = eq.questionId
            WHERE eq.examId = ?
            ORDER BY eq.id ASC
        ");
        $stmt->execute([$examId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}