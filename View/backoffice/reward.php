<?php 
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

// Returns points for the given day number
function getRewardPointsForDay(int $day): int {
    $rewards = [
        1 => 10,
        2 => 15,
        3 => 20,
        4 => 25,
        5 => 30,
        6 => 40,
        7 => 50
    ];
    return $rewards[$day] ?? 5;
}

// Check if the user already claimed today
function hasClaimedToday(PDO $pdo, int $user_id): bool {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_rewards WHERE user_id = ? AND last_claim_date = ?");
    $stmt->execute([$user_id, $today]);
    return $stmt->fetchColumn() > 0;
}

// Claim the reward: insert a new row
function claimDailyReward(PDO $pdo, int $user_id, int $day): string {
    $today = date('Y-m-d');

    if (hasClaimedToday($pdo, $user_id)) {
        return "already_claimed";
    }

    // When day 1 is claimed, reset the rewards for this user only
    if ($day === 1) {
        $deleteStmt = $pdo->prepare("DELETE FROM user_rewards WHERE user_id = ?");
        $deleteStmt->execute([$user_id]);
    }

    $rewardPoints = getRewardPointsForDay($day);

    // Insert the reward for today
    $insertStmt = $pdo->prepare("
        INSERT INTO user_rewards (user_id, current_streak, last_claim_date, points)
        VALUES (?, ?, ?, ?)
    ");

    $insertSuccess = $insertStmt->execute([$user_id, $day, $today, $rewardPoints]);

    if (!$insertSuccess) {
        return "error";
    }

    // Update user's score
    $updateStmt = $pdo->prepare("
        UPDATE utilisateurs
        SET score = score + ?
        WHERE artiste_id = ?
    ");

    $updateSuccess = $updateStmt->execute([$rewardPoints, $user_id]);

    if (!$updateSuccess) {
        return "error";
    }

    return "success";
}




// MAIN
$pdo = config::getConnexion();
$day = $_POST['day'] ?? 1;

$user = getUserInfo($pdo);
if ($user && method_exists($user, 'getArtisteId')) {
    $user_id = $user->getArtisteId();
    $result = claimDailyReward($pdo, $user_id, (int)$day);

    if ($result === "success") {
        $_SESSION['comment_message'] = "✅ Reward claimed.";
        $_SESSION['comment_type'] = "success";
    } elseif ($result === "already_claimed") {
        $_SESSION['comment_message'] = "❌ You have already claimed your reward today.";
        $_SESSION['comment_type'] = "error";
    } else {
        $_SESSION['comment_message'] = "❌ Erreur lors du claiming de votre reward.";
        $_SESSION['comment_type'] = "error";
    }

    header('Location: store.php');
    exit;
} else {
    echo "User not authenticated.";
}
?>
