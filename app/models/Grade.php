<?php

require_once ROOT . "/app/core/Model.php";

class Grade extends Model
{

    // ===== USER =====

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




    // ===== ADMIN =====

    public function getAll()
    {
        $sql = "SELECT * FROM grades ORDER BY gradeId DESC";
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

    // ✅ update cả gradeName lẫn slug
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

    // ✅ CHECK TRÙNG khi CREATE — check cả name lẫn slug (không phân biệt hoa/thường)
    public function existsFull($name, $slug)
    {
        $sql = "SELECT 1 FROM grades 
                WHERE LOWER(gradeName) = LOWER(?) OR LOWER(slug) = LOWER(?)
                LIMIT 1";

        return !empty($this->fetch($sql, [$name, $slug]));
    }

    // ✅ CHECK TRÙNG khi UPDATE — check cả name lẫn slug, loại trừ chính nó
    public function existsFullExcept($name, $slug, $id)
    {
        $sql = "SELECT 1 FROM grades 
                WHERE (LOWER(gradeName) = LOWER(?) OR LOWER(slug) = LOWER(?))
                AND gradeId != ?
                LIMIT 1";

        return !empty($this->fetch($sql, [$name, $slug, $id]));
    }
}