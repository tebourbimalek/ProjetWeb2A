<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\RewardSystem.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';

$db = config::getConnexion();
// Initialize reward system
$userConnected = getUserInfo($db);
$user_id=$userConnected->getArtisteId();

$rewardSystem = new RewardSystem($db);
$unreadCount = countUnreadNotifications($user_id);
$user_role = $userConnected->getTypeUtilisateur();

if (isSubscriptionExpired($db, $user_id)){
    $type= 'expired';
}else{
    $type= 'valid';
}
// Get user ID (from your authentication system)
$userId = $_SESSION['user_id'] ?? null;

// Handle reward claims
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_reward'])) {
    $day = (int)$_POST['day'];
    $rewardSystem->claimDailyReward($user_id, $day);
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

if (isset($_SESSION['comment_message'])) {
    echo '<div class="notification ' . $_SESSION['comment_type'] . '">';
    echo $_SESSION['comment_message'];
    echo '</div>';

    unset($_SESSION['comment_message'], $_SESSION['comment_type']);
}

if (isset($_SESSION['comment_message_delete'])) {
    echo '<div class="notification ' . $_SESSION['comment_type_delete'] . '">';
    echo $_SESSION['comment_message_delete'];
    echo '</div>';

    unset($_SESSION['comment_message_delete'], $_SESSION['comment_type_delete']);
}


