<?php
class News
{
    private $id;
    private $titre;
    private $contenu;
    private $image;
    private $date_publication;

    public function __construct($id = null, $titre = null, $contenu = null, $image = null, $date_publication = null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->image = $image;
        $this->date_publication = $date_publication;
    }

    // Getters
    public function getId()
    {
        return $this->id;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getDate_Publication()
    {
        return $this->date_publication;
    }

    // Setters
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function setContenu($contenu)
    {
        $this->contenu = $contenu;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setDate_Publication($date_publication)
    {
        $this->date_publication = $date_publication;
    }
}
?> 