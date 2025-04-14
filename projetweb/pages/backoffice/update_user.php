<?php
// Start the session
session_start();

// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

// Check if form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the form
    $userId = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Get a PDO instance
    $pdo = config::getConnexion();

    // Prepare the SQL query to update the user information
    $query = "UPDATE utilisateurs SET nom_utilisateur = :name, email = :email, type_utilisateur = :role, score = :status WHERE artiste_id = :id";

    // Prepare the statement
    $stmt = $pdo->prepare($query);

    // Bind parameters to prevent SQL injection
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to a success page or back to the edit page
        header("Location: backoffice.php"); // Change to the appropriate page
        exit();
    } else {
        echo "Error updating user.";
    }
} else {
    echo "Invalid request method.";
}
?>
