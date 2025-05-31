<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';

/**
 * ChatbotController handles interactions with OpenRouter API for the chatbot functionality
 */
class ChatbotController {
    private $apiKey;
    private $apiUrl = 'https://openrouter.ai/api/v1/chat/completions';
    private $model = 'openai/gpt-3.5-turbo';
    private $newsData = [];
    private $maxTokens = 200;
    private $temperature = 0.7;

    public function __construct() {
        // Load API key from environment variable or config file
        $this->apiKey = getenv('OPENROUTER_API_KEY') ?: $this->getConfigApiKey();
        if (!$this->apiKey) {
            throw new Exception('OpenRouter API key not configured');
        }
        $this->fetchNewsData();
    }

    private function getConfigApiKey() {
        $configFile = __DIR__ . '/../config/api_config.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            return $config['openrouter_api_key'] ?? null;
        }
        return null;
    }

    private function fetchNewsData() {
        try {
            $pdo = config::getConnexion();
            $stmt = $pdo->query("SELECT id, titre, SUBSTRING(contenu, 1, 200) AS contenu_resume, date_publication FROM news ORDER BY date_publication DESC LIMIT 10");
            $this->newsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Erreur de base de données: " . $e->getMessage());
            $this->newsData = [];
        }
    }

    private function formatNewsDataForPrompt() {
        if (empty($this->newsData)) {
            return "Aucune actualité n'est disponible actuellement.";
        }

        $output = "Voici les actualités récentes sur Tunify:\n\n";
        foreach ($this->newsData as $i => $news) {
            $output .= ($i + 1) . ". Titre: " . $news['titre'] . "\n";
            $output .= "   Date: " . date('d/m/Y', strtotime($news['date_publication'])) . "\n";
            $output .= "   Résumé: " . $news['contenu_resume'] . "...\n\n";
        }
        return $output;
    }

    public function processMessage($userMessage) {
        try {
            // Sanitize user input
            $userMessage = filter_var(trim($userMessage), FILTER_SANITIZE_STRING);
            if (empty($userMessage)) {
                throw new Exception('Message cannot be empty');
            }

            $newsSummary = $this->formatNewsDataForPrompt();

            $postData = [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => "Tu es un assistant utile pour répondre aux questions sur Tunify. Voici les dernières actualités pour t'aider à répondre :\n" . $newsSummary],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => $this->temperature,
                'max_tokens' => $this->maxTokens,
            ];

            $ch = curl_init($this->apiUrl);
            if ($ch === false) {
                throw new Exception('Failed to initialize CURL');
            }

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->apiKey,
                    'Content-Type: application/json',
                    'Referer: http://localhost'
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($postData)
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $responseData = json_decode($response, true);

            if (isset($responseData['choices'][0]['message']['content'])) {
                return [
                    'success' => true,
                    'message' => trim($responseData['choices'][0]['message']['content']),
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Désolé, je rencontre des difficultés à traiter votre demande.',
                    'debug_error' => $responseData['error']['message'] ?? 'Erreur inconnue',
                ];
            }
        } catch (Exception $e) {
            error_log("Error processing message: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement de votre message.',
            ];
        }
    }

    public function getFallbackResponse($message) {
        $message = strtolower($message);

        if (str_contains($message, 'actualité') || str_contains($message, 'news') || str_contains($message, 'article')) {
            if (empty($this->newsData)) {
                return ['success' => true, 'message' => "Aucune actualité disponible pour le moment. Revenez plus tard !"];
            }
            $response = "Voici les dernières actualités sur Tunify:\n\n";
            foreach (array_slice($this->newsData, 0, 3) as $news) {
                $response .= "- " . $news['titre'] . " (publié le " . date('d/m/Y', strtotime($news['date_publication'])) . ")\n";
            }
            $response .= "\nUtilisez la barre de recherche pour plus de détails.";
            return ['success' => true, 'message' => $response];
        } elseif (str_contains($message, 'recherche')) {
            return ['success' => true, 'message' => "Utilisez la barre de recherche en haut pour rechercher des articles par mot-clé."];
        } elseif (str_contains($message, 'tunify')) {
            return ['success' => true, 'message' => "Tunify est une plateforme musicale avec des actualités sur les artistes et l'industrie musicale."];
        } else {
            return ['success' => true, 'message' => "Je suis là pour vous aider avec les actualités. Posez votre question !"];
        }
    }
}
?>
