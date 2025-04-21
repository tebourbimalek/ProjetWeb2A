const addMusicBtn = document.getElementById('add-music-btn');
const addMusicModal = document.getElementById('add-music-modal');
const editMusicModal = document.getElementById('edit-music-modal');
const deleteModal = document.getElementById('delete-modal');
const topSongsTable = document.getElementById('top-songs-table');
const allSongsTable = document.getElementById('all-songs-table');
const totalSongsCount = document.getElementById('total-songs-count');
const musicLoader = document.getElementById('music-loader');
const tabLinks = document.querySelectorAll('.sidebar-menu li[data-tab]');
const tabContents = document.querySelectorAll('.tab-content');



// Function to show a toast notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Remove the toast after animation completes
    setTimeout(() => {
        document.body.removeChild(toast);
    }, 3300);
}

// Function to populate song tables


// Function to handle tab switching
function switchTab(tabId) {
    // Hide all tabs
    tabContents.forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all tab links
    tabLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Show the selected tab
    document.getElementById(tabId).classList.add('active');
    
    // Set the tab link as active
    document.querySelector(`.sidebar-menu li[data-tab="${tabId}"]`).classList.add('active');
}

// Function to open a modal
function openModal(modal) {
    modal.style.display = 'block';
}

// Function to close a modal
function closeModal(modal) {
    modal.style.display = 'none';
}


// Event listeners for tab switching
tabLinks.forEach(link => {
    link.addEventListener('click', () => {
        const tabId = link.getAttribute('data-tab');
        switchTab(tabId);
    });
});

// Event listener for add music button
addMusicBtn.addEventListener('click', () => {
    openModal(addMusicModal);
});

// Event listeners for closing modals
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        closeModal(btn.closest('.modal'));
    });
});

// Event listener for cancel buttons
document.getElementById('cancel-add').addEventListener('click', () => {
    closeModal(addMusicModal);
});



document.getElementById('cancel-edit').addEventListener('click', () => {
    closeModal(editMusicModal);
});

document.getElementById('cancel-delete').addEventListener('click', () => {
    closeModal(deleteModal);
});

// Bulk add button (demo)
document.getElementById('bulk-add-btn').addEventListener('click', () => {
    showToast('Bulk upload feature coming soon');
});

// Close modals when clicking outside
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target);
    }
        });

// Get the input and the text paragraph
const audioInput = document.getElementById('audio-upload-input');
const uploadText = document.getElementById('audio-upload-text');
const text = document.getElementById('text');
const uploadArea = document.getElementById('audio-upload'); // Get the upload area (parent div)

// Listen for changes when a file is selected
audioInput.addEventListener('change', function(event) {
    const file = event.target.files[0]; // Get the selected file
    console.log(file);

    // Default behavior when no file is selected
    if (!file) {
        uploadText.textContent = 'Drop your audio file here or click to upload'; // Default text
        text.style.display = 'block'; // Ensure the text is visible again
        uploadArea.style.borderColor = ''; // Reset the border color
        return;
    }

    const fileType = file.type;
    const validTypes = ['audio/mp3', 'audio/wav', 'audio/mpeg']; // Add more valid MIME types here
    const maxFileSize = 100 * 1024 * 1024; // 100MB limit

    // Check for invalid file type
    if (!validTypes.includes(fileType)) {
        uploadText.textContent = 'Invalid file type. Please upload an MP3 or WAV file.';
        text.style.display = 'none'; // Hide the "Recommended size" text
        uploadArea.style.borderColor = 'red'; // Change the border color to red
        uploadText.style.color = 'red'; // Make the error text red

        setTimeout(() => {
            uploadText.textContent = 'Drop your audio file here or click to upload'; // Reset text after 3 seconds
            text.style.display = 'block'; // Show the "Recommended size" text again
            uploadArea.style.borderColor = ''; // Reset the border color
            uploadText.style.color = ''; // Reset the error text color
        }, 3000);
        return;
    }

    // Check for file size exceeding the limit
    if (file.size > maxFileSize) {
        alert('File size exceeds the limit of 100MB. Please upload a smaller file.');
        text.style.display = 'none'; // Hide the "Recommended size" text
        uploadArea.style.borderColor = 'red'; // Change the border color to red
        uploadText.style.color = 'red'; // Make the error text red

        setTimeout(() => {
            uploadText.textContent = 'Drop your audio file here or click to upload'; // Reset text after 3 seconds
            text.style.display = 'block'; // Show the "Recommended size" text again
            uploadArea.style.borderColor = ''; // Reset the border color
            uploadText.style.color = ''; // Reset the error text color
        }, 3000);
        return;
    }

    // If file is valid, update the text with the file name and remove the "Recommended size"
    uploadText.textContent = file.name;
    text.style.display = 'none'; // Hide the "Recommended size" text
    uploadArea.style.borderColor = ''; // Reset the border color to normal after valid file
    uploadText.style.color = ''; // Reset the error text color
});


