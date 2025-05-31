<?php
// Debug information - add this at the top to help troubleshoot
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log request information
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

// Include the Config and Reclamation model
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reclamation.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\ReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\TypeReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';
session_start();

// Get a PDO instance from the config class



// Test database connection
try {
    $pdo = Config::getConnexion();
    error_log("Database connection successful");
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_GET['updateReclamation'])) {
    // Process the form data here
    $controller = new ReclamationController();
    $result = $controller->createReclamation($_POST, $_FILES['screenshot'] ?? null);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// Handle reclamation update
if (isset($_GET['updateReclamation']) && !empty($_GET['updateReclamation'])) {
    $idReclamation = (int)$_GET['updateReclamation'];
    
    // Check if this is a multipart form data request (with file upload)
    if (!empty($_FILES) && isset($_FILES['screenshot'])) {
        $controller = new ReclamationController();
        $result = $controller->updateReclamationWithScreenshot(
            $idReclamation, 
            $_POST, 
            $_FILES['screenshot']
        );
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Get the data from POST for JSON requests
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No data provided']);
            exit;
        }
        
        $controller = new ReclamationController();
        $result = $controller->updateReclamation($idReclamation, $data);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}

// Handle reclamation search by email
if (isset($_GET['emailSearch']) && !empty($_GET['emailSearch'])) {
    $email = trim($_GET['emailSearch']);
    
    // Log the search request
    error_log("Search request received for email: " . $email);
    
    try {
        $controller = new ReclamationController();
        $reclamations = $controller->getReclamationsByEmail($email);
        
        // Debug output
        error_log("Reclamations found: " . print_r($reclamations, true));
        
        // Start output buffer to capture just the results HTML
        ob_start();
        echo "<div class='search-results-container'>";
        
        if (is_array($reclamations) && count($reclamations) > 0) {
            echo "<h4 class='result-heading'>Reclamations for: <span class='result-email'>" . htmlspecialchars($email) . "</span></h4>";
            
            foreach ($reclamations as $reclamation) {
                $statusClass = '';
                $status = strtolower($reclamation['status']);
                
                if ($status === 'resolved') {
                    $statusClass = 'status-resolved';
                } else if ($status === 'rejected') {
                    $statusClass = 'status-rejected';
                } else {
                    $statusClass = 'status-pending';
                }
                
                echo '<div class="result-item">';
                echo '<div class="result-header">';
                echo '<div class="result-title">' . htmlspecialchars($reclamation['cause']) . '</div>';
                echo '<div class="result-date">' . htmlspecialchars($reclamation['created_at']) . '</div>';
                echo '</div>';
                
                echo '<div class="result-content">';
                echo '<p><strong>Name:</strong> ' . htmlspecialchars($reclamation['full_name']) . '</p>';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($reclamation['email']) . '</p>';
                echo '<p class="description-text"><strong>Description:</strong> <span class="text-wrap">' . nl2br(htmlspecialchars($reclamation['description'])) . '</span></p>';
                
                // Display screenshot if available
                if (!empty($reclamation['screenshot'])) {
                    echo '<p><strong>Screenshot:</strong> <a href="' . htmlspecialchars($reclamation['screenshot']) . '" target="_blank" class="screenshot-link">View Screenshot</a></p>';
                }
                
                echo '<p><strong>Status:</strong> <span class="result-status ' . $statusClass . '">' . htmlspecialchars($reclamation['status']) . '</span></p>';
                
                if (!empty($reclamation['response'])) {
                    echo '<div class="result-response">';
                    echo '<h6>Response:</h6>';
                    echo '<p class="response-text">' . nl2br(htmlspecialchars($reclamation['response'])) . '</p>';
                    echo '<small>Response Date: ' . htmlspecialchars($reclamation['updated_at']) . '</small>';
                    echo '</div>';
                } else {
                    echo '<p class="result-no-response">No response yet.</p>';
                }
                
                // Only show action buttons if status is NOT resolved
                if ($status !== 'resolved') {
                    echo '<div class="result-actions">';
                    echo '<button type="button" class="btn-edit" data-id="' . htmlspecialchars($reclamation['id']) . '" ';
                    echo 'data-cause="' . htmlspecialchars($reclamation['cause']) . '" ';
                    echo 'data-description="' . htmlspecialchars($reclamation['description']) . '" ';
                    echo 'data-screenshot="' . htmlspecialchars($reclamation['screenshot'] ?? '') . '">';
                    echo '<i class="fas fa-edit"></i> Edit';
                    echo '</button>';
                    
                    if ($status === 'pending') {
                        echo '<button type="button" class="btn-supprimer" data-id="' . htmlspecialchars($reclamation['id']) . '">';
                        echo '<i class="fas fa-trash"></i> Delete';
                        echo '</button>';
                    }
                    echo '</div>';
                }
                
                echo '</div>'; // End result-content
                echo '</div>'; // End result-item
            }
        } else {
            echo '<div class="no-results">No reclamations found for this email address: ' . htmlspecialchars($email) . '</div>';
        }
        
        echo '</div>'; // End search-results-container
        $searchResults = ob_get_clean();
        
        // If this is an AJAX request, return just the results
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo $searchResults;
            exit;
        } else {
            // For direct access, output the results
            echo $searchResults;
            exit;
        }
    } catch (Exception $e) {
        error_log("Error in search: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}

// Handle reclamation deletion
if (isset($_GET['deleteReclamation']) && !empty($_GET['deleteReclamation'])) {
    $idReclamation = (int)$_GET['deleteReclamation'];
    $controller = new ReclamationController();
    $result = $controller->deleteReclamation($idReclamation);
    
    // If this is an AJAX request, return just the result
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => $result]);
        exit;
    } else {
        // Redirect back to the search page
        header('Location: front.php');
        exit;
    }
}

