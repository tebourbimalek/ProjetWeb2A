document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to all delete buttons
    const deleteButtons = document.querySelectorAll('.delete-song');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the song ID and type from the data-id and data-type attributes
            const songId = button.getAttribute('data-id');
            const type = button.getAttribute('data-type');

            // Show the delete modal
            const modal = document.getElementById('delete-modal');
            const deleteSongIdInput = document.getElementById('delete-song-id');
            const typetableInput = document.getElementsByClassName('type_c'); // Ensure this element exists

            // Set the song ID and type in the hidden input fields
            deleteSongIdInput.value = songId;
            typetableInput.value = type;

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
        // Get the song ID and type values from the modal's hidden input fields
        const songId = document.getElementById('delete-song-id').value;
        const type = document.getElementsByClassName('type_c').value;

        console.log('Deleting song with ID:', songId, 'and type:', type);
        // If you're using a GET request for the deletion
        window.location.href = 'delete_paiment.php?song_id=' + songId + '&type=' + type;
    });
});



// Function to open a modal
function openModal(modal) {
    modal.style.display = 'block';
}

// Function to close a modal
function closeModal(modal) {
    modal.style.display = 'none';
}
// Event listeners for closing modals
document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        closeModal(btn.closest('.modal'));
    });
});
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target);
    }
});



document.addEventListener("DOMContentLoaded", function () {
    // Get all edit buttons
    let editButtons = document.querySelectorAll(".edit-paiment-song");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get song details from the button's data attributes
            let paimentId = this.getAttribute("data-id");
            let paimentdate = this.getAttribute("data-date");
            let paiment_abonnement = this.getAttribute("data-abonnement");

            console.log(paimentId, paimentdate, paiment_abonnement);

            // Populate the modal fields
            document.getElementById("song_paiment_id").value = paimentId;
            document.getElementById("edit-date-title").value = paimentdate;
            document.getElementById('edit-abonnement').value = paiment_abonnement;
            

            // Show the modal
            document.getElementById("edit-paiment-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector(".close-modal").addEventListener("click", function () {
        event.preventDefault();
        document.getElementById("edit-paiment-modal").style.display = "none";
    });

    // Cancel button closes the modal
    document.getElementById("cancel-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paiment-modal").style.display = "none";
    });
});


document.addEventListener("DOMContentLoaded", function () {
    // Get all edit buttons
    let editButtons = document.querySelectorAll(".edit-paiment-song");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get song details from the button's data attributes
            let paimentId = this.getAttribute("data-id");
            let paimentdate = this.getAttribute("data-date");
            let paiment_abonnement = this.getAttribute("data-abonnement");

            console.log(paimentId, paimentdate, paiment_abonnement);

            // Populate the modal fields
            document.getElementById("song_paiment_id").value = paimentId;
            document.getElementById("edit-date-title").value = paimentdate;
            document.getElementById('edit-abonnement').value = paiment_abonnement;
            

            // Show the modal
            document.getElementById("edit-paiment-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector(".close-modal").addEventListener("click", function () {
        event.preventDefault();
        document.getElementById("edit-paiment-modal").style.display = "none";
    });

    // Cancel button closes the modal
    document.getElementById("cancel-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paiment-modal").style.display = "none";
    });
});



document.addEventListener("DOMContentLoaded", function () {
    // Get all edit buttons
    let editButtons = document.querySelectorAll(".edit-paimentc-song");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get payment details from the button's data attributes
            let id_carte = this.getAttribute("data-id");
            let type_carte = this.getAttribute("data-carte"); // Get the payment method type
            let numero_carte = this.getAttribute("data-numero");
            let date_expiration = this.getAttribute("data-expiration");
            let type = this.getAttribute("data-type");

            console.log("Selected Payment Method: ", type_carte);

            console.log(type_carte , type);  // Check that the value is logged correctly

            // Populate the modal fields
            document.getElementById("song_paimentc_id").value = id_carte;
            document.getElementById("edit-carte-title").value = numero_carte;
            document.getElementById('edit-carte-type').value = type_carte;  // Set the value of the payment method dropdown
            document.getElementById('edit-datec-title').value = date_expiration;
            document.getElementById('type_paimentc').value = type;

            // Show the modal
            document.getElementById("edit-paimentc-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector(".close-modal").addEventListener("click", function () {
        event.preventDefault();
        document.getElementById("edit-paimentc-modal").style.display = "none";  // Ensure modal ID matches
    });

    // Cancel button closes the modal
    document.getElementById("cancel-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paimentc-modal").style.display = "none";  // Ensure modal ID matches
    });
});


document.addEventListener("DOMContentLoaded", function () {
    // Get all mobile edit buttons
    let editMobileButtons = document.querySelectorAll(".edit-paimentmobile-song");

    editMobileButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get payment details from the button's data attributes
            let id_mobile = this.getAttribute("data-id");
            let provider = this.getAttribute("data-provider");
            let numero_mobile = this.getAttribute("data-numero");
            let date_exp = this.getAttribute("date_exp");
            let type = this.getAttribute("data-type");

            console.log("Selected Mobile Provider: ", provider);
            console.log ("Selected Mobile Type: ", type);  // Check that the value is logged correctly
            console.log(id_mobile, provider, numero_mobile, date_exp);  // Check that the value is logged correctly

            // Populate the modal fields
            document.getElementById("song_paimentmobile_id").value = id_mobile;
            document.getElementById("edit-mobile-title").value = numero_mobile;
            document.getElementById("edit-mobile-type").value = provider;
            document.getElementById("edit-mobile").value = date_exp;
            document.getElementById("type_mobile").value = type;

            // Show the modal
            document.getElementById("edit-paimentmobile-modal").style.display = "block";
        });
    });

    // Close modal when clicking the close button
    document.querySelector("#edit-paimentmobile-modal .close-modal").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paimentmobile-modal").style.display = "none";
    });

    // Cancel button closes the modal
    document.getElementById("cancel-mobile-edit").addEventListener("click", function (event) {
        event.preventDefault();
        document.getElementById("edit-paimentmobile-modal").style.display = "none";
    });
});





