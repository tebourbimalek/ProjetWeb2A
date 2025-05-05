<?php
/**
 * ChatbotController.php
 * Controller for handling chatbot interactions with OpenRouter API
 */

class ChatbotController {
    private $apiKey;
    private $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
    private $model = 'qwen/qwen3-0.6b-04-28:free';
    private $newsData = [];

    public function __construct() {
        // In a production environment, store this in a secure configuration file
        // or environment variable, not directly in the code
        $this->apiKey = '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';

        // Fetch news data when the controller is initialized
        $this->fetchNewsData();
    }

    /**
     * Fetch news data from the database
     */
    private function fetchNewsData() {
        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=localhost;dbname=news_db", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch a limited number of news articles (most recent ones)
            $stmt = $pdo->query("SELECT id, titre, SUBSTRING(contenu, 1, 200) AS contenu_resume, date_publication FROM news ORDER BY date_publication DESC LIMIT 10");
            $this->newsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("Fetched " . count($this->newsData) . " news articles for chatbot context");
        } catch (Exception $e) {
            error_log("Error fetching news data for chatbot: " . $e->getMessage());
            $this->newsData = [];
        }
    }

    /**
     * Format news data as a string for the prompt
     */
    private function formatNewsDataForPrompt() {
        if (empty($this->newsData)) {
            return "Aucune actualité n'est disponible actuellement.";
        }

        $formattedNews = "Voici les actualités récentes sur Tunify:\n\n";

        foreach ($this->newsData as $index => $news) {
            $formattedNews .= ($index + 1) . ". Titre: " . $news['titre'] . "\n";
            $formattedNews .= "   Date: " . date('d/m/Y', strtotime($news['date_publication'])) . "\n";
            $formattedNews .= "   Résumé: " . $news['contenu_resume'] . "...\n\n";
        }

        return $formattedNews;
    }

    /**
     * Process a user message and get a response from the AI
     *
     * @param string $message The user's message
     * @return array Response data
     */
    public function processMessage($message) {
        try {
            // Get formatted news data for the prompt
            $newsContext = $this->formatNewsDataForPrompt();

            // Create a system message that includes the news data
            $systemMessage = "You are a helpful assistant for Tunify news platform. You help users find news articles, understand content, and navigate the platform. Keep responses concise, friendly, and in French.\n\n";
            $systemMessage .= "Here is the current news data you can reference when answering questions:\n";
            $systemMessage .= $newsContext;
            $systemMessage .= "\nWhen users ask about news or articles, refer to this information. If they ask about a specific topic, try to find relevant articles from the list. If they ask for details about an article that isn't in the list, politely explain that you only have information about the listed articles.";

            // Create the request payload
            $payload = [
                'model' => $this->model,
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
                'max_tokens' => 800 // Increased to allow for longer responses
            ];

            // Initialize cURL session
            $ch = curl_init($this->apiUrl);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Set a 30-second timeout
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Set a 10-second connection timeout

            // Enable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            // Get the current domain for the HTTP-Referer header
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            $referer = $protocol . $domain;

            error_log("Using referer: " . $referer);

            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'HTTP-Referer: ' . $referer,
                'X-Title: Tunify News Assistant'
            ]);

            // Log the request for debugging
            error_log("OpenRouter API Request: " . json_encode($payload));

            // Execute the request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Log the raw response for debugging
            error_log("OpenRouter API Raw Response: " . $response);
            error_log("OpenRouter API HTTP Code: " . $httpCode);

            // Check for errors
            if (curl_errno($ch)) {
                $curlError = curl_error($ch);
                error_log("cURL Error: " . $curlError);
                throw new Exception($curlError);
            }

            curl_close($ch);

            // Process the response
            if ($httpCode !== 200) {
                error_log("API Error: Non-200 status code: " . $httpCode);
                throw new Exception("API returned status code: $httpCode");
            }

            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON Error: " . json_last_error_msg());
                throw new Exception("Invalid JSON response: " . json_last_error_msg());
            }

            // Log the parsed response
            error_log("Parsed Response: " . json_encode($responseData));

            // Extract the AI's response
            if (isset($responseData['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'message' => $responseData['choices'][0]['message']['content']
                ];
            } else {
                throw new Exception("Unexpected response format");
            }

        } catch (Exception $e) {
            // Log the error with detailed information
            error_log("ChatbotController Error: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());

            // For debugging purposes, include the error message in the response
            // In production, you would remove the error details from the response
            return [
                'success' => false,
                'message' => "Désolé, je rencontre des difficultés à traiter votre demande. Veuillez réessayer plus tard.",
                'debug_error' => $e->getMessage() // This helps with debugging but should be removed in production
            ];
        }
    }

    /**
     * Fallback method for when the API is not available or for testing
     *
     * @param string $message The user's message
     * @return array Response data
     */
    public function getFallbackResponse($message) {
        $message = strtolower($message);
        $newsContext = $this->formatNewsDataForPrompt();

        if (strpos($message, 'actualité') !== false || strpos($message, 'news') !== false || strpos($message, 'article') !== false) {
            // If asking about news/articles, provide information about available articles
            if (empty($this->newsData)) {
                return [
                    'success' => true,
                    'message' => "Actuellement, aucune actualité n'est disponible. Veuillez revenir plus tard pour consulter nos dernières publications."
                ];
            } else {
                $response = "Voici les dernières actualités disponibles sur Tunify:\n\n";
                foreach ($this->newsData as $index => $news) {
                    if ($index < 3) { // Limit to first 3 for a concise response
                        $response .= "- " . $news['titre'] . " (publié le " . date('d/m/Y', strtotime($news['date_publication'])) . ")\n";
                    }
                }
                $response .= "\nVous pouvez consulter ces articles sur cette page. Utilisez la barre de recherche en haut pour trouver des sujets spécifiques.";

                return [
                    'success' => true,
                    'message' => $response
                ];
            }
        } elseif (strpos($message, 'recherche') !== false) {
            return [
                'success' => true,
                'message' => "Pour rechercher des articles, utilisez la barre de recherche en haut de la page. Entrez des mots-clés et appuyez sur Entrée."
            ];
        } elseif (strpos($message, 'tunify') !== false) {
            return [
                'success' => true,
                'message' => "Tunify est une plateforme musicale qui propose également des actualités sur l'industrie musicale et les artistes."
            ];
        } else {
            // Check if the message might be asking about a specific topic
            $topicMatches = [];
            $highestMatchCount = 0;
            $bestMatchArticle = null;

            // Simple keyword matching (could be improved with more sophisticated NLP)
            foreach ($this->newsData as $article) {
                $title = strtolower($article['titre']);
                $content = strtolower($article['contenu_resume']);

                // Count how many words from the message appear in the article
                $words = explode(' ', $message);
                $matchCount = 0;

                foreach ($words as $word) {
                    if (strlen($word) > 3 && (strpos($title, $word) !== false || strpos($content, $word) !== false)) {
                        $matchCount++;
                    }
                }

                if ($matchCount > $highestMatchCount) {
                    $highestMatchCount = $matchCount;
                    $bestMatchArticle = $article;
                }
            }

            // If we found a decent match, mention it
            if ($highestMatchCount >= 2 && $bestMatchArticle) {
                return [
                    'success' => true,
                    'message' => "Vous pourriez être intéressé par cet article: \"" . $bestMatchArticle['titre'] .
                                 "\". Il a été publié le " . date('d/m/Y', strtotime($bestMatchArticle['date_publication'])) .
                                 ". Vous pouvez le consulter sur cette page."
                ];
            }

            return [
                'success' => true,
                'message' => "Je suis là pour vous aider avec les actualités Tunify. N'hésitez pas à me poser des questions sur les articles ou à me demander des recommandations."
            ];
        }
    }
}
?>
