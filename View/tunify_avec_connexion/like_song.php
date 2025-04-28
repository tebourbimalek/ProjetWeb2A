<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php'; // your PDO setup

// 1) Identify the current user
// Replace `8` with your session logic, e.g. $_SESSION['user_id']
$user_id = 8;

// 2) Retrieve the song ID from POST
$song_id = isset($_POST['song_id_box']) ? $_POST['song_id_box'] : null;

if (!$user_id || !$song_id) {
    // Missing required data
    header('Location: avec_connexion.php?error=missing_parameters');
    exit;
}

try {
    $pdo = config::getConnexion();

    // 3) Check if this user already liked this song
    $chk = $pdo->prepare("
        SELECT 1
        FROM liked_song
        WHERE user_id = :uid
          AND song_id = :sid
        LIMIT 1
    ");
    $chk->execute([
        ':uid' => $user_id,
        ':sid' => $song_id
    ]);

    if ($chk->fetch()) {
        // Already liked—nothing to do
        header('Location: avec_connexion.php?error=already_liked');
        exit;
    }

    // 4) Insert into liked_song
    $ins = $pdo->prepare("
        INSERT INTO liked_song (user_id, song_id)
        VALUES (:uid, :sid)
    ");
    $ins->execute([
        ':uid' => $user_id,
        ':sid' => $song_id
    ]);

    // 5) Redirect back with success
    header('Location: avec_connexion.php?success=song_liked');
    exit;

} catch (PDOException $e) {
    // Log and fail
    error_log("avec_connexion.php error: " . $e->getMessage());
    header('Location: avec_connexion.php?error=sql_error');
    exit;
}
?>
