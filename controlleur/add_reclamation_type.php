<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\TypeReclamationController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type']) && !empty($_POST['type'])) {
        $typeController = new TypeReclamationController();
        $newType = trim($_POST['type']);
        
        try {
            $result = $typeController->addType($newType);
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Type added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to add type']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Type name is required']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
