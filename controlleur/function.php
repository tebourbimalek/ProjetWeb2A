<?php

include_once 'C:\xampp\htdocs\projetweb\model\config.php';
function affichage($user_id) {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();
        
        // Requête SQL pour récupérer les chansons en fonction de l'user_id
        $sql = "SELECT id, image_path, song_title, album_name, release_date, duree FROM chanson WHERE artiste_id = :user_id";
        
        // Préparer la requête avec les paramètres
        $stmt = $pdo->prepare($sql);
        
        // Lier la variable $user_id à :user_id
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        // Exécuter la requête
        $stmt->execute();
        
        // Récupération des résultats
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Vérifier si des chansons ont été trouvées
        if (count($songs) === 0) {
            // Enregistrer l'information dans la session si aucune chanson n'est trouvée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error_message'] = 'Aucune chanson trouvée pour cet utilisateur.';
            return [];
        }
        
        // Retourner les chansons récupérées
        return $songs;
        
    } catch (PDOException $e) {
        // Démarrer la session si ce n'est pas encore fait
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Enregistrer l'erreur dans la session
        $errorMessage = 'Erreur de base de données: ' . $e->getMessage();
        $_SESSION['error_message'] = $errorMessage;
        
        // Log the error to a file for further investigation (optional)
        error_log($errorMessage, 3, 'errors.log');
        
        // Retourner un tableau vide en cas d'erreur
        return [];
    }
}

function affichagetrier($user_id) {
    try {
        // Connexion à la base de données
        $pdo = config::getConnexion();

        // Requête SQL pour récupérer les chansons triées par le nombre de streams et filtrées par user_id
        $sql = "SELECT c.id, c.image_path, c.song_title, c.album_name, c.duree, 
                       IFNULL(SUM(s.stream_count), 0) AS total_streams
                FROM chanson c
                LEFT JOIN streaming_stats s ON c.id = s.music_id
                WHERE c.artiste_id = :user_id
                GROUP BY c.id, c.image_path, c.song_title, c.album_name, c.duree
                ORDER BY total_streams DESC";
        
        // Préparer la requête
        $stmt = $pdo->prepare($sql);

        // Lier la variable $user_id à :user_id
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();
        
        // Récupération des résultats
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retourner les chansons triées
        return $songs;
        
    } catch (PDOException $e) {
        // Retourner un tableau vide en cas d'erreur
        return [];
    }
}



function deleteSong($pdo, $songId) {
    $query = "DELETE FROM chanson WHERE id = :song_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':song_id', $songId, PDO::PARAM_INT);
    return $stmt->execute();
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


function getplaylist($user_id) {
    try {
        $pdo = config::getConnexion();

        $sql = "SELECT * FROM playlist WHERE utilisateur_id = ?";
        $query = $pdo->prepare($sql);
        $query->execute([$user_id]);

        return $query->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
}


function checkSongExists($pdo, $song_title) {
    $query = "SELECT COUNT(*) FROM chanson WHERE song_title = :song_title";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':song_title', $song_title);
    $stmt->execute();
    return $stmt->fetchColumn();
}

function insertSong($pdo, $song_title, $album_name, $release_date, $cover_path, $audio_path, $duree) {
    $query = "INSERT INTO chanson (song_title, album_name, release_date, image_path, music_path, artiste_id, duree)
              VALUES (:song_title, :album_name, :release_date, :cover_path, :audio_path, 1, :duree)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':song_title', $song_title);
    $stmt->bindParam(':album_name', $album_name);
    $stmt->bindParam(':release_date', $release_date);
    $stmt->bindParam(':cover_path', $cover_path);
    $stmt->bindParam(':audio_path', $audio_path);
    $stmt->bindParam(':duree', $duree);
    return $stmt->execute();
}

function getSongPaths($pdo, $songId) {
    $query = "SELECT music_path, image_path FROM chanson WHERE id = :song_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':song_id', $songId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function getMusicPathById($pdo,$songId) {
    $stmt = $pdo->prepare("SELECT music_path FROM chanson WHERE id = :song_id");
    $stmt->bindParam(':song_id', $songId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getSongById($pdo, $song_id) {
    try {
        $stmt = $pdo->prepare("SELECT music_path, image_path FROM chanson WHERE id = :song_id");
        $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
}

function updateSong($pdo, $song_id, $song_title, $album_name, $release_date, $duree, $music_path, $image_path) {
    try {
        $update_stmt = $pdo->prepare("
            UPDATE chanson 
            SET song_title = :song_title, 
                album_name = :album_name, 
                release_date = :release_date, 
                duree = :duree, 
                music_path = :music_path, 
                image_path = :image_path 
            WHERE id = :song_id
        ");

        $update_stmt->bindParam(':song_title', $song_title, PDO::PARAM_STR);
        $update_stmt->bindParam(':album_name', $album_name, PDO::PARAM_STR);
        $update_stmt->bindParam(':release_date', $release_date, PDO::PARAM_STR);
        $update_stmt->bindParam(':duree', $duree, PDO::PARAM_STR);
        $update_stmt->bindParam(':music_path', $music_path, PDO::PARAM_STR);
        $update_stmt->bindParam(':image_path', $image_path, PDO::PARAM_STR);
        $update_stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);

        return $update_stmt->execute();
    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
}

?>