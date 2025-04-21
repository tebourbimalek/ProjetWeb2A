<?php

include_once 'config.php';
function affichage() {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();
        
        // Requête SQL
        $sql = "SELECT id, image_path, song_title, album_name, release_date, duree FROM chanson";
        $stmt = $pdo->query($sql);
        
        // Récupération des résultats
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Retourner les chansons récupérées
        return $songs;
        
    } catch (PDOException $e) {
        // Démarrer la session si ce n'est pas encore fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Enregistrer l'erreur dans la session
        return []; // Retourner un tableau vide en cas d'erreur
    }
}
function affichagetrier () {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();

        // Requête SQL pour récupérer les chansons triées par le nombre de streams
        $sql = "SELECT c.id, c.image_path, c.song_title, c.album_name, c.duree, 
               IFNULL(SUM(s.stream_count), 0) AS total_streams
        FROM chanson c
        LEFT JOIN streaming_stats s ON c.id = s.music_id
        GROUP BY c.id, c.image_path, c.song_title, c.album_name, c.duree
        ORDER BY total_streams DESC";
         // Trie par nombre de streams décroissant

        $stmt = $pdo->query($sql);
        
        // Récupération des résultats
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $songs;
        
    } catch (PDOException $e) {
        return []; // En cas d'erreur, retourner un tableau vide
    }
}


function deleteSong($songId) {
    global $pdo; // Use the database connection globally (assuming $pdo is your database connection)

    // Prepare the DELETE SQL statement
    $stmt = $pdo->prepare("DELETE FROM songs WHERE id = :id");
    $stmt->bindParam(':id', $songId, PDO::PARAM_INT); // Bind the ID parameter to prevent SQL injection
    $stmt->execute(); // Execute the query to delete the song
}

function affichageid($id) {
    $pdo = config::getConnexion();
    
    $query = "SELECT * FROM chanson WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Check if a song was found
    $song = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($song === false) {
        // Handle case when no song is found
        return null;  // Or return a default value, or handle the error as needed
    }

    return $song;  // Return the song if found
}




function generateStreamingStats() {
    try {
        // Get the database connection
        $pdo = config::getConnexion();

        // Get all music IDs from the 'chanson' table (or your specific music table)
        $stmt = $pdo->prepare("SELECT id FROM chanson");
        $stmt->execute();
        $music_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Delete old streaming stats only for existing music IDs
        $delete_stmt = $pdo->prepare("DELETE FROM streaming_stats;");
        $delete_stmt->execute();
        echo "Old streaming stats deleted successfully.\n";

        // Optional: Reset auto-increment (only if needed)
        $pdo->exec("ALTER TABLE streaming_stats AUTO_INCREMENT = 1");

        // Loop through each music ID to generate and insert new streaming stats
        foreach ($music_ids as $music_id) {
            // Generate random streaming stats
            $stream_count = rand(1000, 10000); // Random value for stream count
            $listener_count = rand(100, 1000); // Random value for listener count
            $stat_date = date('Y-m-d H:i:s'); // Current date and time

            // Prepare the insert query for new streaming stats
            $insert_stmt = $pdo->prepare("
                INSERT INTO streaming_stats (music_id, stat_date, stream_count, listener_count)
                VALUES (:music_id, :stat_date, :stream_count, :listener_count)
            ");
            $insert_stmt->bindParam(':music_id', $music_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':stat_date', $stat_date, PDO::PARAM_STR);
            $insert_stmt->bindParam(':stream_count', $stream_count, PDO::PARAM_INT);
            $insert_stmt->bindParam(':listener_count', $listener_count, PDO::PARAM_INT);

            // Execute the insert statement
            $insert_stmt->execute();
            echo "New stats for music ID {$music_id} inserted successfully for {$stat_date}.\n";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


function getStreamingStatistics() {
    try {
        // Database connection
        $pdo = config::getConnexion();

        // Total Listener Count: Sum of all listeners
        $stmt = $pdo->query("SELECT SUM(listener_count) AS total_listeners FROM streaming_stats");
        $totalListeners = $stmt->fetch(PDO::FETCH_ASSOC)['total_listeners'];

        // Top 5 Most Streamed Songs: Sorted by stream_count
        $stmt = $pdo->query("SELECT chanson.song_title, SUM(streaming_stats.stream_count) AS total_streams 
                             FROM streaming_stats
                             JOIN chanson ON streaming_stats.music_id = chanson.id
                             GROUP BY chanson.id
                             ORDER BY total_streams DESC LIMIT 5");
        $topSongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the statistics
        return [
            'total_listeners' => $totalListeners,
            'top_songs' => $topSongs
        ];

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

function getArtistStats() {
    try {
        $pdo = config::getConnexion();

        $sql = "SELECT u.nom_utilisateur, SUM(s.stream_count) AS total_streams
                FROM streaming_stats s
                JOIN chanson c ON s.music_id = c.id
                JOIN utilisateurs u ON c.artiste_id = u.artiste_id
                GROUP BY u.nom_utilisateur
                ORDER BY total_streams DESC
                LIMIT 10";  // Limite aux 10 artistes les plus écoutés

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}


?>