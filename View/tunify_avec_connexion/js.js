document.addEventListener("click", function(event) {
    const shareBox2 = document.getElementById("shareBox2");
    const shareButton2 = document.getElementById("shareButton2");

    // If the click is NOT inside the shareBox2 and not on the button
    if (!shareBox2.contains(event.target) && !shareButton2.contains(event.target)) {
        shareBox2.style.display = "none";
    }
});


window.addEventListener('load', function() {
    setTimeout(function() {
      const preloader = document.getElementById('preloader');
      preloader.style.opacity = '0'; // Start fade out

      setTimeout(function() {
        preloader.style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
      }, 500); // Match transition duration
    }, 1500); // Show preloader for 1.5 seconds minimum
  });

// Toggle between main box and expanded views
function toggleBox(event) {
    event.preventDefault();
    let mainBox = document.getElementById("box2-main");
    let expandedBox = document.getElementById("box2-expanded2");

    mainBox.style.display = mainBox.style.display === "none" ? "block" : "none";
    expandedBox.style.display = mainBox.style.display === "none" ? "block" : "none";
}

function toggleBox2(event) {
    event.preventDefault();
    let mainBox = document.getElementById("box2-main");
    let expandedBox = document.getElementById("box2-expanded");

    mainBox.style.display = mainBox.style.display === "none" ? "block" : "none";
    expandedBox.style.display = mainBox.style.display === "none" ? "block" : "none";
}


// Audio player setup
const audio = document.getElementById('audioPlayer');
const playPauseBtn = document.getElementById('playPause');
const progressCurrent = document.querySelector('.progress-current');
const currentTime = document.getElementById('current-time');
const totalTime = document.getElementById('total-time');
const titleEl = document.getElementById('song-title');
const artistEl = document.getElementById('song-artist');
const coverEl = document.getElementById('song-cover');
const buttonplay=document.getElementById('buttonplay');
const starticon=document.getElementById('starticon');
const icons = document.getElementById('icon_id');
const song_id_box8ne= document.getElementById('song_id_box8ne');
const song_idd= document.getElementById('song_idd');



isFromPlaylist = true;

