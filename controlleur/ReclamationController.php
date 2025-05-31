<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reclamation.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\TypeReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\vendor\autoload.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ReclamationController
{
    public function createReclamation($data, $file = null)
    {

        $pdo = Config::getConnexion();
        $userConnected = getUserInfo($pdo);
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

            // Get type_id from cause if it's a numeric value
            $type_id = null;
            if (isset($data['type_id']) && is_numeric($data['type_id'])) {
                $type_id = (int)$data['type_id'];
            }

            $artiste_id= $userConnected->getArtisteId();

            $stmt = $pdo->prepare(
                "INSERT INTO reclamations 
                (full_name, email, cause, description, screenshot, status, created_at, type_id, id_artiste) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW(), ?, ?)"
            );

            $success = $stmt->execute([
                $data['full_name'],
                $data['email'],
                $data['cause'],
                $data['description'],
                $screenshotPath,
                $type_id,
                $artiste_id

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
    
    /**
     * Get reclamations by email address
     * 
     * @param string $email The email address to search for
     * @return array|string Array of reclamations or error message
     */
    public function getReclamationsByEmail($email)
    {
        try {
            // Get database connection
            $pdo = Config::getConnexion();
            
            // Log the search attempt
            error_log("Searching for reclamations with email: " . $email);
            
            // Direct query to match the database structure exactly
            $query = "SELECT * FROM reclamations WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Fetch all matching records
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log the number of results found
            error_log("Found " . count($results) . " reclamations for email: " . $email);
            
            // Return the results
            return $results;
        } catch (PDOException $e) {
            error_log("Database error in getReclamationsByEmail: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete a reclamation by ID
     * 
     * @param int $id The reclamation ID to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteReclamation($id)
    {
        $pdo = Config::getConnexion();
        try {
            // First delete any associated responses
            $stmt = $pdo->prepare("DELETE FROM responses WHERE reclamation_id = ?");
            $stmt->execute([$id]);
            
            // Then delete the reclamation
            $stmt = $pdo->prepare("DELETE FROM reclamations WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error deleting reclamation: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a reclamation by ID
     * 
     * @param int $id The reclamation ID to update
     * @param array $data The updated data (cause, description)
     * @return array Status and message
     */
    public function updateReclamation($id, $data)
    {
        $pdo = Config::getConnexion();
        try {
            // Validate the data
            if (empty($data['cause']) || empty($data['description'])) {
                return [
                    'status' => 'error',
                    'message' => 'Cause and description are required'
                ];
            }
            
            if (strlen($data['description']) < 30) {
                return [
                    'status' => 'error',
                    'message' => 'Description must be at least 30 characters long'
                ];
            }
            
            // Check if we need to remove the screenshot
            $updateQuery = "UPDATE reclamations SET cause = ?, description = ?";
            $params = [$data['cause'], $data['description']];
            
            if (isset($data['removeScreenshot']) && $data['removeScreenshot']) {
                $updateQuery .= ", screenshot = NULL";
            }
            
            $updateQuery .= " WHERE id = ? AND status = 'pending'";
            $params[] = $id;
            
            // Only allow updating pending reclamations
            $stmt = $pdo->prepare($updateQuery);
            $stmt->execute($params);
            
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 'success',
                    'message' => 'Reclamation updated successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No changes made or reclamation cannot be updated'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error updating reclamation: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Update a reclamation with a new screenshot
     * 
     * @param int $id The reclamation ID to update
     * @param array $data The updated data (cause, description)
     * @param array $file The uploaded file
     * @return array Status and message
     */
    public function updateReclamationWithScreenshot($id, $data, $file)
    {
        $pdo = Config::getConnexion();
        try {
            // Validate the data
            if (empty($data['cause']) || empty($data['description'])) {
                return [
                    'status' => 'error',
                    'message' => 'Cause and description are required'
                ];
            }
            
            if (strlen($data['description']) < 30) {
                return [
                    'status' => 'error',
                    'message' => 'Description must be at least 30 characters long'
                ];
            }
            
            // Process the screenshot
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
            
            // Update the reclamation
            $stmt = $pdo->prepare(
                "UPDATE reclamations 
                SET cause = ?, description = ?, screenshot = ? 
                WHERE id = ? AND status = 'pending'"
            );
            
            $stmt->execute([
                $data['cause'],
                $data['description'],
                $screenshotPath,
                $id
            ]);
            
            if ($stmt->rowCount() > 0) {
                return [
                    'status' => 'success',
                    'message' => 'Reclamation updated successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'No changes made or reclamation cannot be updated'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error updating reclamation with screenshot: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send confirmation email after reclamation submission
     * 
     * @param string $email Recipient email
     * @param string $name Recipient name
     * @return bool True if email sent successfully
     */
    private function sendConfirmationEmail($email, $nom)
    {
        $mail = new PHPMailer(true);
    
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; 
            $mail->SMTPAuth = true;
            $mail->Username = 'tbenalaya81@gmail.com'; 
            $mail->Password = 'kyqt qnrg axfm joes';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            $mail->setFrom('tbenalaya81@gmail.com', 'Support Client'); 
            $mail->addAddress($email, $nom); 
    
            $mail->isHTML(true);
            $mail->Subject = 'Confirmation de reception de votre reclamation';
            $mail->Body = "
                <p>Hello <strong>$nom</strong>,</p>
                <p>We have received your complaint. Our team will contact you shortly for follow-up.</p>
                <p>Thank you for your trust.</p>
            ";
    
            if ($mail->send()) {
                return true; 
            } else {
                return false; 
            }
    
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}");
            return false; 
        }
    }

}