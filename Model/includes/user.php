<?php
require_once 'C:\xampp\htdocs\projetweb\Model\includes\config.php';
class User {
    private $artiste_id; // Changed from $id to $artiste_id
    private $nom_utilisateur;
    private $email;
    private $mot_de_passe;
    private $prenom;
    private $nom_famille;
    private $date_naissance;
    private $image_path;
    private $type_utilisateur;
    private $score;
    private $date_creation;
    private $reset_token;
    private $reset_expires;

    // Constructeur updated with artiste_id
    public function __construct(
        int $artiste_id, // Parameter renamed to artiste_id
        string $nom_utilisateur,
        string $email,
        string $mot_de_passe,
        string $prenom,
        string $nom_famille,
        string $date_naissance,
        string $image_path = 'default.jpg',
        string $type_utilisateur = 'user',
        int $score = 0,
        string $date_creation = ''
    ) {
        $this->artiste_id = $artiste_id; // Assignment updated
        $this->nom_utilisateur = $nom_utilisateur;
        $this->email = $email;
        $this->mot_de_passe = $mot_de_passe;
        $this->prenom = $prenom;
        $this->nom_famille = $nom_famille;
        $this->date_naissance = $date_naissance;
        $this->image_path = $image_path;
        $this->type_utilisateur = $type_utilisateur;
        $this->score = $score;
        $this->date_creation = $date_creation;
    }

    // Getters
    public function getArtisteId(): int { // Method renamed to getArtisteId()
        return $this->artiste_id;
    }

    public function getNomUtilisateur(): string {
        return $this->nom_utilisateur;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getMotDePasse(): string {
        return $this->mot_de_passe;
    }

    public function getPrenom(): string {
        return $this->prenom;
    }

    public function getNomFamille(): string {
        return $this->nom_famille;
    }

    public function getDateNaissance(): string {
        return $this->date_naissance;
    }

    public function getImagePath(): string {
        return $this->image_path;
    }

    public function getTypeUtilisateur(): string {
        return $this->type_utilisateur;
    }

    public function getScore(): int {
        return $this->score;
    }

    public function getDateCreation(): string {
        return $this->date_creation;
    }

    public function getResetToken(): ?string {
        return $this->reset_token;
    }
    
    public function getResetExpires(): ?DateTime {
        return $this->reset_expires ? new DateTime($this->reset_expires) : null;
    }
    // Afficher les détails de l'utilisateur
    public function getUserDetails(): string {
        return "ID: " . $this->artiste_id . "\n" // Updated to artiste_id
               . "Nom Utilisateur: " . $this->nom_utilisateur . "\n"
               . "Email: " . $this->email . "\n"
               . "Prénom: " . $this->prenom . "\n"
               . "Nom de famille: " . $this->nom_famille . "\n"
               . "Date de naissance: " . $this->date_naissance . "\n"
               . "Image: " . $this->image_path . "\n"
               . "Type: " . $this->type_utilisateur . "\n"
               . "Score: " . $this->score . "\n"
               . "Date de création: " . $this->date_creation;
    }

    // Dans User.php
// Setters
public function setArtisteId(int $artiste_id): void {
    $this->artiste_id = $artiste_id;
}

public function setNomUtilisateur(string $nom_utilisateur): void {
    $this->nom_utilisateur = $nom_utilisateur;
}

public function setEmail(string $email): void {
    // Validation basique d'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException("Email invalide");
    }
    $this->email = $email;
}

public function setMotDePasse(string $mot_de_passe): void {
    // Vous pourriez ajouter un hachage ici
    $this->mot_de_passe = $mot_de_passe;
}

public function setPrenom(?string $prenom): void {
    $this->prenom = $prenom;
}

public function setNomFamille(?string $nom_famille): void {
    $this->nom_famille = $nom_famille;
}

public function setDateNaissance(?string $date_naissance): void {
    // Validation basique de date
    if ($date_naissance && !strtotime($date_naissance)) {
        throw new InvalidArgumentException("Format de date invalide");
    }
    $this->date_naissance = $date_naissance;
}

public function setImagePath(string $image_path): void {
    $this->image_path = $image_path;
}

public function setTypeUtilisateur(string $type_utilisateur): void {
    // Validation du type enum
    $allowedTypes = ['artiste', 'user', 'admin'];
    if (!in_array($type_utilisateur, $allowedTypes)) {
        throw new InvalidArgumentException("Type d'utilisateur invalide");
    }
    $this->type_utilisateur = $type_utilisateur;
}

public function setScore(int $score): void {
    $this->score = $score;
}

public function setDateCreation(string $date_creation): void {
    $this->date_creation = $date_creation;
}

public static function createPasswordReset($pdo, $userId, $token, $expires) {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET reset_token = ?, reset_expires = ? WHERE artiste_id = ?");
    $stmt->execute([$token, $expires, $userId]);
}

public static function findByResetToken($pdo, $token) {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

public static function clearResetToken($pdo, $userId) {
    $stmt = $pdo->prepare("UPDATE utilisateurs SET reset_token = NULL, reset_expires = NULL WHERE artiste_id = ?");
    $stmt->execute([$userId]);
}


public static function updatePassword($pdo, $userId, $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE artiste_id = ?");
    $stmt->execute([$hash, $userId]);
}

public static function findByEmail($pdo, $email) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        // Retourne l'utilisateur sous forme d'objet ou false si non trouvé
        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Database error in findByEmail: " . $e->getMessage());
        return false;
    }
}
}
?>