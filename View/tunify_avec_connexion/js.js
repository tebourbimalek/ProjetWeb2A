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
    console.log("Song ID:", songId); // Log the song ID
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
       
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'avec_connexion.php', true); // Replace with your actual PHP file name
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('song_idd=' + encodeURIComponent(song_idd));
    
    
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
    // Determine the request method based on whether we want to load playlist or send playlist ID
    const url = 'avec_connexion.php';
    const bodyData = new URLSearchParams({ playlist_id: playlistId }); // Common body data for POST requests
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: bodyData
    })
    .then(response => response.json()) // Assuming the PHP returns JSON for both use cases
    .then(data => {
        // Handling playlist loading case (if isLoad is true)
        if (isLoad) {
            if (data.playlist && data.recommendations) {
                // Inject the playlist songs HTML into the 'playlist_song' div
                const playlistDiv = document.getElementById('playlist_song');
                playlistDiv.innerHTML = data.playlist;
                playlistDiv.style.display = 'block'; // Make sure the div is visible

                // Inject the recommended songs HTML into the 'recomanded_song' div
                const recomandedDiv = document.getElementById('recomanded_song');
                recomandedDiv.innerHTML = data.recommendations;
                recomandedDiv.style.display = 'block'; // Ensure the div is visible
            }
        } 
        // Handling the case for sending playlist ID to PHP
        else {
            if (data && typeof data === 'string') {
                console.log("Response from PHP:", data);
                
                // Insert the response (HTML content) into the 'playlist_song' div
                const playlistDiv = document.getElementById('playlist_song');
                playlistDiv.innerHTML = data;
                playlistDiv.style.display = 'block';  // Make sure the div is visible
            } else {
                alert("Error: Unexpected response format from PHP.");
            }
        }
    })
    .catch(error => {
        console.error("Error handling playlist request:", error);
        alert("There was an error processing the playlist request. Please try again later.");
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
    fetch('add_to_playlist.php', {
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
    console.log("deleting song with id:", songId, "to playlist with id:", playlistId);

    // Send the song and playlist IDs to the server using fetch
    fetch('delete_from_playlist.php', {
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
        location.reload();
        
    })
    .catch(error => {
        console.error("Error adding song to playlist:", error);
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





let currentSong = null; // Store the current song row
let currentAudio = document.getElementById("audioPlayer"); // Audio element to play the song
let currentPlaybackPosition = 0; // Store the playback position of the current song

function playSongplaylist(row) {
    const songTitle = row.querySelector('.song-title');
    const songNumber = row.querySelector('.song-number');
    const playPauseButton = document.getElementById('playPause');

    // Check if the clicked song is the same as the current one
    if (currentSong === row) {
        if (currentAudio.paused) {
            // Resume song from current playback position
            currentAudio.currentTime = currentPlaybackPosition;
            currentAudio.play();
            updateSongState(songNumber, songTitle, playPauseButton, 'green');
            updatePlaybackControls(currentAudio);
        } else {
            // Pause the song and store the current position
            currentPlaybackPosition = currentAudio.currentTime;
            currentAudio.pause();
            resetSongState(songNumber, songTitle, playPauseButton);
            updatePlaybackControls(currentAudio);
        }
        return;
    }

    // If a song is currently playing, stop it and reset the previous song's state
    if (currentAudio.src) {
        currentAudio.pause();
        resetSongState(currentSong.querySelector('.song-number'), currentSong.querySelector('.song-title'), playPauseButton);
    }

    // Get the URL for the clicked song and play it
    const songURL = row.getAttribute('data-song-url');
    currentAudio.src = songURL;
    currentAudio.play();

    // Update the current song and change the song number to the pause icon
    currentSong = row;
    songNumber.innerHTML = '<i class="fa-solid fa-pause" style="font-size:13px; color:green;"></i>';

    // Change the new song's title color to green
    updateSongState(songNumber, songTitle, playPauseButton, 'green');

    // Update the playback controls and song details for the new song
    updatePlaybackControls(currentAudio);
    updateSongDetails(row);

    // When the song ends, play the next song or loop to the first song if it's the last song
    currentAudio.onended = () => {
        const nextSong = getNextSong(row);
        if (nextSong) {
            playSongplaylist(nextSong);
        } else {
            const firstSong = document.querySelector('.song-row');
            playSongplaylist(firstSong);
        }
    };
}

// Helper function to update the song state (highlighting song title and number)
function updateSongState(songNumber, songTitle, playPauseButton, color) {
    songTitle.style.color = color;
    songNumber.style.color = color;
    playPauseButton.innerHTML = `<i class="fa-solid fa-pause" style="font-size:24px; color:white;"></i>`;
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
    const allSongs = document.querySelectorAll('.song-row');
    let nextSong = null;

    for (let i = 0; i < allSongs.length; i++) {
        if (allSongs[i] === currentRow) {
            nextSong = allSongs[i + 1] || allSongs[0]; // Go to next or loop to the first
            break;
        }
    }
    return nextSong;
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
        if (nextSong) {
            playSongplaylist(nextSong);
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
    const volumeWidth = volumeBar.offsetWidth;
    const volume = Math.min(Math.max(0, e.clientX - volumeBar.offsetLeft), volumeWidth);
    
    const volumePercentage = volume / volumeWidth; // Volume as a percentage (0 to 1)
    
    // Set the volume of the audio
    audioPlayer.volume = volumePercentage;  // Use 'audioPlayer' here instead of 'audio'

    // Update the position of the dot and the volume bar fill
    volumeDot.style.left = `${volumePercentage * volumeWidth - 7.5}px`; // Center dot on click
    volumeCurrent.style.width = `${volumePercentage * 100}%`; // Set volume bar fill width

    // Update the volume icon based on volume level
    if (audioPlayer.volume === 0) {
        volumeIcon.classList.remove('fa-volume-up', 'fa-volume-down');
        volumeIcon.classList.add('fa-volume-mute');
    } else if (audioPlayer.volume < 0.5) {
        volumeIcon.classList.remove('fa-volume-up', 'fa-volume-mute');
        volumeIcon.classList.add('fa-volume-down');
    } else {
        volumeIcon.classList.remove('fa-volume-down', 'fa-volume-mute');
        volumeIcon.classList.add('fa-volume-up');
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
    console.log(downloadButton); // This should log the button

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
const searchInput = document.getElementById('global_search');
const searchBtn = document.getElementById('searchBtn');
const mainDiv = document.getElementById('box2-main');
const mainDiv2 = document.getElementById('box2-expanded3');
const resultsDiv = document.getElementById('resultsDiv');
const playlist_div = document.getElementById('playlist_div');

function performSearch() {
    const query = searchInput.value.trim();
    
    if (query !== "") {
        // Hide main divs and show results div
        mainDiv.style.display = 'none';
        playlist_div.style.display ='none';
        resultsDiv.style.display = 'block';
        mainDiv2.style.display = 'block';
        fetch(`search_handler.php?query=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            resultsDiv.innerHTML = ''; // Clear previous results

            // Display utilisateur results
            if (data.utilisateur && data.utilisateur.length > 0) {
                const utilisateurSection = document.createElement('div');
                utilisateurSection.innerHTML = `
                    <h3 style="color: white;">Meilleur résultat</h3>
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 20px;">
                        <img src="path/to/default-user-image.jpg" alt="User Image" style="width: 100px; height: 100px; border-radius: 50%;">
                        <div>
                            <p style="color: white; font-size: 20px; margin: 0;">${data.utilisateur[0].nom_utilisateur}</p>
                            <p style="color: gray; margin: 0;">Artiste</p>
                        </div>
                        <button style="background-color: green; color: white; border: none; border-radius: 50%; width: 50px; height: 50px; cursor: pointer;">
                            <i class="fa-solid fa-play"></i>
                        </button>
                    </div>
                `;
                resultsDiv.appendChild(utilisateurSection);
            }

            // Display chanson results
            if (data.chanson && data.chanson.length > 0) {
                const chansonSection = document.createElement('div');
                chansonSection.innerHTML = `<h3 style="color: white;">Titres</h3>`;
                data.chanson.forEach(song => {
                    chansonSection.innerHTML += `
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                            <img src="path/to/default-song-image.jpg" alt="Song Image" style="width: 50px; height: 50px; border-radius: 5px;">
                            <div>
                                <p style="color: white; margin: 0;">${song.titre || 'Titre inconnu'}</p>
                                <p style="color: gray; margin: 0;">${song.artiste || 'Artiste inconnu'}</p>
                            </div>
                            <p style="color: gray; margin-left: auto;">${song.duree || '0:00'}</p>
                        </div>
                    `;
                });
                resultsDiv.appendChild(chansonSection);
            }

            // Display playlist results
            if (data.playlist && data.playlist.length > 0) {
                const playlistSection = document.createElement('div');
                playlistSection.innerHTML = `<h3 style="color: white;">Playlists</h3>`;
                data.playlist.forEach(playlist => {
                    playlistSection.innerHTML += `
                        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 10px;">
                            <img src="path/to/default-playlist-image.jpg" alt="Playlist Image" style="width: 50px; height: 50px; border-radius: 5px;">
                            <p style="color: white; margin: 0;">${playlist.nom}</p>
                        </div>
                    `;
                });
                resultsDiv.appendChild(playlistSection);
            }


        })
    } else {
        // If search input is empty, show the main divs
        mainDiv.style.display = 'block';
        mainDiv2.style.display = 'block';
        resultsDiv.style.display = 'none';
    }
}

// Trigger on typing
searchInput.addEventListener('input', () => {
    performSearch();
});

// Trigger on click
searchBtn.addEventListener('click', performSearch);

// Trigger on Enter key press
searchInput.addEventListener('keydown', (e) => {
    if (e.key === "Enter") {
        performSearch();
    }
});