//----------------------------------------------------add file name (edit // add normal----------------------------------------------)


const coverInput = document.getElementById('file-upload');
const uploadTextCover = document.getElementById('cover-upload-text');
const recommendedSize = document.getElementById('recommended-size');
const uploadAreaCover = document.getElementById('cover-upload'); // Get the upload area (parent div)

// Listen for changes when a file is selected
coverInput.addEventListener('change', function(event) {
    const file = event.target.files[0]; // Get the selected file
    console.log(file);

    if (file) {
        const fileType = file.type;
        const validTypes = ['image/jpeg', 'image/png', 'image/gif']; // Add more valid image MIME types if needed

        // Check for invalid file type
        if (!validTypes.includes(fileType)) {
            uploadTextCover.textContent = 'Invalid file type. Please upload a JPEG, PNG, or GIF image.';
            recommendedSize.style.display = 'none'; // Hide the "Recommended size" text
            uploadAreaCover.style.borderColor = 'red'; // Change the border color to red
            uploadTextCover.style.color = 'red'; // Make the error text red

            setTimeout(() => {
                uploadTextCover.textContent = 'Drop your album artwork here or click to upload'; // Reset text after 3 seconds
                recommendedSize.style.display = 'block'; // Show the "Recommended size" text again
                uploadAreaCover.style.borderColor = ''; // Reset the border color
                uploadTextCover.style.color = ''; // Reset the error text color
            }, 3000);
            return;
        }

        // If file type is valid
        uploadTextCover.textContent = file.name; // Update text with the file name
        recommendedSize.style.display = 'none'; // Remove the "Recommended size" text
        uploadAreaCover.style.borderColor = ''; // Reset the border color
        uploadTextCover.style.color = ''; // Reset the text color
    } else {
        uploadTextCover.textContent = 'Drop your album artwork here or click to upload'; // Default text
        recommendedSize.style.display = 'block'; // Show the "Recommended size" text again
        uploadAreaCover.style.borderColor = ''; // Reset the border color
        uploadTextCover.style.color = ''; // Reset the text color
    }
});