let currentSongPath = null;
let songHistory = [];
// Function to play or toggle a song
function playSong(path, title, artist, cover = null, button,songId) {
    isFromPlaylist = false;
    const fullPath = new URL(path, window.location.href).href;
    if (audio.src === fullPath) {
        if (audio.paused) {
            audio.play();
            button.innerHTML = '<i class="fas fa-pause"></i>';
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            starticon.style.display = 'inline-block';
        } else {
            audio.pause();
            button.innerHTML = '<i class="fas fa-play"></i>';
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
    } else {
        currentSongPath= path; // Update current song path
    
        audio.src = fullPath;
        audio.play();
    
        // Update UI
        button.innerHTML = '<i class="fas fa-pause"></i>';
        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        starticon.style.display = 'inline-block';
    
        titleEl.textContent = title;
        artistEl.textContent = artist;
        icons.style.display = 'block'; // Hide the icon when a song is playing
       
        song_id_box8ne.value = songId; // Set the song ID for the box8ne
        song_idd.value = songId; // Set the song ID for the box8ne
       
        const songIdValue = encodeURIComponent(song_idd.value);

        // First request to avec_connexion.php
        const xhr1 = new XMLHttpRequest();
        xhr1.open('POST', '/projetweb/View/tunify_avec_connexion/avec_connexion.php', true);
        xhr1.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr1.send('song_idd=' + songIdValue);

        // Second request to log_song.php (or any second endpoint)
        const xhr2 = new XMLHttpRequest();
        xhr2.open('POST', '/projetweb/View/tunify_avec_connexion/music/realtime_data.php', true);
        xhr2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr2.send('song_idd=' + songIdValue);
    
        if (cover) {
            coverEl.src = cover;
            coverEl.style.display = 'block';
        }

    
        resetAllButtonsExcept(button);
        updateLikeIcon(songId);
    }
   
    
}

const prevButton = document.getElementById('prev');

// Handle 'prev' click to replay the same song from the beginning
prevButton.addEventListener('click', () => {
    if (currentSongPath) {
        // Play the current song from the beginning
        audio.src = currentSongPath; // Set the audio source to the current song's path
        audio.currentTime = 0; // Restart the song from the beginning
        audio.play(); // Play the song from the start

        // Optionally update the play/pause button UI
        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
    } else {
        console.log("No current song to replay.");
    }
});










function updateSongInfo(title, artist, cover,path) {
    audio.src = path;
    audio.play();
    document.getElementById('song-cover').src = cover;
    document.getElementById('song-title').textContent = title;
    document.getElementById('song-artist').textContent = artist;
    document.getElementById('playPause').innerHTML = '<i class="fas fa-pause"></i>';
    currentSongPath=path; // Update current song path
}

document.getElementById('audioPlayer').addEventListener('ended', function () {

    // Load a new random song via iframe
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = 'avec_connexion.php?next=1';
    document.body.appendChild(iframe);

    // Clean up iframe after 2 seconds
    setTimeout(() => {
        document.body.removeChild(iframe);
    }, 2000);
});


// Reset other buttons to play icon
function resetAllButtonsExcept(exceptButton) {
    const allButtons = document.querySelectorAll('.buttonplay');
    allButtons.forEach(button => {
        if (button !== exceptButton) {
            button.innerHTML = '<i class="fas fa-play"></i>';
        }
    });
}

// Global play/pause button
playPauseBtn.addEventListener('click', () => {
    if (audio.paused) {
        audio.play();
        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        starticon.style.display = 'inline-block';

        if (currentSongPath) {
            const currentButton = document.querySelector(`.buttonplay[data-path="${getRelativePath(audio.src)}"]`);
            if (currentButton) currentButton.innerHTML = '<i class="fas fa-pause"></i>';
        }
    } else {
        audio.pause();
        playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        if (currentSongPath) {
            const currentButton = document.querySelector(`.buttonplay[data-path="${getRelativePath(audio.src)}"]`);
            if (currentButton) currentButton.innerHTML = '<i class="fas fa-play"></i>';
        }
    }
});

// Reset when song ends
audio.addEventListener('ended', () => {
    playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
    starticon.style.display = 'inline-block';

    if (currentSongPath) {
        const currentButton = document.querySelector(`.buttonplay[data-path="${getRelativePath(audio.src)}"]`);
        if (currentButton) currentButton.innerHTML = '<i class="fas fa-play"></i>';
    }

    currentSongPath = null;
});

// Helper to get relative path from full URL
function getRelativePath(fullUrl) {
    const baseUrl = window.location.origin;
    return fullUrl.replace(baseUrl, '');
}



// Update progress and time display as the song plays
audio.addEventListener('timeupdate', () => {
    const percentage = (audio.currentTime / audio.duration) * 100;
    progressCurrent.style.width = percentage + '%';

    currentTime.textContent = formatTime(audio.currentTime);
    totalTime.textContent = formatTime(audio.duration);
});

// Format seconds into MM:SS
function formatTime(seconds) {
    if (isNaN(seconds)) return "0:00";
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${minutes}:${secs < 10 ? '0' : ''}${secs}`;
}

// Seek function - jumps audio to click position
function seekSong(event) {
    const progressBar = document.querySelector('.progress-bar');
    const clickX = event.offsetX;
    const width = progressBar.offsetWidth;

    const duration = audio.duration;
    audio.currentTime = (clickX / width) * duration;
}

// Attach click event to progress bar
document.querySelector('.progress-bar').addEventListener('click', seekSong);
// Update progress bar and time
audio.addEventListener('timeupdate', () => {
    const progressPercent = (audio.currentTime / audio.duration) * 100;
    progressCurrent.style.width = `${progressPercent}%`;
    currentTime.textContent = formatTime(audio.currentTime);
    totalTime.textContent = formatTime(audio.duration);
});

// Make the bar clickable
document.querySelector('.progress-bar').addEventListener('click', function (e) {
    const bar = this;
    const width = bar.offsetWidth;
    const clickX = e.offsetX;
    const duration = audio.duration;

    audio.currentTime = (clickX / width) * duration;
});

function toggleMute() {
    const volumeIcon = document.getElementById('volume-icon');  // The volume icon
    const audio = document.getElementById('audioPlayer');       // The audio element
    const volumeBar = document.querySelector('.volume-current');  // The current volume bar
    const volumeDot = document.querySelector('.volume-dot');  // The dot that moves along the volume bar
    const volumeContainer = document.querySelector('.volume-bar');  // The container that holds the volume bar

    // Check if the audio element and volume icon exist
    if (!audio) {
        console.log('No audio element found.');
        return;
    }
    if (!volumeIcon) {
        console.log('No volume icon found.');
        return;
    }
    if (!volumeDot || !volumeContainer) {
        console.log('No volume dot or container found.');
        return;
    }

    // Set the initial volume dot position based on the current volume
    volumeDot.style.left = `${audio.volume * 100}%`;
    volumeBar.style.width = `${audio.volume * 100}%`;  // Initial volume bar width

    // Add click event listener to the volume button (mute/unmute toggle)
    volumeIcon.addEventListener('click', () => {
        if (audio.muted) {
            // Unmute the audio
            audio.muted = false;
            // Restore the volume bar to its current level
            volumeBar.style.width = `${audio.volume * 100}%`;
            volumeDot.style.left = `${audio.volume * 100}%`;

            // Change the icon to volume up (unmuted)
            volumeIcon.classList.remove('fa-volume-mute');
            volumeIcon.classList.add('fa-volume-up');
        } else {
            // Mute the audio
            audio.muted = true;

            // Set the volume bar and dot to 0% (no sound)
            volumeBar.style.width = '0%';
            volumeDot.style.left = '0%';

            // Change the icon to volume mute (muted)
            volumeIcon.classList.remove('fa-volume-up');
            volumeIcon.classList.add('fa-volume-mute');
        }
    });

    // Function to update the volume and the dot's position
    function updateVolume(event) {
        // Calculate the new volume based on the position of the click or drag
        const volumeWidth = volumeContainer.offsetWidth;  // Width of the volume bar container
        const clickPosition = event.clientX - volumeContainer.offsetLeft;  // X position where the click happened
        const newVolume = Math.min(Math.max(clickPosition / volumeWidth, 0), 1);  // Ensure value is between 0 and 1

        // Update the audio volume
        audio.volume = newVolume;

        // Update the volume bar width
        volumeBar.style.width = `${newVolume * 100}%`;

        // Update the dot's position
        volumeDot.style.left = `${newVolume * 100}%`;

        // If the volume is 0, mute the audio and change the icon to mute
        if (newVolume === 0) {
            audio.muted = true;
            volumeIcon.classList.remove('fa-volume-up');
            volumeIcon.classList.add('fa-volume-mute');
        } else if (audio.muted) {
            // If the audio was muted and the volume is not 0, unmute it
            audio.muted = false;
            volumeIcon.classList.remove('fa-volume-mute');
            volumeIcon.classList.add('fa-volume-up');
        }
    }

    // Add mouse events to drag the volume dot or click on the bar to change the volume
    volumeDot.addEventListener('mousedown', (event) => {
        event.preventDefault();
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', () => {
            document.removeEventListener('mousemove', onMouseMove);
        });
    });

    volumeContainer.addEventListener('click', updateVolume);

    function onMouseMove(event) {
        updateVolume(event);
    }
}

// Initialize the toggleMute function
toggleMute();

document.querySelector('.icon-plus').addEventListener('click', function(e) {
    e.stopPropagation();
    const modal = document.querySelector('.create-options-modal');
    const icon = document.querySelector('.toggle-icon');
    
    if (modal.style.display === 'block') {
      modal.style.display = 'none';
      icon.classList.replace('fa-times', 'fa-plus'); // Switch back to plus
      icon.style.color = 'grey'; // Reset color
    } else {
      modal.style.display = 'block';
      icon.classList.replace('fa-solid', 'fa-x'); // Switch to X
      icon.style.color = '#1DB954'; // Make it green like other icons
    }
  });
  
  // Close modal when clicking outside
  document.addEventListener('click', function() {
    const modal = document.querySelector('.create-options-modal');
    const icon = document.querySelector('.toggle-icon');
    
    if (modal.style.display === 'block') {
      modal.style.display = 'none';
      icon.classList.replace('fa-times', 'fa-plus'); // Reset to plus
      icon.style.color = 'grey';
    }
  });
  
  // Prevent modal from closing when clicking inside it
  document.querySelector('.create-options-modal').addEventListener('click', function(e) {
    e.stopPropagation();
  });


window.onload = function() {
var messageBox = document.getElementById('flash-message');
if (messageBox) {
    // Hide it after 3 seconds
    setTimeout(function() {
        messageBox.style.display = 'none';
    }, 1000);
}
};



  let currentPlaylistId = null;

  document.addEventListener('DOMContentLoaded', () => {
    // Delegate right‑click on any playlist button
    document.body.addEventListener('contextmenu', e => {
      const btn = e.target.closest("[id^='playlist_info_']");
      if (!btn) return; // not on a playlist
      e.preventDefault();

      currentPlaylistId = btn.id.replace('playlist_info_', '');
      const menu = document.getElementById('playlistContextMenu');
      menu.style.top  = e.pageY + 'px';
      menu.style.left = e.pageX + 'px';
      menu.style.display = 'block';
    });

    // Hide menu on any click outside
    document.addEventListener('click', () => {
      document.getElementById('playlistContextMenu').style.display = 'none';
    });




  });
  function openPlaylistContextMenu(event, id) {
    event.preventDefault();
    console.log("Right click → Show context menu for:", id);
    
    // example: position and show your context menu
    const menu = document.getElementById("playlistContextMenu");
    menu.style.top = event.pageY + "px";
    menu.style.left = event.pageX + "px";
    menu.style.display = "block";
  
    // optionally set a hidden input or variable with the current id
    document.getElementById("delete_id").value = id;
};


function validateForm() {
    var fileInput = document.getElementById('fileInput');
    var file = fileInput.files[0];
    var previewImage = document.getElementById('previewImage'); // Get the image preview element
    var errorMessage = "Veuillez sélectionner une image pour la playlist."; // Default error message

    console.log(fileInput);
    console.log(file);

    // Check that an image file is selected
    if (!file) {
        previewImage.style.display = 'block'; // Show the preview image (error display)
        previewImage.src = ''; // Clear the previous image
        previewImage.alt = errorMessage; // Set the error message as alt text
        previewImage.style.backgroundColor = 'red'; // Set background color for error
        previewImage.style.display = 'flex'; // Display error in image container
        previewImage.style.alignItems = 'center'; // Center the text
        previewImage.style.justifyContent = 'center'; // Center the text horizontally
        previewImage.style.color = 'white'; // Text color white
        previewImage.style.fontSize = '14px'; // Adjust the font size

        // Reset the error after 5 seconds (5000 milliseconds)
        setTimeout(function() {
            previewImage.style.display = 'none'; // Hide the preview image
            previewImage.src = ''; // Clear any image
            previewImage.alt = "Image Preview"; // Reset the alt text
            previewImage.style.backgroundColor = ''; // Reset the background color
            previewImage.style.color = ''; // Reset text color
            previewImage.style.fontSize = ''; // Reset font size
        }, 5000); // Reset after 5 seconds

        return false; // Prevent form submission
    }

    // Check that the image type is JPG, PNG, or JPEG
    var allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
        previewImage.style.display = 'block'; // Show the preview image (error display)
        previewImage.src = ''; // Clear the previous image
        previewImage.alt = "Le fichier image doit être au format JPG ou PNG."; // Set error message as alt text
        previewImage.style.backgroundColor = 'red'; // Set background color for error
        previewImage.style.display = 'flex'; // Display error in image container
        previewImage.style.alignItems = 'center'; // Center the text
        previewImage.style.justifyContent = 'center'; // Center the text horizontally
        previewImage.style.color = 'white'; // Text color white
        previewImage.style.fontSize = '14px'; // Adjust the font size

        // Reset the error after 5 seconds (5000 milliseconds)
        setTimeout(function() {
            previewImage.style.display = 'none'; // Hide the preview image
            previewImage.src = ''; // Clear any image
            previewImage.alt = "Image Preview"; // Reset the alt text
            previewImage.style.backgroundColor = ''; // Reset the background color
            previewImage.style.color = ''; // Reset text color
            previewImage.style.fontSize = ''; // Reset font size
            document.getElementById("defaultIcon").style.display = 'block'; // Show the default icon again
        }, 3000); // Reset after 5 seconds

        return false; // Prevent form submission
    }

    // If everything is valid, submit the form
    var form = document.getElementById('playlistForm');
    form.submit();  // This will submit the form to update-playlist.php
    return true;
}


document.getElementById('likedSongsButton').addEventListener('click', function() {
    const likedSongs = document.getElementById('likedsongs');
    const playlistsong = document.getElementById('playlist_song');
    const box_liked_song = document.getElementById('box_liked_song');
    const box_img_song = document.getElementById('box_img_song');
    const box_recomandé=document.getElementById('box_recomandé');
    const coverButton = document.querySelector('button[onclick="bocouvrir()"]');

    // Show the likedSongs section
    likedSongs.style.display = 'block';

    // Hide the playlistsong section
    playlistsong.style.display = 'none';
    box_recomandé.style.display = 'none';

    // Change background color to red
    box_liked_song.style.background = 'linear-gradient(to top, blue, purple)';
    box_img_song.style.background = 'linear-gradient(to bottom, blue, purple)'; // Change background color of the image box

    if (coverButton) {
        coverButton.disabled = true;
        coverButton.style.pointerEvents = 'none';
    }
    
});

console.log("buttonplaylist",document.querySelectorAll('.buttonplaylist'));
document.querySelectorAll('.buttonplaylist').forEach(button => {
    button.addEventListener('click', function() {
        const likedSongs = document.getElementById('likedsongs');
        const playlistsong = document.getElementById('playlist_song');
        const box_liked_song = document.getElementById('box_liked_song');
        const box_img_song = document.getElementById('box_img_song');
        const coverButton = document.querySelector('button[onclick="bocouvrir()"]');
        const box_recomandé=document.getElementById('box_recomandé');


        // Hide likedSongs section
        likedSongs.style.display = 'none';

        // Show playlistsong section
        playlistsong.style.display = 'block';
        box_recomandé.style.display = 'block';
        // Change background color to green
        box_liked_song.style.background = 'linear-gradient(to top, rgb(93, 93, 93), rgb(62, 62, 62))';
        box_img_song.style.background = 'linear-gradient(to bottom, rgb(93, 93, 93), rgb(62, 62, 62))'; // Change background color of the image box       
    
    
        if (coverButton) {
            coverButton.disabled = false;
            coverButton.style.pointerEvents = 'auto';
            coverButton.style.opacity = '1';
        }

    });
});


document.addEventListener("DOMContentLoaded", function () {
    const searchInput1 = document.getElementById("search-playlist1");
    const optionContainer = document.querySelector(".option"); // restrict scope

    searchInput1.addEventListener("input", function () {
        const query = this.value.toLowerCase();

        optionContainer.querySelectorAll(".playlist-item").forEach(item => {
            const button = item.querySelector(".buttonplaylist");

            if (!button) return; // skip if no button inside this item

            const originalText = button.getAttribute("data-original") || button.textContent;
            const lowerText = originalText.toLowerCase();

            if (lowerText.includes(query)) {
                item.style.display = "flex";

                // Highlight matching part
                const start = lowerText.indexOf(query);
                const end = start + query.length;

                const highlighted = originalText.substring(0, start)
                    + '<span style="background-color: green; color: black;">'
                    + originalText.substring(start, end)
                    + '</span>'
                    + originalText.substring(end);

                button.innerHTML = highlighted;

                // Store original in data attribute if not already
                if (!button.hasAttribute("data-original")) {
                    button.setAttribute("data-original", originalText);
                }
            } else {
                item.style.display = "none";
                button.innerHTML = originalText;
            }

            if (query === "") {
                item.style.display = "flex";
                button.innerHTML = originalText;
            }
        });
    });
});


document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search-playlist");
    const option2 = document.getElementById("option2");

    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();
        const playlistItems = option2.querySelectorAll(".playlist-item");

        playlistItems.forEach(item => {
            const button = item.querySelector(".buttonplaylist");

            if (!button) return;

            const originalText = button.getAttribute("data-original") || button.textContent;
            const lowerText = originalText.toLowerCase();

            if (lowerText.includes(query)) {
                item.style.display = "flex";

                // Highlight match
                const start = lowerText.indexOf(query);
                const end = start + query.length;

                const highlighted = originalText.substring(0, start)
                    + '<span style="background-color: green; color: black;">'
                    + originalText.substring(start, end)
                    + '</span>'
                    + originalText.substring(end);

                button.innerHTML = highlighted;

                if (!button.hasAttribute("data-original")) {
                    button.setAttribute("data-original", originalText);
                }
            } else {
                item.style.display = "none";
                button.innerHTML = originalText;
            }

            if (query === "") {
                item.style.display = "flex";
                button.innerHTML = originalText;
            }
        });
    });
});


// Unified function to load playlist and send playlist ID
function handlePlaylistRequest(playlistId, isLoad = true) {
    const url = 'avec_connexion.php';
    const bodyData = new URLSearchParams({ playlist_id: playlistId });

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: bodyData
    })
    .then(response => response.json())
    .then(data => {
        const playlistDiv = document.getElementById('playlist_song');
        const recomandedDiv = document.getElementById('recomanded_song');



        if (isLoad) {
            // ✅ Always update content, even if it's empty
            playlistDiv.innerHTML = data.playlist || "<p style='color:white;'></p>";
            playlistDiv.style.display = 'block';

            recomandedDiv.innerHTML = data.recommendations || "<p style='color:white;'>No recommendations.</p>";
            recomandedDiv.style.display = 'block';
        } else {
            if (data && typeof data === 'string') {
                playlistDiv.innerHTML = data;
                playlistDiv.style.display = 'block';
            } else {
                alert("Error: Unexpected response format from PHP.");
            }
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
    });
}




document.querySelectorAll('.albums-container').forEach(button => {
    button.addEventListener('click', function() {

        const box_liked_song = document.getElementById('box_liked_song');
        const box_img_song = document.getElementById('box_img_song');
        const coverButton = document.querySelector('button[onclick="bocouvrir()"]');


        // Change background color to gray gradient
        box_liked_song.style.background = 'linear-gradient(to top, rgb(93, 93, 93), rgb(62, 62, 62))';
        box_img_song.style.background = 'linear-gradient(to bottom, rgb(93, 93, 93), rgb(62, 62, 62))';
        
        if (coverButton) {
            coverButton.disabled = true;
            coverButton.style.pointerEvents = 'none';
        }
        // Enable or show the photo upload section
       
    });
});



function addSongToPlaylist(songId, playlistId) {
    console.log("Adding song with id:", songId, "to playlist with id:", playlistId);

    // Send the song and playlist IDs to the server using fetch
    fetch('/projetweb/View/tunify_avec_connexion/music/add_to_playlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            song_id: songId,
            playlist_id: playlistId
        })
    })
    .then(response => response.text())  // Handle response from the server
    .then(data => {
        console.log("Server response:", data);
        location.reload();  // <<== Simple refresh after success
        // Optionally, you can add some UI feedback to show the song was added successfully
    })
    .catch(error => {
        console.error("Error adding song to playlist:", error);
    });
}


document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function(event) {
        const button = event.target.closest('.addSongButton');
        if (button) {
            const songId = button.dataset.songId;
            const playlistId = document.getElementById("playlist_click_id").value;
            
            console.log("Clicked:", playlistId, songId);

            addSongToPlaylist(songId, playlistId);
        }
    });
});


function delete_from_playlist(songId, playlistId) {
    console.log("Deleting song with ID:", songId, "from playlist with ID:", playlistId);

    fetch('/projetweb/View/tunify_avec_connexion/music/delete_from_playlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            song_id: songId,
            playlist_id: playlistId
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log("Server response:", data);
        // You can optionally reload or update the UI here if needed
        location.reload();
    })
    .catch(error => {
        console.error("Error deleting song from playlist:", error);
    });
}

function delete_from_liked_song(songId, user_id) {
    console.log("Deleting song with ID:", songId, "for user with ID:", user_id);

    fetch('/projetweb/View/tunify_avec_connexion/music/d_liked_song.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            song_id: songId,
            user_id: user_id
        })
    })
    .then(response => response.text())
    .then(data => {
        console.log("Server response:", data);

        location.reload();
    })
    .catch(error => {
        console.error("Error deleting song from liked songs:", error);
    });
}


document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', function(event) {
        const button = event.target.closest('.deleteSongButton');
        if (button) {
            const songId = button.dataset.songId;
            const playlistId = document.getElementById("playlist_click_id").value;
            
            console.log("Clicked:", playlistId, songId);

            delete_from_playlist(songId, playlistId);
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('tr').forEach(row => {
        const numberSpan = row.querySelector('.song-number');
        if (numberSpan) {
            row.addEventListener('mouseover', () => {
                numberSpan.innerHTML = "<i class='fa-solid fa-play'></i>";
            });

            row.addEventListener('mouseout', () => {
                numberSpan.innerHTML = numberSpan.dataset.number;
            });
        }
    });
});



// Create the offline message element
const offlineMessage = document.createElement('div');
offlineMessage.classList.add('offline-message');
offlineMessage.style.display = 'none'; // Initially hidden
offlineMessage.style.position = 'fixed';
offlineMessage.style.top = '40%';
offlineMessage.style.left = '50%';
offlineMessage.style.transform = 'translate(-50%, -50%)';
offlineMessage.style.backgroundColor = '#222';
offlineMessage.style.color = '#fff';
offlineMessage.style.padding = '20px';
offlineMessage.style.borderRadius = '10px';
offlineMessage.style.boxShadow = '0 0 10px rgba(0,0,0,0.5)';
offlineMessage.innerHTML = `
    <p style="margin-bottom: 10px;">Vous êtes hors ligne. Veuillez vérifier votre connexion internet.</p>
    <button id="retryButton">Ressayer</button>
`;

document.body.appendChild(offlineMessage);

// Retry button logic
document.getElementById('retryButton').addEventListener('click', () => {
    if (navigator.onLine) {
        offlineMessage.style.display = 'none';
    } else {
        alert("Toujours hors ligne. Veuillez vérifier votre connexion.");
    }
});

let currentSong = null;
let currentAudio = document.getElementById("audioPlayer");
let currentPlaybackPosition = 0;

function playSongplaylist(row) {
    const songTitle = row.querySelector('.song-title');
    const songNumber = row.querySelector('.song-number');
    const playPauseButton = document.getElementById('playPause');
    const songURL = row.getAttribute('data-song-url');

    if (currentSong === row) {
        if (currentAudio.paused) {
            currentAudio.currentTime = currentPlaybackPosition;
            currentAudio.play();
            updateSongState(songNumber, songTitle, playPauseButton, 'green');
            updatePlaybackControls(currentAudio);
        } else {
            currentPlaybackPosition = currentAudio.currentTime;
            currentAudio.pause();
            resetSongState(songNumber, songTitle, playPauseButton);
            updatePlaybackControls(currentAudio);
        }
        return;
    }

    if (currentAudio.src) {
        currentAudio.pause();
        if (currentSong) {
            resetSongState(currentSong.querySelector('.song-number'), currentSong.querySelector('.song-title'), playPauseButton);
        }
    }

    // --- Play from online or fallback to cache if offline ---
    if (navigator.onLine) {
        offlineMessage.style.display = 'none';
        currentAudio.src = songURL;
        currentAudio.play();
    } else {
        caches.match(songURL).then(cachedResponse => {
            if (cachedResponse) {
                cachedResponse.blob().then(blob => {
                    const offlineURL = URL.createObjectURL(blob);
                    currentAudio.src = offlineURL;
                    currentAudio.play();
                });
            } else {
                offlineMessage.style.display = 'block';
                return;
            }
        });
    }

    currentSong = row;
    songNumber.innerHTML = '<i class="fa-solid fa-pause" style="font-size:13px; color:green;"></i>';
    updateSongState(songNumber, songTitle, playPauseButton, 'green');
    updatePlaybackControls(currentAudio);
    updateSongDetails(row);

    currentAudio.onended = () => {
        const nextSong = getNextSong(row);
        if (nextSong) {
            const nextTitle = nextSong.querySelector('.song-title')?.textContent;
            const nextId = nextSong.getAttribute('data-song-id');
            console.log(`Next song title: ${nextTitle}, ID: ${nextId}`);
        } else {
            console.log("melowel")
            const firstSong = document.querySelector('.song-row');
            const nowPlaying = playSongplaylist(firstSong);
            console.log(`Now playing: ${nowPlaying}`);

        }
        
        
    };


    const songId = row.getAttribute('data-song-id'); // Make sure your row has this attribute
    if (songId) {
        const xhr2 = new XMLHttpRequest();
        xhr2.open('POST', '/projetweb/View/tunify_avec_connexion/music/realtime_data.php', true);
        xhr2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr2.send('song_idd=' + encodeURIComponent(songId));
    }
    
    

}

// Helper function to update the song state (highlighting song title and number)
function updateSongState(songNumber, songTitle, playPauseButton, color) {
    if(songTitle) songTitle.style.color = color;
    if(songNumber) songNumber.style.color = color;
    if(playPauseButton)playPauseButton.innerHTML = `<i class="fa-solid fa-pause" style="font-size:24px; color:white;"></i>`;
    
}

// Helper function to reset the song state (reverting title, number, and button)
function resetSongState(songNumber, songTitle, playPauseButton) {
    if (songTitle) songTitle.style.color = 'white'; // Revert title color to white
    if (songNumber) songNumber.innerHTML = songNumber.dataset.number; // Revert to song number
    if (songNumber) songNumber.style.color = 'white'; // Revert song number color to white
    if (playPauseButton) playPauseButton.innerHTML = '<i class="fa-solid fa-play" style="font-size:24px; color:white;"></i>';
}

// Helper function to get the next song row
function getNextSong(currentRow) {
    const allSongs = Array.from(document.querySelectorAll('.song-row'));
    const index = allSongs.indexOf(currentRow);
    return index !== -1 && index < allSongs.length - 1 ? allSongs[index + 1] : null;
}



// Helper function to get the previous song row
function getPrevSong(currentRow) {
    const allSongs = document.querySelectorAll('.song-row');
    let prevSong = null;

    for (let i = 0; i < allSongs.length; i++) {
        if (allSongs[i] === currentRow) {
            prevSong = allSongs[i - 1] || allSongs[allSongs.length - 1]; // Go to previous or loop to the last
            break;
        }
    }
    return prevSong;
}

function updatePlaybackControls(song) {
    const playPauseButton = document.getElementById('playPause');
    const shuffleButton = document.getElementById('shuffle');
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');
    const repeatButton = document.getElementById('repeat');
    const currentTimeDisplay = document.getElementById('current-time');
    const totalTimeDisplay = document.getElementById('total-time');
    const progressBar = document.querySelector('.progress-bar');
    const progressCurrent = document.querySelector('.progress-current');

    // Enable/Disable buttons
    shuffleButton.disabled = false;
    repeatButton.disabled = false;

    playPauseButton.onclick = function () {
        if (song.paused) {
            song.play();
            playPauseButton.innerHTML = '<i class="fas fa-pause"></i>';
            updateSongState(currentSong.querySelector('.song-number'), currentSong.querySelector('.song-title'), playPauseButton, 'green');
        } else {
            song.pause();
            playPauseButton.innerHTML = '<i class="fas fa-play"></i>';
            resetSongState(currentSong.querySelector('.song-number'), currentSong.querySelector('.song-title'), playPauseButton);
        }
    };

    song.onloadedmetadata = function () {
        totalTimeDisplay.innerText = formatTime(song.duration);
    };

    song.ontimeupdate = function () {
        currentTimeDisplay.innerText = formatTime(song.currentTime);
        const progressPercent = (song.currentTime / song.duration) * 100;
        progressCurrent.style.width = progressPercent + '%';
    };

    progressBar.onclick = function (event) {
        const clickPosition = event.offsetX;
        const progressWidth = progressBar.offsetWidth;
        const newTime = (clickPosition / progressWidth) * song.duration;
        song.currentTime = newTime;
    };

    function formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds < 10 ? '0' + remainingSeconds : remainingSeconds}`;
    }

    nextButton.onclick = function () {
        const nextSong = getNextSong(currentSong);
        console.log("nextsong",nextSong);
        if (nextSong) {
            playSongplaylist(nextSong);
        }else{
            const firstSong = document.querySelectorAll('.song-row')[1];
            playSongplaylist(firstSong);
        }
    };

    prevButton.onclick = function () {
        const prevSong = getPrevSong(currentSong);
        if (prevSong) {
            playSongplaylist(prevSong);
        }
    };

    repeatButton.onclick = function () {
        song.currentTime = 0;
        song.play();
    };
}