/// Common header function
function renderHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tunify - <?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <link rel="stylesheet" href="/projetweb/View/tunify_avec_connexion/reclamation/front/front.css">
    </head>
    <body>
    <?php
}

// Common footer function
function renderFooter() {
    ?>
        <footer>
            <p class="footer-text">&copy; <?php echo date('Y'); ?> Tunify. All rights reserved.</p>
        </footer>
    </body>
    </html>
    <?php
}


// Render the form page
function renderFormPage() {
    renderHeader('Submit Reclamation');
    $pdo = Config::getConnexion();
    $userConnected = getUserInfo($pdo);
    ?>
    <nav class="nav-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="\projetweb\View\tunify_avec_connexion\avec_connexion.php" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div class="search-container">
                <button id="searchBtn" class="search-btn">
                    <i class="fas fa-search"></i> Search by Email
                </button>
            </div>
            <?php 
                $pdo = config::getConnexion();
                $user = getUserInfo($pdo);
                $user_id = $user->getArtisteId();
                $unreadCount = countUnreadNotifications($user_id);
                if (isSubscriptionExpired($pdo, $user_id)){
                    $type= 'expired';
                }else{
                    $type= 'valid';
                }
                $user_role = $userConnected->getTypeUtilisateur();

            ?>
            <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-button">
                        <i class="fas fa-user"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="../../user/overview.php" target="_blank" onclick="reloadPage(); return false;">Account 
                            <i class="fas fa-external-link-alt external-link"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-count1"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>

                        <script>
                            function reloadPage() {
                                // Open overview.php in a new tab
                                window.open('../../user/overview.php', '_blank');

                                // Reload the current page (avec_connexion.php)
                                location.reload();
                            }
                        </script>

                        <a href="../../../tunify_avec_connexion/avec_connexion.php" onclick="toggleBox4(<?= $userdata['artiste_id']; ?>, '<?= $userdata['nom_utilisateur']; ?>', '<?= $userdata['image_path']; ?>')" style="border:none;">Profile</a>
                        <?php
                            if ($type == 'expired') {
                                echo '<a href="../../../tunifypaiement/dashboard.php">Upgrade to Premium <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <?php
                            if ($user_role == 'admin' || $user_role == 'artiste') {
                                echo '<a href="../../../backoffice/backoffice.php">Dashboard <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <a href="#support">Support <i class="fas fa-external-link-alt external-link"></i></a>
                        <a href="#" onclick="showSettingsSection(); return false;">Settings</a>
                        <a href="logout.php">Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/projetweb/View/tunisfy_sans_conexion/login.php" class="nav-link">Se connecter</a>
                <a href="/projetweb/View/tunisfy_sans_conexion/register.php" class="nav-link">S'inscrire</a>
            <?php endif; ?>
        </div>
        </div>
    </nav>
<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        color: #fff;
        padding: 10px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #222;
        min-width: 220px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        z-index: 999;
        overflow: hidden;
    }

    .dropdown-menu a {
        color: #fff;
        padding: 16px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .dropdown-menu a:hover {
        background-color: rgba(255,255,255,0.05);
    }

    .external-link {
        float: right;
        opacity: 0.7;
    }

    .show {
        display: block;
    }
    .notification-icon {
        position: relative;
        display: inline-block;
    }

    .notification-count {
        position: absolute;
        top: -10px;
        right: -8px;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-count1 {
        position: absolute;
        top: 14px;
        right: 50%;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-icon {
    position: relative;
}
</style>
    <div class="container">
        <div class="form-container">
            <h1><i class="fas fa-comment-alt"></i> Submit Your Reclamation</h1>
            <form id="reclamationForm" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="fullName" class="required-field">Full Name</label>
                    <input type="text" id="fullName" name="full_name"
                           value="<?= htmlspecialchars($userConnected->getNomUtilisateur()) ?>"
                           placeholder="Enter your full name" readonly>
                </div>
                <div class="form-group">
                    <label for="email" class="required-field">Email Address</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($userConnected->getEmail()) ?>"
                           placeholder="your@email.com" readonly>
                </div>
            </div>


                <div class="form-group">
                    <label for="cause" class="required-field">Reclamation Cause</label>
                    <select id="cause" name="cause">
                        <option value="">Select a cause...</option>
                        <?php
                        // Get all reclamation types
                        $typeController = new TypeReclamationController();
                        $types = $typeController->listeTypes();
                        foreach ($types as $type) {
                            echo '<option value="' . htmlspecialchars($type->type) . '">' . htmlspecialchars($type->type) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description" class="required-field">Description</label>
                    <textarea id="description" name="description" rows="6" placeholder="Please describe your issue in detail (minimum 30 characters)..."></textarea>
                </div>

                <!-- Screenshot Upload Box -->
                <div class="form-group">
                    <label>Screenshot (Optional)</label>
                    <div class="screenshot-box" id="screenshotBox">
                        <i class="fas fa-camera"></i>
                        <div class="upload-text">Drag & drop your screenshot here</div>
                        <div class="subtext">or click to browse files (JPG, PNG - max 5MB)</div>
                        <input type="file" id="fileInput" name="screenshot" accept="image/*">
                    </div>
                    <div id="previewContainer">
                        <img id="imagePreview" src="#" alt="Preview">
                        <button type="button" class="remove-btn" id="removeBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="submitBtn">
                    <span id="btnText">Submit Reclamation</span>
                    <div class="spinner" id="spinner"></div>
                </button>
                
                <div class="confirmation" id="confirmationMsg">
                    <i class="fas fa-check-circle"></i> Reclamation submitted! We'll contact you within 24 hours.
                </div>
            </form>
        </div>
    </div>


    <script src="front.js"></script>

   

    <?php
    renderFooter();
}

renderFormPage();

?>