document.getElementById('confirm-add').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent immediate form submission

    const releaseDateInput = document.getElementById('song-release');
    const audioInput = document.getElementById('audio-upload-input');
    const imageInput = document.getElementById('file-upload'); // Get image input
    const releaseDate = releaseDateInput.value;
    const song_title = document.getElementById('song-title').value;
    const album = document.getElementById('album-name').value;
    const audioFile = audioInput.files[0]; // Get the selected audio file
    const imageFile = imageInput.files[0]; // Get the selected image file
    const duree = document.getElementById('duree').value;

    // Error messages
    const errer_song = document.getElementById('song-title-error');
    const errer_album = document.getElementById('album-name-error');
    const releaseDateError = document.getElementById('release-date-error');
    const dureeError = document.getElementById('duree-error');

    // Upload areas and texts
    const coverUploadText = document.getElementById('cover-upload-text');
    const audioUploadText = document.getElementById('audio-upload-text');

    // Reset error messages
    errer_song.textContent = '';
    errer_album.textContent = '';
    releaseDateError.textContent = '';
    dureeError.textContent = '';
    errer_song.style.display = 'none';
    errer_album.style.display = 'none';
    releaseDateError.style.display = 'none';
    dureeError.style.display = 'none';

    // Reset input field border colors
    coverUploadText.style.color = 'white';
    audioUploadText.style.color = 'white';

    // Flag to track if validation passed
    let valid = true;

    // Validate release date
    const today = new Date();
    const releaseDateObj = new Date(releaseDate);
    if ((releaseDateObj <= today) || (releaseDate == '')) {
        releaseDateError.textContent = 'Release date must be in the future or not empty.';
        releaseDateError.style.display = 'block';
        valid = false;
    }

    // Validate audio file
    if (!audioFile) {
        audioUploadText.style.color = 'red';
        valid = false;
    }

    // Validate image file
    if (!imageFile) {
        coverUploadText.style.color = 'red';
        valid = false;
    }

    // Validate song title
    if (song_title === '') {
        errer_song.textContent = 'Please fill in the song name.';
        errer_song.style.display = 'block';
        valid = false;
    }

    // Validate album name
    if (album === '') {
        errer_album.textContent = 'Please fill in the album name.';
        errer_album.style.display = 'block';
        valid = false;
    }

    // Validate duration (MM:SS format)
    if (duree === '') {
        dureeError.textContent = 'Please fill in the duration.';
        dureeError.style.display = 'block';
        valid = false;

    } else {
        const dureeRegex = /^([0-5]?[0-9]):([0-5]?[0-9])$/; // MM:SS format
        if (!dureeRegex.test(duree)) {
            dureeError.textContent = 'Duration must be in MM:SS format.';
            dureeError.style.display = 'block';
            valid = false;
        }
    }

    // If validation passed, submit the form
    if (valid) {
        console.log("Form is valid. Submitting now..."); // Debugging
        document.getElementById('song-form').submit(); // Then submit
        closeModal(addMusicModal); // Close modal first
        setTimeout(() => {
            location.reload(); // Delay reload
        }, 1000);
    }

    // Reset validation styles after 3 seconds
    if (!valid) {
        setTimeout(() => {
            errer_song.style.display = 'none';
            errer_album.style.display = 'none';
            releaseDateError.style.display = 'none';
            dureeError.style.display = 'none';
            coverUploadText.style.color = 'white';
            audioUploadText.style.color = 'white';
            audioUploadText.textContent = 'Drop your audio file here or click to upload'; // Reset text
        }, 3000);
    }
});







// Optional: Add cancel button functionality to reset the form
document.getElementById('cancel-add').addEventListener('click', function() {
    document.getElementById('song-release').value = ''; // Clear release date
    document.getElementById('audio-upload-input').value = ''; // Clear file input
    document.getElementById('audio-upload-text').textContent = 'Drop your audio file here or click to upload'; // Reset text
    document.getElementById('release-date-error').textContent = ''; // Clear error message
    document.getElementById('release-date-error').style.display = 'none'; // Hide error message
});


document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-song');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the song ID from the data-id attribute
            const songId = button.getAttribute('data-id');

            // Show the delete modal
            const modal = document.getElementById('delete-modal');
            const deleteSongIdInput = document.getElementById('delete-song-id');
            
            // Set the song ID in the hidden input field
            deleteSongIdInput.value = songId;

            // Display the modal
            modal.style.display = 'block';
        });
    });

    // Add event listener for the cancel button
    document.getElementById('cancel-delete').addEventListener('click', function() {
        // Close the modal
        document.getElementById('delete-modal').style.display = 'none';
    });

    // Add event listener for the confirm delete button
    document.getElementById('confirm-delete').addEventListener('click', function() {
        const songId = document.getElementById('delete-song-id').value;

        // Redirect to the deletion script with the song ID
        window.location.href = 'ajout.php?song_id=' + songId;
    });
});






document.addEventListener("DOMContentLoaded", function () {
    // Get all edit buttons
    let editButtons = document.querySelectorAll(".edit-song");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get song details from the button's data attributes
            let songId = this.getAttribute("data-id");
            let songTitle = this.getAttribute("data-title");
            let songAlbum = this.getAttribute("data-album");
            let songRelease = this.getAttribute("data-release");
            let songDuree = this.getAttribute("data-duree");

            // Populate the modal fields
            document.getElementById("song_id").value = songId;
            document.getElementById("edit-song-title").value = songTitle;
            document.getElementById("edit-song-album").value = songAlbum;
            document.getElementById("edit-song-release").value = songRelease;
            document.getElementById("edit-song-duree").value = songDuree;

            // Show the modal
            document.getElementById("edit-music-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector(".close-modal").addEventListener("click", function () {
        event.preventDefault();
        document.getElementById("edit-music-modal").style.display = "none";
    });

    // Cancel button closes the modal
    document.getElementById("cancel-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-music-modal").style.display = "none";
    });
});




