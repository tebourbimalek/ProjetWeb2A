<?php
// Start the session
session_start();

// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

// Get a PDO instance from the config class
$pdo = config::getConnexion();

// Ensure the user ID is passed via the GET parameter (or you can use POST if necessary)
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Prepare the SQL query to fetch the user from the 'utilisateurs' table
    $query = "SELECT artiste_id, nom_utilisateur, email, type_utilisateur, score, date_creation, image_path FROM utilisateurs WHERE artiste_id = :id";
    $stmt = $pdo->prepare($query);
    
    // Bind the user ID parameter to prevent SQL injection
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    // Execute the query
    $stmt->execute();

    // Fetch the user data (this will be a single row since we expect only one user)
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        // Handle the case where the user was not found, e.g., show an error message
        echo "User not found.";
        exit;
    }
} else {
    // Handle the case where no user ID is provided in the URL
    echo "No user ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="backoffice.css">
    <title>Edit User - Backoffice</title>
</head>
<body>

    <div class="content-section">
        <h2>Edit User Information</h2>

        <!-- Edit Form -->
        <form action="update_user.php" method="POST" id="editUserForm">
            <input type="hidden" name="id" id="userId" value="<?php echo htmlspecialchars($user['artiste_id']); ?>">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" name="name" id="userName" value="<?php echo htmlspecialchars($user['nom_utilisateur']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" id="userEmail" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" name="role" id="userRole" required>
                    <option value="Admin" <?php echo $user['type_utilisateur'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Artiste" <?php echo $user['type_utilisateur'] === 'artiste' ? 'selected' : ''; ?>>Artist</option>
                    <option value="User" <?php echo $user['type_utilisateur'] === 'User' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" id="userStatus" required>
                    <option value="Active" <?php echo $user['score'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $user['score'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-footer">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancel</button>
            </div>
        </form>
    </div>

</body>
</html>
