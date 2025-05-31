<?php 
session_start();

include_once 'C:\xampp\htdocs\projetweb\controlleur\function_2.php';
include_once 'C:\xampp\htdocs\projetweb\model\config.php';



$pdo = config::getConnexion();


require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
// Usage of the function
$user = getUserInfo($pdo);


$user_id = $user->getArtisteId();
$playlistId = $_POST['playlist_id'];
if ($user_id == $playlistId){
    $playlist_songs = getUserSongHistory($user_id)
    ?>
    <br><br><br>
    <div>
        <h3 style="color: white; margin-bottom: -15px;"><strong>Historique d'ecoute</strong></h3>
        <h5 style="color:gray;">Visibles uniquement par vous</h5>
    </div>
<?php 
if ($playlist_songs) {
    echo "<table style='width:100%; border-collapse:collapse; color:white;'>";
    echo "<tbody>";

    $count = 1;
    foreach ($playlist_songs as $song) {
        $imagePath = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['image_path']);
        $imagePath = ltrim($imagePath, '/');
        $imageURL = "/projetweb/" . $imagePath;


        $music_path = str_replace(['C:/xampp/htdocs/projetweb', '\\'], ['', '/'], $song['music_path']);
        $music_path = ltrim($music_path, '/');
        $musicURL = "/projetweb/" . $music_path;
    
            echo "<tr
            class='song-row'
            data-song-id='" . (int)$song['id'] . "' 
            data-song-title='" . htmlspecialchars($song['song_title']) . "' 
            data-song-url='" . htmlspecialchars($musicURL) . "' 
            data-song-cover='" . htmlspecialchars($imageURL) . "' 
            data-song-artiste='" . htmlspecialchars($song['album_name']) . "' 
            onmouseover=\"this.querySelector('.song-number').innerHTML='<i class=&quot;fa-solid fa-play&quot; style=&quot;font-size:13px;&quot;></i>';\" 
            onmouseout=\"this.querySelector('.song-number').innerHTML=this.querySelector('.song-number').dataset.number;\"
            onclick='playSongplaylist(this);'>
    

            <td style='padding:10px;'>
                <span class='song-number' data-number='{$count}'>{$count}</span>
            </td>
            <td style='padding:10px; display:flex; align-items:center;'>
                <img src='" . htmlspecialchars($imageURL) . "' style='width:50px;height:50px;margin-right:10px;'>
                <span class='song-title'>" . htmlspecialchars($song['song_title']) . "</span>
            </td>
            <td style='padding:10px;'>" . htmlspecialchars($song['album_name']) . "</td>
            <td style='padding:10px; text-align:center;'>
                <button style='background:none;border:none;cursor:pointer;'>
                    <i class='fa-solid fa-circle-check plus-icon'></i>
                </button>
            </td>
            <td style='padding:10px;'>
                <span>" . htmlspecialchars($song['duree']) . "</span>
            </td>
        </tr>";

        $count++;

    }
            

    echo "</tbody>";
    echo "</table>";
}




}


?>