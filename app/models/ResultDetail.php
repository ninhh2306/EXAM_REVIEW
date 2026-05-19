<?php

require_once ROOT . "/app/core/Model.php";

class ResultDetail extends Model
{
    protected $table = 'result_details';

    // Lưu chi tiết 1 câu trả lời
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO result_details
                (resultId, questionId, selectedAnswerId, isCorrect)
            VALUES
                (:resultId, :questionId, :selectedAnswerId, :isCorrect)
        ");
        $stmt->execute([
            ':resultId'         => $data['resultId'],
            ':questionId'       => $data['questionId'],
            ':selectedAnswerId' => $data['selectedAnswerId'] ?? null,
            ':isCorrect'        => $data['isCorrect'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Lấy toàn bộ chi tiết bài làm của 1 kết quả
    // JOIN sẵn: nội dung câu hỏi, đáp án đã chọn, đáp án đúng, tất cả đáp án
    public function getByResult($resultId)
    {
        $stmt = $this->db->prepare("
            SELECT questionId, selectedAnswerId, isCorrect
            FROM result_details
            WHERE resultId = ?
        ");
        $stmt->execute([$resultId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
}