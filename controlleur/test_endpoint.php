<?php
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Endpoint reached successfully',
    'server_info' => [
        'document_root' => $_SERVER['DOCUMENT_ROOT'],
        'request_uri' => $_SERVER['REQUEST_URI']
    ]
]);
?>