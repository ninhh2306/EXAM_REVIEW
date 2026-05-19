<?php

require_once ROOT . "/app/core/Model.php";

class ExamQuestion extends Model
{
    protected $table = 'exam_questions';

    // ===============================
    // INSERT
    // ===============================
    public function create($data)
    {
        $sql = "
            INSERT INTO exam_questions (
                examId,
                questionId,
                questionOrder
            )
            VALUES (?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['examId'],
            $data['questionId'],
            $data['questionOrder']
        ]);
    }

    // ===============================
    // GET QUESTIONS BY EXAM
    // ===============================
    public function getByExam($examId)
    {
        $sql = "
            SELECT
                eq.*,
                q.content,
                q.level
            FROM exam_questions eq

            JOIN questions q
                ON eq.questionId = q.questionId

            WHERE eq.examId = ?

            ORDER BY eq.questionOrder ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$examId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===============================
    // DELETE BY EXAM
    // ===============================
    public function deleteByExam($examId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM exam_questions
            WHERE examId = ?
        ");

        return $stmt->execute([$examId]);
    }

    public function getQuestionIdsByExam($examId)
    {
        $stmt = $this->db->prepare("
            SELECT questionId
            FROM exam_questions
            WHERE examId = ?
            ORDER BY questionOrder ASC
        ");

        $stmt->execute([$examId]);

        return array_column(
            $stmt->fetchAll(PDO::FETCH_ASSOC),
            'questionId'
        );
    }


}