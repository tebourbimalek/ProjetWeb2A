<?php
// Start session and include configuration for DB connection
session_start();
require_once 'C:/xampp/htdocs/projetweb/model/config.php'; // Path to your config file (update if necessary)

$pdo = config::getConnexion();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $newsId = $_POST['news_id'] ?? null;
    $title = htmlspecialchars($_POST['news-title'] ?? '');
    $content = htmlspecialchars($_POST['news-content'] ?? '');
    $publicationDate = $_POST['news-date'] ?? '';

    // Validate the inputs
    $errors = [];
    if (empty($title)) {
        $errors[] = "News title is required.";
    }
    if (empty($content)) {
        $errors[] = "News content is required.";
    }
    if (empty($publicationDate)) {
        $errors[] = "Publication date is required.";
    }

    // Handle file upload (optional)
    $filePath = null;
    if (!empty($_FILES['news_file']['name'])) {
        $uploadDir = 'C:/xampp/htdocs/projetweb/View/tunify_avec_connexion/news/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $originalName = basename($_FILES['news_file']['name']);
        $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'png'];

        // Check file type
        if (in_array($fileExtension, $allowedTypes)) {
            $uniqueName = time() . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $fileExtension;
            $uploadFile = $uploadDir . $uniqueName;

            // Move the file to the upload directory
            if (move_uploaded_file($_FILES['news_file']['tmp_name'], $uploadFile)) {
                $filePath = $uploadFile;
            } else {
                $errors[] = "File upload failed.";
            }
        } else {
            $errors[] = "Invalid file type. Allowed types are PDF, DOC, DOCX, JPG, PNG.";
        }
    }

    // If there are validation errors, display them
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
        exit;
    }

    try {
        // Prepare SQL query to update the news item
        $sql = "UPDATE news SET titre = :title, contenu = :content, date_publication = :publicationDate";
        
        if ($filePath) {
            $sql .= ", image = :filePath";
        }

        $sql .= " WHERE id = :newsId";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':publicationDate', $publicationDate);
        
        if ($filePath) {
            $stmt->bindParam(':filePath', $filePath);
        }
        
        $stmt->bindParam(':newsId', $newsId);

        // Execute the update
        if ($stmt->execute()) {
            echo "News updated successfully!";
            // Optionally redirect back to the news page or to another page
            header("Location: ../backoffice.php");
            exit;
        } else {
            echo "Failed to update news.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
