<?php

require_once ROOT . "/app/core/Model.php";

class Question extends Model
{
    protected $table = 'questions';

    // =====================================================
    // ADMIN - PAGINATION
    // =====================================================
    public function getAllPaginated($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT
                q.*,

                g.gradeName,
                s.subjectName,

                c.chapterName,
                c.sortOrder AS chapterSortOrder,

                l.lessonName,
                l.sortOrder AS lessonSortOrder

            FROM questions q

            LEFT JOIN grades g
                ON q.gradeId = g.gradeId

            LEFT JOIN subjects s
                ON q.subjectId = s.subjectId

            LEFT JOIN chapters c
                ON q.chapterId = c.chapterId

            LEFT JOIN lessons l
                ON q.lessonId = l.lessonId

            ORDER BY q.questionId DESC

            LIMIT {$limit} OFFSET {$offset}
        ");

        $stmt->execute();

        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = $this->db
            ->query("SELECT COUNT(*) FROM questions")
            ->fetchColumn();

        return [
            'questions' => $questions,
            'total' => $total
        ];
    }

    // =====================================================
    // GET FULL BY ID
    // =====================================================
    public function getFullById($id)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM questions
            WHERE questionId = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $question = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$question) {
            return null;
        }

        $stmt2 = $this->db->prepare("
            SELECT *
            FROM answers
            WHERE questionId = ?
            ORDER BY answerId ASC
        ");

        $stmt2->execute([$id]);

        $question['answers'] =
            $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $question;
    }


    public function getByIds(array $ids)
    {
        if (empty($ids)) return [];

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $idList       = implode(',', array_map('intval', $ids));

        $sql = "
            SELECT *
            FROM questions
            WHERE questionId IN ({$placeholders})
            ORDER BY FIELD(questionId, {$idList})
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_map('intval', $ids));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // CREATE QUESTION + ANSWERS
    // =====================================================
    public function createQuestionWithAnswers($data, $userId)
    {
        try {

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO questions
                (
                    gradeId,
                    subjectId,
                    chapterId,
                    lessonId,

                    content,
                    level,
                    questionType,

                    createdBy
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['gradeId'],
                $data['subjectId'],

                !empty($data['chapterId'])
                    ? $data['chapterId']
                    : null,

                !empty($data['lessonId'])
                    ? $data['lessonId']
                    : null,

                $data['content'],
                !empty($data['level'])
                    ? $data['level']
                    : null,
                $data['questionType'],

                $userId
            ]);

            $questionId = $this->db->lastInsertId();

            // chỉ insert answer có nội dung
            foreach ($data['answers'] as $index => $answer) {

                $answer = trim($answer);

                if ($answer === '') {
                    continue;
                }

                $isCorrect =
                    ((string)$index === (string)$data['correctAnswer'])
                    ? 1
                    : 0;

                $stmt2 = $this->db->prepare("
                    INSERT INTO answers
                    (
                        questionId,
                        content,
                        isCorrect
                    )
                    VALUES (?, ?, ?)
                ");

                $stmt2->execute([
                    $questionId,
                    $answer,
                    $isCorrect
                ]);
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            $this->db->rollBack();

            die($e->getMessage());
        }
    }

    // =====================================================
    // UPDATE QUESTION + ANSWERS
    // =====================================================
    public function updateQuestionWithAnswers(
        $questionId,
        $data
    ) {
        try {

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE questions
                SET
                    gradeId = ?,
                    subjectId = ?,
                    chapterId = ?,
                    lessonId = ?,

                    content = ?,
                    level = ?,
                    questionType = ?

                WHERE questionId = ?
            ");

            $stmt->execute([
                $data['gradeId'],
                $data['subjectId'],

                !empty($data['chapterId'])
                    ? $data['chapterId']
                    : null,

                !empty($data['lessonId'])
                    ? $data['lessonId']
                    : null,

                $data['content'],
                !empty($data['level'])
                    ? $data['level']
                    : null,
                $data['questionType'],

                $questionId
            ]);

            // xóa answer cũ
            $stmt2 = $this->db->prepare("
                DELETE FROM answers
                WHERE questionId = ?
            ");

            $stmt2->execute([$questionId]);

            // answer mới
            $answers = [
                trim($data['answerA'] ?? ''),
                trim($data['answerB'] ?? ''),
                trim($data['answerC'] ?? ''),
                trim($data['answerD'] ?? ''),
            ];

            foreach ($answers as $index => $answer) {

                // bỏ qua answer rỗng
                if ($answer === '') {
                    continue;
                }

                $isCorrect =
                    ((string)$index === (string)$data['correctAnswer'])
                    ? 1
                    : 0;

                $stmt3 = $this->db->prepare("
                    INSERT INTO answers
                    (
                        questionId,
                        content,
                        isCorrect
                    )
                    VALUES (?, ?, ?)
                ");

                $stmt3->execute([
                    $questionId,
                    $answer,
                    $isCorrect
                ]);
            }

            $this->db->commit();

            return true;

        } catch (Exception $e) {

            $this->db->rollBack();

            die($e->getMessage());
        }
    }

    // =====================================================
    // DELETE
    // =====================================================
    public function delete($id)
    {
        $this->execute(
            "DELETE FROM answers WHERE questionId = ?",
            [$id]
        );

        return $this->execute(
            "DELETE FROM questions WHERE questionId = ?",
            [$id]
        );
    }


    public function isUsedInExam($id)
    {
        $sql = "SELECT 1 FROM exam_questions WHERE questionId = ? LIMIT 1";
        return !empty($this->fetch($sql, [$id]));
    }

    // =====================================================
    // USER - GET EXAM QUESTIONS
    // =====================================================
    public function getByExamWithAnswers($examId)
    {
        $stmt = $this->db->prepare("
            SELECT q.*
            FROM questions q

            JOIN exam_questions eq
                ON q.questionId = eq.questionId

            WHERE eq.examId = ?

            ORDER BY eq.questionOrder ASC, eq.id ASC
        ");

        $stmt->execute([$examId]);

        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questions as &$q) {

            $stmt2 = $this->db->prepare("
                SELECT
                    answerId,
                    content,
                    isCorrect
                FROM answers
                WHERE questionId = ?
                ORDER BY answerId ASC
            ");

            $stmt2->execute([
                $q['questionId']
            ]);

            $q['answers'] =
                $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $questions;
    }

    // =====================================================
    // USER - GET BY EXAM
    // =====================================================
    public function getByExam($examId)
    {
        $stmt = $this->db->prepare("
            SELECT
                q.*,
                eq.questionOrder

            FROM exam_questions eq

            INNER JOIN questions q
                ON eq.questionId = q.questionId

            WHERE eq.examId = ?
            AND q.isActive = 1

            ORDER BY eq.questionOrder ASC, eq.id ASC
        ");

        $stmt->execute([$examId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =====================================================
    // SEARCH AJAX
    // =====================================================
    public function searchAjax($keyword = '')
    {
        $stmt = $this->db->prepare("
            SELECT
                q.*,
                g.gradeName,
                s.subjectName,
                c.chapterName,
                c.sortOrder AS chapterSortOrder,
                l.lessonName,
                l.sortOrder AS lessonSortOrder

            FROM questions q

            LEFT JOIN grades g
                ON q.gradeId = g.gradeId

            LEFT JOIN subjects s
                ON q.subjectId = s.subjectId

            LEFT JOIN chapters c
                ON q.chapterId = c.chapterId

            LEFT JOIN lessons l
                ON q.lessonId = l.lessonId

            WHERE
                q.content      LIKE ?
                OR s.subjectName LIKE ?
                OR g.gradeName   LIKE ?
                OR c.chapterName LIKE ?
                OR l.lessonName  LIKE ?

            ORDER BY
                CASE
                    WHEN q.content      LIKE ? THEN 1
                    WHEN l.lessonName   LIKE ? THEN 2
                    WHEN c.chapterName  LIKE ? THEN 3
                    WHEN s.subjectName  LIKE ? THEN 4
                    WHEN g.gradeName    LIKE ? THEN 5
                    ELSE 6
                END,
                q.questionId DESC

            LIMIT 100
        ");

        $search = '%' . $keyword . '%';

        $stmt->execute([
            // WHERE (5 params)
            $search,
            $search,
            $search,
            $search,
            $search,
            // ORDER BY CASE (5 params)
            $search,
            $search,
            $search,
            $search,
            $search,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // =========================================================
    // GET BY LESSON
    // =========================================================
    public function getByLesson($lessonId)
    {
        $sql = "
            SELECT *
            FROM questions
            WHERE lessonId = ?
            ORDER BY questionId DESC
        ";

        return $this->fetchAll($sql, [$lessonId]);
    }


    // =========================================================
    // THPT QUESTIONS
    // =========================================================
    public function getThptQuestions($gradeId, $subjectId)
    {
        $sql = "
            SELECT *
            FROM questions
            WHERE gradeId = ?
            AND subjectId = ?
            AND questionType = 'thpt'
            ORDER BY questionId DESC
        ";

        return $this->fetchAll($sql, [
            $gradeId,
            $subjectId
        ]);
    }


    public function getByExamOrdered($examId, $questionOrder = 'manual')
    {
        $stmt = $this->db->prepare("
            SELECT
                q.*,
                eq.questionOrder
            FROM exam_questions eq
            INNER JOIN questions q
                ON eq.questionId = q.questionId
            WHERE eq.examId = ?
            AND q.isActive = 1
            ORDER BY eq.questionOrder ASC, eq.id ASC
        ");

        $stmt->execute([$examId]);
        $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Nếu random thì shuffle
        if ($questionOrder === 'random') {
            shuffle($questions);
        }

        return $questions;
    }


    // =====================================================
    // RANDOM QUESTIONS BY LEVEL
    // =====================================================

    public function getRandomQuestionsByLevel($gradeId, $subjectId, $level, $limit) 
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM questions

            WHERE
                gradeId = ?
                AND subjectId = ?
                AND questionType = 'thpt'
                AND level = ?
                AND isActive = 1

            ORDER BY RAND()

            LIMIT ?
        ");

        $stmt->bindValue(1, $gradeId, PDO::PARAM_INT);
        $stmt->bindValue(2, $subjectId, PDO::PARAM_INT);
        $stmt->bindValue(3, $level, PDO::PARAM_STR);
        $stmt->bindValue(4, (int)$limit, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =====================================================
    // COUNT QUESTIONS BY LEVEL
    // =====================================================

    public function countByLevel($subjectId, $level)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*)

            FROM questions

            WHERE
                subjectId = ?
                AND questionType = 'thpt'
                AND level = ?
                AND isActive = 1
        ");

        $stmt->execute([
            $subjectId,
            $level
        ]);

        return (int)$stmt->fetchColumn();
    }



}