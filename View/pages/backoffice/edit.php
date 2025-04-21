<?php 

require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\function.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\classe.php';

try {
    // Get database connection
    $pdo = config::getConnexion();

    // Check if form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $song_id = $_POST['song_id'] ?? null;
        $song_title = $_POST['edit-song-title'] ?? null;
        $album_name = $_POST['edit-song-album'] ?? null;
        $release_date = $_POST['temps'] ?? null; // Ensure this matches your form input
        $duree = $_POST['edit-duree'] ?? null;

        // Define the target directory for uploads
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/projetweb/assets/includes/";

        // Fetch the current file paths from the database
        $stmt = $pdo->prepare("SELECT music_path, image_path FROM chanson WHERE id = :song_id");
        $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);
        $stmt->execute();
        $song = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Check if the song details were fetched correctly
        echo "Fetched Song Details: <br>";
        print_r($song);
        echo "<br><br>";

        // Check if a new music file was uploaded
        if (isset($_FILES['music_input']) && $_FILES['music_input']['error'] === 0) {
            $audio_file = $_FILES['music_input'];
            $new_audio_path = $upload_dir . basename($audio_file["name"]); // Absolute path

            // Delete the old audio file if it exists
            if (!empty($song['music_path']) && file_exists($song['music_path'])) {
                if (unlink($song['music_path'])) {
                    echo "Old music file deleted.<br>";
                } else {
                    echo "Failed to delete old music file.<br>";
                }
            }

            // Move the new file
            if (move_uploaded_file($audio_file["tmp_name"], $new_audio_path)) {
                echo "New music file uploaded to: " . $new_audio_path . "<br>";
            } else {
                echo "Failed to upload the new music file.<br>";
                $new_audio_path = $song['music_path']; // Keep the old path if upload fails
            }
        } else {
            $new_audio_path = $song['music_path']; // Keep existing path if no new file is uploaded
        }

        // Check if a new cover image was uploaded
        if (isset($_FILES['image_input']) && $_FILES['image_input']['error'] === 0) {
            $cover_file = $_FILES['image_input'];
            $new_cover_path = $upload_dir . basename($cover_file["name"]); // Absolute path

            // Debug: Show the uploaded file details
            echo "Cover File: <br>";
            print_r($cover_file);
            echo "<br>";

            // Delete the old cover image if it exists
            if (!empty($song['image_path']) && file_exists($song['image_path'])) {
                if (unlink($song['image_path'])) {
                    echo "Old cover image deleted.<br>";
                } else {
                    echo "Failed to delete old cover image.<br>";
                }
            }

            // Move the new file
            if (move_uploaded_file($cover_file["tmp_name"], $new_cover_path)) {
                echo "New cover image uploaded to: " . $new_cover_path . "<br>";
            } else {
                echo "Failed to upload the new cover image.<br>";
                $new_cover_path = $song['image_path']; // Keep old path if upload fails
            }
        } else {
            $new_cover_path = $song['image_path']; // Keep existing path if no new file is uploaded
        }

        // Debug: Check if both paths are correct
        echo "Final Music Path: " . $new_audio_path . "<br>";
        echo "Final Cover Path: " . $new_cover_path . "<br><br>";

        // Update the song details in the database
        $update_stmt = $pdo->prepare("
            UPDATE chanson 
            SET song_title = :song_title, album_name = :album_name, release_date = :release_date, 
                duree = :duree, music_path = :music_path, image_path = :image_path 
            WHERE id = :song_id
        ");
        $update_stmt->bindParam(':song_title', $song_title, PDO::PARAM_STR);
        $update_stmt->bindParam(':album_name', $album_name, PDO::PARAM_STR);
        $update_stmt->bindParam(':release_date', $release_date, PDO::PARAM_STR);
        $update_stmt->bindParam(':duree', $duree, PDO::PARAM_STR);
        $update_stmt->bindParam(':music_path', $new_audio_path, PDO::PARAM_STR);
        $update_stmt->bindParam(':image_path', $new_cover_path, PDO::PARAM_STR);
        $update_stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);

        // Check if the database update was successful
        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Chanson mise à jour avec succès !";
            header("Location: backoffice.php?updated=true");
            exit;
        } else {
            $_SESSION['message'] = "Échec de la mise à jour de la chanson.";
            header("Location: backoffice.php?error=true");
            exit;
        }
    }
} catch (PDOException $e) {
    $_SESSION['message'] = "Erreur SQL : " . $e->getMessage();
    header("Location: backoffice.php?error=true");
    exit;
}

?>
