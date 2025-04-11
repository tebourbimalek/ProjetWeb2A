<?php
session_start();  // Start the session to retrieve data


include_once 'C:\xampp\htdocs\projetweb\model\function.php';  // Include the function file   

$counter = 1;  // Initialize a counter for the song number
$counter1 = 1; // Initialize a counter for the song number in the top songs section
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';  // Retrieve any error message
unset($_SESSION['message']); // Clear the message after it has been set

$songs = affichage();  // Retrieve all songs
$songstrier= affichagetrier();  // Retrieve songs sorted by streams


// Check if there's a song before trying to access its ID
$song_id = isset($songs[0]['id']) ? $songs[0]['id'] : null;

if ($song_id) {
    $songsid = affichageid($song_id);  // Fetch song by ID
} else {
    $songsid = null;  // No song found
}

$statistics = getStreamingStatistics();

// Convert to JSON only if valid
if ($statistics) {
    $statisticsJson = json_encode($statistics);
} else {
    $statisticsJson = null;
}

$stats = getArtistStats();
$labels = [];
$data = [];

foreach ($stats as $row) {
    $labels[] = $row['nom_utilisateur'];
    $data[] = $row['total_streams'];
}

// Convertir en JSON pour JavaScript
$labelsJson = json_encode($labels);
$dataJson = json_encode($data);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotify Artist Dashboard</title>
    <link rel="stylesheet" href="backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas{
            width: 100% !important;
            height: 100% !important;

        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <img src="" alt="Spotify Logo">
        </div>
        <ul class="sidebar-menu">
            <li class="active" data-tab="dashboard"><i class="fas fa-home"></i> Dashboard</li>
            <li data-tab="music"><i class="fas fa-music"></i> My Music</li>
            <li data-tab="stats"><i class="fas fa-chart-line"></i> Statistics</li>
            <li data-tab="profile"><i class="fas fa-user"></i> Profile</li>
            <li data-tab="settings"><i class="fas fa-cog"></i> Settings</li>
            <li data-tab="user"><i class="fas fa-user"></i> user</li>
            <li data-tab="paiment"><i class="fa-solid fa-cart-shopping"></i>paiment</li>
            <li data-tab="reclamation"><i class="fa-solid fa-circle-exclamation"></i> reclamation</li>
            <li data-tab="jeux"><i class="fa-solid fa-gamepad"></i> jeux</li>
            
            <li><i class="fas fa-sign-out-alt"></i> Logout</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <div class="artist-info">
                <div class="artist-avatar">
                    <img src="" alt="Artist Avatar">
                </div>
                <div class="artist-name">Your Artist Name</div>
            </div>
            <button id="add-music-btn" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Music</button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <div class="stats-container" style="display: grid;grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));gap: 20px;margin-bottom: 20px;">
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s; position:relative ; right:18px;">
                    <div class="stat-title">TOTAL STREAMS</div>
                    <div class="stat-value">1.2M</div>
                    <div class="stat-change">+15% this month</div>
                </div>
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s; position:relative ; right:10px;">
                    <div class="stat-title">MONTHLY LISTENERS</div>
                    <div class="stat-value">345K</div>
                    <div class="stat-change">+8% this month</div>
                </div>
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s; position:relative ; left:6px;">
                    <div class="stat-title">FOLLOWERS</div>
                    <div class="stat-value">89K</div>
                    <div class="stat-change">+5% this month</div>
                </div>
                <div class="stat-card" style="background-color: var(--spotify-gray);border-radius: 8px;padding: 20px;transition: transform 0.3s;position:relative ; left:18px;">
                    <div class="stat-title">REVENUE</div>
                    <div class="stat-value">$4,512</div>
                    <div class="stat-change">+12% this month</div>
                </div>
            </div>

            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Top Performing Songs</div>
                </div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Duree</th>
                            <th>Streams</th>
                            <th>Download</th>
                        </tr>
                    </thead>
                    <tbody id="top-songs-table">
                    <?php foreach ($songstrier as $song): ?>
                            <tr>
                                <td> <?php echo $counter1++; ?></td>
                                <td>
                                    <?php
                                        // Correction du chemin de l'image pour qu'il soit relatif à la racine du projet
                                        $base_path = "/projetweb/assets/includes/"; 
                                        $image_path = str_replace("C:/xampp/htdocs", "", $song['image_path']);
                                    ?>
                                    <img src="<?php echo $image_path; ?>" width="100" height="100" alt="Image de la chanson">
                                </td>
                                <td><?php echo htmlspecialchars($song['song_title']); ?></td>
                                <td><?php echo htmlspecialchars($song['album_name']); ?></td>
                                <td><?php echo htmlspecialchars($song['duree']); ?></td>
                                <td><?php echo isset($song['total_streams']) ? htmlspecialchars($song['total_streams']) : 'Aucune donnée'; ?></td>
                                <td>
                                <a href="download.php?song_id=<?php echo $song['id']; ?>" class="btn btn-info"><i class="fa-solid fa-download fa-xl" style="color:white;"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

            <div class="" style="display :flex; justify-content: space-between;">
                <!-- Left Section: Streaming Performance -->
                <div class="content-section">
                    <div class="section-header" style="">
                        <div class="section-title" style="">Streaming Performance</div>
                    </div>
                    <div class="chart-container" style="width: 500px; height: 400px;">
                        <canvas id="stream-chart"></canvas>
                    </div>
                </div>

                <!-- Right Section: Listener Growth -->
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-title">Top Musiques Performantes</div>
                    </div>
                    <div class="chart-container" style="width: 600px; height: 400px;">
                        <canvas id="artistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Music Tab -->
        <div id="music" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">All Your Music</div>
                    <button id="bulk-add-btn" class="btn btn-secondary"><i class="fas fa-upload"></i> Bulk Upload</button>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                        <?php foreach ($songs as $song): ?>
                            <tr>
                                <td> <?php echo $counter++?></td>
                                <td>
                                    <?php
                                        $base_path = "/projetweb/assets/includes/"; // The base path relative to the document root
                                        $image_path = str_replace("C:/xampp/htdocs", "", $song['image_path']); // Remove the local file system path
                                    ?>
                                    <img src="<?php echo $image_path; ?>" width="70" height="70" alt="">
                                </td>
                                <td><?php echo htmlspecialchars($song['song_title']); ?></td>
                                <td><?php echo htmlspecialchars($song['album_name']); ?></td>
                                <td><?php echo htmlspecialchars($song['release_date']); ?></td>
                                <td><?php echo htmlspecialchars($song['duree']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-secondary edit-song" 
                                            data-id="<?php echo $song['id']; ?>" 
                                            data-title="<?php echo htmlspecialchars($song['song_title']); ?>"
                                            data-album="<?php echo htmlspecialchars($song['album_name']); ?>"
                                            data-release="<?php echo htmlspecialchars($song['release_date']); ?>"
                                            data-duree="<?php echo htmlspecialchars($song['duree']); ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="submit" class="btn btn-danger delete-song" id="supprimer" data-id="<?php echo $song['id']; ?>"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
        <!-- user Tab  omar-->
        <div id="user" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">All Your users</div>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                    </tbody>

                </table>
            </div>
        </div>
        <!-- paiment Tab  islem-->
        <div id="paiment" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">All paiments</div>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                    </tbody>

                </table>
            </div>
        </div>
        <!-- reclamation Tab  taher-->
        <div id="reclamation" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">All reclamations</div>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                    </tbody>

                </table>
            </div>
        </div>
        <!-- jeux Tab    cahine-->
        <div id="jeux" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">games table</div>
                </div>
                <div class="loader" id="music-loader"></div>
                <table class="song-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Title</th>
                            <th>Album</th>
                            <th>Release Date</th>
                            <th>Duree</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="all-songs-table">
                    </tbody>

                </table>
            </div>
        </div>

        <!-- Stats Tab -->
        <div id="stats" class="tab-content">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-title">TOTAL SONGS</div>
                    <div class="stat-value" id="total-songs-count">0</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">AVERAGE STREAMS</div>
                    <div class="stat-value">245K</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">MOST STREAMED</div>
                    <div class="stat-value">487K</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">NEWEST RELEASE</div>
                    <div class="stat-value">2 days ago</div>
                </div>
            </div>

            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Monthly Listeners Trend</div>
                </div>
                <canvas id="listeners-chart"></canvas>
            </div>
        </div>

        <!-- Profile Tab -->
        <div id="profile" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Artist Profile</div>
                    <button class="btn btn-secondary"><i class="fas fa-edit"></i> Edit Profile</button>
                </div>
                <div style="display: flex; margin-top: 20px;">
                    <div style="flex: 0 0 200px; margin-right: 30px;">
                        <img src="" alt="Artist Profile" style="width: 100%; border-radius: 8px;">
                    </div>
                    <div style="flex: 1;">
                        <h2 style="margin-bottom: 20px;">Your Artist Name</h2>
                        <p style="margin-bottom: 15px; line-height: 1.6;">Artist bio goes here. This is where you can tell your fans about yourself, your music style, influences, and journey. Make it personal and engaging to connect with your audience.</p>
                        <div style="margin-top: 30px;">
                            <h3 style="margin-bottom: 15px;">Contact & Social</h3>
                            <p><i class="fas fa-envelope"></i> artist@example.com</p>
                            <p><i class="fab fa-instagram"></i> @yourartistname</p>
                            <p><i class="fab fa-twitter"></i> @yourartistname</p>
                            <p><i class="fab fa-youtube"></i> Your Artist Name</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Tab -->
        <div id="settings" class="tab-content">
            <div class="content-section">
                <div class="section-header">
                    <div class="section-title">Account Settings</div>
                </div>
                <div style="margin-top: 20px;">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" class="form-control" value="artist@example.com">
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="form-control" value="••••••••">
                    </div>
                    <div class="form-group">
                        <label>Payout Method</label>
                        <select class="form-control">
                            <option>Direct Deposit</option>
                            <option>PayPal</option>
                            <option>Check</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notification Preferences</label>
                        <div style="margin-top: 10px;">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <input type="checkbox" id="notify-streams" checked style="margin-right: 10px;">
                                <label for="notify-streams">Streaming milestones</label>
                            </div>
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <input type="checkbox" id="notify-followers" checked style="margin-right: 10px;">
                                <label for="notify-followers">New followers</label>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <input type="checkbox" id="notify-payments" checked style="margin-right: 10px;">
                                <label for="notify-payments">Payment updates</label>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-primary" style="margin-top: 20px;">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Music Modal -->
    <form action="ajout.php" method="POST" enctype="multipart/form-data" id="song-form">
        <div id="add-music-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Add New Music</div>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="upload-area" id="cover-upload">
                    <label for="file-upload" class="upload-icon">
                        <i class="fas fa-image" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="file-upload" name="image_input" accept="image/*" style="display: block; visibility: hidden;"/>
                    <p id="cover-upload-text">Drop your album artwork here or click to upload</p>
                    <p id="recommended-size" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Recommended size: 1000x1000px</p>
                </div>                    
                <div class="form-group">
                    <label for="song-title">Song Title</label>
                    <input type="text" id="song-title" name="song-title" class="form-control"  placeholder="Enter Song name" required>
                    <div id="song-title-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                
                <div class="form-group">
                    <label for="album-name">Album Name</label>
                    <input type="text" id="album-name"  name="album-name" class="form-control" placeholder="Enter album name" required>
                    <div id="album-name-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="song-release">Release Date</label>
                    <input type="date" id="song-release" name="song-release" class="form-control" placeholder="YYYY-MM-DD" required>
                    <div id="release-date-error" class="error-message" style="color: red; display: none;"></div> <!-- Error message here -->
                </div>
                <div class="form-group">
                    <label for="duree-relese">Duree</label>
                    <input type="text" name="duree" id="duree" class="form-control" placeholder="MM:SS/02:20" required>
                    <div id="duree-error" class="error-message" style="color: red; display: none;"></div>
                </div>
                <br>
                <div class="upload-area" id="audio-upload">
                    <label for="audio-upload-input" class="upload-icon">
                        <i class="fas fa-music" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="audio-upload-input" name="music_input" accept="audio/mp3, audio/wav" style="display: block; visibility: hidden;"/>
                    <p id="audio-upload-text">Drop your audio file here or click to upload</p>
                    <p id="text" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Accepted formats: MP3, WAV (max 50MB)</p>
                </div> 
                
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-add">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-add">Add Song</button>
                </div>
                
            </div>
        </div>
    </form>

    <!-- Edit Music Modal -->
    <form action="edit.php" method="POST" enctype="multipart/form-data" id="edit-form">
        <div id="edit-music-modal" class="modal" >
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Edit Song</div>
                    <button type="button" class="close-modal">&times;</button>
                </div>
                <input type="hidden" name="song_id" id="song_id"> <!-- Hidden input for song ID -->
                <div class="upload-area" id="edit-cover-upload">
                        <label for="file-edit" class="upload-icon">
                            <i class="fas fa-image" style="font-size: 40px;"></i>
                        </label>
                        <input type="file" id="file-edit" name="image_input" accept="image/*" style="display: block; visibility: hidden;"/>
                        <p id="edit-text" name="text_edit" >edit si tu veux </p>
                        <p id="recommendeds-size" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Recommended size: 1000x1000px</p>
                    </div>
                <div class="form-group">
                    <label for="edit-song-title">Song Title</label>
                    <input type="text" id="edit-song-title" name="edit-song-title" class="form-control" placeholder="Enter song title" value="">
                    <div id="song-title-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="form-group">
                    <label for="edit-song-album">Album</label>
                    <input type="text" id="edit-song-album" name="edit-song-album" class="form-control" placeholder="Enter album name"value="">
                    <div id="album-name-error-edit" class="error-message" style="color: red; display: none;"></div>

                </div>
                <div class="form-group">
                    <label for="edit-song-release">Release Date</label>
                    <input type="text" id="edit-song-release" name="temps" class="form-control" 
                    value="">
                    <div id="release-date-error-edit" class="error-message" style="color: red; display: none;"></div> <!-- Error message here -->
                </div>
                <div class="form-group">
                    <label for="edit-song-release">Duree</label>
                    <input type="text" id="edit-song-duree" name="edit-duree" class="form-control" placeholder="MM:DD/02:20"
                    value="">   
                    <div id="duree-error-edit" class="error-message" style="color: red; display: none;"></div>
                </div>
                <div class="upload-area" id="audio-edit-upload">
                    <label for="audio-edit-input" class="upload-icon">
                        <i class="fas fa-music" style="font-size: 40px;"></i>
                    </label>
                    <input type="file" id="audio-edit-input" name="music_input" accept="audio/mp3, audio/wav" style="display: block; visibility: hidden;"/>
                    <p id="audio-edit-text">edit si tu veux</p>
                    <p id="texts" style="font-size: 12px; margin-top: 10px; color: var(--spotify-light-gray);">Accepted formats: MP3, WAV (max 50MB)</p>
                </div> 
                <input type="hidden" id="edit-song-id">
                <div class="form-footer">
                    <button class="btn btn-secondary" id="cancel-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirm-edit">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="modal">
        <div class="modal-content" style="width: 400px;">
            <div class="modal-header">
                <div class="modal-title">Confirm Delete</div>
                <button class="close-modal">&times;</button>
            </div>
            <p style="margin-bottom: 20px;">Are you sure you want to delete this song? This action cannot be undone.</p>
            <input type="hidden" id="delete-song-id">
            <div class="form-footer">
                <button class="btn btn-secondary" id="cancel-delete">Cancel</button>
                <button class="btn btn-primary" style="background-color: #e74c3c;" id="confirm-delete">Delete</button>
            </div>
        </div>
    </div>
    <div id="notification" style="display: none; background-color: #4CAF50; color: white; padding: 15px; margin: 20px; border-radius: 50px; position: relative; left: 150px;">
        <span id="message"></span>
    </div>

    <script>
        const statistics = <?php echo $statisticsJson; ?>;

        if (statistics && statistics.top_songs) {
            const songTitles = statistics.top_songs.map(song => song.song_title);
            const songStreams = statistics.top_songs.map(song => song.total_streams);
            const totalListeners = statistics.total_listeners;

            // Calculate the percentage of listeners for each song
            const songPercentages = songStreams.map(streamCount => (streamCount / totalListeners) * 100);

            // Function to generate dark colors
            function generateDarkColor() {
                const letters = '0123456789ABCDEF';
                let color = '#';
                for (let i = 0; i < 6; i++) {
                    // Restrict the random values to the darker half of the color spectrum (0-7 for RGB)
                    const randomValue = Math.floor(Math.random() * 8); // 0-7 for darker shades
                    color += letters[randomValue];
                }
                return color;
            }

            // Generate dark colors for each song
            const backgroundColor = songTitles.map(() => generateDarkColor());
            const borderColor = backgroundColor.map(color => color); // Same as background for border

            const chartData = {
                labels: songTitles,
                datasets: [{
                    label: 'Stream Count Percentage by Listeners',
                    data: songPercentages,
                    backgroundColor: backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            };

            const ctx = document.getElementById('stream-chart').getContext('2d');
            const streamChart = new Chart(ctx, {
                type: 'pie', // Changed to 'pie' for a pie chart
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: 'white' // Set legend text color to white
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    const percentage = tooltipItem.raw.toFixed(2);
                                    return tooltipItem.label + ': ' + percentage + '%';
                                }
                            }
                        }
                    }
                }
            });
        } else {
            console.error("No valid data for chart.");
        }
        document.addEventListener("DOMContentLoaded", function() {
            let message = "<?php echo $message; ?>";
            if (message.trim() !== "") {
                let notification = document.createElement("div");
                notification.innerText = message;
                notification.style.position = "fixed";
                notification.style.top = "10px";
                notification.style.right = "10px";
                notification.style.backgroundColor = "green";
                notification.style.color = "white";
                notification.style.padding = "10px";
                notification.style.borderRadius = "5px";
                document.body.appendChild(notification);

                // Remove notification after 3 seconds
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 4000);
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('artistChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo $labelsJson; ?>,
                    datasets: [{
                        label: 'Total Streams',
                        data: <?php echo $dataJson; ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                        
                            
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });

        
        
    
  
        
    </script>
    <script src="backoffice.js"></script>
</body>
</html>
