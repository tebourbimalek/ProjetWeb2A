<?php
require_once "C:/xampp/htdocs/Tunify/config.php";
require_once "C:/xampp/htdocs/Tunify/model/Reclamation.php";

class ReclamationController
{

public function createReclamation($data, $file = null)
{
    $pdo = Config::getConnexion();
    try {
        $screenshotPath = null;
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $filename = uniqid() . '_' . basename($file['name']);
            $targetPath = $uploadDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $screenshotPath = $targetPath;
            }
        }

        $stmt = $pdo->prepare(
            "INSERT INTO reclamations 
            (full_name, email, cause, description, screenshot, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())"
        );

        $success = $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['cause'],
            $data['description'],
            $screenshotPath
        ]);

        if ($success && $this->sendConfirmationEmail($data['email'], $data['full_name'])) {
            return ['status' => 'success', 'message' => 'Reclamation submitted successfully'];
        }
        return ['status' => 'error', 'message' => 'Error submitting reclamation'];
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()];
    }
}

    public static function getAllReclamations()
    {
        $pdo = Config::getConnexion();
        return $pdo->query("SELECT * FROM reclamations ORDER BY created_at DESC")->fetchAll();
    }
}

// Form handling
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_reporting(0); // Disable error display
    header('Content-Type: application/json');
    
    try {
        // Process form data
        $controller = new ReclamationController();
        $result = $controller->createReclamation($_POST, $_FILES['screenshot'] ?? null);
        
        echo json_encode(['status' => 'success']);
        exit();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit();
    }
}