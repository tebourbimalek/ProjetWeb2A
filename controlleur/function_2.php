<?php
// playlist_functions.php

require_once 'C:\xampp\htdocs\projetweb\model\config.php';

function createPlaylist($utilisateur_id) {
    try {
        // Get the database connection
        $pdo = config::getConnexion();

        // Count existing playlists for this user
        $stmt = $pdo->prepare("SELECT COUNT(*) AS playlist_count FROM playlist WHERE utilisateur_id = :utilisateur_id");
        $stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Generate the playlist name based on the count
        $playlist_number = $result['playlist_count'] + 1;
        $playlist_name = "playlist_" . $playlist_number;

        // Insert the new playlist into the database
        $insert_stmt = $pdo->prepare("INSERT INTO playlist (nom, utilisateur_id) VALUES (:nom, :utilisateur_id)");
        $insert_stmt->bindParam(':nom', $playlist_name, PDO::PARAM_STR);
        $insert_stmt->bindParam(':utilisateur_id', $utilisateur_id, PDO::PARAM_INT);
        $insert_stmt->execute();

        return true;  // Success
    } catch (PDOException $e) {
        // Return false if any exception occurs
        return false;
    }
}


function updatePlaylistImage($playlistId, $fileArray, $nom_playlist, $description_playlist) {
    try {
        $pdo = config::getConnexion();

        $fieldsToUpdate = [];
        $params = [':id' => $playlistId];

        // Handle image upload if there's a new file
        if ($fileArray 
            && isset($fileArray['error']) 
            && $fileArray['error'] === UPLOAD_ERR_OK
        ) {
            // Fetch old image path
            $stmt = $pdo->prepare("SELECT img FROM playlist WHERE id = :id");
            $stmt->bindParam(':id', $playlistId, PDO::PARAM_INT);
            $stmt->execute();
            $old = $stmt->fetch(PDO::FETCH_ASSOC);

            // Delete old file if it exists
            if (!empty($old['img'])) {
                $oldFilePath = $_SERVER['DOCUMENT_ROOT'] . $old['img'];
                if (file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
            }

            // Prepare upload directory
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/projetweb/assets/img-playlist/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate new filename
            $ext = pathinfo($fileArray['name'], PATHINFO_EXTENSION);
            $newFilename = "playlist_{$playlistId}_" . time() . "." . $ext;
            $dest = $uploadDir . $newFilename;

            // Move uploaded file to destination
            if (!move_uploaded_file($fileArray['tmp_name'], $dest)) {
                throw new Exception("Échec de l’upload de l’image.");
            }

            // Relative URL for the database
            $relativePath = "/projetweb/assets/img-playlist/" . $newFilename;
            $fieldsToUpdate[] = "img = :img";
            $params[':img'] = $relativePath;
        }

        // Update name if provided
        if (!empty($nom_playlist)) {
            $fieldsToUpdate[] = "nom = :nom";
            $params[':nom'] = $nom_playlist;
        }

        // Update description if provided
        if (!empty($description_playlist)) {
            $fieldsToUpdate[] = "disc = :description";
            $params[':description'] = $description_playlist;
        }

        // If no changes — skip update
        if (empty($fieldsToUpdate)) {
            throw new Exception("Aucune modification à apporter.");
        }

        // Build and execute the dynamic query
        $sql = "UPDATE playlist SET " . implode(", ", $fieldsToUpdate) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return true;

    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
}



function getPlaylistById($playlistId) {
    try {
        $pdo = config::getConnexion();

        $sql = "SELECT * FROM playlist WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();

        $playlist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$playlist) {
            throw new Exception("Playlist non trouvée.");
        }

        return $playlist;

    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
}
function getPlaylistuser($playlistId) {
    try {
        $pdo = config::getConnexion();

        $sql = "SELECT * FROM playlist WHERE utilisateur_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $playlistId, PDO::PARAM_INT);
        $stmt->execute();

        $playlists = $stmt->fetchAll(PDO::FETCH_ASSOC); // <-- use fetchAll instead of fetch

        return $playlists;

    } catch (PDOException $e) {
        throw new Exception("Erreur SQL : " . $e->getMessage());
    }
}

function deletePlaylist($id) {
    try {
        // Get the PDO connection
        $pdo = config::getConnexion();

        // SQL query to delete the playlist by ID
        $sql = "DELETE FROM playlist WHERE id = :id";

        // Prepare the query
        $stmt = $pdo->prepare($sql);

        // Bind the ID parameter to the query
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            // Return success status
            return true;
        } else {
            // Return failure status
            return false;
        }
    } catch (PDOException $e) {
        // Handle the exception and return false
        echo "Error: " . $e->getMessage();
        return false;
    }
}

function hasLikedSongs($user_id) {
    $pdo = config::getConnexion();
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as count
            FROM liked_song
            WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result && $result['count'] > 0);

    } catch (PDOException $e) {
        error_log("hasLikedSongs error: " . $e->getMessage());
        return false;
    }
}
function fetchLikedSongs($user_id) {
    $pdo = config::getConnexion();
    try {
        // Prepare the SQL query to join liked_song and chanson tables
        $stmt = $pdo->prepare("
            SELECT chanson.* 
            FROM chanson
            INNER JOIN liked_song ON chanson.id = liked_song.song_id
            WHERE liked_song.user_id = :uid
        ");
        $stmt->execute([':uid' => $user_id]);
        
        // Fetch all the liked songs data
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return the array of songs
        return $songs;

    } catch (PDOException $e) {
        // Log error and return false
        error_log("fetchLikedSongs error: " . $e->getMessage());
        return false;
    }
}
function fetchLikedSongIds($user_id) {
    $pdo = config::getConnexion();
    try {
        $stmt = $pdo->prepare("
            SELECT chanson.id 
            FROM chanson
            INNER JOIN liked_song ON chanson.id = liked_song.song_id
            WHERE liked_song.user_id = :uid
        ");
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Just the ids
    } catch (PDOException $e) {
        error_log("fetchLikedSongIds error: " . $e->getMessage());
        return false;
    }
}
function isSongInPlaylist($song_id, $playlist_id) {
    // Access your DB connection (update this part as needed)
    $pdo = config::getConnexion();

    $query = "SELECT COUNT(*) FROM playlist_chanson WHERE chanson_id = :song_id AND playlist_id = :playlist_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
    $stmt->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchColumn() > 0;
}



function fetchSongsFromPlaylist($playlist_id) {
    // Create a database connection
    $conn = config::getConnexion();
    
    // SQL query to fetch song data for the given playlist including image_path
    $sql = "
        SELECT c.id, c.song_title, c.album_name, c.duree, c.image_path , c.music_path
        FROM chanson c
        JOIN playlist_chanson pc ON c.id = pc.chanson_id
        WHERE pc.playlist_id = :playlist_id
        ORDER BY c.song_title"; // Ordering by song title

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Bind the playlist_id parameter to the prepared statement
    $stmt->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the results as an associative array
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the fetched songs
    return $songs;
}



function fetchSongsFromadartiste($playlist_id) {
    // Create a database connection
    $conn = config::getConnexion();
    
    // SQL query to fetch song data for the given playlist including image_path
    $sql = "
        SELECT c.id, c.song_title, c.album_name, c.duree, c.image_path
        FROM chanson c
        JOIN playlist_chanson pc ON c.id = pc.chanson_id
        WHERE pc.playlist_id = :playlist_id
        ORDER BY c.song_title"; // Ordering by song title

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Bind the playlist_id parameter to the prepared statement
    $stmt->bindParam(':playlist_id', $playlist_id, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the results as an associative array
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the fetched songs
    return $songs;
}

function fetchSongsFromArtiste($artiste_id) {
    // Create a database connection
    $conn = config::getConnexion();
    
    $sql = "
       SELECT c.* 
       FROM chanson c
       JOIN utilisateurs u ON c.artiste_id = u.artiste_id
       WHERE u.artiste_id = :artiste_id";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':artiste_id', $artiste_id, PDO::PARAM_INT);

    $stmt->execute();

    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $songs;
}

function executeFetchRandomSongs($playlist_id, $limit = 10) {
    $conn = config::getConnexion();  // Make sure the database connection is set up

    $sql = "
        SELECT c.* 
        FROM chanson c
        WHERE c.id NOT IN (
            SELECT pc.chanson_id
            FROM playlist_chanson pc
            WHERE pc.playlist_id = :playlist_id
        )
        ORDER BY RAND()
        LIMIT :limit
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':playlist_id', $playlist_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // returns an array of songs
}


// Function to add a song to the playlist
function addSongToPlaylist($playlist_id, $song_id) {
    $pdo = config::getConnexion();  // Make sure the database connection is set up

    // SQL query to insert the song into the playlist_songs table
    $sql = "INSERT INTO playlist_chanson (playlist_id, chanson_id) VALUES (?, ?)";
    
    // Prepare the statement
    $stmt = $pdo->prepare($sql);
    
    // Execute the statement with the provided IDs
    $stmt->execute([$playlist_id, $song_id]);

    return "Song added to playlist successfully.";
}


function deleteSongFromPlaylist($playlistId, $songId) {
    try {
        $conn = config::getConnexion(); // PDO connection

        // Prepare the SQL query safely
        $stmt = $conn->prepare("DELETE FROM playlist_chanson WHERE playlist_id = :playlist_id AND chanson_id = :chanson_id");
        
        // Execute with parameters
        $stmt->execute([
            ':playlist_id' => $playlistId,
            ':chanson_id' => $songId
        ]);

        // Check if a row was deleted
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false; // Nothing was deleted
        }

    } catch (PDOException $e) {
        // Optional: log the error somewhere instead of echo
        throw new Exception('Database error: ' . $e->getMessage());
    }
}


function deleteSongFromlikedsong($user_id, $songId) {
    try {
        $conn = config::getConnexion(); // PDO connection

        // Prepare the SQL query safely
        $stmt = $conn->prepare("DELETE FROM liked_song WHERE user_id = :user_id AND song_id = :song_id");
        
        // Execute with parameters
        $stmt->execute([
            ':user_id' => $user_id,
            ':song_id' => $songId
        ]);

        // Check if a row was deleted
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false; // Nothing was deleted
        }

    } catch (PDOException $e) {
        // Optional: log the error somewhere instead of echo
        throw new Exception('Database error: ' . $e->getMessage());
    }
}

function updateStreamStats($musicId, $pdo,$id_user) {
    $statDate = date('Y-m-d');

    try {
        // Check if entry exists
        $checkSql = "SELECT * FROM streaming_stats 
                     WHERE music_id = :music_id AND stat_date = :stat_date AND id_user = :id_user";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute([
            ':music_id' => $musicId,
            ':stat_date' => $statDate,
            ':id_user' => $id_user
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Update existing entry: increment stream_count by 1
            $updateSql = "UPDATE streaming_stats 
                          SET stream_count = stream_count + 1 
                          WHERE music_id = :music_id AND stat_date = :stat_date AND id_user = :id_user";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                ':music_id' => $musicId,
                ':stat_date' => $statDate,
                ':id_user' => $id_user
            ]);
        } else {
            // Insert new entry
            $insertSql = "INSERT INTO streaming_stats (music_id, stat_date, stream_count, listener_count, id_user)
                          VALUES (:music_id, :stat_date, 1, 1, :id_user)";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                ':music_id' => $musicId,
                ':stat_date' => $statDate,
                ':id_user' => $id_user
            ]);
        }

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
}


