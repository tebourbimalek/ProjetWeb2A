<?php

require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reaction.php';

class ReactionController {
    public static function listReactions() {
        $db = config::getConnexion();
        $sql = "SELECT * FROM reactions ORDER BY date_reaction DESC";
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $result = $query->fetchAll();
            
            $reactions = [];
            foreach ($result as $row) {
                $reactions[] = new Reaction(
                    $row['id'],
                    $row['id_news'],
                    $row['ip_address'],
                    $row['date_reaction']
                );
            }
            return $reactions;
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function listReactionsByNews($newsId) {
        $db = config::getConnexion();
        $sql = "SELECT * FROM reactions WHERE id_news = :newsId ORDER BY date_reaction DESC";
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':newsId', $newsId);
            $query->execute();
            $result = $query->fetchAll();
            
            $reactions = [];
            foreach ($result as $row) {
                $reactions[] = new Reaction(
                    $row['id'],
                    $row['id_news'],
                    $row['ip_address'],
                    $row['date_reaction']
                );
            }
            return $reactions;
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public static function getReactionCount($newsId) {
        $db = config::getConnexion();
        $sql = "SELECT COUNT(*) as count FROM reactions WHERE id_news = :newsId";
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':newsId', $newsId);
            $query->execute();
            $result = $query->fetch();
            return $result['count'];
        }
        catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    public function deleteReaction($id)
    {
        $sql = "DELETE FROM reactions WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    function addReaction($reaction)
    {
        $sql = "INSERT INTO reactions (id_news, ip_address, date_reaction) 
                VALUES (:id_news, :ip_address, :date_reaction)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_news' => $reaction->getId_News(),
                'ip_address' => $reaction->getIp_Address(),
                'date_reaction' => $reaction->getDate_Reaction() ?? date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    function hasUserReacted($newsId, $ipAddress)
    {
        $sql = "SELECT COUNT(*) as count FROM reactions 
                WHERE id_news = :newsId AND ip_address = :ipAddress";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'newsId' => $newsId,
                'ipAddress' => $ipAddress
            ]);
            $result = $query->fetch();
            return $result['count'] > 0;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    function showReaction($id)
    {
        $sql = "SELECT * FROM reactions WHERE id = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':id', $id);
            $query->execute();

            $row = $query->fetch();
            if ($row) {
                return new Reaction(
                    $row['id'],
                    $row['id_news'],
                    $row['ip_address'],
                    $row['date_reaction']
                );
            }
            return null;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
} 