<?php


function getAllNews($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT n.*, COUNT(c.id) AS comment_count
            FROM news n
            LEFT JOIN commentaires c ON n.id = c.id_news
            GROUP BY n.id
            ORDER BY n.id DESC
        ");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error fetching news with comment count: " . $e->getMessage();
        return [];
    }
}

function ajouterCommentaire($pdo, $id_news, $auteur, $contenu, $id_user) {
    try {
        // Prepare the SQL query with placeholders
        $query = "INSERT INTO commentaires (id_news, auteur, contenu, date_commentaire, id_user) 
                  VALUES (:id_news, :auteur, :contenu, NOW(), :id_user)";

        // Prepare the statement
        $stmt = $pdo->prepare($query);

        // Bind the parameters to the placeholders
        $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
        $stmt->bindParam(':auteur', $auteur, PDO::PARAM_STR);
        $stmt->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);

        // Execute the query and check if it was successful
        if ($stmt->execute()) {
            // Return the ID of the inserted comment
            return $pdo->lastInsertId();
        } else {
            // If the query fails, return an error message or false
            return false;
        }
    } catch (PDOException $e) {
        // Handle errors gracefully, return false or log the error
        error_log('Error in adding comment: ' . $e->getMessage());  // Log the error for debugging
        return false;
    }
}

// Function to check if the user has reacted to the news
function hasUserReactednews($conn, $id_news, $id_user) {
    // Prepare the SQL query
    $sql = "SELECT COUNT(*) FROM reactions WHERE id_news = :id_news AND id_user = :id_user";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);

    // Bind parameters using PDO's bindParam
    $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch the result
    $reactionCount = $stmt->fetchColumn();
    
    // Check if the user has reacted
    if ($reactionCount > 0) {
        return true;  // User has reacted
    } else {
        return false; // User has not reacted
    }
}



function ajouterReaction($pdo, $id_news, $user_id) {
    try {
        $query = "INSERT INTO reactions (id_news, id_user, date_reaction) 
                  VALUES (:id_news, :id_user, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
        $stmt->bindParam(':id_user', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $pdo->lastInsertId(); // Return the ID of the inserted reaction
    } catch (PDOException $e) {
        // Handle exception and return false if something goes wrong
        return false;
    }
}


function countReactionsByNews($conn, $id_news) {
    // Prepare the SQL query to count reactions for the specific news ID
    $sql = "SELECT COUNT(*) FROM reactions WHERE id_news = :id_news";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the parameter to the prepared statement
    $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch the result (the count of reactions)
    $reactionCount = $stmt->fetchColumn();
    
    // Return the count
    return $reactionCount;
}

function localGetNews() {
    try {
        $pdo = config::getConnexion();
        $stmt = $pdo->query("SELECT * FROM news ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}


function deleteNewsById($pdo, $id_news) {
    $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id_news");
    $stmt->bindParam(':id_news', $id_news, PDO::PARAM_INT);
    return $stmt->execute();
}


?> 