<?php
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';

require_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';
session_start();

$pdo = config::getConnexion();
  $userConnected = getUserInfo($pdo);
  $user_id=$userConnected->getArtisteId();
  $unreadCount = countUnreadNotifications($user_id);
  $user_role = $userConnected->getTypeUtilisateur();

if (isSubscriptionExpired($pdo, $user_id)){
    $type= 'expired';
}else{
    $type= 'valid';
}
// Get user points from database if logged in
$userPoints = $userConnected->getScore();
if (isset($_SESSION['user_id'])) {
  
  $stmt = $pdo->prepare("SELECT points FROM user_rewards WHERE user_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
  $result = $stmt->fetch();
  $userPoints = $result ? $result['points'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tunify - Music Quiz Platform</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #121212;
      color: white;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    header {
      background-color: #1f1f1f;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 32px;
      border-bottom: 4px solid #333;
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

    .container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      width: 100%;
      max-width: 1000px;
      padding: 30px;
      margin: auto;
    }

    .card {
      background-color: #1e1e2e;
      border-radius: 12px;
      padding: 2rem;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
      transition: transform 0.2s ease;
      cursor: pointer;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 15px rgba(156, 39, 176, 0.3);
    }

    .card i {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: #ffffffcc;
    }

    .card h2 {
      font-size: 1.3rem;
      margin-bottom: 0.5rem;
    }

    .card p {
      color: #cccccc;
      font-size: 0.95rem;
      margin-bottom: 0;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.8);
    }

    .modal-content {
      background-color: #1e1e2e;
      margin: 5% auto;
      padding: 25px;
      border-radius: 12px;
      width: 80%;
      max-width: 800px;
      max-height: 80vh;
      overflow-y: auto;
      border: 2px solid #9c27b0;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: white;
    }

    .update-item {
      background-color: #2d2d3a;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      text-align: left;
      border-left: 4px solid #9c27b0;
    }

    .update-item h3 {
      margin-top: 0;
      color: #9c27b0;
    }

    .update-date {
      font-size: 0.8rem;
      color: #888;
      margin-bottom: 10px;
    }

    .export-btn {
      background-color: #9c27b0;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
      margin-top: 20px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .export-btn:hover {
      background-color: #ba68c8;
    }

    .loading {
      text-align: center;
      padding: 20px;
      color: #9c27b0;
    }

    .update-icon {
      float: right;
      font-size: 1.5rem;
      margin-left: 10px;
    }
    .update-item {
    background-color: #2a2a3a;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 8px;
    position: relative;
    border-left: 4px solid #9c27b0;
}

.update-item.error {
    border-left-color: #f44336;
}

.update-icon {
    position: absolute;
    right: 15px;
    top: 15px;
    font-size: 1.5rem;
}

.update-meta {
    display: flex;
    gap: 10px;
    margin: 8px 0;
    flex-wrap: wrap;
    align-items: center;
}

.update-name {
    font-weight: bold;
    color: #ffffff;
}

.update-type {
    background-color: #3a3a4a;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
}

.update-points {
    color: #ffc107;
    font-weight: bold;
}

.update-image {
    max-width: 100%;
    max-height: 100px;
    border-radius: 4px;
    margin: 8px 0;
    display: block;
}

.update-date {
    font-size: 0.8rem;
    color: #888;
    margin-top: 8px;
}

.retry-btn {
    background-color: #9c27b0;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    margin-top: 10px;
    cursor: pointer;
}
.loading {
    text-align: center;
    padding: 10px;
    color: #9c27b0;
    font-size: 0.9rem;
}

.card .loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
}

.card {
    position: relative;
    transition: all 0.3s ease;
}

.card.disabled {
    opacity: 0.7;
    pointer-events: none;
}
.error-message {
    color: #ff5252;
    padding: 10px;
    text-align: center;
}

.error-message i {
    margin-right: 8px;
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
  <div class="profile-section">
    <a href="../../backoffice/store.php" style="text-decoration:none; color:white;">
      <div class="points">
        <i class="fas fa-coins"></i>
        <span><?= htmlspecialchars($userPoints, ENT_QUOTES, 'UTF-8') ?></span>
      </div>
    </a>
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
                        <a href="../user/overview.php" target="_blank" onclick="reloadPage(); return false;">Account 
                            <i class="fas fa-external-link-alt external-link"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-count1"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>

                        <script>
                            function reloadPage() {
                                // Open overview.php in a new tab
                                window.open('../user/overview.php', '_blank');

                                // Reload the current page (avec_connexion.php)
                                location.reload();
                            }
                        </script>

                        <a href="../avec_connexion.php" onclick="toggleBox4(<?= $userdata['artiste_id']; ?>, '<?= $userdata['nom_utilisateur']; ?>', '<?= $userdata['image_path']; ?>')" style="border:none;">Profile</a>
                        <?php
                            if ($type == 'expired') {
                                echo '<a href="../../tunifypaiement/dashboard.php">Upgrade to Premium <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <?php
                            if ($user_role == 'admin' || $user_role == 'artiste') {
                                echo '<a href="../../backoffice/backoffice.php">Dashboard <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <a href="#support">Support <i class="fas fa-external-link-alt external-link"></i></a>
                        <a href="#" onclick="showSettingsSection(); return false;">Settings</a>
                        <a href="../logout.php">Log out</a>
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

  <!-- Music icon with link to avec_connexion.php -->

  <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>
<style>
  .music-icon {
  font-size: 24px;
  color: #000;
  margin-right: 20px;
  text-decoration: none;
  transition: transform 0.2s;
  position: absolute;
  top: 20px;
  left: 1230px; /* Adjust position as needed */
}

.music-icon:hover {
  transform: scale(1.2);
  color:rgb(255, 0, 187);
}

</style>

<h1>SongQuiz Menu</h1>

<div class="container">
  <div class="card" onclick="redirectToRandomGame()">
    <i class="fas fa-play-circle"></i>
    <h2>Quickplay</h2>
    <p>Jump into a quick game and test your song knowledge!</p>
  </div>

  <div class="card" onclick="window.location.href='heardle.php'">
    <i class="fas fa-music"></i>
    <h2>Heardle</h2>
    <p>Guess the song by listening to the first few seconds!</p>
  </div>

  <div class="card" onclick="window.location.href='arcade.php'">
    <i class="fas fa-gamepad"></i>
    <h2>Arcade</h2>
    <p>Hands-free music quizzes with no need to interact!</p>
  </div>

  <div class="card" onclick="openUpdatesModal()">
    <i class="fas fa-newspaper"></i>
    <h2>Updates</h2>
    <p>See all recent additions and changes to the platform</p>
  </div>

  <div class="card" onclick="window.location.href='/projetweb/View/backoffice/store.php'">
    <i class="fas fa-shopping-cart"></i>
    <h2>Store</h2>
    <p>Redeem your hard-earned coins for exclusive rewards!</p>
  </div>
</div>

<!-- Updates Modal -->
<div id="updatesModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeUpdatesModal()">&times;</span>
    <h2 style="text-align: center; margin-bottom: 20px;">Recent Platform Updates</h2>
    
    <div id="updatesContainer">
      <div class="loading">
        <i class="fas fa-spinner fa-spin"></i> Loading updates...
      </div>
    </div>
    
    <div style="text-align: center;">
      <button class="export-btn" onclick="exportToPDF()">
        <i class="fas fa-file-pdf"></i> Export to PDF
      </button>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
  // Initialize jsPDF
  const { jsPDF } = window.jspdf;

  // Function to open updates modal
  function openUpdatesModal() {
    document.getElementById('updatesModal').style.display = 'block';
    loadUpdates();
  }

  // Function to close updates modal
  function closeUpdatesModal() {
    document.getElementById('updatesModal').style.display = 'none';
  }

  // Function to load updates from database
  async function loadUpdates() {
    const updatesContainer = document.getElementById('updatesContainer');
    updatesContainer.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Loading updates...</div>';

    try {
        const response = await fetch('../../../controlleur/get_updates.php');
        
        if (!response.ok) {
            const err = await response.json();
            throw new Error(err.message || `HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        if (!result.success || !Array.isArray(result.data)) {
            throw new Error('Invalid data format received from server');
        }

        updatesContainer.innerHTML = '';
        
        if (result.data.length === 0) {
            updatesContainer.innerHTML = '<div class="update-item"><p>No recent updates available</p></div>';
            return;
        }

        result.data.forEach(update => {
            const iconMap = {
                'game': 'fa-gamepad',
                'question': 'fa-question-circle',
                'reward': 'fa-gift'
            };
            
            const typeMap = {
                'game': 'Game',
                'question': 'Question',
                'reward': 'Reward'
            };

            const updateItem = document.createElement('div');
            updateItem.className = 'update-item';
            updateItem.innerHTML = `
                <i class="fas ${iconMap[update.type]} update-icon" style="color: ${update.type === 'reward' ? '#ffc107' : '#9c27b0'}"></i>
                <h3>New ${typeMap[update.type]} Added</h3>
                <div class="update-meta">
                    <span class="update-name">${update.name}</span>
                    ${update.subtype ? `<span class="update-type">${update.subtype}</span>` : ''}
                    ${update.points ? `<span class="update-points"><i class="fas fa-coins"></i> ${update.points}</span>` : ''}
                </div>
                ${update.image ? `<img src="${update.image}" class="update-image" onerror="this.style.display='none'">` : ''}
                <div class="update-date">Added: ${new Date(update.created_at).toLocaleString()}</div>
            `;
            updatesContainer.appendChild(updateItem);
        });

    } catch (error) {
        console.error('Update error:', error);
        updatesContainer.innerHTML = `
            <div class="update-item error">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Update Error</h3>
                <p>${error.message}</p>
                <button onclick="loadUpdates()" class="retry-btn">
                    <i class="fas fa-sync-alt"></i> Try Again
                </button>
            </div>
        `;
    }
}

  // Format date for display
  function formatDate(dateString) {
    try {
      const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
      return new Date(dateString).toLocaleDateString(undefined, options);
    } catch (e) {
      return dateString; // Return raw string if date parsing fails
    }
  }

  // Export updates to PDF
  function exportToPDF() {
    try {
      const doc = new jsPDF();
      
      // Title
      doc.setFontSize(20);
      doc.setTextColor(156, 39, 176);
      doc.text('Tunify Platform Updates Report', 105, 20, { align: 'center' });
      
      // Subtitle
      doc.setFontSize(12);
      doc.setTextColor(100, 100, 100);
      doc.text(`Generated on: ${new Date().toLocaleDateString()}`, 105, 30, { align: 'center' });
      
      // Get all update items
      const updateItems = document.querySelectorAll('.update-item');
      const updatesData = [];
      
      updateItems.forEach(item => {
        const title = item.querySelector('h3')?.textContent || 'Update';
        const date = item.querySelector('.update-date')?.textContent.replace('Added on: ', '') || 'Unknown date';
        const nameElement = item.querySelector('p strong');
        const name = nameElement ? item.querySelector('p').textContent.replace(nameElement.textContent, '').trim() : '';
        const description = item.querySelectorAll('p').length > 1 ? item.querySelectorAll('p')[1].textContent : '';
        
        updatesData.push({
          'Update Type': title,
          'Date Added': date,
          'Item': name,
          'Details': description
        });
      });
      
      // Add table to PDF
      doc.autoTable({
        head: [['Update Type', 'Date Added', 'Item', 'Details']],
        body: updatesData.map(item => [item['Update Type'], item['Date Added'], item.Item, item.Details]),
        startY: 40,
        theme: 'grid',
        headStyles: {
          fillColor: [30, 30, 46],
          textColor: [255, 255, 255],
          fontSize: 10
        },
        bodyStyles: {
          textColor: [0, 0, 0]
        },
        alternateRowStyles: {
          fillColor: [245, 245, 245]
        },
        styles: {
          fontSize: 9,
          cellPadding: 4,
          overflow: 'linebreak',
          halign: 'left'
        },
        margin: { top: 40 }
      });
      
      // Save the PDF
      doc.save(`Tunify_Updates_${new Date().toISOString().split('T')[0]}.pdf`);
    } catch (error) {
      console.error('PDF generation failed:', error);
      alert('Failed to generate PDF. Please check console for details.');
    }
  }

  // Close modal when clicking outside content
  window.onclick = function(event) {
    const modal = document.getElementById('updatesModal');
    if (event.target == modal) {
      closeUpdatesModal();
    }
  }
  async function redirectToRandomGame() {
    try {
        // Show loading state
        const quickplayCard = document.querySelector('.card:nth-child(1)');
        quickplayCard.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Finding a game...</div>';
        
        // Fetch active games list
        const response = await fetch('../controller/get_active_games.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const games = await response.json();
        
        if (!Array.isArray(games) || games.length === 0) {
            throw new Error('No active games available');
        }
        
        // Select a random game
        const randomGame = games[Math.floor(Math.random() * games.length)];
        
        // Redirect to the game
        window.location.href = `arcade/${randomGame.type_jeu}.php?id_game=${randomGame.id_game}`;
        
    } catch (error) {
        console.error('Quickplay error:', error);
        alert(`Couldn't start a random game: ${error.message}`);
        window.location.reload(); // Refresh to restore card
    }
}
function redirectToRandomGame() {
    const quickplayCard = document.querySelector('.card:nth-child(1)');
    quickplayCard.classList.add('disabled');
    quickplayCard.innerHTML += '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Finding a game...</div>';
    
    fetch('../../../controlleur/get_active_games.php')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(games => {
            if (!games.length) throw new Error('No active games');
            const randomGame = games[Math.floor(Math.random() * games.length)];
            // Remove 'arcade/' from the path
            window.location.href = `${randomGame.type_jeu}.php?id_game=${randomGame.id_game}`;
        })
        .catch(error => {
            console.error('Quickplay error:', error);
            alert('Failed to start random game: ' + error.message);
            window.location.reload();
        });
}
</script>
</body>
</html>