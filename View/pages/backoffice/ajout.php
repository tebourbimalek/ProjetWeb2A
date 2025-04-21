<?php
// Start session to store messages
session_start();

// Include the database configuration
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\function.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\classe.php'; 

try {
    // Get database connection
    $pdo = config::getConnexion();

    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Validate and retrieve form inputs
        $song_title = $_POST['song-title'] ?? null;
        $album_name = $_POST['album-name'] ?? null;
        $release_date = $_POST['song-release'] ?? null;
        $duree=$_POST['duree'] ?? null;

        // Initialize file paths
        $audio_path = null;
        $cover_path = null;

        // Define the target directory for uploads
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/projetweb/assets/includes/";

        // Check if song already exists in the database
        $check_song_query = "SELECT COUNT(*) FROM chanson WHERE song_title = :song_title";
        $stmt = $pdo->prepare($check_song_query);
        $stmt->bindParam(':song_title', $song_title);
        $stmt->execute();
        $song_exists = $stmt->fetchColumn(); // Fetch the count of songs with the same title

        if ($song_exists > 0) {
            $_SESSION['message'] = "Cette chanson existe déjà .";
            header('Location: backoffice.php'); // Redirect to backoffice
            exit; // Stop further execution
        }

        // Upload audio file (music)
        if (!empty($_FILES['music_input']['name'])) {
            $audio_file = $_FILES['music_input'];
            $audio_path = $upload_dir . basename($audio_file["name"]); // Absolute path
            if (move_uploaded_file($audio_file["tmp_name"], $audio_path)) {
                $_SESSION['message'] = "Audio file uploaded successfully.";
            } else {
                $_SESSION['message'] = "Failed to move audio file to destination.";
                header('Location: backoffice.php'); // Redirect to backoffice
                exit;
            }
        } else {
            $_SESSION['message'] = "Audio file is not selected.";
            header('Location: backoffice.php'); // Redirect to backoffice
            exit;
        }

        // Upload cover image
        if (!empty($_FILES['image_input']['name'])) {
            $cover_file = $_FILES['image_input'];
            $cover_path = $upload_dir . basename($cover_file["name"]); // Absolute path
            if (move_uploaded_file($cover_file["tmp_name"], $cover_path)) {
                $_SESSION['message'] = "Cover image uploaded successfully.";
            } else {
                $_SESSION['message'] = "Failed to move cover image to destination.";
                header('Location: backoffice.php'); // Redirect to backoffice
                exit;
            }
        } else {
            $_SESSION['message'] = "Cover image is not selected.";
            header('Location: backoffice.php'); // Redirect to backoffice
            exit;
        }

        // Prepare SQL statement with placeholders to prevent SQL injection
        $sql = "INSERT INTO chanson (song_title, album_name, release_date, image_path, music_path, artiste_id ,duree) 
                VALUES (:song_title, :album_name, :release_date, :cover_path, :audio_path, 1, :duree)";

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':song_title', $song_title);
        $stmt->bindParam(':album_name', $album_name);
        $stmt->bindParam(':release_date', $release_date);
        $stmt->bindParam(':cover_path', $cover_path);
        $stmt->bindParam(':audio_path', $audio_path);
        $stmt->bindParam(':duree', $duree);

        // Execute query and check for success
        if ($stmt->execute()) {
            $_SESSION['message'] = "New song added successfully!";
            generateStreamingStats();
            $_SESSION['songs'] = $songs;
        } else {
            $_SESSION['message'] = "Error: Could not insert data.";
        }

        // Redirect to backoffice
        header('Location: backoffice.php');
        exit;
    }
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}


if (isset($_GET['song_id'])) {
    $songId = $_GET['song_id'];
    
    try {
        // Récupérer les chemins du fichier audio et de la photo
        $query = "SELECT music_path, image_path FROM chanson WHERE id = :song_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':song_id', $songId, PDO::PARAM_INT);
        $stmt->execute();
        $song = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($song) {
            // Supprimer le fichier audio si le chemin existe
            if (!empty($song['music_path']) && file_exists($song['music_path'])) {
                unlink($song['music_path']);
            }

            // Supprimer l'image si le chemin existe
            if (!empty($song['image_path']) && file_exists($song['image_path'])) {
                unlink($song['image_path']);
            }

            // Supprimer la chanson de la base de données
            $deleteQuery = "DELETE FROM chanson WHERE id = :song_id";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->bindParam(':song_id', $songId, PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                $_SESSION['message'] = "Chanson supprimée avec succès !";
                header("Location: backoffice.php?deleted=true");
                exit;
            } else {
                $_SESSION['message'] = "Erreur lors de la suppression de la chanson.";
                header("Location: backoffice.php?error=true");
                exit;
            }
        } else {
            $_SESSION['message'] = "Chanson introuvable.";
            header("Location: backoffice.php?error=true");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
        header("Location: backoffice.php?error=true");
        exit;
    }
} else {
    $_SESSION['message'] = "Aucun ID de chanson fourni.";
    header("Location: backoffice.php?error=true");
    exit;
}



?>
