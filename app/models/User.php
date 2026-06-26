<?php

require_once ROOT . '/config/database.php';

class User
{
    public $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function create($fullName, $email, $password)
    {
        $sql = "INSERT INTO users (fullName, email, password)
                VALUES (:fullName, :email, :password)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'fullName' => $fullName,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function find($id)
    {
        $sql = "SELECT * FROM users WHERE userId = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAvatar($userId, $avatarPath)
    {
        $sql = "UPDATE users SET avatar = :avatar WHERE userId = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'avatar' => $avatarPath,
            'id'     => $userId,
        ]);
    }


    public function updateName($userId, $fullName)
    {
        $sql = "UPDATE users SET fullName = :fullName WHERE userId = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'fullName' => $fullName,
            'id'       => $userId,
        ]);
    }

    public function findByEmailExceptId($email, $userId)
    {
        $sql = "SELECT * FROM users
                WHERE email = :email
                AND userId != :userId
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'email'  => $email,
            'userId' => $userId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 
    public function updateEmail($userId, $email)
    {
        $sql = "UPDATE users SET email = :email WHERE userId = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'email' => $email,
            'id'    => $userId,
        ]);
    }
 
    public function updatePassword($userId, $hashedPassword)
    {
        $sql = "UPDATE users SET password = :password WHERE userId = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'password' => $hashedPassword,
            'id'       => $userId,
        ]);
    }
 
    public function delete($userId)
    {
        $sql = "DELETE FROM users WHERE userId = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $userId]);
    }
 
    public function getAll()
    {
        $sql = "SELECT userId, fullName, email, avatar, role, status, createdAt
                FROM users ORDER BY createdAt DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePasswordByEmail($email, $hashedPassword)
    {
        $sql = "
            UPDATE users
            SET password = :password
            WHERE email = :email
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'password' => $hashedPassword,
            'email'    => $email
        ]);

        return $stmt->rowCount();
    }


    // ============ SEARCH ADMIN USERS ================
    public function searchAdminUsers($keyword = '')
    {
        $sql = "
            SELECT
                userId,
                fullName,
                email,
                avatar,
                role,
                status,
                createdAt
            FROM users
            WHERE
                fullName LIKE :keyword
                OR email LIKE :keyword
                OR role LIKE :keyword
            ORDER BY createdAt DESC
        ";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'keyword' => '%' . $keyword . '%'
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ======== ĐẾM SỐ LƯỢNG ADMIN ==============
    public function countAdmins()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) FROM users WHERE role = 'admin'
        ");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // ======  LẤY ROLE CỦA USER =============
    public function getRoleById($userId)
    {
        $stmt = $this->conn->prepare("
            SELECT role FROM users WHERE userId = :id LIMIT 1
        ");
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['role'] : null;
    }



    // =====================================================
    // DASHBOARD
    // =====================================================

    // Học viên mới trong tháng
    public function countNewThisMonth()
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM users 
            WHERE MONTH(createdAt) = MONTH(NOW())
            AND YEAR(createdAt) = YEAR(NOW())
            AND role = 'user'
        ");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Top 5 học viên có điểm TB cao nhất
    public function getTopStudents($limit = 5)
    {
        $limit = (int)$limit;
        $stmt = $this->conn->prepare("
            SELECT 
                u.userId,
                u.fullName,
                u.avatar,
                COUNT(r.resultId)      AS totalExams,
                ROUND(AVG(r.score), 1) AS avgScore,
                (
                    SELECT CONCAT(s.subjectName, ' - ', g.gradeName)
                    FROM results r2
                    JOIN exams e2    ON r2.examId    = e2.examId
                    JOIN subjects s  ON e2.subjectId = s.subjectId
                    JOIN grades g    ON s.gradeId    = g.gradeId
                    WHERE r2.userId = u.userId
                    GROUP BY s.subjectId
                    ORDER BY COUNT(*) DESC
                    LIMIT 1
                ) AS favoriteSubject
            FROM users u
            JOIN results r ON r.userId = u.userId
            WHERE u.role = 'user'
            GROUP BY u.userId, u.fullName, u.avatar
            ORDER BY avgScore DESC
            LIMIT $limit
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}