const coverInputs = document.getElementById('file-edit');
const uploadTextCovers = document.getElementById('edit-text');
const recommendedSizes = document.getElementById('recommendeds-size');
const uploadAreaCovers = document.getElementById('edit-cover-upload'); // Get the upload area (parent div)

// Listen for changes when a file is selected
coverInputs.addEventListener('change', function(event) {
    const file = event.target.files[0]; // Get the selected file
    console.log(file);

    if (file) {
        const fileType = file.type;
        const validTypes = ['image/jpeg', 'image/png', 'image/gif']; // Add more valid image MIME types if needed

        // Check for invalid file type
        if (!validTypes.includes(fileType)) {
            uploadTextCovers.textContent = 'Invalid file type. Please upload a JPEG, PNG, or GIF image.';
            recommendedSizes.style.display = 'none'; // Hide the "Recommended size" text
            uploadAreaCovers.style.borderColor = 'red'; // Change the border color to red
            uploadTextCovers.style.color = 'red'; // Make the error text red

            setTimeout(() => {
                uploadTextCovers.textContent = 'edit si tu veux'; // Reset text after 3 seconds
                recommendedSizes.style.display = 'block'; // Show the "Recommended size" text again
                uploadAreaCovers.style.borderColor = ''; // Reset the border color
                uploadTextCovers.style.color = ''; // Reset the error text color
            }, 3000);
            return;
        }

        // If file type is valid
        uploadTextCovers.textContent = file.name; // Update text with the file name
        recommendedSizes.style.display = 'none'; // Remove the "Recommended size" text
        uploadAreaCovers.style.borderColor = ''; // Reset the border color
        uploadTextCovers.style.color = ''; // Reset the text color
    } else {
        uploadTextCovers.textContent = 'edit si tu veux'; // Default text
        recommendedSizes.style.display = 'block'; // Show the "Recommended size" text again
        uploadAreaCovers.style.borderColor = ''; // Reset the border color
        uploadTextCovers.style.color = ''; // Reset the text color
    }
});


const audioInputs = document.getElementById('audio-edit-input');
const uploadTexts = document.getElementById('audio-edit-text');
const texts = document.getElementById('texts');
const uploadAreas = document.getElementById('audio-edit-upload'); // Get the upload area (parent div)

// Listen for changes when a file is selected
audioInputs.addEventListener('change', function(event) {
    const file = event.target.files[0]; // Get the selected file
    console.log(file);

    // Default behavior when no file is selected
    if (!file) {
        uploadTexts.textContent = 'edit si tu veux'; // Default text
        texts.style.display = 'block'; // Ensure the text is visible again
        uploadAreas.style.borderColor = ''; // Reset the border color
        return;
    }

    const fileType = file.type;
    const validTypes = ['audio/mp3', 'audio/wav', 'audio/mpeg']; // Add more valid MIME types here
    const maxFileSize = 100 * 1024 * 1024; // 100MB limit

    // Check for invalid file type
    if (!validTypes.includes(fileType)) {
        uploadTexts.textContent = 'Invalid file type. Please upload an MP3 or WAV file.';
        texts.style.display = 'none'; // Hide the "Recommended size" text
        uploadAreas.style.borderColor = 'red'; // Change the border color to red
        uploadTexts.style.color = 'red'; // Make the error text red

        setTimeout(() => {
            uploadTexts.textContent = 'edit si tu veux'; // Reset text after 3 seconds
            texts.style.display = 'block'; // Show the "Recommended size" text again
            uploadAreas.style.borderColor = ''; // Reset the border color
            uploadTexts.style.color = ''; // Reset the error text color
        }, 3000);
        return;
    }

    // Check for file size exceeding the limit
    if (file.size > maxFileSize) {
        alert('File size exceeds the limit of 100MB. Please upload a smaller file.');
        texts.style.display = 'none'; // Hide the "Recommended size" text
        uploadAreas.style.borderColor = 'red'; // Change the border color to red
        uploadTexts.style.color = 'red'; // Make the error text red

        setTimeout(() => {
            uploadTexts.textContent = 'edit si tu veux'; // Reset text after 3 seconds
            texts.style.display = 'block'; // Show the "Recommended size" text again
            uploadAreas.style.borderColor = ''; // Reset the border color
            uploadTexts.style.color = ''; // Reset the error text color
        }, 3000);
        return;
    }

    // If file is valid, update the text with the file name and remove the "Recommended size"
    uploadTexts.textContent = file.name;
    texts.style.display = 'none'; // Hide the "Recommended size" text
    uploadAreas.style.borderColor = ''; // Reset the border color to normal after valid file
    uploadTexts.style.color = ''; // Reset the error text color
});


