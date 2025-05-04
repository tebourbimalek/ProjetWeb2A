<?php
require_once "/ReclamationController.php";

$controller = new ReclamationController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $id = intval($_POST['id']);

    if ($action === 'update') {
        $status = $_POST['status'];
        $controller->updateReclamationStatus($id, $status);
    } elseif ($action === 'delete') {
        $controller->deleteReclamation($id);
    }

    echo json_encode(['success' => true]);
    exit;
}
?>
