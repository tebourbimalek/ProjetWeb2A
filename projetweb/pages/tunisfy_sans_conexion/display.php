<?php

require_once 'C:\xampp\htdocs\projetweb\includes\config.php';


function chanson(){
    try{
        $pdo = config::getConnexion();
    
    
    
        $stmt = $pdo->query("SELECT chanson.*, utilisateurs.nom_utilisateur AS artist_name
            FROM chanson
            JOIN utilisateurs ON chanson.artiste_id = utilisateurs.artiste_id
            LIMIT 6");
        $albums = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $albums;
    
    }catch (PDOException $e) {
        $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
    }
}




function artiste(){
    try{
        $pdo = config::getConnexion();
    
    
    
        $stmt = $pdo->query("SELECT * from utilisateurs  where type_utilisateur ='artiste' LIMIT 6");
        $artiste = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $artiste;
    
    }catch (PDOException $e) {
        $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
    }
}



function allmusic(){
    try{
        $pdo = config::getConnexion();
    
    
    
        $stmt = $pdo->query("SELECT chanson.*, utilisateurs.nom_utilisateur AS artist_name
            FROM chanson
            JOIN utilisateurs ON chanson.artiste_id = utilisateurs.artiste_id");
        $allmusic = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $allmusic;
    
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