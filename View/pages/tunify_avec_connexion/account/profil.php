<?php
// Start session for user authentication
session_start();
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
require_once 'C:\xampp\htdocs\projetweb\Model\includes\user.php';
require_once 'C:\xampp\htdocs\projetweb\controller\controller.php';

// Initialize database connection
try {
    $pdo = config::getConnexion();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}
// Get current user using the provided function
$userConnected = getUserInfo($pdo);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $nom_famille = filter_input(INPUT_POST, 'nom_famille', FILTER_SANITIZE_STRING);
    $bio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
    $marketing_consent = isset($_POST['marketing_consent']) ? 1 : 0;
    
    // Parse date of birth if needed to update
    $date_naissance = null;
    if (!empty($_POST['birth_day']) && !empty($_POST['birth_month']) && !empty($_POST['birth_year'])) {
        // Convert month name to number
        $months = [
            'Janvier' => '01', 'Février' => '02', 'Mars' => '03', 'Avril' => '04',
            'Mai' => '05', 'Juin' => '06', 'Juillet' => '07', 'Août' => '08',
            'Septembre' => '09', 'Octobre' => '10', 'Novembre' => '11', 'Décembre' => '12'
        ];
        
        $month_num = $months[$_POST['birth_month']] ?? '01';
        $date_naissance = $_POST['birth_year'] . '-' . $month_num . '-' . str_pad($_POST['birth_day'], 2, '0', STR_PAD_LEFT);
    }
    
    // Handle profile image upload
    $image_path = $userConnected->getImagePath();
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/uploads/profile_images/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true); // Ensure the directory exists
        }
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $new_filename = uniqid('profile_') . '.' . $file_ext;
        $upload_file = $upload_dir . $new_filename;

        // Check if file is an image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_file)) {
                $image_path = 'uploads/profile_images/' . $new_filename;
            } else {
                $_SESSION['error_message'] = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $_SESSION['error_message'] = "Type de fichier non valide. Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        }
    }
    
    // Update user in database
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET 
            email = ?, 
            prenom = ?, 
            nom_famille = ?, 
            date_naissance = ?, 
            image_path = ? 
            WHERE artiste_id = ?");
            
        $stmt->execute([
            $email, 
            $prenom, 
            $nom_famille, 
            $date_naissance ?? $userConnected->getDateNaissance(), 
            $image_path,
            $userConnected->getArtisteId()
        ]);
        
        // Save bio to a separate table if needed
        // This would depend on your database structure
        
        $_SESSION['success_message'] = "Profil mis à jour avec succès!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Erreur lors de la mise à jour du profil: " . $e->getMessage();
    }
}

// Parse birth date for form display
$birth_date = new DateTime($userConnected->getDateNaissance());
$birth_day = $birth_date->format('j');
$birth_month_num = $birth_date->format('n');
$birth_year = $birth_date->format('Y');

// Convert month number to name
$month_names = [
    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
];
$birth_month = $month_names[$birth_month_num] ?? 'Janvier';

// Get user bio from separate table if needed
$bio = ''; // Replace with actual query to get bio if needed
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil | Tunisfy</title>
    <link rel="stylesheet" href="acc.css">
    
