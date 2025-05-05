<?php
require_once __DIR__ . '/../../config/database.php';
session_start();

// Get user points from database if logged in
$userPoints = 0;
if (isset($_SESSION['user_id'])) {
    $db = Database::connect();
    $stmt = $db->prepare("SELECT points FROM user_rewards WHERE user_id = ?");
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
  </style>
</head>
<body>

<header>
  <div class="profile-section">
    <div class="points">
      <i class="fas fa-coins"></i>
      <span><?= htmlspecialchars($userPoints) ?></span>
    </div>
    <span class="profile-name"><?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest' ?></span>
    <img src="<?= isset($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'https://i.pravatar.cc/150?img=3' ?>" alt="Profile Picture" class="profile-pic">
  </div>
  <img src="/tunifiy(gamification)/sources/uploads/logoprojet1.jpg" alt="Tunify Logo" class="logo">
</header>

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

  <div class="card" onclick="window.location.href='/tunifiy(gamification)/backoffice/gamification/store.php'">
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
        const response = await fetch('/tunifiy(gamification)/backoffice/gamification/controllers/get_updates.php');
        
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
        const response = await fetch('../backoffice/gamification/controllers/get_active_games.php');
        
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
    
    fetch('/tunifiy(gamification)/backoffice/gamification/controllers/get_active_games.php')
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