$claimedDays = [];
$stmt = $db->prepare("SELECT current_streak FROM user_rewards WHERE user_id = ?");
$stmt->execute([$user_id]);
$claimedDays = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // returns an array of claimed day numbers



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
    .notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            z-index: 9999;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            animation: slideIn 0.4s ease-out, fadeOut 4s 4s forwards;
            font-family: Arial, sans-serif;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .warning {
            background-color: #fff3cd;
            color: #856404;
        }

        @keyframes slideIn {
            from {
                top: -100px;
                opacity: 0;
            }
            to {
                top: 20px;
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                top: -100px;
            }
        }
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
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        color: #fff;
        padding: 10px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #222;
        min-width: 220px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        z-index: 999;
        overflow: hidden;
    }

    .dropdown-menu a {
        color: #fff;
        padding: 16px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .dropdown-menu a:hover {
        background-color: rgba(255,255,255,0.05);
    }

    .external-link {
        float: right;
        opacity: 0.7;
    }

    .show {
        display: block;
    }
    .notification-icon {
        position: relative;
        display: inline-block;
    }

    .notification-count {
        position: absolute;
        top: -10px;
        right: -8px;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-count1 {
        position: absolute;
        top: 14px;
        right: 50%;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-icon {
    position: relative;
}
  </style>
</head>
<body>

  <header>
    <!-- PROFILE SECTION -->
    <div class="profile-section">
      <div class="points">
        <i class="fas fa-coins"></i>
        <span><?= $userPoints = $userConnected->getScore(); ?></span>
      </div>
    <span class="profile-name">
        <?= htmlspecialchars($userConnected->getNomUtilisateur()) ?>
    </span>
    <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-button">
                        <i class="fas fa-user"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="../tunify_avec_connexion/user/overview.php" target="_blank" onclick="reloadPage(); return false;">Account 
                            <i class="fas fa-external-link-alt external-link"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-count1"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>

                        <script>
                            function reloadPage() {
                                // Open overview.php in a new tab
                                window.open('../tunify_avec_connexion/user/overview.php', '_blank');

                                // Reload the current page (avec_connexion.php)
                                location.reload();
                            }
                        </script>

                        <a href="../tunify_avec_connexion/avec_connexion.php" onclick="toggleBox4(<?= $userdata['artiste_id']; ?>, '<?= $userdata['nom_utilisateur']; ?>', '<?= $userdata['image_path']; ?>')" style="border:none;">Profile</a>
                        <?php
                            if ($type == 'expired') {
                                echo '<a href="../tunifypaiement/dashboard.php">Upgrade to Premium <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <?php
                            if ($user_role == 'admin' || $user_role == 'artiste') {
                                echo '<a href="backoffice.php">Dashboard <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <a href="#support">Support <i class="fas fa-external-link-alt external-link"></i></a>
                        <a href="#" onclick="showSettingsSection(); return false;">Settings</a>
                        <a href="../tunify_avec_connexion/logout.php">Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/projetweb/View/tunisfy_sans_conexion/login.php" class="nav-link">Se connecter</a>
                <a href="/projetweb/View/tunisfy_sans_conexion/register.php" class="nav-link">S'inscrire</a>
            <?php endif; ?>
        </div>
  </div>
  <script>
      function toggleDropdown() {
        const dropdown = document.getElementById("dropdownMenu");
        dropdown.classList.toggle("show");
    }

    // Close dropdown when clicking outside of it
    document.addEventListener("click", function(event) {
        const button = document.querySelector(".dropdown-button");
        const menu = document.getElementById("dropdownMenu");

        if (!button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.remove("show");
        }
    });
  </script>
</div>
    
    <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/logoprojet1.jpg" alt="Logo" class="logo">
  </header>
 <div class="weekly-reward-section">
    <h1>Récompenses Quotidiennes</h1>
    <p style="text-align: center; margin-bottom: 20px;">Connectez-vous chaque jour pour gagner des pièces!</p>

    <div class="reward-grid">
      <?php 
      $dailyCoins = [1, 1, 2, 2, 2, 3, 5];
      $currentDayOfWeek = date('N'); // 1 (Mon) - 7 (Sun)
      ?>
      
      <?php for ($day = 1; $day <= 7; $day++): ?>
        <?php
        $coins = $dailyCoins[$day - 1];
        $isClaimed = in_array($day, $claimedDays);
        $isToday = ($day == $currentDayOfWeek);
        ?>
        <div class="reward-card <?= $isToday && !$isClaimed ? 'available' : 'locked' ?>">
          <h4>Jour <?= $day ?></h4>
          <div class="coin-row">
            <span><?= $coins ?></span>
            <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/coin.png" alt="coin" style="width:50px; height:50px;">
          </div>
          
          <?php if ($isClaimed): ?>
            <button disabled style="background-color: gray;">✅ Déjà obtenu</button>
          <?php elseif ($isToday): ?>
            <form method="POST" style="display: inline;" action="reward.php">
              <input type="hidden" name="day" value="<?= $day ?>">
              <button type="submit" name="claim_reward">🎁 Obtenir</button>
            </form>
          <?php else: ?>
            <button disabled style="opacity: 0.6;">🔒 Disponible le jour <?= $day ?></button>
          <?php endif; ?>
        </div>
      <?php endfor; ?>
    </div>
</div>


  <h1>Récompenses Disponibles</h1>

  <div class="reward-grid">
    <?php foreach ($rewards as $reward): ?>
      <div class="reward-card <?= $reward['disponibilite'] ? '' : 'unavailable' ?>">
        <?php if (!empty($reward['image_path'])): ?>
          <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?= htmlspecialchars($reward['image_path']) ?>" 
               alt="<?= htmlspecialchars($reward['nom_reward']) ?>"
               onerror="this.src='assets/img/default-reward.png'">
        <?php else: ?>
          <img src="assets/img/default-reward.png" alt="Default Reward">
        <?php endif; ?>
        <h4><?= htmlspecialchars($reward['nom_reward']) ?></h4>
        <div class="coin-row">
          <span><?= $reward['points_requis'] ?></span>
          <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/coin.png" alt="coin" style="width:50px; height:50px;">
        </div>
        <a href="../tunifypaiement/paiement.php">
          <button <?= (!$reward['disponibilite'] || $userPoints < $reward['points_requis']) ? 'disabled' : '' ?>>
            Échanger
          </button>
        </a>
      </div>
    <?php endforeach; ?>
  </div>

  

</body>
</html>