function updateSongDetails(row) {
    // Get elements where details will be updated
    const songCover = document.getElementById('song-cover');
    const songTitle = document.getElementById('song-title');
    const songArtist = document.getElementById('song-artist');
    const songIdBox = document.getElementById('song_id_box8ne');
    const songLikeIcon = document.getElementById('icon_id');

    // Retrieve the song details from the clicked row
    const songNumber = row.querySelector('.song-number');
    const song = row.querySelector('.song-title') ? row.querySelector('.song-title').innerText : 'Unknown Song';
    const artist = row.getAttribute('data-song-artiste');
    const songId = row.getAttribute('data-song-id') || ''; // Song ID from data attribute
    const coverUrl = row.getAttribute('data-song-cover') || ''; // Cover URL from data attribute

    // Update the song cover image
    if (songCover) {
        songCover.src = coverUrl || 'default-cover.jpg'; // Fallback to a default image if no cover URL
    }

    // Update the song title and artist
    if (songTitle) {
        songTitle.innerText = song;
    }
    if (songArtist) {
        songArtist.innerText = artist;
    }

    // Update the song ID for the form (hidden input)
    if (songIdBox) {
        songIdBox.value = songId;
    }

    if (songLikeIcon) {
        songLikeIcon.style.display = 'block'; // Show the like button
        songLikeIcon.innerHTML = '<i class="fa-solid fa-circle-check"></i>'; // Change the icon
        songLikeIcon.style.color = 'green'; // Set the color to green when liked
    }

    // Optionally, highlight the song in the playlist (style updates)
    const playlistItems = document.querySelectorAll('.playlist-item');
    playlistItems.forEach(item => {
        item.style.backgroundColor = ''; // Reset all items' background
        if (item.querySelector('.song-title') && item.querySelector('.song-title').innerText === song) {
            item.style.backgroundColor = '#5f5'; // Highlight the currently playing song
        }
    });
}

