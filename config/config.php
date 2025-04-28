<?php
class Config
{
    private static $pdo = null;
    
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "zebla";
            try {
                // Connexion à la base de données avec PDO
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
                

            } catch (Exception $e) {
                // En cas d'échec de connexion, on arrête le script et affiche l'erreur
                die('Erreur de connexion à la base de données : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

// Test de connexion
Config::getConnexion();
?>
