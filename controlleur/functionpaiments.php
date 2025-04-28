<?php 

include_once 'C:\xampp\htdocs\islem\projetweb\model\config.php';

function affichagePaiement() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();
        
        // Requête SQL correcte
        $sql = "SELECT ID, Date, Abonnement, user_id, payment_method FROM transactions";
        $stmt = $pdo->query($sql);
        
        // Récupération des résultats
        $paiements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner les paiements
        return $paiements;
        
    } catch (PDOException $e) {
        // En cas d'erreur, démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Enregistrer l'erreur dans une session ou journal
        $_SESSION['erreur_db'] = $e->getMessage();

        // Retourner un tableau vide
        return [];
    }
}

function affichagePaiementscart() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();
        
        // Requête SQL correcte
        $sql = "SELECT ID, Type_Carte, Numero_Carte, Date_Expiration, Transaction_id FROM paiment_par_card";
        $stmt = $pdo->query($sql);
        
        // Récupération des résultats
        $paiementsCarte = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner les paiements
        return $paiementsCarte;
        
    } catch (PDOException $e) {
        // En cas d'erreur, démarrer la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Enregistrer l'erreur dans une session ou journal
        $_SESSION['erreur_db'] = $e->getMessage();

        // Retourner un tableau vide
        return [];
    }
}





function affichagePaiementsMobile() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();

        // Requête SQL pour la table paiment_mobile
        $sql = "SELECT ID, mobile_provider, phone_number, Date_Expiration, transaction_id FROM paiment_mobile";
        $stmt = $pdo->query($sql);

        // Récupération des résultats
        $paiementsMobile = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retourner les paiements
        return $paiementsMobile;

    } catch (PDOException $e) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['erreur_db'] = $e->getMessage();
        return [];
    }
}




function insertTransaction($conn, $userId, $paymentMethod, $abonnement) {
    $stmt = $conn->prepare("INSERT INTO transactions (user_id, payment_method, date, Abonnement) VALUES (?, ?, NOW(), ?)");
    $stmt->execute([$userId, $paymentMethod, $abonnement]);
    return $conn->lastInsertId();
}

// Function to insert card payment
function insertCardPayment($conn, $transactionId, $cardType, $cardNumber, $expiryDate) {
    $stmt = $conn->prepare("INSERT INTO paiment_par_card (transaction_id, Type_Carte, Numero_Carte, Date_Expiration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$transactionId, $cardType, $cardNumber, $expiryDate]);
}

// Function to insert mobile payment
function insertMobilePayment($conn, $transactionId, $mobileProvider, $phoneNumber, $expirationDate) {
    $stmt = $conn->prepare("INSERT INTO paiment_mobile (transaction_id, mobile_provider, phone_number, Date_Expiration) VALUES (?, ?, ?, ?)");
    $stmt->execute([$transactionId, $mobileProvider, $phoneNumber, $expirationDate]);
}



function deleteTransaction($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE ID = :id");
    return $stmt->execute([':id' => $id]);
}

function deleteCardPayment($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM paiment_par_card WHERE ID = :id");
    return $stmt->execute([':id' => $id]);
}

function deleteMobilePayment($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM paiment_mobile WHERE ID = :id");
    return $stmt->execute([':id' => $id]);
}



function updatePaymentCard($pdo, $song_paimentc_id, $numero_carte, $type_carte, $date_expiration) {
    $query = "UPDATE paiment_par_card 
              SET Numero_Carte = :numero_carte, 
                  Type_Carte = :type_carte, 
                  Date_Expiration = :date_expiration
              WHERE ID = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':numero_carte', $numero_carte);
    $stmt->bindParam(':type_carte', $type_carte);
    $stmt->bindParam(':date_expiration', $date_expiration);
    $stmt->bindParam(':id', $song_paimentc_id, PDO::PARAM_INT);
    return $stmt->execute();
}

function updatePaymentMobile($pdo, $id, $numero, $provider, $expiration) {
    $query = "UPDATE paiment_mobile 
              SET phone_number = :numero, 
                  mobile_provider = :provider, 
                  date_expiration = :expiration
              WHERE ID = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':numero', $numero);
    $stmt->bindParam(':provider', $provider);
    $stmt->bindParam(':expiration', $expiration);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

function updateTransaction($pdo, $song_id, $date_paiement, $abonnement) {
    $query = "UPDATE transactions 
              SET Date = :date_paiement, 
                  Abonnement = :abonnement 
              WHERE ID = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':date_paiement', $date_paiement);
    $stmt->bindParam(':abonnement', $abonnement);
    $stmt->bindParam(':id', $song_id, PDO::PARAM_INT);
    return $stmt->execute();
}


function getPaimentById($id) {
    try {
        $pdo = config::getConnexion();
        $stmt = $pdo->prepare("SELECT * FROM transactions WHERE ID = :id");
        $stmt->execute([':id' => $id]);
        $paiment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$paiment) {
            return false;
        }
        
        // Ajoutez le nom du client 
        $paiment['client_name'] = "Client ID: " . $paiment['user_id']; // Adaptez selon votre structure
        
        // Ajoutez le montant en fonction de l'abonnement
        switch($paiment['Abonnement']) {
            case 'Mini':
                $paiment['montant'] = 9.99;
                break;
            case 'Personnel':
                $paiment['montant'] = 14.99;
                break;
            case 'Duo':
                $paiment['montant'] = 19.99;
                break;
            case 'Familial':
                $paiment['montant'] = 24.99;
                break;
            default:
                $paiment['montant'] = 0;
        }
        
        return $paiment;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du paiement: " . $e->getMessage());
        return false;
    }
}

