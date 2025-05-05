<?php
/**
 * chatbot.php
 * API endpoint for the chatbot
 */

header('Content-Type: application/json');

// Enable CORS for development
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get the request body
$requestData = json_decode(file_get_contents('php://input'), true);

// Validate the request
if (!isset($requestData['message']) || empty($requestData['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message is required']);
    exit();
}

// Include the ChatbotController
require_once(__DIR__ . '/../controllers/ChatbotController.php');

// Initialize the controller
$chatbotController = new ChatbotController();

// Process the message
try {
    // Try to use the OpenRouter API
    $response = $chatbotController->processMessage($requestData['message']);

    // Log the response for debugging
    error_log("OpenRouter API Response: " . json_encode($response));
} catch (Exception $e) {
    // If there's an error, fall back to the local response
    error_log("Error calling OpenRouter API: " . $e->getMessage());
    $response = $chatbotController->getFallbackResponse($requestData['message']);
}

// Return the response
echo json_encode($response);
?>
