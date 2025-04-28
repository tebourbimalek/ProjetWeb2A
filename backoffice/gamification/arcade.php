<?php 
require_once 'config/database.php';

$db = Database::connect();

try {
    $stmt = $db->prepare("SELECT * FROM jeux");
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
  <link rel="stylesheet" href="/tunifiy(gamification)/backoffice/gamification/public/assets/css/frontoffice.css">
</head>
<body>

  <header>
    <img src="C:/xampp/htdocs/tunifiy(gamification)/sources/logoprojet1.jpg" alt="Logo" class="logo">
    <div class="header-buttons">
      <button>Se connecter</button>
      <button>Premium</button>
    </div>
  </header>

  <h1>Jeux</h1>

  <div class="game-grid">
    <?php if (!empty($games)): ?>
      <?php foreach ($games as $game): ?>
  <?php
    $statusClass = $game['statut'] === 'actif' ? 'active-game' : 'inactive-game';
  ?>
  <div class="game-card <?php echo $statusClass; ?>">
    <img src="<?php echo htmlspecialchars($game['cover_path']); ?>" alt="<?php echo htmlspecialchars($game['nom_jeu']); ?>">
    <h4><?php echo htmlspecialchars($game['nom_jeu']); ?></h4>
    <p><strong>Points to Win:</strong> <?php echo $game['points_attribues']; ?></p>
    <button>Jouer</button>
  </div>
<?php endforeach; ?>

    <?php else: ?>
      <p style="text-align: center;">No games available.</p>
    <?php endif; ?>
  </div>

</body>
</html>