</head>
<body>
    <div class="header">
        <div class="logo">
            <a href="/projetweb/index.php">
                <img src="/projetweb/assets/images/logo/tunisfy-logo.png" alt="Tunisfy">
            </a>
        </div>
        <div class="nav-links">
            <a href="/projetweb/View/pages/premium.php">Premium</a>
            <a href="/projetweb/View/pages/assistance.php">Assistance</a>
            <a href="/projetweb/View/pages/download.php">Télécharger</a>
            <a href="/projetweb/View/pages/profile.php">Profil</a>
        </div>
    </div>

    <div class="content-container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success-message">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error-message">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <h1>Modifier le profil</h1>
        
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <div class="avatar-section">
                <div class="avatar">
                    <?php $profile_image = $userConnected->getImagePath() ? '/projetweb/' . $userConnected->getImagePath() : '/uploads/profile_images/default-avatar.png'; ?>
                    <img id="profile-image-preview" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Photo de profil">
                </div>
                <input type="file" id="profile_image" name="profile_image" style="display:none" accept="image/jpeg,image/png,image/gif">
                <button type="button" class="upload-btn" onclick="document.getElementById('profile_image').click()">Changer la photo</button>
            </div>
            
            <div class="name-fields">
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($userConnected->getPrenom()); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nom_famille">Nom de famille</label>
                    <input type="text" id="nom_famille" name="nom_famille" value="<?php echo htmlspecialchars($userConnected->getNomFamille()); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="username">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userConnected->getNomUtilisateur()); ?>" readonly>
                <small>L'identifiant unique ne peut pas être modifié</small>
            </div>

            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userConnected->getEmail()); ?>" required>
            </div>

            <div class="form-group">
                <label for="bio">Biographie</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Partagez quelque chose à propos de vous"><?php echo htmlspecialchars($bio); ?></textarea>
            </div>

            <div class="form-group">
                <label>Date de naissance</label>
                <div class="date-container">
                    <input type="number" id="birth_day" name="birth_day" min="1" max="31" value="<?php echo $birth_day; ?>">
                    
                    <select id="birth_month" name="birth_month">
                        <?php foreach ($month_names as $num => $name): ?>
                            <option value="<?php echo $name; ?>" <?php if ($birth_month === $name) echo 'selected'; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                    
                    <input type="number" id="birth_year" name="birth_year" min="1900" max="<?php echo date('Y'); ?>" value="<?php echo $birth_year; ?>">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="marketing_consent" id="marketing_consent">
                    Partagez les données sur mon inscription avec les fournisseurs de contenu de Tunisfy à des fins de marketing.
                </label>
            </div>

            <div class="button-group">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='/projetweb/View/pages/profile.php'">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer le profil</button>
            </div>
        </form>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <img src="/projetweb/assets/images/logo/tunisfy-logo.png" alt="Tunisfy" style="height: 40px; margin-bottom: 20px;">
                <div class="social-icons">
                    <a href="#"><img src="/projetweb/assets/images/icons/instagram.svg" alt="Instagram"></a>
                    <a href="#"><img src="/projetweb/assets/images/icons/twitter.svg" alt="Twitter"></a>
                    <a href="#"><img src="/projetweb/assets/images/icons/facebook.svg" alt="Facebook"></a>
                </div>
            </div>
            
            <div class="footer-column">
                <h4>Société</h4>
                <ul>
                    <li><a href="/projetweb/View/pages/about.php">À propos</a></li>
                    <li><a href="/projetweb/View/pages/jobs.php">Offres d'emploi</a></li>
                    <li><a href="/projetweb/View/pages/for-the-record.php">For the Record</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Communautés</h4>
                <ul>
                    <li><a href="/projetweb/View/pages/artists.php">Espace artistes</a></li>
                    <li><a href="/projetweb/View/pages/developers.php">Développeurs</a></li>
                    <li><a href="/projetweb/View/pages/advertising.php">Campagnes publicitaires</a></li>
                    <li><a href="/projetweb/View/pages/investors.php">Investisseurs</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Liens utiles</h4>
                <ul>
                    <li><a href="/projetweb/View/pages/assistance.php">Assistance</a></li>
                    <li><a href="/projetweb/View/pages/web-player.php">Lecteur Web</a></li>
                    <li><a href="/projetweb/View/pages/mobile-app.php">Appli mobile gratuite</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Abonnements Tunisfy</h4>
                <ul>
                    <li><a href="/projetweb/View/pages/premium/personal.php">Premium Personnel</a></li>
                    <li><a href="/projetweb/View/pages/premium/duo.php">Premium Duo</a></li>
                    <li><a href="/projetweb/View/pages/premium/family.php">Premium Famille</a></li>
                    <li><a href="/projetweb/View/pages/free.php">Tunisfy Free</a></li>
                </ul>
            </div>
        </div>
        
        <div style="max-width: 1200px; margin: 40px auto 0; padding-top: 20px; border-top: 1px solid #282828; font-size: 12px; color: #919496;">
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between;">
                <div style="display: flex; gap: 20px; margin-bottom: 20px;">
                    <a href="/projetweb/View/pages/legal.php" style="color: #919496; text-decoration: none;">Legal</a>
                    <a href="/projetweb/View/pages/privacy-center.php" style="color: #919496; text-decoration: none;">Centre de sécurité et de confidentialité</a>
                    <a href="/projetweb/View/pages/privacy-policy.php" style="color: #919496; text-decoration: none;">Protection des données</a>
                    <a href="/projetweb/View/pages/cookies.php" style="color: #919496; text-decoration: none;">Cookies</a>
                    <a href="/projetweb/View/pages/about-ads.php" style="color: #919496; text-decoration: none;">À propos des pubs</a>
                    <a href="/projetweb/View/pages/accessibility.php" style="color: #919496; text-decoration: none;">Accessibilité</a>
                </div>
                <div>
                    © <?php echo date('Y'); ?> Tunisfy
                </div>
            </div>
        </div>
    </footer>
    <style>
        :root {
            --spotify-green: #1DB954;
            --spotify-black: #191414;
            --spotify-dark-gray: #121212;
            --spotify-light-gray: #282828;
            --spotify-white: #FFFFFF;
        }
        
        body {
            font-family: 'Circular', Helvetica, Arial, sans-serif;
            background-color: var(--spotify-black);
            color: var(--spotify-white);
            margin: 0;
            padding: 0;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: var(--spotify-black);
        }
        
        .logo img {
            height: 40px;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
        }
        
        .nav-links a {
            color: var(--spotify-white);
            text-decoration: none;
            font-weight: 700;
        }
        
        .content-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        h1 {
            font-size: 2.5rem;
            margin-bottom: 40px;
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            background-color: var(--spotify-light-gray);
            border: 1px solid var(--spotify-light-gray);
            color: var(--spotify-white);
            font-size: 16px;
        }
        
        .date-container {
            display: flex;
            gap: 10px;
        }
        
        .date-container input,
        .date-container select {
            flex: 1;
        }
        
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .btn {
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: var(--spotify-green);
            color: var(--spotify-white);
        }
        
        .btn-secondary {
            background-color: transparent;
            border: 1px solid var(--spotify-white);
            color: var(--spotify-white);
        }
        
        .avatar-section {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--spotify-light-gray);
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .upload-btn {
            background-color: transparent;
            border: 1px solid var(--spotify-white);
            color: var(--spotify-white);
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
        }
        
        footer {
            padding: 40px;
            background-color: var(--spotify-black);
            border-top: 1px solid var(--spotify-light-gray);
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-column {
            margin-bottom: 30px;
            min-width: 180px;
        }
        
        .footer-column h4 {
            color: var(--spotify-white);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
        }
        
        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-column li {
            margin-bottom: 15px;
        }
        
        .footer-column a {
            color: #919496;
            text-decoration: none;
            font-size: 14px;
        }
        
        .footer-column a:hover {
            color: var(--spotify-green);
        }
        
        .social-icons {
            display: flex;
            gap: 15px;
        }
        
        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--spotify-light-gray);
            border-radius: 50%;
        }
        
        .message {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .success-message {
            background-color: var(--spotify-green);
            color: white;
        }
        
        .error-message {
            background-color: #E91429;
            color: white;
        }
        
        .name-fields {
            display: flex;
            gap: 15px;
        }
        
        .name-fields .form-group {
            flex: 1;
        }
    </style>
    <script>
        // Preview uploaded profile image
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-image-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>