<?php

require_once "../app/core/Model.php";

class Result extends Model
{
    protected $table = 'results';

    // Lưu kết quả bài thi → trả về resultId vừa tạo
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO results
                (examId, userId, score, totalCorrect, totalQuestions,
                 correctKnowledge, correctComprehension, correctApplication, isGenerated, startTime, endTime)
            VALUES
                (:examId, :userId, :score, :totalCorrect, :totalQuestions,
                 :correctKnowledge, :correctComprehension, :correctApplication, :isGenerated, :startTime, :endTime)
        ");
        $stmt->execute([
            ':examId'         => $data['examId'],
            ':userId'         => $data['userId']      ?? null,
            ':score'          => $data['score'],
            ':totalCorrect'   => $data['totalCorrect'],
            ':totalQuestions' => $data['totalQuestions'],
            ':correctKnowledge'      => $data['easyCount']   ?? 0,
            ':correctComprehension'    => $data['mediumCount']  ?? 0,
            ':correctApplication'      => $data['hardCount']    ?? 0,
            ':isGenerated'    => $data['isGenerated']  ?? 0,
            ':startTime'      => $data['startTime']   ?? null,
            ':endTime'        => $data['endTime']     ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Lấy kết quả theo resultId
    public function getById($resultId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM results WHERE resultId = ? LIMIT 1"
        );
        $stmt->execute([$resultId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả kết quả của 1 user
    public function getByUser($userId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM results WHERE userId = ? ORDER BY resultId DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả kết quả của 1 đề thi
    public function getByExam($examId)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM results WHERE examId = ? ORDER BY resultId DESC"
        );
        $stmt->execute([$examId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy filter môn học 
    public function getSubjectsHistory($userId)
    {
        $sql = "
            SELECT DISTINCT
                s.subjectId,
                s.subjectName
            FROM results r
            JOIN exams e ON r.examId = e.examId
            JOIN subjects s ON e.subjectId = s.subjectId
            WHERE r.userId = ?
            ORDER BY s.subjectName ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Lấy lịch sử làm bài
    public function getHistoryByUser($userId, $keyword = '', $subjectId = '')
    {
        $sql = "
            SELECT 
                r.*,

                e.examId,
                e.title AS examTitle,
                e.slug  AS examSlug,

                s.subjectId,
                s.subjectName,
                s.slug AS subjectSlug,
                s.image

            FROM results r

            INNER JOIN exams e 
                ON r.examId = e.examId

            INNER JOIN subjects s
                ON e.subjectId = s.subjectId

            WHERE r.userId = ?
        ";

        $params = [$userId];

        if ($subjectId != '') {
            $sql .= " AND s.subjectId = ? ";
            $params[] = $subjectId;
        }

        if ($keyword != '') {
            $sql .= " AND e.title LIKE ? ";
            $params[] = "%$keyword%";
        }

        $sql .= " ORDER BY r.resultId DESC ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Lấy điểm trung bình
    public function getSummary($userId)
    {
        $sql = "
            SELECT 
                COUNT(*) as totalExam,
                AVG(score) as avgScore
            FROM results
            WHERE userId = ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}