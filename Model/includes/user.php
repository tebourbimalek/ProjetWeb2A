<?php

class User {
    private $id;
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

    // Constructeur
    public function __construct(
        int $id,
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
        $this->id = $id;
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
    public function getId(): int {
        return $this->id;
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

    // Afficher les détails de l'utilisateur
    public function getUserDetails(): string {
        return "ID: " . $this->id . "\n" .
               "Nom Utilisateur: " . $this->nom_utilisateur . "\n" .
               "Email: " . $this->email . "\n" .
               "Prénom: " . $this->prenom . "\n" .
               "Nom de famille: " . $this->nom_famille . "\n" .
               "Date de naissance: " . $this->date_naissance . "\n" .
               "Image: " . $this->image_path . "\n" .
               "Type: " . $this->type_utilisateur . "\n" .
               "Score: " . $this->score . "\n" .
               "Date de création: " . $this->date_creation;
    }
    // Dans User.php
public function setId(int $id): void {
    $this->id = $id;
}
}
?>