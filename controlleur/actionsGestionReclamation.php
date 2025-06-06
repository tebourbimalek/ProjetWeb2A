<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\GestionReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\ResponseController.php';

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

            if ($gestionController->repondreReclamation($id, $response)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Response submitted successfully'
                ]);
            }
            break;
            
        case 'edit_response':
            $response = $_POST['response'] ?? '';
            if (empty($response)) {
                throw new Exception('Response cannot be empty');
            }

            if ($gestionController->modifierReponse($id, $response)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Response updated successfully'
                ]);
            }
            break;
            
        case 'delete_response':
            if ($gestionController->supprimerReponse($id)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Response deleted successfully'
                ]);
            }
            break;

        case 'reject':
            if ($gestionController->rejeterReclamation($id)) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Reclamation rejected successfully'
                ]);
            } else {
                throw new Exception('Failed to reject reclamation');
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
