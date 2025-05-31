<?php

require_once 'C:\xampp\htdocs\projetweb\model\config.php' ;
require_once 'C:\xampp\htdocs\projetweb\model\News.php';

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
        $sql = "SELECT COUNT(*) as count FROM reactions WHERE id_news = :id_news";
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

    public function searchNews($searchTerm)
    {
        try {
            $db = config::getConnexion();

            // Debug: Log database connection
            error_log("Database connection established");

            // Debug: Check if the news table exists and has data
            $checkSql = "SELECT COUNT(*) as count FROM news";
            $checkQuery = $db->prepare($checkSql);
            $checkQuery->execute();
            $count = $checkQuery->fetch(PDO::FETCH_ASSOC)['count'];
            error_log("Total news in database: " . $count);

            // Proceed with search
            $sql = "SELECT * FROM news
                    WHERE titre LIKE :searchTerm
                    OR contenu LIKE :searchTerm
                    ORDER BY date_publication DESC";

            // Debug: Log the search term
            error_log("Searching for: " . $searchTerm);
            error_log("SQL Query: " . $sql);

            $query = $db->prepare($sql);
            $searchPattern = '%' . $searchTerm . '%';
            $query->bindValue(':searchTerm', $searchPattern);
            error_log("Search pattern: " . $searchPattern);

            $query->execute();
            $result = $query->fetchAll();

            // Debug: Log the number of results
            error_log("Number of results: " . count($result));

            // If no results, try a more basic query to check if the issue is with the LIKE operator
            if (count($result) === 0) {
                $basicSql = "SELECT * FROM news LIMIT 1";
                $basicQuery = $db->prepare($basicSql);
                $basicQuery->execute();
                $basicResult = $basicQuery->fetch(PDO::FETCH_ASSOC);
                if ($basicResult) {
                    error_log("Basic query returned a result: " . $basicResult['titre']);
                    // Try a direct match instead of LIKE
                    $directSql = "SELECT * FROM news WHERE titre = :titre LIMIT 1";
                    $directQuery = $db->prepare($directSql);
                    $directQuery->bindValue(':titre', $basicResult['titre']);
                    $directQuery->execute();
                    $directResult = $directQuery->fetch(PDO::FETCH_ASSOC);
                    if ($directResult) {
                        error_log("Direct match query works");
                    } else {
                        error_log("Direct match query failed");
                    }
                } else {
                    error_log("Basic query returned no results");
                }
            }

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
        } catch (Exception $e) {
            // Debug: Log any errors
            error_log("Error in searchNews: " . $e->getMessage());
            return [];
        }
    }
}
?>