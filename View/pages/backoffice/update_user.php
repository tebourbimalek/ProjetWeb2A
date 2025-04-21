<?php
// Start the session


// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['id']) &&
    isset($_POST['name']) &&
    isset($_POST['email']) &&
    isset($_POST['role']) &&
    isset($_POST['status'])
) {
    // Get a PDO instance
    $pdo = config::getConnexion();

    // Call the function to update the user
    if (updateUser()) {
        header("Location: backoffice.php"); // Change as needed
        exit();
    } else {
        echo "Failed to update user.";
    }
} else {
    echo "Form not submitted or missing data.";
}
?>