function playPauseToggle() {
    // Get the first song element in the playlist
    const firstSong = document.querySelector('.song-row'); // Adjust if necessary to target the right song
    const playPauseButton = document.getElementById('playPause');
    const tooltipText = playPauseButton.querySelector('.tooltip-text');
    const icon = playPauseButton.querySelector('i');
    const audio = document.getElementById('currentAudio'); // Make sure this is the correct ID for your audio element

    // Call the playSongplaylist function to play the first song
    playSongplaylist(firstSong);

    if (audio.paused) {
        // Play the song
        audio.play();
        icon.classList.remove('fa-play');
        icon.classList.add('fa-pause');
        tooltipText.textContent = 'Pause'; // Change tooltip text when playing
    } else {
        // Pause the song
        audio.pause();
        icon.classList.remove('fa-pause');
        icon.classList.add('fa-play');
        tooltipText.textContent = 'Lecture'; // Change tooltip text when paused
    }
}


// Get the audio element (make sure to replace 'currentAudio' with your actual audio ID)
const audioPlayer = document.getElementById('audioPlayer');  // Change 'audioPlayer' to the actual ID of your audio element

// Get the volume dot and volume icon
const volumeDot = document.querySelector('.volume-dot');
const volumeCurrent = document.querySelector('.volume-current');
const volumeIcon = document.getElementById('volume-icon');

