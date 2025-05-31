<?php

session_start();

require_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';

$playlistId = $_POST['playlist_id'];

// Fetch playlists
$playlistsusers = getPlaylistuser($playlistId);

// Check if the result is an array and not empty
if (is_array($playlistsusers) && !empty($playlistsusers)) {
    ?>
    <div class="carousel-container">
        <div class="section_title">
            <span id="tendance"><strong>Playlists publiques</strong></span>
        </div>

        <div class="albums-wrapper">
            <div class="albums-container">
                <?php 
                // Slice the array to get the first 5 playlists
                foreach (array_slice($playlistsusers, 0, 5) as $playlist): 
                    $imagePath = str_replace('\\', '/', $playlist['img']);
                    $cleanPath = str_replace('C:/xampp/htdocs', '', $imagePath);
                ?>
                    <div class="album-item" style="border:rgb(128, 128, 128);">
                        <div class="cover-img-container" onclick="toggleBox3(
                            <?= htmlspecialchars($playlist['id']) ?>,
                            '<?= addslashes(htmlspecialchars($playlist['nom'])) ?>',
                            '<?= $cleanPath ?>')">
                            <?php if (!empty($playlist['img'])): ?>
                                <img src="<?= htmlspecialchars($cleanPath) ?>" alt="Playlist Image" class="cover-img">
                            <?php else: ?>
                                <div style="background-color: rgb(62, 62, 62); width: 200px; height: 240px; display: flex; align-items: center; justify-content: center; border-radius: 5px;">
                                    <i class="fa-solid fa-music fa-lg" style="color: white; font-size: 30px;"></i>
                                </div>
                            <?php endif; ?>
                            <input class="img_url" type="hidden" name="playlist_name" value="<?= htmlspecialchars($cleanPath ?: 'vide') ?>">
                        </div>
                        <div class="album-info">
                            <h3 style="color:white; padding:20px; text-align: center;"><?= htmlspecialchars($playlist['nom']) ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php
};
?>
