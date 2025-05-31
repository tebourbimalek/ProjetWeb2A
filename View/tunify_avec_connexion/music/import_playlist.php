<?php 

session_start();

require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php'; // Ensure this includes necessary functions
require_once 'C:\xampp\htdocs\projetweb\model\config.php'; // Your PDO setup
require_once 'C:\xampp\htdocs\projetweb\model\user.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

$conn = config::getConnexion();


if (isset($_POST['shared_link'])) {
    $user = getUserInfo($conn); 
    $user_id = $user->getArtisteId();
    $sharedLink = $_POST['shared_link'];
    
    // Parse the shared link to get parameters
    $urlParts = parse_url($sharedLink);
    parse_str($urlParts['query'], $queryParams);

    // Check if 'id' and 'user_id' are set in the query string
    if (isset($queryParams['id']) && isset($queryParams['user_id'])) {
        $playlistId = intval($queryParams['id']); // Ensure it's an integer
        $userId = intval($queryParams['user_id']); // Ensure it's an integer



        // Prepare and execute the query to check if the playlist exists and belongs to the user
        $query = "SELECT * FROM playlist WHERE id = :playlistId AND utilisateur_id = :userId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':playlistId', $playlistId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            // Playlist does not exist — stop here
            header("Location: ../avec_connexion.php?playlist_created=false");
            exit();
        } else {
            // Fetch the playlist data for the specified playlist and user
            $fetchdataplaylist = "SELECT * FROM playlist WHERE id = :playlistId AND utilisateur_id = :userId";
            $stmt = $conn->prepare($fetchdataplaylist);
            $stmt->bindParam(':playlistId', $playlistId, PDO::PARAM_INT);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->execute();

            // Check if playlist data was found
            if ($stmt->rowCount() > 0) {
                $playlistData = $stmt->fetch(PDO::FETCH_ASSOC);

                // Insert the fetched playlist data into the new playlist
                $createQuery = "INSERT INTO playlist (utilisateur_id, nom, img, disc) VALUES (:userId, :playlistName, :playlistImg, :playlistDisc)";
                $createStmt = $conn->prepare($createQuery);
                
                // Bind the values from the fetched playlist data
                $createStmt->bindParam(':userId', $user_id, PDO::PARAM_INT);
                $createStmt->bindParam(':playlistName', $playlistData['nom'], PDO::PARAM_STR);
                $createStmt->bindParam(':playlistImg', $playlistData['img'], PDO::PARAM_STR);
                $createStmt->bindParam(':playlistDisc', $playlistData['disc'], PDO::PARAM_STR);
                $createStmt->execute();

                // Get the last inserted playlist ID
                $newPlaylistId = $conn->lastInsertId();

                // Fetch the songs (playlist_chanson) associated with the original playlist
                $fetchSongsQuery = "SELECT * FROM playlist_chanson WHERE playlist_id = :playlistId";
                $fetchSongsStmt = $conn->prepare($fetchSongsQuery);
                $fetchSongsStmt->bindParam(':playlistId', $playlistId, PDO::PARAM_INT);
                $fetchSongsStmt->execute();

                // Check if there are any songs in the original playlist
                if ($fetchSongsStmt->rowCount() > 0) {
                    // Insert each song into the new playlist
                    while ($song = $fetchSongsStmt->fetch(PDO::FETCH_ASSOC)) {
                        $insertSongQuery = "INSERT INTO playlist_chanson (playlist_id, chanson_id) VALUES (:newPlaylistId, :songId)";
                        $insertSongStmt = $conn->prepare($insertSongQuery);
                        $insertSongStmt->bindParam(':newPlaylistId', $newPlaylistId, PDO::PARAM_INT);
                        $insertSongStmt->bindParam(':songId', $song['chanson_id'], PDO::PARAM_INT);
                        $insertSongStmt->execute();
                    }
                }

                // Redirect after successfully creating the playlist
                header("Location: ../avec_connexion.php?playlist_created=import");
                exit();
            } else {
                // If the playlist doesn't exist, you can handle this error here
                echo "Playlist not found or doesn't belong to the user.";
            }
        }
    } else {
        // Invalid link format
        echo "Invalid link format.";
    }
}
?>
