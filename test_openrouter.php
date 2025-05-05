<?php
/**
 * test_openrouter.php
 * A simple script to test the OpenRouter API connection
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// API configuration
$apiKey = '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';
$apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
$model = 'qwen/qwen3-0.6b-04-28:free';

// Test message
$message = "Bonjour, comment puis-je vous aider aujourd'hui?";

echo "<h1>OpenRouter API Test</h1>";
echo "<p>Testing connection to OpenRouter API with model: $model</p>";

// Fetch some news data to include in the prompt
try {
    echo "<h2>Fetching news data...</h2>";
    $pdo = new PDO("mysql:host=localhost;dbname=news_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, titre, SUBSTRING(contenu, 1, 200) AS contenu_resume, date_publication FROM news ORDER BY date_publication DESC LIMIT 10");
    $newsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<p>Found " . count($newsData) . " news articles</p>";

    // Format news data for the prompt
    $newsContext = "Voici les actualités récentes sur Tunify:\n\n";
    foreach ($newsData as $index => $news) {
        $newsContext .= ($index + 1) . ". Titre: " . $news['titre'] . "\n";
        $newsContext .= "   Date: " . date('d/m/Y', strtotime($news['date_publication'])) . "\n";
        $newsContext .= "   Résumé: " . $news['contenu_resume'] . "...\n\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error fetching news data: " . $e->getMessage() . "</p>";
    $newsContext = "Aucune actualité n'est disponible actuellement.";
}

// Create system message with news context
$systemMessage = "You are a helpful assistant for Tunify news platform. Keep responses concise and in French.\n\n";
$systemMessage .= "Here is the current news data you can reference when answering questions:\n";
$systemMessage .= $newsContext;
$systemMessage .= "\nWhen users ask about news or articles, refer to this information. If they ask about a specific topic, try to find relevant articles from the list.";

echo "<h2>News Context:</h2>";
echo "<pre>" . htmlspecialchars($newsContext) . "</pre>";

// Create the request payload
$payload = [
    'model' => $model,
    'messages' => [
        [
            'role' => 'system',
            'content' => $systemMessage
        ],
        [
            'role' => 'user',
            'content' => $message
        ]
    ],
    // Add additional parameters that might be required
    'temperature' => 0.7,
    'max_tokens' => 800
];

echo "<h2>Request Payload:</h2>";
echo "<pre>" . htmlspecialchars(json_encode($payload, JSON_PRETTY_PRINT)) . "</pre>";

// Initialize cURL session
$ch = curl_init($apiUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

// Get the current domain for the HTTP-Referer header
$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$referer = $protocol . $domain;

echo "<p>Using referer: $referer</p>";

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
    'HTTP-Referer: ' . $referer,
    'X-Title: Tunify News Assistant Test'
]);

// Execute the request
echo "<h2>Sending request...</h2>";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for errors
if (curl_errno($ch)) {
    echo "<h2>cURL Error:</h2>";
    echo "<p style='color: red;'>" . curl_error($ch) . "</p>";
} else {
    echo "<h2>HTTP Status Code: $httpCode</h2>";

    if ($httpCode === 200) {
        echo "<h2>Raw Response:</h2>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";

        $responseData = json_decode($response, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            echo "<h2>Parsed Response:</h2>";
            echo "<pre>" . htmlspecialchars(json_encode($responseData, JSON_PRETTY_PRINT)) . "</pre>";

            if (isset($responseData['choices'][0]['message']['content'])) {
                echo "<h2>AI Response:</h2>";
                echo "<p style='background-color: #f0f0f0; padding: 15px; border-radius: 10px;'>" .
                     nl2br(htmlspecialchars($responseData['choices'][0]['message']['content'])) .
                     "</p>";
            } else {
                echo "<h2>Error:</h2>";
                echo "<p style='color: red;'>Unexpected response format. Could not find AI response.</p>";
            }
        } else {
            echo "<h2>JSON Error:</h2>";
            echo "<p style='color: red;'>" . json_last_error_msg() . "</p>";
        }
    } else {
        echo "<h2>Error:</h2>";
        echo "<p style='color: red;'>API returned non-200 status code: $httpCode</p>";
        echo "<h3>Response:</h3>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

curl_close($ch);
?>
