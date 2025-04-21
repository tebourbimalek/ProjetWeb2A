<?php


// Classe pour représenter une chanson
class Song {
    private $id;
    private $song_title;
    private $album_name;
    private $release_date;
    private $audio_file_path;
    private $cover_file_path;
    private $artist_id;
    private $duree;

    // Constructeur pour initialiser la classe
    public function __construct(int $id, string $song_title, string $album_name, string $release_date, string $audio_file_path, string $cover_file_path, int $artist_id) {
        $this->id = $id;
        $this->song_title = $song_title;
        $this->album_name = $album_name;
        $this->release_date = $release_date;
        $this->audio_file_path = $audio_file_path;
        $this->cover_file_path = $cover_file_path;
        $this->artist_id = $artist_id;
        $this->duree = $duree;
    }

    // Méthode pour obtenir l'ID de la chanson
    public function getId(): int {
        return $this->id;
    }

    // Méthode pour obtenir le titre de la chanson
    public function getSongTitle(): string {
        return $this->song_title;
    }

    // Méthode pour obtenir le nom de l'album
    public function getAlbumName(): string {
        return $this->album_name;
    }

    // Méthode pour obtenir la date de sortie
    public function getReleaseDate(): string {
        return $this->release_date;
    }

    // Méthode pour obtenir le chemin du fichier audio
    public function getAudioFilePath(): string {
        return $this->audio_file_path;
    }

    // Méthode pour obtenir le chemin de l'image de la couverture
    public function getCoverFilePath(): string {
        return $this->cover_file_path;
    }

    // Méthode pour obtenir l'ID de l'artiste
    public function getArtistId(): int {
        return $this->artist_id;
    }
    public function getDuree(): int {
        return $this->duree;
    }

    // Méthode pour afficher les détails de la chanson
    public function getSongDetails(): string {
        return "Song Title: " . $this->song_title . "\n" .
               "Album Name: " . $this->album_name . "\n" .
               "Release Date: " . $this->release_date . "\n" .
               "Audio Path: " . $this->audio_file_path . "\n" .
               "Cover Image Path: " . $this->cover_file_path . "\n" .
               "Artist ID: " . $this->artist_id . "\n" .
               "Duree: " . $this->duree;
    }
}
?>