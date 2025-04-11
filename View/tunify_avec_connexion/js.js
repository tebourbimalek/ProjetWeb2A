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




let currentSongPath = null;
let songHistory = [];
// Function to play or toggle a song
function playSong(path, title, artist, cover = null, button) {
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
    
        if (cover) {
            coverEl.src = cover;
            coverEl.style.display = 'block';
        }
    
        resetAllButtonsExcept(button);
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








document.getElementById('next').addEventListener('click', function () {
    document.getElementById('hiddenFrame').src = 'avec_connexion.php?next=1';
});


function updateSongInfo(title, artist, cover,path) {
    audio.src = path;
    audio.play();
    document.getElementById('song-cover').src = cover;
    document.getElementById('song-title').textContent = title;
    document.getElementById('song-artist').textContent = artist;
    document.getElementById('playPause').innerHTML = '<i class="fas fa-pause"></i>';
    currentSongPath=    path; // Update current song path
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

function addNewDiv() {
    // Create a new div element
    var newDiv = document.createElement("div");
    newDiv.classList.add("new-div");

    // Add content to the new div (optional)
    newDiv.innerHTML = "<p>This is a new div!</p>";

    // Find the parent container (main div) where you want to insert the new div beside
    var mainDiv = document.getElementById("box2-main");

    // Insert the new div beside the main div
    mainDiv.parentNode.insertBefore(newDiv, mainDiv.nextSibling);
}

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