document.getElementById('confirm-edit').addEventListener('click', function(event) {
    event.preventDefault(); // Empêcher la soumission du formulaire

    console.log('Editing song...');
    const releaseDateInput = document.getElementById('edit-song-release');
    const releaseDate = releaseDateInput.value;
    const songTitleField = document.getElementById('edit-song-title');
    const albumField = document.getElementById('edit-song-album');
    const duree = document.getElementById('edit-song-duree').value;
    const date =document.getElementById('edit-song-release').value;

    // Messages d'erreur
    const errer_song = document.getElementById('song-title-error-edit');
    const errer_album = document.getElementById('album-name-error-edit');
    const releaseDateError = document.getElementById('release-date-error-edit');
    const dureeError = document.getElementById('duree-error-edit');

    // Réinitialisation des messages d'erreur
    errer_song.textContent = '';
    errer_album.textContent = '';
    releaseDateError.textContent = '';
    dureeError.textContent = '';
    errer_song.style.display = 'none';
    errer_album.style.display = 'none';
    releaseDateError.style.display = 'none';
    dureeError.style.display = 'none';

    let valid = true;

    // Validation du format de la date (YYYY/MM/DD ou YYYY-MM-DD)
    const dateRegex = /^(?!0000)(\d{4})[\/-](0[1-9]|1[0-2])[\/-](0[1-9]|[12][0-9]|3[01])$/;
    if (!dateRegex.test(releaseDate)) {
        releaseDateError.textContent = 'Date invalide. Format accepté : YYYY/MM/DD ou YYYY-MM-DD';
        releaseDateError.style.display = 'block';
        releaseDateInput.style.borderColor = 'red';
        valid = false;
    } else {
        releaseDateInput.style.borderColor = ''; // Réinitialiser la couleur si valide
    }

    // Vérifier si la date est dans le futur
    const today = new Date();
    const normalizedDate = releaseDate.replace(/-/g, '/'); // Normalisation en YYYY/MM/DD
    const [year, month, day] = normalizedDate.split('/').map(Number);
    const releaseDateObj = new Date(year, month - 1, day);

    if (releaseDateObj <= today) {
        releaseDateError.textContent = 'La date doit être dans le futur.';
        releaseDateError.style.display = 'block';
        valid = false;
    }

    // Validation du titre de la chanson
    if (songTitleField.value.trim() === '') {
        errer_song.textContent = 'Veuillez entrer le titre de la chanson.';
        errer_song.style.display = 'block';
        songTitleField.style.borderColor = 'red';
        valid = false;
    } else {
        songTitleField.style.borderColor = '';
    }

    // Validation du nom de l'album
    if (albumField.value.trim() === '') {
        errer_album.textContent = 'Veuillez entrer le nom de l’album.';
        errer_album.style.display = 'block';
        albumField.style.borderColor = 'red';
        valid = false;
    } else {
        albumField.style.borderColor = '';
    }

    // Validation de la durée (format MM:SS)
    const dureeRegex = /^(0[0-9]|[1-5][0-9]):([0-5][0-9])$/;
    if (!dureeRegex.test(duree)) {
        dureeError.textContent = 'La durée doit être au format MM:SS.';
        dureeError.style.display = 'block';
        valid = false;
    }

    // Soumettre si tout est valide
    if (valid) {
        console.log("Formulaire valide. Envoi...");
        document.getElementById('edit-form').submit();
        closeModal(editMusicModal);
        setTimeout(() => location.reload(), 1000);
    } else {
        console.log("Échec de la validation. Formulaire non soumis.");
        setTimeout(() => {
            errer_song.style.display = 'none';
            errer_album.style.display = 'none';
            releaseDateError.style.display = 'none';
            dureeError.style.display = 'none';
            date.style.borderColor = ''; // Reset the border color
        }, 3000);
    }
   
    

    
});
