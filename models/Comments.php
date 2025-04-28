<?php
class Comments
{
    private ?int $id = null;
    private ?int $id_news = null;
    private ?string $auteur = null;
    private ?string $contenu = null;
    private ?string $date_commentaire = null;

    public function __construct($id, $id_news, $auteur, $contenu, $date_commentaire)
    {
        $this->id = $id;
        $this->id_news = $id_news;
        $this->auteur = $auteur;
        $this->contenu = $contenu;
        $this->date_commentaire = $date_commentaire;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getId_News()
    {
        return $this->id_news;
    }

    public function getAuteur()
    {
        return $this->auteur;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function getDate_Commentaire()
    {
        return $this->date_commentaire;
    }
}