// Get the fullscreen icon
const fullscreenIcon = document.getElementById('fullscreen-icon');

// Set initial volume state
let isFullscreen = false;

// Function to update volume and icon
function updateVolume(e) {
    const volumeBar = document.querySelector('.volume-bar');
    const rect = volumeBar.getBoundingClientRect(); // More accurate than offsetLeft
    const volumeWidth = rect.width;
    const offsetX = e.clientX - rect.left;

    // Clamp between 0 and width
    const volume = Math.min(Math.max(0, offsetX), volumeWidth);
    const volumePercentage = volume / volumeWidth;

    // Set the volume (make sure audioPlayer is defined and is a valid audio element)
    if (!isNaN(volumePercentage) && isFinite(volumePercentage)) {
        audioPlayer.volume = volumePercentage;

        // Update UI
        volumeDot.style.left = `${volumePercentage * volumeWidth - 7.5}px`;
        volumeCurrent.style.width = `${volumePercentage * 100}%`;

        // Update icon
        if (volumePercentage === 0) {
            volumeIcon.classList.remove('fa-volume-up', 'fa-volume-down');
            volumeIcon.classList.add('fa-volume-mute');
        } else if (volumePercentage < 0.5) {
            volumeIcon.classList.remove('fa-volume-up', 'fa-volume-mute');
            volumeIcon.classList.add('fa-volume-down');
        } else {
            volumeIcon.classList.remove('fa-volume-down', 'fa-volume-mute');
            volumeIcon.classList.add('fa-volume-up');
        }
    } else {
        console.warn("Invalid volume value:", volumePercentage);
    }
}


// Add event listener to make the volume dot draggable
volumeDot.addEventListener('mousedown', (e) => {
    // Prevent the default action (e.g., text selection)
    e.preventDefault();

    // Handle dragging logic
    const onMouseMove = (moveEvent) => {
        updateVolume(moveEvent);
    };

    // Stop dragging on mouse up
    const onMouseUp = () => {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
    };

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
});

// Fullscreen toggle
function toggleFullscreen() {
    if (!isFullscreen) {
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) { // Firefox
            document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari, Opera
            document.documentElement.webkitRequestFullscreen();
        } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
            document.documentElement.msRequestFullscreen();
        }
        fullscreenIcon.classList.remove('fa-expand');
        fullscreenIcon.classList.add('fa-compress');
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) { // Firefox
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) { // Chrome, Safari, Opera
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) { // IE/Edge
            document.msExitFullscreen();
        }
        fullscreenIcon.classList.remove('fa-compress');
        fullscreenIcon.classList.add('fa-expand');
    }

    // Toggle fullscreen state
    isFullscreen = !isFullscreen;
}

// Add event listener to fullscreen icon
fullscreenIcon.addEventListener('click', toggleFullscreen);

// Initial call to set volume
updateVolume({ clientX: 100 });




const playPauseButton = document.getElementById("playPauseButton");

// Get the audio element
const currentAudi = document.getElementById("audioPlayer");

// Flag to track if the song has started
let songStarted = false;

// Toggle play/pause icon when clicked
playPauseButton.addEventListener("click", function() {
    if (!songStarted && currentAudi.paused) {
        // First click - Play the audio from the beginning (only if no song is playing)
        const firstSong = document.querySelector('.song-row'); // Get the first song element
        playSongplaylist(firstSong);  // Play the first song
        
        currentAudi.play();  // Start playing the song
        // Change the button icon to pause
        playPauseButton.innerHTML = '<i class="fa-solid fa-circle-pause" style="color:black; background-color:green; border-radius:50%; padding:10px; font-size:30px;"></i>';
        
        // Mark song as started
        songStarted = true;
    } else if (currentAudi.paused) {
        // If the audio is paused, resume it from the current position
        currentAudi.play();
        // Change the button icon to pause
        playPauseButton.innerHTML = '<i class="fa-solid fa-circle-pause" style="color:black; background-color:green; border-radius:50%; padding:10px; font-size:30px;"></i>';
    } else {
        // If the audio is playing, pause it and store the current position
        currentAudi.pause();
        // Change the button icon to play
        playPauseButton.innerHTML = '<i class="fa-solid fa-circle-play" style="color:black; background-color:green; border-radius:50%; padding:10px; font-size:30px;"></i>';
    }
});


document.getElementById("shareButton").addEventListener("click", function() {
    const playlistID = document.getElementById("id_playlist").value;
    const user_id = document.getElementById("id_user").value;
    
    // Include both the playlistID and user_id in the shareable link
    const shareLink = `http://localhost/projetweb/View/tunify_avec_connexion/avec_connexion.php?id=${playlistID}&user_id=${user_id}`;

    const shareLinkInput = document.getElementById("shareLinkInput");
    shareLinkInput.value = shareLink;

    // Show the share box
    document.getElementById("shareBox").style.display = 'block';
});


// Handle copying the link to the clipboard
document.getElementById("copyButton").addEventListener("click", function() {
    const shareLinkInput = document.getElementById("shareLinkInput");
    const resultDiv = document.getElementById("result");

    shareLinkInput.select();
    shareLinkInput.setSelectionRange(0, 99999); // For mobile

    document.execCommand("copy");

    // Show success message
    resultDiv.innerText = "Copied successfully!";
    resultDiv.style.color = "green";
    resultDiv.style.marginTop = "10px";

    // After 2 seconds, hide the shareBox
    setTimeout(function() {
        document.getElementById("shareBox").style.display = 'none';
        resultDiv.innerText = ""; // Clear the message
    }, 2000); // 2000 milliseconds = 2 seconds
});
document.getElementById("shareButton2").addEventListener("click", function() {
    const shareBox2 = document.getElementById("shareBox2");

    // Toggle the visibility
    if (shareBox2.style.display === "none" || shareBox2.style.display === "") {
        shareBox2.style.display = "block"; // Show
    } else {
        shareBox2.style.display = "none";  // Hide if already shown
    }
});

console.log("Script loaded");

