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









function updateSongInfo(title, artist, cover,path) {
    audio.src = path;
    audio.play();
    document.getElementById('song-cover').src = cover;
    document.getElementById('song-title').textContent = title;
    document.getElementById('song-artist').textContent = artist;
    document.getElementById('playPause').innerHTML = '<i class="fas fa-pause"></i>';
    currentSongPath=    path; // Update current song path
}


document.getElementById('search-input').addEventListener('keyup', function () {
    const query = this.value.trim().toLowerCase();
    const rows = document.querySelectorAll('#transactions-table tbody tr');

    rows.forEach(row => {
        let textFound = false;
        row.querySelectorAll('td').forEach(cell => {
            const text = cell.textContent;
            const lowerText = text.toLowerCase();

            // Nettoyage du contenu (évite double highlighting)
            cell.innerHTML = text;

            if (query && lowerText.includes(query)) {
                const regex = new RegExp(`(${query})`, 'gi');
                cell.innerHTML = text.replace(regex, `<span class="highlight">$1</span>`);
                textFound = true;
            }
        });

        row.style.display = textFound || !query ? '' : 'none';
    });
});

