<?php

require_once(__DIR__."/../../../config/config.php");
require_once(__DIR__."/../models/Comments.php");

class CommentsController {
    public static function listComments() {
        $db = config::getConnexion();
        $sql = "SELECT * FROM commentaires ORDER BY date_commentaire DESC";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $result = $query->fetchAll();
            
            $comments = [];
            foreach ($result as $row) {
                $comments[] = new Comments(
                    $row['id'],
                    $row['id_news'],
                    $row['auteur'],
                    $row['contenu'],
                    $row['date_commentaire']
                );
            }
            return $comments;
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function listCommentsByNews($newsId) {
        $db = config::getConnexion();
        $sql = "SELECT * FROM commentaires WHERE id_news = :newsId ORDER BY date_commentaire DESC";
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':newsId', $newsId);
            $query->execute();
            $result = $query->fetchAll();
            
            $comments = [];
            foreach ($result as $row) {
                $comments[] = new Comments(
                    $row['id'],
                    $row['id_news'],
                    $row['auteur'],
                    $row['contenu'],
                    $row['date_commentaire']
                );
            }
            return $comments;
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function deleteComment($id)
    {
        $sql = "DELETE FROM commentaires WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    function addComment($comment)
    {
        $sql = "INSERT INTO commentaires (id_news, auteur, contenu, date_commentaire) 
                VALUES (:id_news, :auteur, :contenu, :date_commentaire)";
        $db = config::getConnexion();
        $cleanContenu = self::sanitizeComment($comment->getContenu());
        try {
            $query = $db->prepare($sql);
            echo $cleanContenu;
            $query->execute([
                'id_news' => $comment->getId_News(),
                'auteur' => $comment->getAuteur(),
                'contenu' => $cleanContenu,
                'date_commentaire' => $comment->getDate_Commentaire() ?? date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    function updateComment($comment, $id)
    {
        $sql = "UPDATE commentaires 
                SET auteur = :auteur,
                    contenu = :contenu
                WHERE id = :id";
        
        $db = config::getConnexion();
        
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id,
                'auteur' => $comment->getAuteur(),
                'contenu' => $comment->getContenu()
            ]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    function showComment($id)
    {
        $sql = "SELECT * FROM commentaires WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();

            $row = $query->fetch();
            if ($row) {
                return new Comments(
                    $row['id'],
                    $row['id_news'],
                    $row['auteur'],
                    $row['contenu'],
                    $row['date_commentaire']
                );
            }
            return null;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    private static function sanitizeComment(string $contenu): string {
        $badWords = ['con', 'merde', 'putain', 'salope', 'enculé', 'batard', 'fuck', 'shit'];

        foreach ($badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $contenu = preg_replace_callback($pattern, function ($matches) {
                return str_repeat('*', strlen($matches[0]));
            }, $contenu);
        }

        return $contenu;
    }
    public function filterNews($keyword = '', $categorie = '', $date = '') {
        $db = config::getConnexion();
        $sql = "SELECT * FROM news WHERE 1=1";
    
        if (!empty($keyword)) {
            $sql .= " AND (titre LIKE :kw OR contenu LIKE :kw)";
        }
        if (!empty($categorie)) {
            $sql .= " AND categorie = :cat";
        }
        if (!empty($date)) {
            $sql .= " AND DATE(date_publication) = :dp";
        }
    
        $query = $db->prepare($sql);
    
        if (!empty($keyword)) {
            $query->bindValue(':kw', '%' . $keyword . '%');
        }
        if (!empty($categorie)) {
            $query->bindValue(':cat', $categorie);
        }
        if (!empty($date)) {
            $query->bindValue(':dp', $date);
        }
    
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
    
}


