<?php
require_once "C:/xampp/htdocs/Tunify/config.php";
require_once "C:/xampp/htdocs/Tunify/controller/GestionReclamationController.php";
require_once "C:/xampp/htdocs/Tunify/controller/ResponseController.php";

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $gestionController = new GestionReclamationController();

    $action = $_POST['action'] ?? null;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if (!$action || !$id) {
        throw new Exception('Missing parameters');
    }

    switch ($action) {
        case 'delete':
            if ($gestionController->supprimerReclamation($id)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Reclamation deleted successfully'
                ]);
            }
            break;

        case 'respond':
            $response = $_POST['response'] ?? '';
            if (empty($response)) {
                throw new Exception('Response cannot be empty');
            }

            if ($gestionController->repondreReclamation($id, $response)) { // Fixed variable name
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Response submitted successfully'
                ]);
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>