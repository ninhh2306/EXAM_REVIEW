<?php

require_once ROOT . "/app/core/Model.php";

class Result extends Model
{
    protected $table = 'results';

    // Lưu kết quả bài thi → trả về resultId vừa tạo
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO results
                (examId, userId, score, totalCorrect, totalQuestions,
                totalKnowledge, totalComprehension, totalApplication,
                correctKnowledge, correctComprehension, correctApplication,
                isGenerated, startTime, endTime)
            VALUES
                (:examId, :userId, :score, :totalCorrect, :totalQuestions,
                :totalKnowledge, :totalComprehension, :totalApplication,
                :correctKnowledge, :correctComprehension, :correctApplication,
                :isGenerated, :startTime, :endTime)
        ");
        $stmt->execute([
            ':examId'              => $data['examId'],
            ':userId'              => $data['userId']         ?? null,
            ':score'               => $data['score'],
            ':totalCorrect'        => $data['totalCorrect'],
            ':totalQuestions'      => $data['totalQuestions'],
            ':totalKnowledge'      => $data['totalKnowledge']     ?? 0,
            ':totalComprehension'  => $data['totalComprehension'] ?? 0,
            ':totalApplication'    => $data['totalApplication']   ?? 0,
            ':correctKnowledge'    => $data['correctKnowledge']   ?? 0,
            ':correctComprehension'=> $data['correctComprehension'] ?? 0,
            ':correctApplication'  => $data['correctApplication']  ?? 0,
            ':isGenerated'         => $data['isGenerated']        ?? 0,
            ':startTime'           => $data['startTime']          ?? null,
            ':endTime'             => $data['endTime']            ?? null,
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
                s.subjectName,

                g.gradeId,
                g.gradeName,

                e.examType

            FROM results r

            JOIN exams e
                ON r.examId = e.examId

            JOIN subjects s
                ON e.subjectId = s.subjectId

            JOIN grades g
                ON s.gradeId = g.gradeId

            WHERE r.userId = ?
            AND e.isActive = 1
            AND (
                e.isTemporary = 0
                OR e.examType = 'random'
            )

            ORDER BY
                g.gradeId ASC,
                s.subjectName ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result  = [];
        $hasThpt = false;
        $seenSubjects = [];

        foreach ($rows as $row) {

            // THPT + RANDOM
            if (
                $row['examType'] === 'thpt'
                || $row['examType'] === 'random'
            ) {

                if (!$hasThpt) {

                    $result[] = [
                        'subjectId'   => 'thpt',
                        'subjectName' => 'THPT Quốc Gia',
                        'gradeName'   => ''
                    ];

                    $hasThpt = true;
                }

                continue;
            }

            // LESSON
            $key = $row['subjectId'];

            if (!isset($seenSubjects[$key])) {

                $seenSubjects[$key] = true;

                $result[] = $row;
            }
        }

        return $result;
    }


    // Lấy lịch sử làm bài
    public function getHistoryByUser($userId, $keyword = '', $subjectId = '')
    {
        $sql = "
            SELECT 
                r.*,

                (
                    SELECT COUNT(*)
                    FROM exam_questions eq
                    WHERE eq.examId = e.examId
                ) AS realTotalQuestions,

                e.examId,
                e.title    AS examTitle,
                e.slug     AS examSlug,
                e.examType,

                s.subjectId,
                s.subjectName,
                s.slug  AS subjectSlug,
                s.image

            FROM results r

            INNER JOIN exams e 
                ON r.examId = e.examId

            INNER JOIN subjects s
                ON e.subjectId = s.subjectId

            WHERE r.userId = ?
            AND e.isActive = 1
            AND (
                e.isTemporary = 0
                OR e.examType = 'random'
            )
        ";

        $params = [$userId];

        if ($subjectId != '') {

            if ($subjectId === 'thpt') {
                $sql .= " AND e.examType IN ('thpt', 'random') ";
            }

            else {
                $sql .= " AND s.subjectId = ? AND e.examType = 'lesson' ";
                $params[] = $subjectId;
            }
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


    // Thống kê tiến độ học tập
    public function getProgressStats($userId)
    {
        $stmt = $this->db->prepare("
            SELECT
                SUM(totalCorrect)                    AS totalCorrect,
                SUM(totalQuestions)                  AS totalQuestions,
                SUM(totalQuestions - totalCorrect)   AS totalWrong,
                SUM(correctKnowledge)                AS sumCorrectKnowledge,
                SUM(correctComprehension)            AS sumCorrectComprehension,
                SUM(correctApplication)              AS sumCorrectApplication,
                SUM(totalKnowledge)                  AS totalKnowledge,
                SUM(totalComprehension)              AS totalComprehension,
                SUM(totalApplication)                AS totalApplication,
                COUNT(resultId)                      AS totalExams,
                ROUND(AVG(score), 1)                 AS avgScore
            FROM results
            WHERE userId = ?
        ");
        $stmt->execute([$userId]);
        $overall = $stmt->fetch(PDO::FETCH_ASSOC);

        // Thêm gradeName vào bySubject
        $stmt2 = $this->db->prepare("
            SELECT
                s.subjectId,
                s.subjectName,
                g.gradeName,
                COUNT(r.resultId)      AS examCount,
                ROUND(AVG(r.score), 1) AS avgScore
            FROM results r
            JOIN exams    e ON r.examId    = e.examId
            JOIN subjects s ON e.subjectId = s.subjectId
            JOIN grades   g ON s.gradeId   = g.gradeId
            WHERE r.userId = ?
            GROUP BY s.subjectId, s.subjectName, g.gradeName
            ORDER BY avgScore DESC
            LIMIT 5
        ");
        $stmt2->execute([$userId]);
        $bySubject = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return [
            'overall'   => $overall,
            'bySubject' => $bySubject,
        ];
    }


    public function countByExam($examId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM results WHERE examId = ?
        ");
        $stmt->execute([$examId]);
        return (int)$stmt->fetchColumn();
    }

    // =====================================================
    // ADMIN PAGINATION
    // =====================================================
    public function getAllPaginated($page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT
                r.*,
                u.fullName,
                e.title AS examTitle,
                s.subjectName,
                g.gradeName,
                l.lessonName,
                l.sortOrder AS lessonSortOrder,
                c.chapterName,
                c.sortOrder AS chapterSortOrder,

                (
                    SELECT COUNT(*)
                    FROM exam_questions eq
                    WHERE eq.examId = e.examId
                ) AS realTotalQuestions,

                COALESCE(
                    ROUND(
                        (
                            r.totalCorrect /
                            NULLIF(
                                (
                                    SELECT COUNT(*)
                                    FROM exam_questions eq2
                                    WHERE eq2.examId = e.examId
                                ),
                                0
                            )
                        ) * 10,
                        1
                    ),
                    0
                ) AS realScore

            FROM results r

            LEFT JOIN users u
                ON r.userId = u.userId

            LEFT JOIN exams e
                ON r.examId = e.examId

            LEFT JOIN subjects s
                ON e.subjectId = s.subjectId

            LEFT JOIN grades g
                ON s.gradeId = g.gradeId

            LEFT JOIN lessons l
                ON e.lessonId = l.lessonId

            LEFT JOIN chapters c
                ON l.chapterId = c.chapterId

            ORDER BY r.resultId DESC

            LIMIT {$limit} OFFSET {$offset}
        ");

        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $this->db
            ->query("SELECT COUNT(*) FROM results")
            ->fetchColumn();

        return [
            'results' => $results,
            'total'   => $total
        ];
    }



    // =====================================================
    // ADMIN SEARCH
    // =====================================================
    public function searchAdmin($keyword = '')
    {
        $sql = "
            SELECT
                r.*,

                u.fullName,
                u.email,

                e.title AS examTitle,

                s.subjectName,
                g.gradeName,

                l.lessonName,
                l.sortOrder AS lessonSortOrder,

                c.chapterName,
                c.sortOrder AS chapterSortOrder,

                (
                    SELECT COUNT(*)
                    FROM exam_questions eq
                    WHERE eq.examId = e.examId
                ) AS realTotalQuestions,

                COALESCE(
                    ROUND(
                        (
                            r.totalCorrect /
                            NULLIF(
                                (
                                    SELECT COUNT(*)
                                    FROM exam_questions eq2
                                    WHERE eq2.examId = e.examId
                                ),
                                0
                            )
                        ) * 10,
                        1
                    ),
                    0
                ) AS realScore

            FROM results r

            LEFT JOIN users u
                ON r.userId = u.userId

            LEFT JOIN exams e
                ON r.examId = e.examId

            LEFT JOIN subjects s
                ON e.subjectId = s.subjectId

            LEFT JOIN grades g
                ON s.gradeId = g.gradeId

            LEFT JOIN lessons l
                ON e.lessonId = l.lessonId

            LEFT JOIN chapters c
                ON l.chapterId = c.chapterId
        ";

        $params = [];

        if ($keyword !== '') {

            $sql .= "
                WHERE
                    r.resultId LIKE ?
                    OR u.fullName LIKE ?
                    OR u.email LIKE ?
                    OR e.title LIKE ?
                    OR s.subjectName LIKE ?
                    OR g.gradeName LIKE ?
                    OR CAST(r.score AS CHAR) LIKE ?
                    OR DATE_FORMAT(r.endTime, '%d/%m/%Y') LIKE ?
            ";

            $search = "%{$keyword}%";

            $params = [
                $search,
                $search,
                $search,
                $search,
                $search,
                $search,
                $search,
                $search
            ];
        }

        $sql .= "
            ORDER BY r.resultId DESC
            LIMIT 200
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // =====================================================
    // ADMIN - FULL DETAIL
    // =====================================================
    public function getFullDetail($id)
    {
        $stmt = $this->db->prepare("
            SELECT
                r.*,

                u.fullName,
                u.email,

                e.title AS examTitle,

                s.subjectName,
                g.gradeName,

                (
                    SELECT COUNT(*)
                    FROM exam_questions eq
                    WHERE eq.examId = e.examId
                ) AS realTotalQuestions,

                COALESCE(
                    ROUND(
                        (
                            r.totalCorrect /
                            NULLIF(
                                (
                                    SELECT COUNT(*)
                                    FROM exam_questions eq2
                                    WHERE eq2.examId = e.examId
                                ),
                                0
                            )
                        ) * 10,
                        1
                    ),
                    0
                ) AS realScore

            FROM results r

            LEFT JOIN users u
                ON r.userId = u.userId

            LEFT JOIN exams e
                ON r.examId = e.examId

            LEFT JOIN subjects s
                ON e.subjectId = s.subjectId

            LEFT JOIN grades g
                ON s.gradeId = g.gradeId

            WHERE r.resultId = ?

            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



        // =====================================================
        // DASHBOARD
        // =====================================================

    // Tổng lượt làm bài trong tháng hiện tại
    public function countThisMonth()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM results 
            WHERE MONTH(endTime) = MONTH(NOW())
            AND YEAR(endTime) = YEAR(NOW())
        ");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Điểm trung bình toàn hệ thống
    public function getAvgScoreAll()
    {
        $stmt = $this->db->query("
            SELECT ROUND(AVG(score), 1) FROM results
        ");
        return (float)$stmt->fetchColumn();
    }

    // Tỉ lệ đỗ (score > 8)
    public function getPassRate()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN score > 8 THEN 1 ELSE 0 END) AS passed
            FROM results
        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row['total']) return 0;
        return round(($row['passed'] / $row['total']) * 100, 1);
    }

    public function getMonthlyStats($year = null)
    {
        $year = $year ?? date('Y');
        $stmt = $this->db->prepare("
            SELECT 
                MONTH(endTime) AS monthNum,
                CONCAT('Tháng ', MONTH(endTime)) AS month,
                COUNT(*) AS count
            FROM results
            WHERE YEAR(endTime) = ?
            AND endTime IS NOT NULL
            GROUP BY MONTH(endTime), month   /* <-- thêm 'month' vào GROUP BY */
            ORDER BY MONTH(endTime) ASC
        ");
        $stmt->execute([$year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Top 5 môn học có lượt nộp bài nhiều nhất
    public function getTopSubjects($limit = 5)
    {
        $limit = (int)$limit;
        $stmt = $this->db->prepare("
            SELECT 
                s.subjectId,
                s.subjectName,
                g.gradeName,
                COUNT(r.resultId) AS totalSubmits
            FROM results r
            JOIN exams e    ON r.examId    = e.examId
            JOIN subjects s ON e.subjectId = s.subjectId
            JOIN grades g   ON s.gradeId   = g.gradeId
            GROUP BY s.subjectId, s.subjectName, g.gradeName
            ORDER BY totalSubmits DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tổng lượt nộp bài (dùng để tính %)
    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM results");
        return (int)$stmt->fetchColumn();
    }

}