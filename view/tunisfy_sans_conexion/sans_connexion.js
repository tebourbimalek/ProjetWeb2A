window.addEventListener('load', function() {
    showModal();
});

function showModal() {
    const overlay = document.getElementById('modalOverlay');
    overlay.style.display = 'flex';
}

// Close button functionality
document.getElementById('closeBtn').addEventListener('click', function() {
    const overlay = document.getElementById('modalOverlay');
    overlay.style.display = 'none';
    
    // If you want to test quickly, uncomment this to auto-reload
    // setTimeout(function() {
    //     window.location.reload();
    // }, 2000);
});

// Animation for the logo
const logo = document.querySelector('.logo');

function pulseAnimation() {
    logo.style.transform = 'scale(1.05)';
    setTimeout(() => {
        logo.style.transform = 'scale(1)';
    }, 500);
}

// Set initial transition
logo.style.transition = 'transform 0.5s ease-in-out';

// Start animation cycle
setInterval(pulseAnimation, 3000);


// Function to show the modal with either album cover or static image
function showAlbumModal(imagePath) {
    const modalOverlay = document.getElementById('modalOverlay');
    const albumCover = document.getElementById('albumCover');
    
    // Set the image source to the album cover passed in
    albumCover.src = imagePath;
    modalOverlay.addEventListener('click', function(event) {
        if (!modalContent.contains(event.target)) {
            modalOverlay.style.display = 'none';  // Hide modal if clicked outside
        }
    });

    // Display the modal
    modalOverlay.style.display = 'flex';
    
}

// Handle the close button to hide the modal
document.getElementById('closeBtn').addEventListener('click', function() {
    document.getElementById('modalOverlay').style.display = 'none';
});
// On page load, set the static image source with the correct path
// Event listener to close the modal when clicking outside of it
window.onload = function() {
    const modalOverlay = document.getElementById('modalOverlay');
    const albumCover = document.getElementById('albumCover');
    const modalContent = document.querySelector('.modal-container'); // Assuming .modal-container is the modal content div

    // Correct static image path with forward slashes
    albumCover.src = '/projetweb/assets/img/focused_stroy_play.0a7b9c70.png';  // Correct path
    
    // Show the modal by default when the page is loaded
    modalOverlay.style.display = 'flex';

    // Close modal if clicked outside of modal content
    modalOverlay.addEventListener('click', function(event) {
        if (!modalContent.contains(event.target)) {
            modalOverlay.style.display = 'none';  // Hide modal if clicked outside
        }
    });
};



function toggleBox(event) {
    event.preventDefault(); // Prevent default link behavior

    let mainBox = document.getElementById("box2-main");
    let expandedBox = document.getElementById("box2-expanded");

    if (mainBox.style.display === "none") {
        mainBox.style.display = "block";
        expandedBox.style.display = "none";
    } else {
        mainBox.style.display = "none";
        expandedBox.style.display = "block";
    }
}
function toggleBox2(event) {
    event.preventDefault(); // Prevent default link behavior

    let mainBox = document.getElementById("box2-main");
    let expandedBox = document.getElementById("box2-expanded2");

    if (mainBox.style.display === "none") {
        mainBox.style.display = "block";
        expandedBox.style.display = "none";
    } else {
        mainBox.style.display = "none";
        expandedBox.style.display = "block";
    }
}




