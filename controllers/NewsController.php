<?php

require_once(__DIR__."/../../../config/config.php");
require_once(__DIR__."/../models/News.php");

class NewsController {
    public function listNews() {
        $db = config::getConnexion();
        $sql = "SELECT * FROM news ORDER BY date_publication DESC";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $result = $query->fetchAll();
            
            $news = [];
            foreach ($result as $row) {
                $news[] = new News(
                    $row['id'],
                    $row['titre'],
                    $row['contenu'],
                    $row['image'],
                    $row['date_publication']
                );
            }
            return $news;
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function deleteNews($id)
    {
        $sql = "DELETE FROM news WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function addNews($news)
    {
        $sql = "INSERT INTO news (titre, contenu, image, date_publication) 
                VALUES (:titre, :contenu, :image, :date_publication)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $news->getTitre(),
                'contenu' => $news->getContenu(),
                'image' => $news->getImage(),
                'date_publication' => $news->getDate_Publication() ?? date('Y-m-d H:i:s')
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateNews($news, $id)
    {
        $sql = "UPDATE news 
                SET titre = :titre,
                    contenu = :contenu,
                    image = :image
                WHERE id = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'titre' => $news->getTitre(),
                'contenu' => $news->getContenu(),
                'image' => $news->getImage()
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function showNews($id)
    {
        $sql = "SELECT * FROM news WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();

            $row = $query->fetch();
            if ($row) {
                return new News(
                    $row['id'],
                    $row['titre'],
                    $row['contenu'],
                    $row['image'],
                    $row['date_publication']
                );
            }
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getReactionCount($newsId)
    {
        $sql = "SELECT * FROM reactions WHERE ip_address = :ip_address";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id_news', $newsId);
            $query->execute();
            $result = $query->fetch();
            return $result['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getNewNotifications($lastCheck)
    {
        $db = config::getConnexion();
        try {
            // Récupérer toutes les publications des dernières 24 heures
            $sql = "SELECT id, titre, date_publication FROM news 
                    WHERE date_publication > :lastCheck 
                    ORDER BY date_publication DESC 
                    LIMIT 5";
            
            $query = $db->prepare($sql);
            $query->bindValue(':lastCheck', $lastCheck);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            
            // Si aucun résultat, récupérer les 3 publications les plus récentes
            if (empty($result)) {
                $sql = "SELECT id, titre, date_publication FROM news 
                        ORDER BY date_publication DESC 
                        LIMIT 3";
                $query = $db->prepare($sql);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Erreur dans getNewNotifications: " . $e->getMessage());
            return [];
        }
    }
}
?> 