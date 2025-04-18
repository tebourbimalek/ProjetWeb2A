<?php
// Start the session

// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

// Check if the user ID is passed via GET (or POST, depending on your needs)
if (isset($_GET['id'])) {
    // Get the user ID from the URL
    $userId = $_GET['id'];

    // Get a PDO instance
    $pdo = config::getConnexion();

    // Prepare the SQL query to delete the user
    $query = "DELETE FROM utilisateurs WHERE artiste_id = :id";

    // Prepare the statement
    $stmt = $pdo->prepare($query);

    // Bind the user ID parameter to prevent SQL injection
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to a success page or back to the user list
        header("Location: backoffice.php"); // Change to your page after deletion
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    // Handle the case where no user ID is provided in the URL
    echo "No user ID provided.";
}
?>
