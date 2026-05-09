<?php

require_once ROOT . '/app/core/Model.php';

class Subject extends Model
{
    public function getByGrade($gradeId)
    {
        $sql = "SELECT * FROM subjects WHERE gradeId = ?";
        return $this->fetchAll($sql, [$gradeId]);
    }


    public function getById($subjectId)
    {
        $sql = "SELECT * FROM subjects WHERE subjectId = ?";
        return $this->fetch($sql, [$subjectId]);
    }



    // lấy theo slug
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM subjects WHERE slug = ?";
        return $this->fetch($sql, [$slug]);
    }


    // dùng cho URL dạng /lop-10/lich-su
    public function getBySlugAndGrade($slug, $gradeId)
    {
        $sql = "SELECT * FROM subjects WHERE slug = ? AND gradeId = ?";
        return $this->fetch($sql, [$slug, $gradeId]);
    }




    // ========== ADMIN ==========
    public function getAll()
    {
        $sql = "SELECT s.*, g.gradeName 
                FROM subjects s
                JOIN grades g ON s.gradeId = g.gradeId
                ORDER BY s.subjectId DESC";

        return $this->fetchAll($sql);
    }


    public function create($name, $slug, $gradeId, $image = null, $desc = null, $detail = null)
    {
        $sql = "INSERT INTO subjects (subjectName, slug, gradeId, image, description, detailDesc)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$name, $slug, $gradeId, $image, $desc, $detail]);
    }


    public function exists($slug, $gradeId, $excludeId = null)
    {
        $sql = "SELECT * FROM subjects WHERE slug = ? AND gradeId = ?";
        $params = [$slug, $gradeId];

        if ($excludeId) {
            $sql .= " AND subjectId != ?";
            $params[] = $excludeId;
        }

        return !empty($this->fetchAll($sql, $params));
    }

    // Lấy theo ID (thay cho find)
    public function find($id)
    {
        $sql = "SELECT * FROM subjects WHERE subjectId = ?";
        return $this->fetch($sql, [$id]);
    }


    // UPDATE linh hoạt
    public function update($id, $data)
    {
        $sql = "UPDATE subjects SET 
                    subjectName = ?, 
                    slug = ?, 
                    gradeId = ?, 
                    description = ?, 
                    detailDesc = ?";

        $params = [
            $data['subjectName'],
            $data['slug'],
            $data['gradeId'],
            $data['description'],
            $data['detailDesc']
        ];

        // nếu có ảnh thì update thêm
        if (!empty($data['image'])) {
            $sql .= ", image = ?";
            $params[] = $data['image'];
        }

        $sql .= " WHERE subjectId = ?";
        $params[] = $id;

        return $this->execute($sql, $params);
    }

    public function existsFull($name, $slug, $gradeId)
    {
        $sql = "SELECT 1 FROM subjects 
                WHERE gradeId = ?
                AND (LOWER(subjectName) = LOWER(?) OR slug = ?)
                LIMIT 1";

        return !empty($this->fetch($sql, [$gradeId, $name, $slug]));
    }

    public function existsFullExcept($name, $slug, $gradeId, $id)
    {
        $sql = "SELECT 1 FROM subjects 
                WHERE gradeId = ?
                AND (LOWER(subjectName) = LOWER(?) OR slug = ?)
                AND subjectId != ?
                LIMIT 1";

        return !empty($this->fetch($sql, [$gradeId, $name, $slug, $id]));
    }


    public function delete($id)
    {
        $sql = "DELETE FROM subjects WHERE subjectId = ?";
        return $this->execute($sql, [$id]);
    }


}





