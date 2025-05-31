document.addEventListener('DOMContentLoaded', function () {
    const deleteButtons = document.querySelectorAll('.delete-news');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const newsId = button.getAttribute('data-id');
            console.log('Delete button clicked for news ID:', newsId);
            if (confirm("Are you sure you want to delete this news item?")) {
                window.location.href = 'news/deletenews.php?news_id=' + newsId;
            }
        });
    }); 
});

document.getElementById('confirm-add').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default form submission

    // Input fields
    const newsTitle = document.getElementById('news-title').value.trim();
    const newsContent = document.getElementById('news-content').value.trim();
    const newsDateValue = document.getElementById('news-date').value;
    const fileInput = document.getElementById('news-upload-input');
    const newsFile = fileInput?.files[0];

    // Error elements
    const titleError = document.getElementById('news-title-error');
    const contentError = document.getElementById('news-content-error');
    const dateError = document.getElementById('news-date-error');
    const uploadText = document.getElementById('news-upload-text');

    // Reset errors
    titleError.textContent = '';
    contentError.textContent = '';
    dateError.textContent = '';
    titleError.style.display = 'none';
    contentError.style.display = 'none';
    dateError.style.display = 'none';
    uploadText.style.color = 'white';

    let valid = true;

    // Validate title
    if (newsTitle === '') {
        titleError.textContent = 'Please enter the news title.';
        titleError.style.display = 'block';
        valid = false;
    }

    // Validate content
    if (newsContent === '') {
        contentError.textContent = 'Please enter the news content.';
        contentError.style.display = 'block';
        valid = false;
    }

    // Validate date (must be in the future)
    if (!newsDateValue || new Date(newsDateValue) <= new Date()) {
        dateError.textContent = 'Date must be in the future.';
        dateError.style.display = 'block';
        valid = false;
    }

    // Validate file upload
    if (!newsFile) {
        uploadText.style.color = 'red';
        valid = false;
    }

    // Submit if valid
    if (valid) {
        document.getElementById('news-form').submit();
    } else {
        // Reset error styles after 3 seconds
        setTimeout(() => {
            titleError.style.display = 'none';
            contentError.style.display = 'none';
            dateError.style.display = 'none';
            uploadText.style.color = 'white';
            uploadText.textContent = 'Drop your file here or click to upload';
        }, 3000);
    }
});

var modal = document.getElementById('add-news-modal');
    var btn = document.getElementById('add-news-btn');
    var closeBtn = document.getElementsByClassName('close-modal')[0];
    var cancelBtn = document.getElementById('cancel-add');

    // When the user clicks the button, show the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks the close button (×), hide the modal
    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks the cancel button, hide the modal
    cancelBtn.onclick = function(event) {
        event.preventDefault(); // Prevent form submission
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside the modal, hide it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }


    document.addEventListener("DOMContentLoaded", function () {
    const editButtons = document.querySelectorAll(".edit-news");
    const modal = document.getElementById("edit-news-modal");

    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Get data from button
            const id = this.getAttribute("data-id");
            const title = this.getAttribute("data-title");
            const content = this.getAttribute("data-album"); // "album" used as "content"
            const date = this.getAttribute("data-release");
            const description = this.getAttribute("data-duree");

            // Fill the modal form fields
            document.getElementById("news_id").value = id;
            document.getElementById("edit-news-title").value = title;
            document.getElementById("edit-news-content").value = content;
            document.getElementById("edit-news-date").value = date;

            // Optional: you could show description somewhere else if needed

            // Show the modal
            modal.style.display = "block";
        });
    });

    // Close modal when clicking on the close button
    document.querySelector(".close-modal").addEventListener("click", function () {
        modal.style.display = "none";
    });

    // Optional: close modal if clicking outside of modal content
    window.addEventListener("click", function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});