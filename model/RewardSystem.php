<?php
class RewardSystem {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Get all rewards from recompenses table
    public function getAllRewards() {
        $stmt = $this->db->prepare("SELECT * FROM recompenses");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get user's reward status
    public function getUserRewardStatus($userId) {
        $stmt = $this->db->prepare("
            SELECT points, last_claim_date, current_streak 
            FROM user_rewards 
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'points' => 0,
            'last_claim_date' => null,
            'current_streak' => 0
        ];
    }

    // Claim daily reward
    public function claimDailyReward($userId, $day) {
        // Define coin rewards for each day
        $dailyCoins = [1, 1, 2, 2, 2, 3, 5];
        $coinsEarned = $dailyCoins[$day-1] ?? 0;
        
        // Verify it's the correct day to claim
        $status = $this->getUserRewardStatus($userId);
        $expectedDay = $status['current_streak'] + 1;
        
        if ($day != $expectedDay) {
            return false;
        }

        $newStreak = $status['current_streak'] + 1;
        $today = date('Y-m-d');

        // Start transaction
        $this->db->beginTransaction();

        try {
            // Update user rewards
            $stmt = $this->db->prepare("
                INSERT INTO user_rewards (user_id, points, last_claim_date, current_streak)
                VALUES (:user_id, :points, :date, :streak)
                ON DUPLICATE KEY UPDATE 
                    points = points + VALUES(points),
                    last_claim_date = VALUES(last_claim_date),
                    current_streak = VALUES(current_streak)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':points' => $coinsEarned,
                ':date' => $today,
                ':streak' => $newStreak
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error claiming reward: " . $e->getMessage());
            return false;
        }
    }

    // Reset streak if user missed a day
    public function resetStreak($userId) {
        $stmt = $this->db->prepare("
            UPDATE user_rewards 
            SET current_streak = 0 
            WHERE user_id = :user_id
        ");
        $stmt->bindParam(':user_id', $userId);
        return $stmt->execute();
    }

    // Redeem a reward (subtract points)
    public function redeemReward($userId, $rewardId) {
        // Get reward details
        $stmt = $this->db->prepare("SELECT * FROM recompenses WHERE id_reward = :id");
        $stmt->bindParam(':id', $rewardId);
        $stmt->execute();
        $reward = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$reward || !$reward['disponibilite']) {
            return false;
        }

        // Check user has enough points
        $userStatus = $this->getUserRewardStatus($userId);
        if ($userStatus['points'] < $reward['points_requis']) {
            return false;
        }

        // Start transaction
        $this->db->beginTransaction();

        try {
            // Subtract points
            $stmt = $this->db->prepare("
                UPDATE user_rewards 
                SET points = points - :cost 
                WHERE user_id = :user_id AND points >= :cost
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':cost' => $reward['points_requis']
            ]);

            if ($stmt->rowCount() === 0) {
                throw new Exception("Not enough points");
            }

            // Record redemption (you'll need to create this table)
            $stmt = $this->db->prepare("
                INSERT INTO user_redemptions (user_id, reward_id, redeemed_at)
                VALUES (:user_id, :reward_id, NOW())
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':reward_id' => $rewardId
            ]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error redeeming reward: " . $e->getMessage());
            return false;
        }
    }
}