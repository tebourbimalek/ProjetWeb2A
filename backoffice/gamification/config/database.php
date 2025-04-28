<?php
class Database {
    private static $host = 'localhost';
    private static $db_name = 'tunify';  // The name of your database
    private static $username = 'root';   // Default MySQL username for XAMPP
    private static $password = '';       // Default password for XAMPP MySQL
    private static $connection;

    public static function connect() {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
