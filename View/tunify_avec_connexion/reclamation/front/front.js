document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById("reclamationForm");
    const fullNameInput = document.getElementById("fullName");
    const emailInput = document.getElementById("email");
    const causeInput = document.getElementById("cause");
    const descriptionInput = document.getElementById("description");
    const fileInput = document.getElementById("fileInput");
    const screenshotBox = document.getElementById("screenshotBox");
    const previewContainer = document.getElementById("previewContainer");
    const imagePreview = document.getElementById("imagePreview");
    const removeBtn = document.getElementById("removeBtn");
    const submitBtn = document.getElementById("submitBtn");
    const spinner = document.getElementById("spinner");
    const btnText = document.getElementById("btnText");
    const confirmationMsg = document.getElementById("confirmationMsg");

    // Regular expressions for validation
    const namePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Clear all error messages
    function clearErrors() {
        document.querySelectorAll(".error-message").forEach(el => el.remove());
    }

    // Show error message for an input
    function showError(input, message) {
        clearErrorForInput(input);

        const error = document.createElement("div");
        error.className = "error-message";
        error.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;

        input.parentElement.appendChild(error);
        input.style.borderColor = "#ff4d4d";
    }

    // Clear error for a specific input
    function clearErrorForInput(input) {
        const existingError = input.parentElement.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }
        input.style.borderColor = "";
    }

    // Handle screenshot upload UI
    screenshotBox.addEventListener('click', () => fileInput.click());
    
    // Handle file selection
    fileInput.addEventListener('change', handleFileSelect);

    // Handle drag and drop
    screenshotBox.addEventListener("dragover", (e) => {
        e.preventDefault();
        screenshotBox.style.borderColor = "var(--primary)";
        screenshotBox.style.backgroundColor = "var(--primary-light)";
    });

    screenshotBox.addEventListener("dragleave", () => {
        screenshotBox.style.borderColor = "";
        screenshotBox.style.backgroundColor = "";
    });

    screenshotBox.addEventListener("drop", (e) => {
        e.preventDefault();
        screenshotBox.style.borderColor = "";
        screenshotBox.style.backgroundColor = "";

        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        }
    });

    function handleFileSelect() {
        const file = fileInput.files[0];

        if (file) {
            // Validate file type
            const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            if (!allowedTypes.includes(file.type)) {
                showError(fileInput, "Invalid file type. Please upload JPG, PNG, or GIF.");
                fileInput.value = "";
                return;
            }

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showError(fileInput, "File is too large. Maximum size is 5MB.");
                fileInput.value = "";
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                previewContainer.style.display = "block";
                screenshotBox.style.display = "none";
                clearErrorForInput(fileInput);
            };
            reader.readAsDataURL(file);
        }
    }

    // Remove selected file
    removeBtn.addEventListener("click", () => {
        fileInput.value = "";
        previewContainer.style.display = "none";
        screenshotBox.style.display = "block";
    });

    // Input validation on blur
    fullNameInput.addEventListener("blur", function() {
        if (this.value.trim() !== "" && !namePattern.test(this.value.trim())) {
            showError(this, "Please enter a valid name (at least 2 letters, letters only).");
        } else {
            clearErrorForInput(this);
        }
    });

    emailInput.addEventListener("blur", function() {
        if (this.value.trim() !== "" && !emailPattern.test(this.value.trim())) {
            showError(this, "Please enter a valid email address.");
        } else {
            clearErrorForInput(this);
        }
    });

    causeInput.addEventListener("blur", function() {
        if (this.value === "") {
            showError(this, "Please select a reclamation cause.");
        } else {
            clearErrorForInput(this);
        }
    });

    descriptionInput.addEventListener("blur", function() {
        if (this.value.trim() !== "" && this.value.trim().length < 30) {
            showError(this, "Description must be at least 30 characters long.");
        } else {
            clearErrorForInput(this);
        }
    });

    // Form submission
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        clearErrors();

        let hasError = false;

        // Validate full name
        const fullName = fullNameInput.value.trim();
        if (fullName === "" || !namePattern.test(fullName)) {
            showError(fullNameInput, "Please enter a valid name (at least 2 letters, letters only).");
            hasError = true;
        }

        // Validate email
        const email = emailInput.value.trim();
        if (email === "" || !emailPattern.test(email)) {
            showError(emailInput, "Please enter a valid email address.");
            hasError = true;
        }

        // Validate cause
        const cause = causeInput.value;
        if (cause === "") {
            showError(causeInput, "Please select a reclamation cause.");
            hasError = true;
        }

        // Validate description
        const description = descriptionInput.value.trim();
        if (description === "" || description.length < 30) {
            showError(descriptionInput, "Description must be at least 30 characters long.");
            hasError = true;
        }

        // Validate file if one is selected
        const file = fileInput.files[0];
        if (file) {
            const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            if (!allowedTypes.includes(file.type)) {
                showError(fileInput, "Invalid file type. Please upload JPG, PNG, or GIF.");
                hasError = true;
            }

            if (file.size > 5 * 1024 * 1024) {
                showError(fileInput, "File is too large. Maximum size is 5MB.");
                hasError = true;
            }
        }

        // If there are errors, stop form submission
        if (hasError) {
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        spinner.style.display = "inline-block";
        btnText.style.display = "none";

        // Create FormData object
        const formData = new FormData(form);

        // Send AJAX request
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            // Try to parse as JSON, but don't fail if it's not JSON
            let result;
            try {
                result = JSON.parse(data);
            } catch (e) {
                // If not JSON, assume success
                result = { status: 'success' };
            }

            if (result.status === 'success') {
                // Show success message
                confirmationMsg.style.display = "block";
                
                // Reset form after delay
                setTimeout(() => {
                    form.reset();
                    previewContainer.style.display = "none";
                    screenshotBox.style.display = "block";
                    confirmationMsg.style.display = "none";
                }, 3000);
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            spinner.style.display = "none";
            btnText.style.display = "inline-block";
        });
    });
});

