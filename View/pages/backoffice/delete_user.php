<?php
session_start();
// Start the session

// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';

// Check if the user ID is passed via GET (or POST, depending on your needs)
if (isset($_GET['id'])) {
    // Get the user ID from the URL
    $userId = $_GET['id'];

    // Get a PDO instance
    $pdo = config::getConnexion();
    if (deleteUserById($userId)) {
        header("Location: backoffice.php"); // Change as needed
        exit();
    } else {
        echo "Failed to delete user.";
    }
} else {
        // Handle the case where no user ID is provided in the URL

    echo "No user ID provided.";
}

?>