function searchUtilisateursartiste($query) {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur LIKE :query and type_utilisateur LIKE 'artiste'");
    $stmt->execute(['query' => '%' . $query . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function searchUtilisateursuser($query) {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE nom_utilisateur LIKE :query and type_utilisateur LIKE 'user'");
    $stmt->execute(['query' => '%' . $query . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchChansons($query) {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM chanson WHERE song_title LIKE :query");
    $stmt->execute(['query' => '%' . $query . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function searchPlaylists($query, $id_user) {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM playlist WHERE nom LIKE :query AND utilisateur_id = :id_user");
    $stmt->execute([
        'query' => '%' . $query . '%',
        'id_user' => $id_user
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getSongsWithStatsSortedByUser($userId) {
    $pdo = config::getConnexion();
    try {
        $sql = "
            SELECT 
                m.*, 
                s.stream_count, 
                s.listener_count, 
                s.stat_date, 
                s.id_user
            FROM 
                chanson m
            JOIN 
                streaming_stats s ON m.id = s.music_id
            WHERE 
                s.id_user = :userId
            ORDER BY 
                s.stream_count DESC, 
                m.id ASC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des chansons : " . $e->getMessage();
        return [];
    }
}


function getUserData($userId) {
    $pdo = config::getConnexion();

    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE artiste_id = :id");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération de l'utilisateur : " . $e->getMessage();
        return null;
    }
}

function getUserSongHistory($userId) {
    $pdo = config::getConnexion();  // PDO connection from your config

    try {
        // SQL query to fetch song history for the user
        $sql = "
            SELECT c.*, s.stat_date, s.stream_count
            FROM streaming_stats s
            JOIN chanson c ON s.music_id = c.id
            WHERE s.id_user = :user_id
            ORDER BY s.stat_date DESC
        ";
        
        // Prepare the SQL statement
        $stmt = $pdo->prepare($sql);
        
        // Bind the user ID parameter
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        
        // Fetch all results as an associative array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // In case of error, print the error message
        echo "Error fetching song history: " . $e->getMessage();
        return [];
    }
}

?>