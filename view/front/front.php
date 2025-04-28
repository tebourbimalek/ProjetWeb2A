<?php
// Debug information - add this at the top to help troubleshoot
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log request information
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

// Include the Config and Reclamation model
require_once "C:/xampp/htdocs/Tunify/config.php";
require_once "C:/xampp/htdocs/Tunify/model/Reclamation.php";
require_once "C:/xampp/htdocs/Tunify/controller/ReclamationController.php";

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
        <style>
            :root {
                --primary: #9b5de5;
                --primary-dark: #7d3cff;
                --primary-light: rgba(155, 93, 229, 0.1);
                --dark: #191414;
                --dark-light: #282828;
                --darker: #121212;
                --light: #f8f9fa;
                --text-light: #ffffff;
                --text-muted: #b3b3b3;
                --border-radius: 8px;
                --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                --transition: all 0.3s ease;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            }

            body {
                background-color: var(--darker);
                color: var(--text-light);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .nav-bar {
                background-color: rgba(0, 0, 0, 0.8);
                padding: 1.5rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 100;
                backdrop-filter: blur(10px);
            }

            .logo {
                height: 40px;
                filter: brightness(0) invert(1);
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 3rem 2rem;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            footer {
                background-color: var(--dark);
                padding: 2rem;
                text-align: center;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .footer-text {
                color: var(--text-muted);
                font-size: 0.9rem;
            }

            /* Home page specific styles */
            .hero {
                text-align: center;
                margin-bottom: 4rem;
            }

            h1 {
                font-size: 3rem;
                font-weight: 800;
                margin-bottom: 1rem;
                background: linear-gradient(to right, var(--primary), #ff6b6b);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .subtitle {
                font-size: 1.2rem;
                color: var(--text-muted);
                max-width: 700px;
                margin: 0 auto;
            }

            .cards {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
                margin-top: 2rem;
            }

            .card {
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                padding: 2rem;
                text-align: center;
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: var(--transition);
                box-shadow: var(--box-shadow);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 250px;
            }

            .card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
                border-color: var(--primary);
            }

            .card-icon {
                font-size: 3rem;
                color: var(--primary);
                margin-bottom: 1.5rem;
            }

            .card-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }

            .card-text {
                color: var(--text-muted);
                margin-bottom: 1.5rem;
            }

            .btn {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
                font-weight: 600;
                border-radius: 50px;
                cursor: pointer;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
            }

            .btn-primary {
                background-color: var(--primary);
                color: white;
                border: none;
            }

            .btn-primary:hover {
                background-color: var(--primary-dark);
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(155, 93, 229, 0.4);
            }

            .btn-outline {
                background-color: transparent;
                color: var(--primary);
                border: 2px solid var(--primary);
            }

            .btn-outline:hover {
                background-color: var(--primary-light);
                transform: scale(1.05);
            }

            /* Form page specific styles */
            .form-container {
                max-width: 700px;
                margin: 2rem auto;
                padding: 2.5rem;
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
                position: relative;
            }

            label {
                display: block;
                margin-bottom: 0.75rem;
                font-weight: 600;
                color: var(--text-light);
                font-size: 0.95rem;
            }

            .required-field::after {
                content: " *";
                color: var(--primary);
            }

            input, select, textarea {
                width: 100%;
                padding: 1rem;
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: var(--border-radius);
                font-size: 1rem;
                color: var(--text-light);
                transition: var(--transition);
            }

            input::placeholder, textarea::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            input:focus, select:focus, textarea:focus {
                outline: none;
                border-color: var(--primary);
                background-color: var(--primary-light);
                box-shadow: 0 0 0 2px rgba(155, 93, 229, 0.3);
            }

            textarea {
                min-height: 150px;
                resize: vertical;
            }

            select {
                appearance: none;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 1rem center;
                background-size: 1rem;
            }
 
            /* For better contrast on all options */
            select#cause option {
                color: #333333;
                padding: 8px;
                background-color: #f8f9fa;
            }

            /* Improved dropdown styling */
            .swal2-select {
                color: #333333 !important;
                background-color: #ffffff !important;
            }
            
            .swal2-select option {
                color: #333333 !important;
                background-color: #ffffff !important;
                padding: 10px !important;
            }
            
            /* Selected option */
            .swal2-select option:checked,
            .swal2-select option:hover {
                background-color: #9b5de5 !important;
                color: #ffffff !important;
            }

            button {
                background-color: var(--primary);
                color: white;
                border: none;
                padding: 1.2rem;
                font-size: 1rem;
                font-weight: 600;
                border-radius: 50px;
                cursor: pointer;
                width: 100%;
                transition: var(--transition);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-top: 1rem;
            }

            button:hover {
                background-color: var(--primary-dark);
                transform: scale(1.02);
                box-shadow: 0 5px 15px rgba(155, 93, 229, 0.4);
            }

            button:active {
                transform: scale(0.98);
            }

            .confirmation {
                display: none;
                background-color: rgba(25, 135, 84, 0.2);
                color: #d1fae5;
                padding: 1.25rem;
                border-radius: var(--border-radius);
                margin-top: 1.5rem;
                text-align: center;
                border: 1px solid rgba(74, 222, 128, 0.3);
                animation: fadeIn 0.4s ease-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* Screenshot Upload Box */
            .screenshot-box {
                border: 2px dashed rgba(255, 255, 255, 0.3);
                border-radius: var(--border-radius);
                padding: 2rem;
                text-align: center;
                cursor: pointer;
                transition: var(--transition);
                margin-bottom: 1.5rem;
                background-color: rgba(255, 255, 255, 0.05);
                position: relative;
                overflow: hidden;
            }

            .screenshot-box:hover {
                border-color: var(--primary);
                background-color: var(--primary-light);
            }

            .screenshot-box i {
                font-size: 2.5rem;
                color: var(--primary);
                margin-bottom: 1rem;
                display: block;
            }

            .screenshot-box .upload-text {
                font-weight: 500;
                margin-bottom: 0.5rem;
            }

            .screenshot-box .subtext {
                font-size: 0.85rem;
                color: rgba(255, 255, 255, 0.6);
            }

            #fileInput {
                display: none;
            }

            #previewContainer {
                display: none;
                margin-top: 1rem;
                position: relative;
            }

            #imagePreview {
                max-width: 100%;
                max-height: 200px;
                border-radius: var(--border-radius);
                display: block;
                margin: 0 auto;
            }

            .remove-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: rgba(0, 0, 0, 0.7);
                border: none;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--transition);
            }

            .remove-btn:hover {
                background-color: rgba(255, 0, 0, 0.7);
            }

            /* Loading spinner */
            .spinner {
                display: none;
                width: 20px;
                height: 20px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: spin 1s ease-in-out infinite;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            .error-message {
                color: #ff4d4d;
                font-size: 0.85rem;
                margin-top: 0.5rem;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .container {
                    padding: 2rem 1.5rem;
                }
                
                h1 {
                    font-size: 2.5rem;
                }
                
                .cards {
                    grid-template-columns: 1fr;
                }
                
                .form-grid {
                    grid-template-columns: 1fr;
                }
                
                .form-container {
                    padding: 1.5rem;
                    margin: 1.5rem;
                }
            }

            /* Search button styles */
            .search-container {
                position: relative;
            }

            .search-btn {
                background-color: var(--primary);
                color: white;
                border: none;
                padding: 0.6rem 1rem;
                font-size: 0.9rem;
                font-weight: 600;
                border-radius: 50px;
                cursor: pointer;
                transition: var(--transition);
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .search-btn:hover {
                background-color: var(--primary-dark);
                transform: scale(1.05);
            }

            .search-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }

            .search-modal-content {
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                padding: 2rem;
                width: 90%;
                max-width: 500px;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: var(--box-shadow);
            }

            .search-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
            }

            .search-modal-title {
                font-size: 1.5rem;
                font-weight: 600;
            }

            .close-modal {
                background: none;
                border: none;
                color: var(--text-muted);
                font-size: 1.5rem;
                cursor: pointer;
                transition: var(--transition);
                padding: 0;
                width: auto;
                height: auto;
            }

            .close-modal:hover {
                color: var(--text-light);
                transform: none;
            }

            .search-form {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .search-results {
                margin-top: 1.5rem;
                max-height: 300px;
                overflow-y: auto;
            }

            .no-results {
                text-align: center;
                color: var(--text-muted);
                padding: 1rem;
            }

            .result-item {
                background-color: rgba(255, 255, 255, 0.05);
                border-radius: var(--border-radius);
                padding: 1rem;
                margin-bottom: 0.75rem;
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: var(--transition);
            }

            .result-item:hover {
                background-color: rgba(255, 255, 255, 0.1);
                border-color: var(--primary);
            }

            .result-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }

            .result-title {
                font-weight: 600;
            }

            .result-date {
                color: var(--text-muted);
                font-size: 0.85rem;
            }

            .result-description {
                color: var(--text-muted);
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }

            .result-status {
                display: inline-block;
                padding: 0.25rem 0.75rem;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
            }

            .status-pending {
                background-color: rgba(255, 193, 7, 0.2);
                color: #ffc107;
            }

            .status-resolved {
                background-color: rgba(25, 135, 84, 0.2);
                color: #20c997;
            }

            .status-processing {
                background-color: rgba(13, 110, 253, 0.2);
                color: #0d6efd;
            }
            .status-rejected {
                background-color: rgba(220, 53, 69, 0.2);
                color: #dc3545;
            }
            .result-content {
                margin-bottom: 10px;
            }

            .result-content p {
                margin: 5px 0;
            }

            /* Fix for description text overflow */
            .description-text, .response-text {
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                max-width: 100% !important;
                display: block !important;
            }

            .text-wrap {
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                white-space: normal !important;
                width: 100% !important;
                display: inline-block !important;
            }

            .result-response {
                margin-top: 10px;
                padding: 10px;
                background-color: rgba(0, 0, 0, 0.1);
                border-radius: 5px;
            }

            .result-response h6 {
                margin-bottom: 5px;
                font-weight: bold;
            }

            .result-no-response {
                font-style: italic;
                color: var(--text-muted);
            }

            .result-actions {
                margin-top: 10px;
                text-align: right;
            }

            .btn-supprimer {
                background-color: rgba(220, 53, 69, 0.2);
                color: #ff6b6b;
                border: 1px solid rgba(220, 53, 69, 0.3);
                padding: 0.5rem 1rem;
                border-radius: 50px;
                font-size: 0.9rem;
                cursor: pointer;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin-right: 0.5rem;
            }

            .btn-supprimer:hover {
                background-color: rgba(220, 53, 69, 0.3);
                transform: scale(1.05);
            }
            
            /* Edit button styles */
            .btn-edit {
                background-color: rgba(13, 110, 253, 0.2);
                color: #0d6efd;
                border: 1px solid rgba(13, 110, 253, 0.3);
                padding: 0.5rem 1rem;
                border-radius: 50px;
                font-size: 0.9rem;
                cursor: pointer;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                margin-right: 0.5rem;
            }
            
            .btn-edit:hover {
                background-color: rgba(13, 110, 253, 0.3);
                transform: scale(1.05);
            }

            /* Search results styling */
            .result-heading {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 1.5rem;
                color: var(--text-light);
            }

            .result-email {
                color: var(--primary);
            }

            .result-content {
                margin-top: 0.75rem;
            }

            .result-content p {
                margin-bottom: 0.5rem;
            }

            .result-response {
                margin-top: 1rem;
                padding: 1rem;
                background-color: rgba(255, 255, 255, 0.05);
                border-radius: var(--border-radius);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .result-response h6 {
                font-weight: 600;
                margin-bottom: 0.5rem;
                color: var(--primary);
            }

            .result-response small {
                display: block;
                margin-top: 0.5rem;
                color: var(--text-muted);
                font-size: 0.8rem;
            }

            .result-no-response {
                color: var(--text-muted);
                font-style: italic;
            }

            .result-actions {
                margin-top: 1rem;
                display: flex;
                justify-content: flex-end;
            }

            .status-resolved {
                background-color: rgba(25, 135, 84, 0.2);
                color: #20c997;
            }

            .status-rejected {
                background-color: rgba(220, 53, 69, 0.2);
                color: #ff6b6b;
            }

            .status-pending {
                background-color: rgba(255, 193, 7, 0.2);
                color: #ffc107;
            }

            /* Make the search modal scrollable for many results */
            .search-modal-content {
                max-height: 90vh;
                overflow-y: auto;
            }
            
            /* SweetAlert2 custom styles */
            .swal2-popup {
                background: var(--dark-light) !important;
                color: var(--text-light) !important;
                border-radius: var(--border-radius) !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
            }
            
            .swal2-title, .swal2-html-container {
                color: var(--text-light) !important;
            }
            
            .swal2-input, .swal2-textarea {
                background-color: rgba(255, 255, 255, 0.1) !important;
                border: 1px solid rgba(255, 255, 255, 0.2) !important;
                color: var(--text-light) !important;
            }
            
            .swal2-input:focus, .swal2-textarea:focus {
                border-color: var(--primary) !important;
                box-shadow: 0 0 0 2px rgba(155, 93, 229, 0.3) !important;
            }
            
            .swal2-select {
                background-color: rgba(255, 255, 255, 0.1) !important;
                border: 1px solid rgba(255, 255, 255, 0.2) !important;
                color: var(--text-light) !important;
            }
            
            .swal2-select option {
                background-color: var(--dark) !important;
                color: var(--text-light) !important;
            }
            
            .swal2-confirm {
                background-color: var(--primary) !important;
            }
            
            .swal2-confirm:hover {
                background-color: var(--primary-dark) !important;
            }
            
            /* Fix for SweetAlert2 textarea */
            .swal2-textarea {
                word-wrap: break-word !important;
                overflow-wrap: break-word !important;
                white-space: pre-wrap !important;
                max-width: 100% !important;
            }

            /* Screenshot link styles */
            .screenshot-link {
                color: var(--primary);
                text-decoration: none;
                font-weight: 600;
                transition: var(--transition);
            }

            .screenshot-link:hover {
                color: var(--primary-dark);
                text-decoration: underline;
            }

            /* Edit modal screenshot styles */
            .edit-screenshot-box {
                border: 2px dashed rgba(255, 255, 255, 0.3);
                border-radius: var(--border-radius);
                padding: 1.5rem;
                text-align: center;
                cursor: pointer;
                transition: var(--transition);
                margin-top: 1rem;
                background-color: rgba(255, 255, 255, 0.05);
            }

            .edit-screenshot-box:hover {
                border-color: var(--primary);
                background-color: var(--primary-light);
            }

            .edit-screenshot-preview {
                max-width: 100%;
                max-height: 150px;
                border-radius: var(--border-radius);
                margin: 0.5rem auto;
                display: block;
            }

            .current-screenshot {
                margin-top: 0.5rem;
                padding: 0.5rem;
                background-color: rgba(0, 0, 0, 0.2);
                border-radius: var(--border-radius);
                text-align: center;
            }

            .current-screenshot img {
                max-width: 100%;
                max-height: 100px;
                border-radius: var(--border-radius);
                margin: 0.5rem auto;
            }
            
            /* Edit modal centered styles */
            .edit-modal-container .swal2-html-container {
                margin: 0 auto;
                padding: 0;
            }
            
            .edit-modal-popup {
                width: 90% !important;
                max-width: 550px !important;
            }
            
            .edit-modal-content {
                padding: 0 !important;
            }
            
            /* Improved edit form layout */
            .edit-form-group {
                margin-bottom: 20px;
                text-align: left;
            }
            
            .edit-form-label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: var(--text-light);
                font-size: 1rem;
            }
            
            .edit-form-control {
                width: 100%;
                padding: 12px;
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: var(--border-radius);
                color: var(--text-light);
                font-size: 1rem;
            }
            
            /* Screenshot action buttons */
            .screenshot-actions {
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
            }
            
            .btn-keep, .btn-remove {
                flex: 1;
                margin: 0 5px;
                padding: 8px 0;
                border-radius: 50px;
                font-weight: 600;
                font-size: 0.9rem;
                cursor: pointer;
                transition: var(--transition);
                text-align: center;
            }
            
            .btn-keep {
                background-color: rgba(25, 135, 84, 0.2);
                color: #20c997;
                border: 1px solid rgba(25, 135, 84, 0.3);
            }
            
            .btn-keep:hover {
                background-color: rgba(25, 135, 84, 0.3);
            }
            
            .btn-remove {
                background-color: rgba(220, 53, 69, 0.2);
                color: #ff6b6b;
                border: 1px solid rgba(220, 53, 69, 0.3);
            }
            
            .btn-remove:hover {
                background-color: rgba(220, 53, 69, 0.3);
            }
        </style>
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
    ?>
    <nav class="nav-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="?page=home" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        
            <img src="C:/xampp/htdocs/Tunify/view/Tunify.png" alt="Tunify Logo" class="logo" width="50">
        </div>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div class="search-container">
                <button id="searchBtn" class="search-btn">
                    <i class="fas fa-search"></i> Search by Email
                </button>
            </div>
            <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                <i class="fas fa-headphones"></i> Support Center
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1><i class="fas fa-comment-alt"></i> Submit Your Reclamation</h1>
            <form id="reclamationForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fullName" class="required-field">Full Name</label>
                        <input type="text" id="fullName" name="full_name" placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="email" class="required-field">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cause" class="required-field">Reclamation Cause</label>
                    <select id="cause" name="cause">
                        <option value="">Select a cause...</option>
                        <option value="technical">Technical Issue</option>
                        <option value="billing">Billing Problem</option>
                        <option value="account">Account Issue</option>
                        <option value="content">Content Concern</option>
                        <option value="other">Other</option>
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

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById("reclamationForm");
    const fullNameInput = document.getElementById("fullName");
    const emailInput = document.getElementById("email");
    const causeInput = document.getElementById("cause");
    const descriptionInput = document.getElementById("description");
    const fileInput = document.getElementById("fileInput");
    const screenshotBox = document.getElementById("screenshotBox");
    const previewContainer = document.getElementById("previewContainer");
    const imagePreview = document.getElementById("imagePreview");
    const removeBtn = document.getElementById("removeBtn");
    const submitBtn = document.getElementById("submitBtn");
    const spinner = document.getElementById("spinner");
    const btnText = document.getElementById("btnText");
    const confirmationMsg = document.getElementById("confirmationMsg");

    // Regular expressions for validation
    const namePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Clear all error messages
    function clearErrors() {
        document.querySelectorAll(".error-message").forEach(el => el.remove());
    }

    // Show error message for an input
    function showError(input, message) {
        clearErrorForInput(input);

        const error = document.createElement("div");
        error.className = "error-message";
        error.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

        input.parentElement.appendChild(error);
        input.style.borderColor = "#ff4d4d";
    }

    // Clear error for a specific input
    function clearErrorForInput(input) {
        const existingError = input.parentElement.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }
        input.style.borderColor = "";
    }

    // Handle screenshot upload UI
    screenshotBox.addEventListener('click', () => fileInput.click());
    
    // Handle file selection
    fileInput.addEventListener('change', handleFileSelect);

    // Handle drag and drop
    screenshotBox.addEventListener("dragover", (e) => {
        e.preventDefault();
        screenshotBox.style.borderColor = "var(--primary)";
        screenshotBox.style.backgroundColor = "var(--primary-light)";
    });

    screenshotBox.addEventListener("dragleave", () => {
        screenshotBox.style.borderColor = "";
        screenshotBox.style.backgroundColor = "";
    });

    screenshotBox.addEventListener("drop", (e) => {
        e.preventDefault();
        screenshotBox.style.borderColor = "";
        screenshotBox.style.backgroundColor = "";

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    function handleFileSelect() {
        const file = fileInput.files[0];

        if (file) {
            // Validate file type
            const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            if (!allowedTypes.includes(file.type)) {
                showError(fileInput, "Invalid file type. Please upload JPG, PNG, or GIF.");
                fileInput.value = "";
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showError(fileInput, "File is too large. Maximum size is 5MB.");
                fileInput.value = "";
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                previewContainer.style.display = "block";
                screenshotBox.style.display = "none";
                clearErrorForInput(fileInput);
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove selected file
    removeBtn.addEventListener("click", () => {
        fileInput.value = "";
        previewContainer.style.display = "none";
        screenshotBox.style.display = "block";
    });

    // Input validation on blur
    fullNameInput.addEventListener("blur", function() {
        if (this.value.trim() !== "" && !namePattern.test(this.value.trim())) {
            showError(this, "Please enter a valid name (at least 2 letters, letters only).");
        } else {
            clearErrorForInput(this);
        }
    });

    emailInput.addEventListener("blur", function() {
        if (this.value.trim() !== "" && !emailPattern.test(this.value.trim())) {
            showError(this, "Please enter a valid email address.");
        } else {
            clearErrorForInput(this);
        }
    });

    causeInput.addEventListener("blur", function() {
        if (this.value === "") {
            showError(this, "Please select a reclamation cause.");
        } else {
            clearErrorForInput(this);
        }
    });

    descriptionInput.addEventListener("blur", function() {
        if (this.value.trim() !== "" && this.value.trim().length < 30) {
            showError(this, "Description must be at least 30 characters long.");
        } else {
            clearErrorForInput(this);
        }
    });

    // Form submission
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        clearErrors();

        let hasError = false;

        // Validate full name
        const fullName = fullNameInput.value.trim();
        if (fullName === "" || !namePattern.test(fullName)) {
            showError(fullNameInput, "Please enter a valid name (at least 2 letters, letters only).");
            hasError = true;
        }

        // Validate email
        const email = emailInput.value.trim();
        if (email === "" || !emailPattern.test(email)) {
            showError(emailInput, "Please enter a valid email address.");
            hasError = true;
        }

        // Validate cause
        const cause = causeInput.value;
        if (cause === "") {
            showError(causeInput, "Please select a reclamation cause.");
            hasError = true;
        }

        // Validate description
        const description = descriptionInput.value.trim();
        if (description === "" || description.length < 30) {
            showError(descriptionInput, "Description must be at least 30 characters long.");
            hasError = true;
        }

        // Validate file if one is selected
        const file = fileInput.files[0];
        if (file) {
            const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            if (!allowedTypes.includes(file.type)) {
                showError(fileInput, "Invalid file type. Please upload JPG, PNG, or GIF.");
                hasError = true;
            }

            if (file.size > 5 * 1024 * 1024) {
                showError(fileInput, "File is too large. Maximum size is 5MB.");
                hasError = true;
            }
        }

        // If there are errors, stop form submission
        if (hasError) {
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        spinner.style.display = "inline-block";
        btnText.style.display = "none";

        // Create FormData object
        const formData = new FormData(form);

        // Send AJAX request
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            // Try to parse as JSON, but don't fail if it's not JSON
            let result;
            try {
                result = JSON.parse(data);
            } catch (e) {
                // If not JSON, assume success
                result = { status: 'success' };
            }

            if (result.status === 'success') {
                // Show success message
                confirmationMsg.style.display = "block";
                
                // Reset form after delay
                setTimeout(() => {
                    form.reset();
                    previewContainer.style.display = "none";
                    screenshotBox.style.display = "block";
                    confirmationMsg.style.display = "none";
                }, 3000);
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            spinner.style.display = "none";
            btnText.style.display = "inline-block";
        });
    });
});

// Search functionality
document.addEventListener('DOMContentLoaded', () => {
    // Create search modal
    const searchModal = document.createElement('div');
    searchModal.className = 'search-modal';
    searchModal.innerHTML = `
    <div class="search-modal-content">
        <div class="search-modal-header">
            <div class="search-modal-title">Search Reclamations by Email</div>
            <button class="close-modal">&times;</button>
        </div>
        <form class="search-form" id="searchForm">
            <div class="form-group">
                <label for="emailSearch">Email Address</label>
                <input type="email" id="emailSearch" name="emailSearch" placeholder="Enter email to search">
            </div>
            <button type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <div class="search-results" id="searchResults"></div>
    </div>
`;
    document.body.appendChild(searchModal);

    // Get elements
    const searchBtn = document.getElementById('searchBtn');
    const closeModalBtn = document.querySelector('.close-modal');
    const searchForm = document.getElementById('searchForm');
    const searchResults = document.getElementById('searchResults');

    // Open search modal
    searchBtn.addEventListener('click', () => {
        searchModal.style.display = 'flex';
    });

    // Close search modal
    closeModalBtn.addEventListener('click', () => {
        searchModal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            searchModal.style.display = 'none';
        }
    });

    // Handle search form submission
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const emailInput = document.getElementById('emailSearch');
        const email = emailInput.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // Clear any existing error messages
        const existingError = emailInput.parentElement.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }
        emailInput.style.borderColor = "";
        
        // Validate email
        if (email === "" || !emailPattern.test(email)) {
            // Show error message
            const error = document.createElement("div");
            error.className = "error-message";
            error.innerHTML = `<i class="fas fa-exclamation-circle"></i> Please enter a valid email address.`;
        
            emailInput.parentElement.appendChild(error);
            emailInput.style.borderColor = "#ff4d4d";
            return;
        }
        
        // Show loading state
        searchResults.innerHTML = '<div class="no-results">Searching...</div>';
        
        // Use the current URL for the AJAX request
        const searchUrl = `${window.location.href.split('?')[0]}?emailSearch=${encodeURIComponent(email)}`;
        console.log("Search URL:", searchUrl);
        
        // Make AJAX request to search for reclamations
        fetch(searchUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
        .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
                throw new Error(`Server responded with status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            console.log("Search response received");
            
            // Display the results directly
            searchResults.innerHTML = data;
            
            // Add event listeners to delete buttons
            document.querySelectorAll('.btn-supprimer').forEach(btn => {
                btn.addEventListener('click', function() {
                    const reclamationId = this.getAttribute('data-id');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#9b5de5',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send delete request
                            const deleteUrl = `${window.location.href.split('?')[0]}?deleteReclamation=${reclamationId}`;
                            
                            fetch(deleteUrl, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Server responded with status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Reclamation deleted successfully',
                                        icon: 'success'
                                    }).then(() => {
                                        // Refresh the search results
                                        searchForm.dispatchEvent(new Event('submit'));
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Could not delete the reclamation. Please try again.',
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Network Error',
                                    text: 'Failed to connect to the server. Please check your connection.',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });
            
            // Add event listeners to edit buttons
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const reclamationId = this.getAttribute('data-id');
                    const currentCause = this.getAttribute('data-cause');
                    const currentDescription = this.getAttribute('data-description');
                    const currentScreenshot = this.getAttribute('data-screenshot');
                    
                    // Create the HTML for the edit form with improved styling
                    let editFormHtml = `
                        <div class="edit-form-group">
                            <label for="swal-cause" class="edit-form-label">Cause</label>
                            <select id="swal-cause" class="edit-form-control" style="background-color: #f8f9fa; color: #333333;">
                                <option value="technical" ${currentCause === 'technical' ? 'selected' : ''}>Technical Issue</option>
                                <option value="billing" ${currentCause === 'billing' ? 'selected' : ''}>Billing Problem</option>
                                <option value="account" ${currentCause === 'account' ? 'selected' : ''}>Account Issue</option>
                                <option value="content" ${currentCause === 'content' ? 'selected' : ''}>Content Concern</option>
                                <option value="other" ${currentCause === 'other' ? 'selected' : ''}>Other</option>
                            </select>
                        </div>
                        <div class="edit-form-group">
                            <label for="swal-description" class="edit-form-label">Description</label>
                            <textarea id="swal-description" class="edit-form-control" placeholder="Enter description..." style="min-height: 120px; word-wrap: break-word; white-space: pre-wrap;">${currentDescription}</textarea>
                            <div id="description-error" class="error-message" style="display: none;"></div>
                        </div>
                    `;
                    
                    // Add screenshot section
                    editFormHtml += `
                        <div class="edit-form-group">
                            <label for="swal-screenshot" class="edit-form-label">Screenshot</label>
                    `;
                    
                    // Show current screenshot if available
                    if (currentScreenshot) {
                        editFormHtml += `
                            <div class="current-screenshot">
                                <p style="margin-bottom: 5px; font-size: 0.9rem; color: #f8f9fa;">Current Screenshot:</p>
                                <img src="${currentScreenshot}" alt="Current Screenshot">
                                <div class="screenshot-actions">
                                    <button type="button" id="keep-screenshot" class="btn-keep">KEEP</button>
                                    <button type="button" id="remove-screenshot" class="btn-remove">REMOVE</button>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Add upload new screenshot option
                    editFormHtml += `
                        <div id="edit-screenshot-box" class="edit-screenshot-box">
                            <i class="fas fa-camera" style="font-size: 2rem; color: #9b5de5; margin-bottom: 10px; display: block;"></i>
                            <div style="font-weight: 500; margin-bottom: 5px;">Upload new screenshot</div>
                            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.6);">Click to browse files (JPG, PNG - max 5MB)</div>
                        </div>
                        <div id="edit-preview-container" style="display: none; margin-top: 10px; position: relative;">
                            <img id="edit-image-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 150px; border-radius: 8px; display: block; margin: 0   style="max-width: 100%; max-height: 150px; border-radius: 8px; display: block; margin: 0 auto;">
                            <button type="button" id="edit-remove-btn" style="position: absolute; top: 5px; right: 5px; background-color: rgba(0, 0, 0, 0.7); border: none; color: white; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="file" id="edit-file-input" name="screenshot" accept="image/*" style="display: none;">
                        <div id="screenshot-error" class="error-message" style="display: none;"></div>
                    </div>
                    `;
                    
                    Swal.fire({
                        title: 'Edit Reclamation',
                        html: editFormHtml,
                        showCancelButton: true,
                        confirmButtonColor: '#9b5de5',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Cancel',
                        focusConfirm: false,
                        customClass: {
                            container: 'edit-modal-container',
                            popup: 'edit-modal-popup',
                            content: 'edit-modal-content'
                        },
                        didOpen: () => {
                            // Focus on the description field
                            document.getElementById('swal-description').focus();
                            
                            // Add input validation
                            const descriptionField = document.getElementById('swal-description');
                            descriptionField.addEventListener('input', function() {
                                const descriptionError = document.getElementById('description-error');
                                if (this.value.trim().length < 30) {
                                    descriptionError.textContent = 'Description must be at least 30 characters long.';
                                    descriptionError.style.display = 'block';
                                } else {
                                    descriptionError.style.display = 'none';
                                }
                            });
                            
                            // Screenshot handling
                            const editFileInput = document.getElementById('edit-file-input');
                            const editScreenshotBox = document.getElementById('edit-screenshot-box');
                            const editPreviewContainer = document.getElementById('edit-preview-container');
                            const editImagePreview = document.getElementById('edit-image-preview');
                            const editRemoveBtn = document.getElementById('edit-remove-btn');
                            
                            // Handle screenshot box click
                            editScreenshotBox.addEventListener('click', () => {
                                editFileInput.click();
                            });
                            
                            // Handle file selection
                            editFileInput.addEventListener('change', function() {
                                const file = this.files[0];
                                
                                if (file) {
                                    // Validate file type
                                    const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
                                    if (!allowedTypes.includes(file.type)) {
                                        const screenshotError = document.getElementById('screenshot-error');
                                        screenshotError.textContent = "Invalid file type. Please upload JPG, PNG, or GIF.";
                                        screenshotError.style.display = 'block';
                                        this.value = "";
                                        return;
                                    }
                                    
                                    // Validate file size (5MB max)
                                    if (file.size > 5 * 1024 * 1024) {
                                        const screenshotError = document.getElementById('screenshot-error');
                                        screenshotError.textContent = "File is too large. Maximum size is 5MB.";
                                        screenshotError.style.display = 'block';
                                        this.value = "";
                                        return;
                                    }
                                    
                                    // Show preview
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        editImagePreview.src = e.target.result;
                                        editPreviewContainer.style.display = "block";
                                        editScreenshotBox.style.display = "none";
                                        
                                        // Hide current screenshot if exists
                                        const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                        if (currentScreenshotDiv) {
                                            currentScreenshotDiv.style.display = 'none';
                                        }
                                        
                                        // Hide error message if exists
                                        const screenshotError = document.getElementById('screenshot-error');
                                        if (screenshotError) {
                                            screenshotError.style.display = 'none';
                                        }
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                            
                            // Handle remove button click
                            if (editRemoveBtn) {
                                editRemoveBtn.addEventListener('click', () => {
                                    editFileInput.value = "";
                                    editPreviewContainer.style.display = "none";
                                    editScreenshotBox.style.display = "block";
                                    
                                    // Show current screenshot if exists
                                    const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                    if (currentScreenshotDiv) {
                                        currentScreenshotDiv.style.display = 'block';
                                    }
                                });
                            }
                            
                            // Handle keep/remove current screenshot buttons
                            const keepScreenshotBtn = document.getElementById('keep-screenshot');
                            const removeScreenshotBtn = document.getElementById('remove-screenshot');
                            
                            if (keepScreenshotBtn) {
                                keepScreenshotBtn.addEventListener('click', () => {
                                    // Just keep the current screenshot, no action needed
                                    // Hide the upload box
                                    editScreenshotBox.style.display = 'none';
                                    
                                    // Add a visual indicator that it's selected
                                    const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                    if (currentScreenshotDiv) {
                                        currentScreenshotDiv.style.border = '2px solid #20c997';
                                    }
                                });
                            }
                            
                            if (removeScreenshotBtn) {
                                removeScreenshotBtn.addEventListener('click', () => {
                                    // Mark the screenshot for removal
                                    const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                    if (currentScreenshotDiv) {
                                        currentScreenshotDiv.style.display = 'none';
                                    }
                                    
                                    // Show the upload box
                                    editScreenshotBox.style.display = 'block';
                                    
                                    // Add a hidden input to indicate removal
                                    const removeScreenshotInput = document.createElement('input');
                                    removeScreenshotInput.type = 'hidden';
                                    removeScreenshotInput.id = 'remove-screenshot-flag';
                                    removeScreenshotInput.value = '1';
                                    document.querySelector('.swal2-html-container').appendChild(removeScreenshotInput);
                                });
                            }
                        },
                        preConfirm: () => {
                            const cause = document.getElementById('swal-cause').value;
                            const description = document.getElementById('swal-description').value;
                            const descriptionError = document.getElementById('description-error');
                            
                            // Validate description
                            if (!description || description.trim().length < 30) {
                                descriptionError.textContent = 'Description must be at least 30 characters long.';
                                descriptionError.style.display = 'block';
                                return false;
                            }
                            
                            // Check if we have a new file
                            const fileInput = document.getElementById('edit-file-input');
                            const file = fileInput.files[0];
                            
                            // Check if we're removing the current screenshot
                            const removeScreenshotFlag = document.getElementById('remove-screenshot-flag');
                            const removeScreenshot = removeScreenshotFlag ? true : false;
                            
                            // Return the form data
                            return { 
                                cause, 
                                description, 
                                file,
                                removeScreenshot
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Updating...',
                                text: 'Please wait while we update your reclamation.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Get the update URL
                            const updateUrl = `${window.location.href.split('?')[0]}?updateReclamation=${reclamationId}`;
                            
                            // Check if we have a file to upload
                            if (result.value.file) {
                                // Create FormData for file upload
                                const formData = new FormData();
                                formData.append('cause', result.value.cause);
                                formData.append('description', result.value.description);
                                formData.append('screenshot', result.value.file);
                                
                                // Send update request with file
                                fetch(updateUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Server responded with status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: data.message,
                                            icon: 'success',
                                            confirmButtonColor: '#9b5de5'
                                        }).then(() => {
                                            // Refresh the search results
                                            searchForm.dispatchEvent(new Event('submit'));
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message || 'Failed to update reclamation.',
                                            icon: 'error',
                                            confirmButtonColor: '#9b5de5'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Network Error',
                                        text: 'Failed to connect to the server. Please check your connection.',
                                        icon: 'error',
                                        confirmButtonColor: '#9b5de5'
                                    });
                                });
                            } else {
                                // Create data object for JSON request
                                const data = {
                                    cause: result.value.cause,
                                    description: result.value.description,
                                    removeScreenshot: result.value.removeScreenshot
                                };
                                
                                // Send update request without file
                                fetch(updateUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify(data)
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Server responded with status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: data.message,
                                            icon: 'success',
                                            confirmButtonColor: '#9b5de5'
                                        }).then(() => {
                                            // Refresh the search results
                                            searchForm.dispatchEvent(new Event('submit'));
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message || 'Failed to update reclamation.',
                                            icon: 'error',
                                            confirmButtonColor: '#9b5de5'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Network Error',
                                        text: 'Failed to connect to the server. Please check your connection.',
                                        icon: 'error',
                                        confirmButtonColor: '#9b5de5'
                                    });
                                });
                            }
                        }
                    });
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            searchResults.innerHTML = `<div class="no-results">Error: ${error.message}</div>`;
            
            Swal.fire({
                title: 'Network Error',
                text: 'Failed to connect to the server. Please check your connection.',
                icon: 'error',
                confirmButtonColor: '#9b5de5'
            });
        });
    });
});
    </script>

    <?php
    renderFooter();
}

renderFormPage();

?>