window.addEventListener('DOMContentLoaded', () => {
    const downloadButton = document.querySelector('.download-button');

    if (!downloadButton) return; // Safeguard if button not found

    const progressRing = downloadButton.querySelector('.progress-ring');

    downloadButton.addEventListener('click', function () {
        // Show the loading circle and start the animation
        progressRing.classList.add('show'); // This will display the progress ring and start the animation

        const icon = downloadButton.querySelector('i');
        icon.classList.add('spinner'); // You can add any additional spinner class for animation if you want

        // Get playlist name (you can change this if needed)
        const playlistTitleElement = document.querySelector('.playlist-title');
        let playlistName = 'playlist_songs';

        if (playlistTitleElement) {
            playlistName = playlistTitleElement.textContent.trim().replace(/[^a-z0-9]/gi, '_');
        }

        // Create new JSZip instance
        const zip = new JSZip();
        const songs = [];

        // Loop through all songs in the playlist
        document.querySelectorAll('.song-row').forEach(row => {
            const songURL = row.getAttribute('data-song-url');
            const songTitle = row.getAttribute('data-song-title');
            const songName = songTitle ? songTitle + '.mp3' : 'unknown_song.mp3';
            const songCover = row.getAttribute('data-song-cover');
            const songArtist = row.getAttribute('data-song-artiste');

            console.log('Song Name:', songName);

            songs.push({ name: songName, url: songURL, cover: songCover, artist: songArtist });
        });

        if (songs.length === 0) {
        
            progressRing.classList.remove('show'); // Hide animation if no songs found
            return;
        }

        // Add songs to the ZIP folder
        const folder = zip.folder(playlistName);
        let songsAdded = 0;

        // Function to fetch each song and add it to the ZIP
        function addSongToZip(song, callback) {
            fetch(song.url)
                .then(response => response.blob())
                .then(blob => {
                    folder.file(song.name, blob);
                    callback();
                })
                .catch(error => {
                    console.error('Error downloading song:', song.name, error);
                    callback();
                });
        }

        // Loop through songs and add them to the ZIP
        songs.forEach(song => {
            addSongToZip(song, () => {
                songsAdded++;
                if (songsAdded === songs.length) {
                    // Once all songs are added, generate the ZIP
                    zip.generateAsync({ type: 'blob' }).then(content => {
                        saveAs(content, playlistName + '.zip');
                        progressRing.classList.remove('show'); // Hide loading animation after download is ready
                    });
                }
            });
        });
    });
});


function playSongsearsh(path, title, artist, cover = null,songId) {
    isFromPlaylist = false;
    const fullPath = new URL(path, window.location.href).href;
    if (audio.src === fullPath) {
        if (audio.paused) {
            audio.play();
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
            starticon.style.display = 'inline-block';
        } else {
            audio.pause();
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        }
    } else {
        currentSongPath= path; // Update current song path
    
        audio.src = fullPath;
        audio.play();
    
        // Update UI
        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        starticon.style.display = 'inline-block';
    
        titleEl.textContent = title;
        artistEl.textContent = artist;
        icons.style.display = 'block'; // Hide the icon when a song is playing
       
        song_id_box8ne.value = songId; // Set the song ID for the box8ne
        song_idd.value = songId; // Set the song ID for the box8ne
       
        const songIdElement = document.getElementById('song_idd'); // Ensure your input has id="song_idd"
        if (songIdElement && songIdElement.value) {
            const songIdValue = encodeURIComponent(songIdElement.value);

            const xhr2 = new XMLHttpRequest();
            xhr2.open('POST', '/projetweb/View/tunify_avec_connexion/music/realtime_data.php', true);
            xhr2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr2.send('song_idd=' + songIdValue);
        }

    
    
        if (cover) {
            coverEl.src = cover;
            coverEl.style.display = 'block';
        }

        updateLikeIcon(songId);
    }
   
    
}
const searchInput = document.getElementById('global_search');
const searchBtn = document.getElementById('searchBtn');
const mainDiv = document.getElementById('box2-main');
const mainDiv2 = document.getElementById('box2-expanded3');
const resultsDiv = document.getElementById('resultsDiv');
const playlist_div = document.getElementById('playlist_div');
const photo_upload = document.querySelectorAll('.photo-upload');
const button_artiste = document.getElementById('button_artiste');

// Function to clean and normalize file paths
function cleanPath(path) {
    return path
        .replace(/\\/g, '/')              // backslashes → forward slashes
        .replace(/^C:/, '')               // remove leading "C:"
        .replace(/^\/xampp\/htdocs/, ''); // remove leading "/xampp/htdocs"
}

