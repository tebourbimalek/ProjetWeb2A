<?php
// Start the session
session_start();

// Include the config file to reuse the database connection
require_once 'C:\xampp\htdocs\projetweb\includes\config.php';

// Get a PDO instance from the config class
$pdo = config::getConnexion();

// Check if the user is logged in (you can adjust this depending on your session management)
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to view this page.";
    exit;
}

// Get the logged-in user's ID from the session
$userId = $_SESSION['user'];

// Prepare the SQL query to fetch the user information from the 'utilisateurs' table
$query = "SELECT artiste_id, nom_utilisateur, email, type_utilisateur, score, date_creation, image_path FROM utilisateurs WHERE artiste_id = :id";
$stmt = $pdo->prepare($query);

// Bind the user ID parameter to prevent SQL injection
$stmt->bindParam(':id', $userId, PDO::PARAM_INT);

// Execute the query
$stmt->execute();

// Fetch the user data
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user exists
if (!$user) {
    echo "User not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="backoffice.css">
    <title>User Profile</title>
</head>
<body>

    <div class="content-section">
        <h2>Your Profile</h2>

        <!-- User Information -->
        <div class="user-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['nom_utilisateur']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['type_utilisateur']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($user['score']); ?></p>
            <p><strong>Account Created On:</strong> <?php echo htmlspecialchars($user['date_creation']); ?></p>

            <?php if ($user['image_path']) { ?>
                <img src="<?php echo htmlspecialchars($user['image_path']); ?>" alt="User Image" class="user-image">
            <?php } else { ?>
                <p>No profile image available.</p>
            <?php } ?>
        </div>

        <hr>

        <!-- Edit Profile Form -->
        <h3>Edit Profile</h3>
        <form action="update_user.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['artiste_id']); ?>">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($user['nom_utilisateur']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" name="role" required>
                    <option value="Admin" <?php echo $user['type_utilisateur'] === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="Artiste" <?php echo $user['type_utilisateur'] === 'Artiste' ? 'selected' : ''; ?>>Artist</option>
                    <option value="User" <?php echo $user['type_utilisateur'] === 'User' ? 'selected' : ''; ?>>User</option>
                </select>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" name="status" required>
                    <option value="Active" <?php echo $user['score'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $user['score'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" class="form-control" name="profile_image">
            </div>

            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>

    </div>

</body>
</html>
