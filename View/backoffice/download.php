<?php 

require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\function.php';
require_once 'C:\xampp\htdocs\projetweb\model\classe.php'; 

// Check if song_id is provided in the URL
if (isset($_GET['song_id'])) {
    $song_id = $_GET['song_id']; // Get the song_id from the query string

    try {
        // Database connection
        $pdo = config::getConnexion();

        // Prepare query to get the music file path by song_id
        $stmt = $pdo->prepare("SELECT music_path FROM chanson WHERE id = :song_id");
        
        // Bind the :song_id parameter to the actual song_id
        $stmt->bindParam(':song_id', $song_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $song = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the song exists
        if ($song) {
            $file_path = $song['music_path']; // Get the music file path from the database

            // Check if the file exists on the server
            if (file_exists($file_path)) {
                // Set headers for download
                header('Content-Description: File Transfer');
                header('Content-Type: audio/mpeg'); // Set MIME type for music (change this if the format is different)
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Content-Length: ' . filesize($file_path));
                header('Pragma: no-cache');
                header('Expires: 0');

                // Clear the output buffer
                ob_clean();
                flush();

                // Read the file and output it to the browser
                readfile($file_path);
                exit; // Ensure no further processing is done
            } else {
                // File does not exist
                echo "The requested music file does not exist.";
            }
        } else {
            // Song not found
            echo "Song not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage(); // Catch any errors
    }
} else {
    // No song_id provided in the query string
    echo "No song ID specified.";
}
?>
