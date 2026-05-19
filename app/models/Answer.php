<?php

require_once ROOT . "/app/core/Model.php";

class Answer extends Model
{
    protected $table = 'answers';

    // Lấy tất cả đáp án của 1 câu hỏi
    public function getByQuestion($questionId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM answers WHERE questionId = ? ORDER BY answerId ASC"
        );
        $stmt->execute([$questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy đáp án ĐÚNG của 1 câu hỏi (dùng khi chấm điểm)
    public function getCorrectByQuestion($questionId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM answers WHERE questionId = ? AND isCorrect = 1 LIMIT 1"
        );
        $stmt->execute([$questionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy 1 đáp án theo id
    public function getById($answerId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM answers WHERE answerId = ? LIMIT 1"
        );
        $stmt->execute([$answerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}