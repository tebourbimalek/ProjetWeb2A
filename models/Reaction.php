<?php
class Reaction
{
    private ?int $id = null;
    private ?int $id_news = null;
    private ?string $ip_address = null;
    private ?string $date_reaction = null;

    public function __construct($id, $id_news, $ip_address, $date_reaction)
    {
        $this->id = $id;
        $this->id_news = $id_news;
        $this->ip_address = $ip_address;
        $this->date_reaction = $date_reaction;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getId_News()
    {
        return $this->id_news;
    }

    public function getIp_Address()
    {
        return $this->ip_address;
    }

    public function getDate_Reaction()
    {
        return $this->date_reaction;
    }
} 