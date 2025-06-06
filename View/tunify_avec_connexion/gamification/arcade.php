<?php 
require_once 'C:\xampp\htdocs\projetweb\model\config.php';

$pdo = config::getConnexion();

try {
  $stmt = $pdo->prepare("SELECT * FROM jeux");
  $stmt->execute();
  $games = $stmt->fetchAll(PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
  die("Error fetching games: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Jeux</title>
  <link rel="stylesheet" href="/projetweb/View/tunify_avec_connexion/gamification/public/assets/css/frontoffice.css">
  <link rel="stylesheet" href="/projetweb/View/tunify_avec_connexion/gamification/public/assets/css/arcade.css">
</head>
<body>

<header>
    <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/logoprojet1.jpg" alt="Logo" class="logo">
    <div class="header-buttons">
        <button onclick="window.location.href='frontoffice.php'">Back to Menu</button>
        
    </div>
</header>

<h1>Jeux</h1>

<div class="game-grid">
  <?php if (!empty($games)): ?>
    <?php foreach ($games as $game): ?>
      <?php
        $statusClass = $game['statut'] === 'actif' ? 'active-game' : 'inactive-game';
        // Determine game file based on type_jeu
        $gameFile = $game['type_jeu'] . '.php'; // This will generate guess.php, puzzle.php, etc.
      ?>
      <div class="game-card <?php echo $statusClass; ?>">
        <img src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/<?php echo htmlspecialchars($game['cover_path'] ?? 'default_game.jpg'); ?>" 
             alt="<?php echo htmlspecialchars($game['nom_jeu']); ?>">
        <h4><?php echo htmlspecialchars($game['nom_jeu']); ?></h4>
        <p><strong>Points:</strong> <?php echo (int)$game['points_attribues']; ?></p>
        <p><strong>Type:</strong> <?php echo htmlspecialchars($game['type_jeu']); ?></p>
        <?php if ($game['statut'] === 'actif'): ?>
          <button onclick="window.location.href='../gamification/<?php echo $gameFile; ?>?id_game=<?php echo (int)$game['id_game']; ?>'">
            Jouer
          </button>
        <?php else: ?>
          <button disabled>Indisponible</button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p style="text-align: center; color: white; grid-column: 1 / -1;">Aucun jeu disponible pour le moment.</p>
  <?php endif; ?>
</div>

<script src="/projetweb/View/tunify_avec_connexion/gamification/public/assets/js/arcade.js"></script>
</body>
</html>