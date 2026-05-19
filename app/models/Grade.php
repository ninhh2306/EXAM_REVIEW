<?php

require_once ROOT . "/app/core/Model.php";

class Grade extends Model
{

    // ================= USER ==================

    public function getAllAsc()
    {
        $sql = "SELECT * FROM grades ORDER BY gradeId ASC";
        return $this->fetchAll($sql);
    }

    public function getById($gradeId)
    {
        $sql = "SELECT * FROM grades WHERE gradeId = ?";
        return $this->fetch($sql, [$gradeId]);
    }


    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM grades WHERE slug = ?";
        return $this->fetch($sql, [$slug]);
    }


    public function bySlug($gradeSlug, $subjectSlug)
    {
        $subjectModel = new Subject();
        $lessonModel  = new Lesson();

        $grade = $this->getBySlug($gradeSlug);
        if (!$grade) return false;

        $subject = $subjectModel->getBySlugAndGrade($subjectSlug, $grade['gradeId']);
        if (!$subject) return false;

        $lessons = $lessonModel->getBySubject($subject['subjectId']);

        return compact('grade', 'subject', 'lessons');
    }


    // =============== ADMIN ==================

    public function getAll()
    {
        $sql = "SELECT * FROM grades ORDER BY gradeId ASC";
        return $this->fetchAll($sql);
        
    }

    public function find($id)
    {
        $sql = "SELECT * FROM grades WHERE gradeId = ?";
        return $this->fetch($sql, [$id]);
    }

    public function create($name, $slug)
    {
        $sql = "INSERT INTO grades (gradeName, slug) VALUES (?, ?)";
        return $this->execute($sql, [$name, $slug]);
    }

    // update cả gradeName lẫn slug
    public function update($id, $name, $slug)
    {
        $sql = "UPDATE grades SET gradeName = ?, slug = ? WHERE gradeId = ?";
        return $this->execute($sql, [$name, $slug, $id]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM grades WHERE gradeId = ?";
        return $this->execute($sql, [$id]);
    }

    public function hasSubjects($gradeId)
    {
        $sql = "SELECT 1 FROM subjects WHERE gradeId = ? LIMIT 1";
        return !empty($this->fetch($sql, [$gradeId]));
    }

    public function hasExams($gradeId)
    {
        $sql = "SELECT 1 FROM exams WHERE gradeId = ? LIMIT 1";
        return !empty($this->fetch($sql, [$gradeId]));
    }

    public function existsName($name)
    {
        $sql = "SELECT 1 FROM grades
                WHERE LOWER(gradeName) = LOWER(?)
                LIMIT 1";

        return !empty($this->fetch($sql, [$name]));
    }

    public function existsSlug($slug)
    {
        $sql = "SELECT 1 FROM grades
                WHERE LOWER(slug) = LOWER(?)
                LIMIT 1";

        return !empty($this->fetch($sql, [$slug]));
    }


    public function existsNameExcept($name, $id)
    {
        $sql = "SELECT 1 FROM grades
                WHERE LOWER(gradeName) = LOWER(?)
                AND gradeId != ?
                LIMIT 1";

        return !empty($this->fetch($sql, [$name, $id]));
    }


    public function existsSlugExcept($slug, $id)
    {
        $sql = "SELECT 1 FROM grades
                WHERE LOWER(slug) = LOWER(?)
                AND gradeId != ?
                LIMIT 1";

        return !empty($this->fetch($sql, [$slug, $id]));
    }


    public function search($keyword)
    {
        $sql = "
            SELECT *
            FROM grades
            WHERE
                gradeName LIKE ?
                OR slug LIKE ?
            ORDER BY gradeId DESC
        ";

        return $this->fetchAll($sql, [
            "%{$keyword}%",
            "%{$keyword}%"
        ]);
    }


    public function all()
    {
        $sql = "
            SELECT *
            FROM grades
            ORDER BY gradeId ASC
        ";

        return $this->fetchAll($sql);
    }


}