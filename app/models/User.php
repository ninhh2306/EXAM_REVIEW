<?php

require_once ROOT . '/config/database.php';

class User
{
    private $conn;

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

    
}