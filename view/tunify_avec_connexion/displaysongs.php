<?php 




require_once 'C:\xampp\htdocs\islem\projetweb\includes\config.php';



function chansonrand(){
    try{
        $pdo = config::getConnexion();
    
    
    
        $stmt = $pdo->query("SELECT * FROM chanson ORDER BY RAND() LIMIT 10;");
        $chansonrand = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $chansonrand;
    
    }catch (PDOException $e) {
        $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
    }
}


function onechansonrand(){
    try{
        $pdo = config::getConnexion();
    
    
    
        $stmt = $pdo->query("SELECT * FROM chanson ORDER BY RAND() LIMIT 1;");
        $chansonrand = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $chansonrand;
    
    }catch (PDOException $e) {
        $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
    }
}


function allartiste(){
    try{
        $pdo = config::getConnexion();
    
    
    
        $stmt = $pdo->query("SELECT * from utilisateurs where type_utilisateur ='artiste'");
        $artiste = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $artiste;
    
    }catch (PDOException $e) {
        $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
    }
}


























?>