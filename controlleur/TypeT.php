<?php

require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\TypeT.php';

class TypeT
{
    // Lister tous les types
    public function listTypes($order = 'ASC')
    {

        $allowedOrders = ['ASC', 'DESC'];
        $order = in_array($order, $allowedOrders) ? $order : 'ASC';
        $sql = "SELECT * FROM type ORDER BY id_typeproduit $order";
        $db = config::getConnexion();
        try {
            $liste = $db->query($sql);
            return $liste->fetchAll();
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function chercher($recherche)
    {
        $sql = "SELECT * FROM type WHERE categorie LIKE :recherche";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(":recherche", "%$recherche%");
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function deleteType($id_type) {
        $sql = "DELETE FROM type WHERE id_type = :id_type";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_type', $id_type, PDO::PARAM_INT);
    
        try {
            $req->execute();
    
            // Log de l'action
            self::logAction('Delete', $id_type, "Type supprimé avec ID: $id_type");
    
            if ($req->rowCount() > 0) {
                echo "Suppression effectuée avec succès.";
            } else {
                echo "Aucun type trouvé avec cet ID, aucune suppression effectuée.";
            }
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
    

    public function addType($id_typeproduit, $categorie, $board) {
        $db = config::getConnexion();
        
        // Vérifier si l'id_produit existe dans la table produit
        try {
            // Vérification de l'existence du produit
            $query = $db->prepare("SELECT id_produit FROM produit WHERE id_produit = :id_produit");
            $query->execute(['id_produit' => $id_typeproduit]);
            
            if ($query->rowCount() > 0) {
                // Si l'id_produit existe, on peut insérer le type dans la table type
                $query = $db->prepare("INSERT INTO type (id_typeproduit, categorie, board) 
                                       VALUES (:id_typeproduit, :categorie, :board)");
                $query->execute([
                    'id_typeproduit' => $id_typeproduit, 
                    'categorie' => $categorie, 
                    'board' => $board
                ]);
                self::logAction('Add', null, "Type ajouté : $categorie (Produit ID: $id_typeproduit)");

                return "Type produit ajouté avec succès !";
            } else {
                return "Erreur : Le produit sélectionné n'existe pas.";
            }
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
    }
    public function updateType($id_typeproduit, $new_id_typeproduit, $new_categorie, $new_board)
    {
        $db = config::getConnexion();
    
        // Validation des entrées (vous pouvez les adapter à vos besoins)
        if (empty($new_categorie) || empty($new_board)) {
            throw new Exception('Les champs ne peuvent pas être vides');
        }
    
        try {
            // Préparation de la requête UPDATE
            $query = $db->prepare(
                'UPDATE type SET id_typeproduit = :id_typeproduit, categorie = :categorie, board = :board
                 WHERE id_typeproduit = :id_typeproduit'
            );
    
            // Exécution de la requête avec les paramètres
            $query->execute([
                'id_typeproduit' => $new_id_typeproduit,
                'categorie' => $new_categorie,
                'board' => $new_board
            ]);
            self::logAction('Update', $id_typeproduit, "Mise à jour : $new_categorie (Nouveau Board: $new_board)");

    
            // Vérification si une ligne a été affectée par la mise à jour
            if ($query->rowCount() > 0) {
                return true; // Mise à jour réussie
            } else {
                return false; // Aucun type mis à jour, probablement parce que l'ID n'existait pas
            }
    
        } catch (PDOException $e) {
            // Log de l'erreur (pour un développement en production, on préfère loguer l'erreur que l'afficher)
            error_log("Erreur lors de la mise à jour du type : " . $e->getMessage());
            throw new Exception('Erreur lors de la mise à jour du type');
        }
    }
    
    

// jointureeee

public function getAllProduits() {
    $db = config::getConnexion();  // Connexion à la base de données
    try {
        $query = $db->prepare('SELECT id_produit FROM produit');  // Récupérer tous les id_produit
        $query->execute();  // Exécuter la requête
        return $query->fetchAll(PDO::FETCH_ASSOC);  // Retourner les résultats sous forme de tableau associatif
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
        return [];  // Retourne un tableau vide en cas d'erreur
    }
}


    public function afficherType($id_typeproduit) {
        $db = config::getConnexion();  // Connexion via la classe config
        try {
            // Préparation de la requête pour récupérer le type par ID
            $query = $db->prepare('SELECT * FROM type WHERE id_typeproduit = :id_typeproduit');
            $query->execute(['id_typeproduit' => $id_typeproduit]);

            // Récupérer les résultats sous forme de tableau associatif
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            // Retourner les résultats
            return $result;
        } catch (PDOException $e) {
            // Gestion des erreurs
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    public static function logAction($action, $id_type = null, $additional_info = '') {
        $logFile = '../../historiquetype/type_history.txt'; // Chemin vers le fichier de log
        $date = new DateTime();
        $formattedDate = $date->format('Y-m-d H:i:s');
    
        // Construire le message de log
        $logMessage = "[$formattedDate] - Action: $action";
        if ($id_type) {
            $logMessage .= " - ID Type: $id_type";
        }
        if ($additional_info) {
            $logMessage .= " - Info: $additional_info";
        }
        $logMessage .= "\n";
    
        // Ajouter le message dans le fichier
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
   
    

    
    
}
?>
