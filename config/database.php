<?php

class Database
{
    private static $host = "localhost";
    private static $db_name = "exam_review_test";
    private static $username = "root";
    private static $password = "123456";

    public static function getConnection()
    {
        try {
            $conn = new PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8",
                self::$username,
                self::$password
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conn;

        } catch (PDOException $e) {
            die("Kết nối database thất bại: " . $e->getMessage());
        }
    }
}