// Search functionality
document.addEventListener('DOMContentLoaded', () => {
    // Create search modal
    const searchModal = document.createElement('div');
    searchModal.className = 'search-modal';
    searchModal.innerHTML = `
    <div class="search-modal-content">
        <div class="search-modal-header">
            <div class="search-modal-title">Search Reclamations by Email</div>
            <button class="close-modal">&times;</button>
        </div>
        <form class="search-form" id="searchForm">
            <div class="form-group">
                <label for="emailSearch">Email Address</label>
                <input type="email" id="emailSearch" name="emailSearch" placeholder="Enter email to search">
            </div>
            <button type="submit">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
        <div class="search-results" id="searchResults"></div>
    </div>
`;
    document.body.appendChild(searchModal);

    // Get elements
    const searchBtn = document.getElementById('searchBtn');
    const closeModalBtn = document.querySelector('.close-modal');
    const searchForm = document.getElementById('searchForm');
    const searchResults = document.getElementById('searchResults');

    // Open search modal
    searchBtn.addEventListener('click', () => {
        searchModal.style.display = 'flex';
    });

    // Close search modal
    closeModalBtn.addEventListener('click', () => {
        searchModal.style.display = 'none';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            searchModal.style.display = 'none';
        }
    });

    // Handle search form submission
    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const emailInput = document.getElementById('emailSearch');
        const email = emailInput.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // Clear any existing error messages
        const existingError = emailInput.parentElement.querySelector(".error-message");
        if (existingError) {
            existingError.remove();
        }
        emailInput.style.borderColor = "";
        
        // Validate email
        if (email === "" || !emailPattern.test(email)) {
            // Show error message
            const error = document.createElement("div");
            error.className = "error-message";
            error.innerHTML = `<i class="fas fa-exclamation-circle"></i> Please enter a valid email address.`;
        
            emailInput.parentElement.appendChild(error);
            emailInput.style.borderColor = "#ff4d4d";
            return;
        }
        
        // Show loading state
        searchResults.innerHTML = '<div class="no-results">Searching...</div>';
        
        // Use the current URL for the AJAX request
        const searchUrl = `${window.location.href.split('?')[0]}?emailSearch=${encodeURIComponent(email)}`;
        console.log("Search URL:", searchUrl);
        
        // Make AJAX request to search for reclamations
        fetch(searchUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
        .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
                throw new Error(`Server responded with status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            console.log("Search response received");
            
            // Display the results directly
            searchResults.innerHTML = data;
            
            // Add event listeners to delete buttons
            document.querySelectorAll('.btn-supprimer').forEach(btn => {
                btn.addEventListener('click', function() {
                    const reclamationId = this.getAttribute('data-id');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#9b5de5',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send delete request
                            const deleteUrl = `${window.location.href.split('?')[0]}?deleteReclamation=${reclamationId}`;
                            
                            fetch(deleteUrl, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Server responded with status: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: 'Reclamation deleted successfully',
                                        icon: 'success'
                                    }).then(() => {
                                        // Refresh the search results
                                        searchForm.dispatchEvent(new Event('submit'));
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Could not delete the reclamation. Please try again.',
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Network Error',
                                    text: 'Failed to connect to the server. Please check your connection.',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });
            
            // Add event listeners to edit buttons
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', function() {
                    const reclamationId = this.getAttribute('data-id');
                    const currentCause = this.getAttribute('data-cause');
                    const currentDescription = this.getAttribute('data-description');
                    const currentScreenshot = this.getAttribute('data-screenshot');
                    
                    // Create the HTML for the edit form with improved styling
                    let editFormHtml = `
                        <div class="edit-form-group">
                            <label for="swal-cause" class="edit-form-label">Cause</label>
                            <select id="swal-cause" class="edit-form-control" style="background-color: #f8f9fa; color: #333333;">
                                <option value="technical" ${currentCause === 'technical' ? 'selected' : ''}>Technical Issue</option>
                                <option value="billing" ${currentCause === 'billing' ? 'selected' : ''}>Billing Problem</option>
                                <option value="account" ${currentCause === 'account' ? 'selected' : ''}>Account Issue</option>
                                <option value="content" ${currentCause === 'content' ? 'selected' : ''}>Content Concern</option>
                                <option value="other" ${currentCause === 'other' ? 'selected' : ''}>Other</option>
                            </select>
                        </div>
                        <div class="edit-form-group">
                            <label for="swal-description" class="edit-form-label">Description</label>
                            <textarea id="swal-description" class="edit-form-control" placeholder="Enter description..." style="min-height: 120px; word-wrap: break-word; white-space: pre-wrap;">${currentDescription}</textarea>
                            <div id="description-error" class="error-message" style="display: none;"></div>
                        </div>
                    `;
                    
                    // Add screenshot section
                    editFormHtml += `
                        <div class="edit-form-group">
                            <label for="swal-screenshot" class="edit-form-label">Screenshot</label>
                    `;
                    
                    // Show current screenshot if available
                    if (currentScreenshot) {
                        editFormHtml += `
                            <div class="current-screenshot">
                                <p style="margin-bottom: 5px; font-size: 0.9rem; color: #f8f9fa;">Current Screenshot:</p>
                                <img src="${currentScreenshot}" alt="Current Screenshot">
                                <div class="screenshot-actions">
                                    <button type="button" id="keep-screenshot" class="btn-keep">KEEP</button>
                                    <button type="button" id="remove-screenshot" class="btn-remove">REMOVE</button>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Add upload new screenshot option
                    editFormHtml += `
                        <div id="edit-screenshot-box" class="edit-screenshot-box">
                            <i class="fas fa-camera" style="font-size: 2rem; color: #9b5de5; margin-bottom: 10px; display: block;"></i>
                            <div style="font-weight: 500; margin-bottom: 5px;">Upload new screenshot</div>
                            <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.6);">Click to browse files (JPG, PNG - max 5MB)</div>
                        </div>
                        <div id="edit-preview-container" style="display: none; margin-top: 10px; position: relative;">
                            <img id="edit-image-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 150px; border-radius: 8px; display: block; margin: 0   style="max-width: 100%; max-height: 150px; border-radius: 8px; display: block; margin: 0 auto;">
                            <button type="button" id="edit-remove-btn" style="position: absolute; top: 5px; right: 5px; background-color: rgba(0, 0, 0, 0.7); border: none; color: white; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="file" id="edit-file-input" name="screenshot" accept="image/*" style="display: none;">
                        <div id="screenshot-error" class="error-message" style="display: none;"></div>
                    </div>
                    `;
                    
                    Swal.fire({
                        title: 'Edit Reclamation',
                        html: editFormHtml,
                        showCancelButton: true,
                        confirmButtonColor: '#9b5de5',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Update',
                        cancelButtonText: 'Cancel',
                        focusConfirm: false,
                        customClass: {
                            container: 'edit-modal-container',
                            popup: 'edit-modal-popup',
                            content: 'edit-modal-content'
                        },
                        didOpen: () => {
                            // Focus on the description field
                            document.getElementById('swal-description').focus();
                            
                            // Add input validation
                            const descriptionField = document.getElementById('swal-description');
                            descriptionField.addEventListener('input', function() {
                                const descriptionError = document.getElementById('description-error');
                                if (this.value.trim().length < 30) {
                                    descriptionError.textContent = 'Description must be at least 30 characters long.';
                                    descriptionError.style.display = 'block';
                                } else {
                                    descriptionError.style.display = 'none';
                                }
                            });
                            
                            // Screenshot handling
                            const editFileInput = document.getElementById('edit-file-input');
                            const editScreenshotBox = document.getElementById('edit-screenshot-box');
                            const editPreviewContainer = document.getElementById('edit-preview-container');
                            const editImagePreview = document.getElementById('edit-image-preview');
                            const editRemoveBtn = document.getElementById('edit-remove-btn');
                            
                            // Handle screenshot box click
                            editScreenshotBox.addEventListener('click', () => {
                                editFileInput.click();
                            });
                            
                            // Handle file selection
                            editFileInput.addEventListener('change', function() {
                                const file = this.files[0];
                                
                                if (file) {
                                    // Validate file type
                                    const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
                                    if (!allowedTypes.includes(file.type)) {
                                        const screenshotError = document.getElementById('screenshot-error');
                                        screenshotError.textContent = "Invalid file type. Please upload JPG, PNG, or GIF.";
                                        screenshotError.style.display = 'block';
                                        this.value = "";
                                        return;
                                    }
                                    
                                    // Validate file size (5MB max)
                                    if (file.size > 5 * 1024 * 1024) {
                                        const screenshotError = document.getElementById('screenshot-error');
                                        screenshotError.textContent = "File is too large. Maximum size is 5MB.";
                                        screenshotError.style.display = 'block';
                                        this.value = "";
                                        return;
                                    }
                                    
                                    // Show preview
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        editImagePreview.src = e.target.result;
                                        editPreviewContainer.style.display = "block";
                                        editScreenshotBox.style.display = "none";
                                        
                                        // Hide current screenshot if exists
                                        const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                        if (currentScreenshotDiv) {
                                            currentScreenshotDiv.style.display = 'none';
                                        }
                                        
                                        // Hide error message if exists
                                        const screenshotError = document.getElementById('screenshot-error');
                                        if (screenshotError) {
                                            screenshotError.style.display = 'none';
                                        }
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                            
                            // Handle remove button click
                            if (editRemoveBtn) {
                                editRemoveBtn.addEventListener('click', () => {
                                    editFileInput.value = "";
                                    editPreviewContainer.style.display = "none";
                                    editScreenshotBox.style.display = "block";
                                    
                                    // Show current screenshot if exists
                                    const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                    if (currentScreenshotDiv) {
                                        currentScreenshotDiv.style.display = 'block';
                                    }
                                });
                            }
                            
                            // Handle keep/remove current screenshot buttons
                            const keepScreenshotBtn = document.getElementById('keep-screenshot');
                            const removeScreenshotBtn = document.getElementById('remove-screenshot');
                            
                            if (keepScreenshotBtn) {
                                keepScreenshotBtn.addEventListener('click', () => {
                                    // Just keep the current screenshot, no action needed
                                    // Hide the upload box
                                    editScreenshotBox.style.display = 'none';
                                    
                                    // Add a visual indicator that it's selected
                                    const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                    if (currentScreenshotDiv) {
                                        currentScreenshotDiv.style.border = '2px solid #20c997';
                                    }
                                });
                            }
                            
                            if (removeScreenshotBtn) {
                                removeScreenshotBtn.addEventListener('click', () => {
                                    // Mark the screenshot for removal
                                    const currentScreenshotDiv = document.querySelector('.current-screenshot');
                                    if (currentScreenshotDiv) {
                                        currentScreenshotDiv.style.display = 'none';
                                    }
                                    
                                    // Show the upload box
                                    editScreenshotBox.style.display = 'block';
                                    
                                    // Add a hidden input to indicate removal
                                    const removeScreenshotInput = document.createElement('input');
                                    removeScreenshotInput.type = 'hidden';
                                    removeScreenshotInput.id = 'remove-screenshot-flag';
                                    removeScreenshotInput.value = '1';
                                    document.querySelector('.swal2-html-container').appendChild(removeScreenshotInput);
                                });
                            }
                        },
                        preConfirm: () => {
                            const cause = document.getElementById('swal-cause').value;
                            const description = document.getElementById('swal-description').value;
                            const descriptionError = document.getElementById('description-error');
                            
                            // Validate description
                            if (!description || description.trim().length < 30) {
                                descriptionError.textContent = 'Description must be at least 30 characters long.';
                                descriptionError.style.display = 'block';
                                return false;
                            }
                            
                            // Check if we have a new file
                            const fileInput = document.getElementById('edit-file-input');
                            const file = fileInput.files[0];
                            
                            // Check if we're removing the current screenshot
                            const removeScreenshotFlag = document.getElementById('remove-screenshot-flag');
                            const removeScreenshot = removeScreenshotFlag ? true : false;
                            
                            // Return the form data
                            return { 
                                cause, 
                                description, 
                                file,
                                removeScreenshot
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Updating...',
                                text: 'Please wait while we update your reclamation.',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                willOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Get the update URL
                            const updateUrl = `${window.location.href.split('?')[0]}?updateReclamation=${reclamationId}`;
                            
                            // Check if we have a file to upload
                            if (result.value.file) {
                                // Create FormData for file upload
                                const formData = new FormData();
                                formData.append('cause', result.value.cause);
                                formData.append('description', result.value.description);
                                formData.append('screenshot', result.value.file);
                                
                                // Send update request with file
                                fetch(updateUrl, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Server responded with status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: data.message,
                                            icon: 'success',
                                            confirmButtonColor: '#9b5de5'
                                        }).then(() => {
                                            // Refresh the search results
                                            searchForm.dispatchEvent(new Event('submit'));
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message || 'Failed to update reclamation.',
                                            icon: 'error',
                                            confirmButtonColor: '#9b5de5'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Network Error',
                                        text: 'Failed to connect to the server. Please check your connection.',
                                        icon: 'error',
                                        confirmButtonColor: '#9b5de5'
                                    });
                                });
                            } else {
                                // Create data object for JSON request
                                const data = {
                                    cause: result.value.cause,
                                    description: result.value.description,
                                    removeScreenshot: result.value.removeScreenshot
                                };
                                
                                // Send update request without file
                                fetch(updateUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify(data)
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`Server responded with status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            title: 'Success!',
                                            text: data.message,
                                            icon: 'success',
                                            confirmButtonColor: '#9b5de5'
                                        }).then(() => {
                                            // Refresh the search results
                                            searchForm.dispatchEvent(new Event('submit'));
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error!',
                                            text: data.message || 'Failed to update reclamation.',
                                            icon: 'error',
                                            confirmButtonColor: '#9b5de5'
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Network Error',
                                        text: 'Failed to connect to the server. Please check your connection.',
                                        icon: 'error',
                                        confirmButtonColor: '#9b5de5'
                                    });
                                });
                            }
                        }
                    });
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            searchResults.innerHTML = `<div class="no-results">Error: ${error.message}</div>`;
            
            Swal.fire({
                title: 'Network Error',
                text: 'Failed to connect to the server. Please check your connection.',
                icon: 'error',
                confirmButtonColor: '#9b5de5'
            });
        });
    });
});

  function toggleDropdown() {
        const dropdown = document.getElementById("dropdownMenu");
        dropdown.classList.toggle("show");
    }

    // Close dropdown when clicking outside of it
    document.addEventListener("click", function(event) {
        const button = document.querySelector(".dropdown-button");
        const menu = document.getElementById("dropdownMenu");

        if (!button.contains(event.target) && !menu.contains(event.target)) {
            menu.classList.remove("show");
        }
    });