// Perform search and display results
function performSearch() {
    const query = searchInput.value.trim();

    if (query !== "") {
        // Hide main divs and show results div
        mainDiv.style.display = 'none';
        playlist_div.style.display = 'none';
        resultsDiv.style.display = 'block';
        mainDiv2.style.display = 'block';

        fetch(`search_handler.php?query=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                resultsDiv.innerHTML = "";

                const sectionsWrapper = document.createElement('div');
                sectionsWrapper.style.cssText = `
                    display: flex;
                    gap: 20px;
                    flex-wrap: wrap;
                    align-items: stretch;
                    margin-top: 20px;
                `;

                // === UTILISATEUR OR FIRST SONG ===
                if (data.utilisateurs?.length) {
                    const u = data.utilisateurs[0];
                    const imgUrl = cleanPath(u.image_path);

                    const utilisateurSection = document.createElement('div');
                    utilisateurSection.style.cssText = `
                        flex: 1 1 300px;
                        background: #333;
                        padding: 10px;
                        border-radius: 10px;
                        display: flex;
                        flex-direction: column;
                    `;

                    const utilisateurCard = document.createElement('div');
                    utilisateurCard.style.cssText = `
                        flex:1;
                        display:flex;
                        align-items:center;
                        gap:20px;
                        cursor:pointer;
                    `;

                    utilisateurCard.innerHTML = `
                        <img src="${imgUrl}" alt="User" style="width:100px;height:100px;border-radius:50%;">
                        <div>
                            <p style="color:white;font-size:20px;margin:0">${u.nom_utilisateur}</p>
                            <p style="color:gray;margin:0;">Artiste</p>
                        </div>
                        <button style="
                            margin-left:auto;
                            background:green;
                            color:black;
                            border:none;
                            border-radius:50%;
                            width:50px;
                            height:50px;
                            cursor:pointer;">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    `;

                    utilisateurCard.addEventListener('click', () => {
                        toggleBox3(u.artiste_id, u.nom_utilisateur, imgUrl);
                    });

                    const titre = document.createElement('h3');
                    titre.textContent = 'Meilleur résultat';
                    titre.style.cssText = 'color:white;margin-bottom:10px;';

                    utilisateurSection.appendChild(titre);
                    utilisateurSection.appendChild(utilisateurCard);

                    sectionsWrapper.appendChild(utilisateurSection);
                    }else if (data.chanson?.length) {
                        const song = data.chanson[0];
                        const cover = cleanPath(song.image_path);  // Path for the song cover
                        const path = cleanPath(song.music_path);  // Assuming there's an audio path
                        const title = song.song_title;
                        const artist = song.album_name;  // Default to 'Unknown Artist' if no artist available
                        const songId = song.id;  // Get the song's unique ID

                        console.log(song);
                        console.log(cover,path,title,artist,songId);
                        
                        const songSection = document.createElement('div');
                        songSection.style.cssText = `
                            flex: 1 1 300px;
                            background: #333;
                            padding: 10px;
                            border-radius: 10px;
                            display: flex;
                            flex-direction: column;
                        `;
                    
                        songSection.innerHTML = `
                            <h3 style="color:white;margin-bottom:10px;">Chanson du moment</h3>
                            <div style="flex:1; display:flex; align-items:center; gap:20px;">
                                <img src="${cover}" alt="Song" style="width:100px;height:100px;border-radius:10px;">
                                <div>
                                    <p style="color:white;font-size:20px;margin:0">${title}</p>
                                    <p style="color:gray;margin:0">${song.album_name}</p>
                                </div>
                                <button style="
                                    margin-left:auto;
                                    background:green;
                                    color:white;
                                    border:none;
                                    border-radius:50%;
                                    width:50px;
                                    height:50px;
                                    cursor:pointer;">
                                    <i class="fa-solid fa-play"></i>
                                </button>
                            </div>
                        `;
                    
                        // Add event listener to the songSection
                        songSection.addEventListener('click', () => {
                            playSongsearsh(path,title,artist,cover,songId);
                        });
                        // Append the songSection to the wrapper
                        sectionsWrapper.appendChild(songSection);
                    }
                    

                // === SONG LIST SECTION ===
                if (data.chanson?.length) {
                    const chansonSection = document.createElement('div');
                    chansonSection.style.cssText = `
                        flex: 2 1 500px;
                        background: #222;
                        padding: 10px;
                        border-radius: 10px;
                        display: flex;
                        flex-direction: column;
                    `;
                    chansonSection.innerHTML = `<h3 style="color:white;margin-bottom:10px;">Titres</h3>`;
                
                    const listContainer = document.createElement('div');
                    listContainer.style.cssText = `
                        flex: 1;
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                    `;
                
                    // Inject CSS for hover effect
                    if (!document.getElementById('hover-play-style')) {
                        const style = document.createElement('style');
                        style.id = 'hover-play-style';
                        style.innerHTML = `
                            .song-item {
                                position: relative;
                                transition: background 0.3s;
                            }
                            .song-item:hover {
                                background: #444;
                            }
                            .play-button {
                                position: absolute;
                                right: 15px;
                                top: 50%;
                                transform: translateY(-50%);
                                display: none;
                                background: green;
                                color: white;
                                border: none;
                                border-radius: 50%;
                                width: 35px;
                                height: 35px;
                                cursor: pointer;
                                justify-content: center;
                                align-items: center;
                            }
                            .song-item:hover .play-button {
                                display: flex;
                            }
                        `;
                        document.head.appendChild(style);
                    }
                
                    data.chanson.slice(0, 4).forEach(song => {
                        const cover = cleanPath(song.image_path);
                        const item = document.createElement('div');
                        item.className = 'song-item';
                        item.style.cssText = `
                            display:flex;
                            align-items:center;
                            justify-content: space-between;
                            gap:20px;
                            background: #333;
                            padding: 8px;
                            border-radius: 5px;
                            position: relative;
                        `;
                        item.innerHTML = `
                            <div style="display:flex; align-items:center; gap:15px;">
                                <img src="${cover}" alt="Song" style="width:50px;height:50px;border-radius:5px;">
                                <div>
                                    <p style="color:white;margin:0">${song.song_title}</p>
                                    <p style="color:gray;margin:0">${song.album_name}</p>
                                </div>
                            </div>
                            <div style="color:gray;margin-right:50px;">${song.duree}</div>
                            <button class="play-button">
                                <i class="fa-solid fa-play"></i>
                            </button>
                        `;
                
                        // Add event listener to play song on click
                        item.addEventListener('click', () => {
                            const path = cleanPath(song.music_path);  // Assuming there's an audio path
                            const title = song.song_title;
                            const artist = song.artist_name;
                            const songId = song.id;
                            
                            playSongsearsh(path, title, artist, cover, songId);
                        });
                
                        listContainer.appendChild(item);
                    });
                
                    chansonSection.appendChild(listContainer);
                    sectionsWrapper.appendChild(chansonSection);
                }                

                // === PLAYLIST SECTION ===
                if (data.playlist?.length) {
                    const playlistSection = document.createElement('div');
                    playlistSection.style.cssText = `
                        flex: 2 1 500px;
                        background: #222;
                        padding: 10px;
                        border-radius: 10px;
                        display: flex;
                        flex-direction: column;
                    `;
                    playlistSection.innerHTML = `<h2 style="color:white;margin-bottom:10px;">Playlists</h2>`;
                
                    const playlistContainer = document.createElement('div');
                    playlistContainer.style.cssText = `
                        flex: 1;
                        display: flex;
                        gap: 15px;
                        flex-wrap: wrap;
                    `;
                
                    data.playlist.slice(0, 4).forEach(pl => {
                        const rawImg = cleanPath(pl.img || '')
                            .replace(/^C:/, '')
                            .replace(/\\/g, '/')
                            .replace(/\/xampp\/htdocs/, '');
                
                        const hasImg = Boolean(rawImg);
                
                        const playlistItem = document.createElement('div');
                        playlistItem.className = 'album-item';
                        playlistItem.style.cssText = `
                            background: #333;
                            width: 200px;
                            padding: 10px;
                            border-radius: 10px;
                            display: flex;
                            flex-direction: column;
                            cursor: pointer;
                            position: relative;
                        `;
                
                        let imgHtml;
                        if (hasImg) {
                            imgHtml = `<img src="${rawImg}" class="cover-img" alt="Playlist ${pl.nom}"
                                        onerror="this.src='/assets/default-playlist.jpg'">`;
                        } else {
                            imgHtml = `
                                <div style="background-color: rgb(62, 62, 62); width:200px; height: 240px; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                    <i class="fa-solid fa-music fa-lg" style="color: white; font-size: 40px;"></i>
                                </div>
                            `;
                        }
                
                        playlistItem.innerHTML = `
                            ${imgHtml}
                            <button class="playlist-play-button"><i class="fa-solid fa-play"></i></button>
                            <div style="margin-top:10px;">
                                <p style="color:white;">${pl.nom}</p>
                            </div>
                        `;
                
                        // Add click event for the entire item
                        playlistItem.addEventListener('click', () => {
                            toggleBox3(pl.id, pl.nom, rawImg);
                        });
                
                        // Optional: Add event for just the play button
                        const playBtn = playlistItem.querySelector('.playlist-play-button');
                        playBtn.addEventListener('click', (e) => {
                            e.stopPropagation(); // Prevent triggering main click
                            playPlaylist(pl.id); // Replace with your actual play logic
                        });
                
                        playlistContainer.appendChild(playlistItem);
                    });
                
                    playlistSection.appendChild(playlistContainer);
                    sectionsWrapper.appendChild(playlistSection);
                }
                
                // Inject style once
                if (!document.getElementById('hover-playlist-style')) {
                    const style = document.createElement('style');
                    style.id = 'hover-playlist-style';
                    style.innerHTML = `
                        .album-item {
                            position: relative;
                            transition: background 0.3s;
                        }
                        .album-item:hover {
                            background: #444;
                        }
                        .playlist-play-button {
                            position: absolute;
                            top: 200px;
                            right: 10px;
                            display: none;
                            background: green;
                            color: black;
                            border: none;
                            border-radius: 50%;
                            width: 50px;
                            height: 50px;
                            cursor: pointer;
                            justify-content: center;
                            align-items: center;
                            font-size: 16px;
                        }
                        .album-item:hover .playlist-play-button {
                            display: flex;
                        }
                    `;
                    document.head.appendChild(style);
                }
                                
                if (data.utilisateurs_artiste?.length) {
                    const utilisateurSection = document.createElement('div');
                    utilisateurSection.style.cssText = `
                        flex: 2 1 500px;
                        background: #222;
                        padding: 10px;
                        border-radius: 10px;
                        display: flex;
                        flex-direction: column;
                    `;
                    utilisateurSection.innerHTML = `<h2 style="color:white;margin-bottom:10px;">Utilisateurs</h2>`;
                
                    const utilisateurContainer = document.createElement('div');
                    utilisateurContainer.style.cssText = `
                        flex: 1;
                        display: flex;
                        gap: 15px;
                        flex-wrap: wrap;
                    `;
                
                    data.utilisateurs_artiste.slice(0, 4).forEach(user => {
                        const userImg = cleanPath(user.image_path || '');
                
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        userItem.style.cssText = `
                            background: #333;
                            width: 200px;
                            padding: 10px;
                            border-radius: 10px;
                            display: flex;
                            flex-direction: column;
                            cursor: pointer;
                            align-items: center;
                            position: relative;
                        `;
                
                        userItem.innerHTML = `
                            <img src="${userImg}" class="cover-img" alt="${user.nom_utilisateur}" 
                                 onerror="this.src='/assets/default-user.jpg'" 
                                 style="width:150px;height:150px;border-radius:50%;">
                            <button class="user-play-button"><i class="fa-solid fa-play"></i></button>
                            <div style="margin-top:10px;">
                                <p style="color:white;text-align:center;">${user.nom_utilisateur}</p>
                            </div>
                        `;
                
                        // Click event for whole user item
                        userItem.addEventListener('click', () => {
                            toggleBox3(user.artiste_id, user.nom_utilisateur, userImg);
                        });
                
                        // Optional: Separate click for play button
                        const playBtn = userItem.querySelector('.user-play-button');
                        playBtn.addEventListener('click', (e) => {
                            e.stopPropagation(); // Prevent main click
                            playUserTracks(user.artiste_id); // Replace with your actual play logic
                        });
                
                        utilisateurContainer.appendChild(userItem);
                    });
                
                    utilisateurSection.appendChild(utilisateurContainer);
                    sectionsWrapper.appendChild(utilisateurSection);
                }
                
                // Inject hover button style once
                if (!document.getElementById('hover-user-style')) {
                    const style = document.createElement('style');
                    style.id = 'hover-user-style';
                    style.innerHTML = `
                        .user-item {
                            position: relative;
                            transition: background 0.3s;
                        }
                        .user-item:hover {
                            background: #444;
                        }
                        .user-play-button {
                            position: absolute;
                            top: 210px;
                            right: 12px;
                            display: none;
                            background: green;
                            color: black;
                            border: none;
                            border-radius: 50%;
                            width: 40px;
                            height: 40px;
                            cursor: pointer;
                            justify-content: center;
                            align-items: center;
                            font-size: 16px;
                        }
                        .user-item:hover .user-play-button {
                            display: flex;
                        }
                    `;
                    document.head.appendChild(style);
                }
                if (data.utilisateurs_user?.length) {
                    const utilisateurSection = document.createElement('div');
                    utilisateurSection.style.cssText = `
                        flex: 2 1 500px;
                        background: #222;
                        padding: 10px;
                        border-radius: 10px;
                        display: flex;
                        flex-direction: column;
                    `;
                    utilisateurSection.innerHTML = `<h2 style="color:white;margin-bottom:10px;">Users</h2>`;
                
                    const utilisateurContainer = document.createElement('div');
                    utilisateurContainer.style.cssText = `
                        flex: 1;
                        display: flex;
                        gap: 15px;
                        flex-wrap: wrap;
                    `;
                
                    data.utilisateurs_user.slice(0, 4).forEach(user => {
                        const userImg = cleanPath(user.image_path);
                        const hasImg = user.image_path && user.image_path.trim() !== '';
                        const rawImg = cleanPath(user.image_path);
                    
                        const userItem = document.createElement('div');
                        userItem.className = 'user-item';
                        userItem.style.cssText = `
                            background: #333;
                            width: 200px;
                            padding: 10px;
                            border-radius: 10px;
                            display: flex;
                            flex-direction: column;
                            cursor: pointer;
                            align-items: center;
                            position: relative;
                        `;
                    
                        let imgHtml;
                        if (hasImg) {
                            imgHtml = `<img src="${rawImg}" class="cover-img" alt="User ${user.nom_utilisateur}"
                                        onerror="this.src='/assets/default-user.jpg'" style="width:100%; height:240px; border-radius:5px;">`;
                        } else {
                            imgHtml = `
                                <div style="background-color: rgb(62, 62, 62); width:200px; height: 240px; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-user fa-lg" style="color: white; font-size: 40px;"></i>
                                </div>
                            `;
                        }
                    
                        userItem.innerHTML = `
                            ${imgHtml}
                            <button class="user-play-button"><i class="fa-solid fa-play"></i></button>
                            <div style="margin-top:10px;">
                                <p style="color:white;text-align:center;">${user.nom_utilisateur}</p>
                            </div>
                        `;
                    
                        userItem.addEventListener('click', () => {
                            toggleBox4(user.artiste_id, user.nom_utilisateur, userImg);
                        });
                    
                        const playBtn = userItem.querySelector('.user-play-button');
                        playBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            playUserTracks(user.artiste_id);
                        });
                    
                        utilisateurContainer.appendChild(userItem);
                    });
                    
                    utilisateurSection.appendChild(utilisateurContainer);
                    sectionsWrapper.appendChild(utilisateurSection);
                }
                
                // Inject hover button style once
                if (!document.getElementById('hover-user-style')) {
                    const style = document.createElement('style');
                    style.id = 'hover-user-style';
                    style.innerHTML = `
                        .user-item {
                            position: relative;
                            transition: background 0.3s;
                        }
                        .user-item:hover {
                            background: #444;
                        }
                        .user-play-button {
                            position: absolute;
                            top: 120px;
                            right: 50px;
                            display: none;
                            background: green;
                            color: black;
                            border: none;
                            border-radius: 50%;
                            width: 40px;
                            height: 40px;
                            cursor: pointer;
                            justify-content: center;
                            align-items: center;
                            font-size: 16px;
                        }
                        .user-item:hover .user-play-button {
                            display: flex;
                        }
                    `;
                    document.head.appendChild(style);
                }
                
                

                // Append all sections
                resultsDiv.appendChild(sectionsWrapper);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                resultsDiv.innerHTML = "<p style='color:red;'>Something went wrong. Please try again later.</p>";
            });
    } else {
        mainDiv.style.display = 'block';
        playlist_div.style.display = 'block';
        resultsDiv.style.display = 'none';
        mainDiv2.style.display = 'none';
        }
    }

    // Trigger on typing
    searchInput.addEventListener('input', performSearch);

    // Trigger on click
    searchBtn.addEventListener('click', performSearch);

    // Trigger on Enter key press
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === "Enter") {
            performSearch();
        }
    });

    function loadPlaylists(playlistId) {
        fetch('/projetweb/View/tunify_avec_connexion/music/load_playlists.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'playlist_id=' + encodeURIComponent(playlistId)
        })
        .then(response => response.text())
        .then(html => {
            const playlistDiv = document.getElementById('playlistdiv');
            const playlistDiv2 = document.getElementById('playlist_song');
            const recomandedDiv = document.getElementById('recomanded_song');
            const historique_song = document.getElementById('historique_song');

            playlistDiv2 .style.display = 'none'
            recomandedDiv .style.display = 'none'
            historique_song.style.display = 'none'
    
            // Trim whitespace to accurately check if content is empty
            if (html.trim() === '') {
                playlistDiv.innerHTML = '';
                playlistDiv.style.display = 'none'; // Hide the div if nothing to show
            } else {
                playlistDiv.innerHTML = html;
                playlistDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des playlists :', error);
        });
    }

    
    function loadhistoriquesongs(playlistId) {
        fetch('/projetweb/View/tunify_avec_connexion/music/historiquesongs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'playlist_id=' + encodeURIComponent(playlistId)
        })
        .then(response => response.text())
        .then(html => {
            const playlistDiv = document.getElementById('playlistdiv');
            const playlistDiv2 = document.getElementById('playlist_song');
            const recomandedDiv = document.getElementById('recomanded_song');
            const historique_song = document.getElementById('historique_song');

            playlistDiv2 .style.display = 'none'
            recomandedDiv .style.display = 'none'
    
            // Trim whitespace to accurately check if content is empty
            if (html.trim() === '') {
                historique_song.innerHTML = '';
                historique_song.style.display = 'none'; // Hide the div if nothing to show
            } else {
                historique_song.innerHTML = html;
                historique_song.style.display = 'block'
                
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des playlists :', error);
        });
    }
    function loadhistoriquesongslogs(playlistId) {
        fetch('music/log_songs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'playlist_id=' + encodeURIComponent(playlistId)
        })
        .then(response => response.text())
        .then(html => {
            const playlistDiv = document.getElementById('playlistdiv');
            const playlistDiv2 = document.getElementById('playlist_song');
            const recomandedDiv = document.getElementById('recomanded_song');
            const historique_song = document.getElementById('historique_song');
            const log_song = document.getElementById('log_song');

            playlistDiv2 .style.display = 'none'
            recomandedDiv .style.display = 'none'
    
            // Trim whitespace to accurately check if content is empty
            if (html.trim() === '') {
                log_song.innerHTML = '';
                log_song.style.display = 'none'; // Hide the div if nothing to show
            } else {
                log_song.innerHTML = html;
                log_song.style.display = 'block'
                
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des playlists :', error);
        });
    }
    function loadhistoriquesongs(playlistId) {
        fetch('/projetweb/View/tunify_avec_connexion/music/historiquesongs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'playlist_id=' + encodeURIComponent(playlistId)
        })
        .then(response => response.text())
        .then(html => {
            const playlistDiv = document.getElementById('playlistdiv');
            const playlistDiv2 = document.getElementById('playlist_song');
            const recomandedDiv = document.getElementById('recomanded_song');
            const historique_song = document.getElementById('historique_song');

            playlistDiv2 .style.display = 'none'
            recomandedDiv .style.display = 'none'
    
            // Trim whitespace to accurately check if content is empty
            if (html.trim() === '') {
                historique_song.innerHTML = '';
                historique_song.style.display = 'none'; // Hide the div if nothing to show
            } else {
                historique_song.innerHTML = html;
                historique_song.style.display = 'block'
                
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des playlists :', error);
        });
    }

    function news(playlistId) {
    fetch('news/view_news.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'playlist_id=' + encodeURIComponent(playlistId)
    })
    .then(response => response.text())
    .then(html => {
        const playlistDiv = document.getElementById('playlistdiv');
        const playlistDiv2 = document.getElementById('playlist_song');
        const recomandedDiv = document.getElementById('recomanded_song');
        const historique_song = document.getElementById('historique_song');
        const log_song = document.getElementById('log_song');
        const newsdiv = document.getElementById("news");
        const box_liked_song = document.getElementById("box_liked_song");

        playlistDiv2.style.display = 'none';
        recomandedDiv.style.display = 'none';
        box_liked_song.style.display = 'none';

        // Trim whitespace to accurately check if content is empty
        if (html.trim() === '') {
            newsdiv.innerHTML = '';
            newsdiv.style.display = 'none'; // Hide the div if nothing to show
        } else {
            newsdiv.innerHTML = html;
            newsdiv.style.display = 'block';
  
    

            // Dynamically load a JS file
            loadJSFile('news/app.js');
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des playlists :', error);
    });
}

// Function to dynamically load a JS file
function loadJSFile(filePath) {
    const script = document.createElement('script');
    script.src = filePath;
    script.type = 'text/javascript';
    script.onload = function() {
        console.log(`Script loaded successfully: ${filePath}`);
    };
    script.onerror = function() {
        console.error(`Failed to load script: ${filePath}`);
    };
    document.head.appendChild(script); // Append the script to the head of the document
}
    
