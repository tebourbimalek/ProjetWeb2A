<?php
session_start();
require_once 'config/database.php';
require_once 'models/RewardSystem.php';
$db = Database::connect();
// Initialize reward system
$rewardSystem = new RewardSystem($db);

// Get user ID (from your authentication system)
$userId = $_SESSION['user_id'] ?? null;

// Handle reward claims
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_reward'])) {
    $day = (int)$_POST['day'];
    $rewardSystem->claimDailyReward($userId, $day);
    header("Location: store.php"); // Prevent form resubmission
    exit;
}

// Get user's reward status
$userStatus = $userId ? $rewardSystem->getUserRewardStatus($userId) : null;
$lastClaimDate = $userStatus['last_claim_date'] ?? null;
$currentStreak = $userStatus['current_streak'] ?? 0;
$userPoints = $userStatus['points'] ?? 0;

// Check if streak should be reset (missed a day)
if ($userId && $lastClaimDate) {
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $lastClaim = date('Y-m-d', strtotime($lastClaimDate));
    
    if ($lastClaim < $yesterday) {
        $rewardSystem->resetStreak($userId);
        $currentStreak = 0;
    }
}

// Get all rewards from recompenses table
$rewards = $rewardSystem->getAllRewards();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Récompenses</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Your existing CSS styles */
    body {  
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #121212;
      color: white;
    }

    header {
      background-color: #1f1f1f;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 24px;
      border-bottom: 2px solid #333;
      flex-direction: row-reverse;
    }

    .logo {
      height: 50px;
    }

    .profile-section {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .profile-pic {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #9c27b0;
    }

    .profile-name {
      font-weight: bold;
      margin-right: 8px;
    }

    .points {
      background-color: #9c27b0;
      padding: 5px 10px;
      border-radius: 20px;
      font-weight: bold;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    h1 {
      text-align: center;
      margin: 30px 0 10px;
      font-size: 2rem;
    }

    .reward-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 24px;
      padding: 30px;
      max-width: 1200px;
      margin: auto;
    }

    .reward-card {
      background-color: #1e1e1e;
      border: 2px solid #444;
      border-radius: 12px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.6);
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .reward-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 18px rgba(255, 255, 255, 0.1);
    }

    .reward-card.unavailable {
      opacity: 0.6;
      filter: grayscale(70%);
    }

    .reward-card.locked {
      background-color: #333;
      border: 2px dashed #777;
    }

    .coin-row {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-weight: bold;
      font-size: 16px;
      margin: 8px 0;
    }

    .coin-row img {
      width: 30px;
      height: 30px;
      object-fit: contain;
    }

    .reward-card button {
      margin-top: 8px;
      padding: 10px 18px;
      background-color: #9c27b0;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      transition: background-color 0.2s ease;
    }

    .reward-card button:hover:not(:disabled) {
      background-color: #ba68c8;
    }

    .reward-card button:disabled {
      background-color: #555;
      cursor: not-allowed;
    }

    .reward-card img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: 6px;
      margin-bottom: 10px;
    }

    .weekly-reward-section {
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <header>
    <!-- PROFILE SECTION -->
    <div class="profile-section">
      <div class="points">
        <i class="fas fa-coins"></i>
        <span><?= $userPoints ?></span>
      </div>
      <span class="profile-name"><?= $_SESSION['username'] ?? 'Guest' ?></span>
      <img src="https://i.pravatar.cc/150?img=3" alt="Profile Picture" class="profile-pic">
    </div>
    
    <img src="/tunifiy(gamification)/sources/uploads/logoprojet1.jpg" alt="Logo" class="logo">
  </header>

  <h1>Récompenses Disponibles</h1>

  <div class="reward-grid">
    <?php foreach ($rewards as $reward): ?>
      <div class="reward-card <?= $reward['disponibilite'] ? '' : 'unavailable' ?>">
        <?php if (!empty($reward['image_path'])): ?>
          <img src="/tunifiy(gamification)/sources/uploads/<?= htmlspecialchars($reward['image_path']) ?>" 
               alt="<?= htmlspecialchars($reward['nom_reward']) ?>"
               onerror="this.src='assets/img/default-reward.png'">
        <?php else: ?>
          <img src="assets/img/default-reward.png" alt="Default Reward">
        <?php endif; ?>
        <h4><?= htmlspecialchars($reward['nom_reward']) ?></h4>
        <div class="coin-row">
          <span><?= $reward['points_requis'] ?></span>
          <img src="/tunifiy(gamification)/sources/uploads/coin.png" alt="coin" style="width:50px; height:50px;">
        </div>
        <button <?= (!$reward['disponibilite'] || $userPoints < $reward['points_requis']) ? 'disabled' : '' ?>>
          Échanger
        </button>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="weekly-reward-section">
    <h1>Récompenses Quotidiennes</h1>
    <p style="text-align: center; margin-bottom: 20px;">Connectez-vous chaque jour pour gagner des pièces!</p>

    <div class="reward-grid">
      <?php 
      // Define coin rewards for each day
      $dailyCoins = [1, 1, 2, 2, 2, 3, 5];
      $currentDayOfWeek = date('N'); // 1 (Monday) through 7 (Sunday)
      ?>
      
      <?php for ($day = 1; $day <= 7; $day++): ?>
        <?php
        $isAvailable = $userId && ($day <= ($currentStreak + 1)) && ($day <= $currentDayOfWeek);
        $isClaimed = $userId && ($day <= $currentStreak);
        $coins = $dailyCoins[$day-1];
        ?>
        <div class="reward-card <?= $isAvailable ? 'available' : 'locked' ?>">
          <h4>Jour <?= $day ?></h4>
          <div class="coin-row">
            <span><?= $coins ?></span>
            <img src="/tunifiy(gamification)/sources/uploads/coin.png" alt="coin" style="width:50px; height:50px;">
          </div>
          <?php if ($isAvailable && !$isClaimed): ?>
            <form method="POST" style="display: inline;">
              <input type="hidden" name="day" value="<?= $day ?>">
              <button type="submit" name="claim_reward">Obtenir</button>
            </form>
          <?php elseif ($isClaimed): ?>
            <button disabled>Déjà obtenu</button>
          <?php else: ?>
            <button disabled><?= ($day > $currentDayOfWeek) ? 'Disponible demain' : 'Connectez-vous' ?></button>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
    </div>
  </div>

</body>
</html>