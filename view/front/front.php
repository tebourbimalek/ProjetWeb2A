<?php

// Include the Config and Reclamation model
require_once "C:/xampp/htdocs/Tunify/config.php";
require_once "C:/xampp/htdocs/Tunify/model/Reclamation.php";
require_once "C:/xampp/htdocs/Tunify/controller/ReclamationController.php";
require_once "C:/xampp/htdocs/Tunify/controller/GestionReclamationController.php";



/// Common header function
function renderHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tunify - <?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary: #9b5de5;
                --primary-dark: #7d3cff;
                --primary-light: rgba(155, 93, 229, 0.1);
                --dark: #191414;
                --dark-light: #282828;
                --darker: #121212;
                --light: #f8f9fa;
                --text-light: #ffffff;
                --text-muted: #b3b3b3;
                --border-radius: 8px;
                --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                --transition: all 0.3s ease;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Circular', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            }

            body {
                background-color: var(--darker);
                color: var(--text-light);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .nav-bar {
                background-color: rgba(0, 0, 0, 0.8);
                padding: 1.5rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 100;
                backdrop-filter: blur(10px);
            }

            .logo {
                height: 40px;
                filter: brightness(0) invert(1);
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 3rem 2rem;
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            footer {
                background-color: var(--dark);
                padding: 2rem;
                text-align: center;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
            }

            .footer-text {
                color: var(--text-muted);
                font-size: 0.9rem;
            }

            /* Home page specific styles */
            .hero {
                text-align: center;
                margin-bottom: 4rem;
            }

            h1 {
                font-size: 3rem;
                font-weight: 800;
                margin-bottom: 1rem;
                background: linear-gradient(to right, var(--primary), #ff6b6b);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .subtitle {
                font-size: 1.2rem;
                color: var(--text-muted);
                max-width: 700px;
                margin: 0 auto;
            }

            .cards {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 2rem;
                margin-top: 2rem;
            }

            .card {
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                padding: 2rem;
                text-align: center;
                border: 1px solid rgba(255, 255, 255, 0.1);
                transition: var(--transition);
                box-shadow: var(--box-shadow);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 250px;
            }

            .card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
                border-color: var(--primary);
            }

            .card-icon {
                font-size: 3rem;
                color: var(--primary);
                margin-bottom: 1.5rem;
            }

            .card-title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }

            .card-text {
                color: var(--text-muted);
                margin-bottom: 1.5rem;
            }

            .btn {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
                font-weight: 600;
                border-radius: 50px;
                cursor: pointer;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
            }

            .btn-primary {
                background-color: var(--primary);
                color: white;
                border: none;
            }

            .btn-primary:hover {
                background-color: var(--primary-dark);
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(155, 93, 229, 0.4);
            }

            .btn-outline {
                background-color: transparent;
                color: var(--primary);
                border: 2px solid var(--primary);
            }

            .btn-outline:hover {
                background-color: var(--primary-light);
                transform: scale(1.05);
            }

            /* Form page specific styles */
            .form-container {
                max-width: 700px;
                margin: 2rem auto;
                padding: 2.5rem;
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                box-shadow: var(--box-shadow);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
                margin-bottom: 1.5rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
                position: relative;
            }

            label {
                display: block;
                margin-bottom: 0.75rem;
                font-weight: 600;
                color: var(--text-light);
                font-size: 0.95rem;
            }

            .required-field::after {
                content: " *";
                color: var(--primary);
            }

            input, select, textarea {
                width: 100%;
                padding: 1rem;
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: var(--border-radius);
                font-size: 1rem;
                color: var(--text-light);
                transition: var(--transition);
            }

            input::placeholder, textarea::placeholder {
                color: rgba(255, 255, 255, 0.5);
            }

            input:focus, select:focus, textarea:focus {
                outline: none;
                border-color: var(--primary);
                background-color: var(--primary-light);
                box-shadow: 0 0 0 2px rgba(155, 93, 229, 0.3);
            }

            textarea {
                min-height: 150px;
                resize: vertical;
            }

            select {
                appearance: none;
                background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 1rem center;
                background-size: 1rem;
            }
 

/* For better contrast on all options */
select#cause option {
    color: var(--text-light);
    padding: 8px;
    background-color: var(--dark-light);
}

/* Hover state */
select#cause option:hover {
    background-color: var(--primary-light) !important;
}

            button {
                background-color: var(--primary);
                color: white;
                border: none;
                padding: 1.2rem;
                font-size: 1rem;
                font-weight: 600;
                border-radius: 50px;
                cursor: pointer;
                width: 100%;
                transition: var(--transition);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-top: 1rem;
            }

            button:hover {
                background-color: var(--primary-dark);
                transform: scale(1.02);
                box-shadow: 0 5px 15px rgba(155, 93, 229, 0.4);
            }

            button:active {
                transform: scale(0.98);
            }

            .confirmation {
                display: none;
                background-color: rgba(25, 135, 84, 0.2);
                color: #d1fae5;
                padding: 1.25rem;
                border-radius: var(--border-radius);
                margin-top: 1.5rem;
                text-align: center;
                border: 1px solid rgba(74, 222, 128, 0.3);
                animation: fadeIn 0.4s ease-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* Screenshot Upload Box */
            .screenshot-box {
                border: 2px dashed rgba(255, 255, 255, 0.3);
                border-radius: var(--border-radius);
                padding: 2rem;
                text-align: center;
                cursor: pointer;
                transition: var(--transition);
                margin-bottom: 1.5rem;
                background-color: rgba(255, 255, 255, 0.05);
                position: relative;
                overflow: hidden;
            }

            .screenshot-box:hover {
                border-color: var(--primary);
                background-color: var(--primary-light);
            }

            .screenshot-box i {
                font-size: 2.5rem;
                color: var(--primary);
                margin-bottom: 1rem;
                display: block;
            }

            .screenshot-box .upload-text {
                font-weight: 500;
                margin-bottom: 0.5rem;
            }

            .screenshot-box .subtext {
                font-size: 0.85rem;
                color: rgba(255, 255, 255, 0.6);
            }

            #fileInput {
                display: none;
            }

            #previewContainer {
                display: none;
                margin-top: 1rem;
                position: relative;
            }

            #imagePreview {
                max-width: 100%;
                max-height: 200px;
                border-radius: var(--border-radius);
                display: block;
                margin: 0 auto;
            }

            .remove-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: rgba(0, 0, 0, 0.7);
                border: none;
                color: white;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: var(--transition);
            }

            .remove-btn:hover {
                background-color: rgba(255, 0, 0, 0.7);
            }

            /* Loading spinner */
            .spinner {
                display: none;
                width: 20px;
                height: 20px;
                border: 3px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                border-top-color: white;
                animation: spin 1s ease-in-out infinite;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            .error-message {
                color: #ff4d4d;
                font-size: 0.85rem;
                margin-top: 0.5rem;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .container {
                    padding: 2rem 1.5rem;
                }
                
                h1 {
                    font-size: 2.5rem;
                }
                
                .cards {
                    grid-template-columns: 1fr;
                }
                
                .form-grid {
                    grid-template-columns: 1fr;
                }
                
                .form-container {
                    padding: 1.5rem;
                    margin: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
    <?php
}

// Common footer function
function renderFooter() {
    ?>
        <footer>
            <p class="footer-text">&copy; <?php echo date('Y'); ?> Tunify. All rights reserved.</p>
        </footer>
    </body>
    </html>
    <?php
}


// Render the form page
function renderFormPage() {
    renderHeader('Submit Reclamation');
    ?>
    <nav class="nav-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="?page=home" style="color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
            
            <img src="../Tunify.png" alt="Tunify Logo" class="logo" width="50">
        </div>
        <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600;">
            <i class="fas fa-headphones"></i> Support Center
        </a>
    </nav>

    <div class="container">
        <div class="form-container">
            <h1><i class="fas fa-comment-alt"></i> Submit Your Reclamation</h1>
            <form id="reclamationForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fullName" class="required-field">Full Name</label>
                        <input type="text" id="fullName" name="full_name" placeholder="Enter your full name">
                    </div>
                    <div class="form-group">
                        <label for="email" class="required-field">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your@email.com">
                    </div>
                </div>

                <div class="form-group">
                    <label for="cause" class="required-field">Reclamation Cause</label>
                    <select id="cause" name="cause">
                        <option value="">Select a cause...</option>
                        <option value="technical">Technical Issue</option>
                        <option value="billing">Billing Problem</option>
                        <option value="account">Account Issue</option>
                        <option value="content">Content Concern</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description" class="required-field">Description</label>
                    <textarea id="description" name="description" rows="6" placeholder="Please describe your issue in detail (minimum 30 characters)..."></textarea>
                </div>

                <!-- Screenshot Upload Box -->
                <div class="form-group">
                    <label>Screenshot (Optional)</label>
                    <div class="screenshot-box" id="screenshotBox">
                        <i class="fas fa-camera"></i>
                        <div class="upload-text">Drag & drop your screenshot here</div>
                        <div class="subtext">or click to browse files (JPG, PNG - max 5MB)</div>
                        <input type="file" id="fileInput" name="screenshot" accept="image/*">
                    </div>
                    <div id="previewContainer">
                        <img id="imagePreview" src="#" alt="Preview">
                        <button type="button" class="remove-btn" id="removeBtn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="submitBtn">
                    <span id="btnText">Submit Reclamation</span>
                    <div class="spinner" id="spinner"></div>
                </button>
                
                <div class="confirmation" id="confirmationMsg">
                    <i class="fas fa-check-circle"></i> Reclamation submitted! We'll contact you within 24 hours.
                </div>
            </form>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById("reclamationForm");
    const fileInput = document.getElementById("fileInput");
    const previewContainer = document.getElementById("previewContainer");
    const imagePreview = document.getElementById("imagePreview");
    const removeBtn = document.getElementById("removeBtn");
    const spinner = document.getElementById("spinner");
    const btnText = document.getElementById("btnText");
    const confirmationMsg = document.getElementById("confirmationMsg");

    // Handle screenshot upload UI
    document.getElementById('screenshotBox').addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    removeBtn.addEventListener('click', () => {
        fileInput.value = '';
        previewContainer.style.display = 'none';
    });

    // Form submission
    form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(form);
    
    try {
        spinner.style.display = 'inline-block';
        btnText.style.display = 'none';

        const response = await fetch('front.php', {
            method: 'POST',
            body: formData
        });

        // Handle both JSON and text responses
        const textResponse = await response.text();
        try {
            const result = JSON.parse(textResponse);
            if (result.status === 'success') {
                confirmationMsg.style.display = 'block';
                setTimeout(() => {
                    form.reset();
                    previewContainer.style.display = 'none';
                    confirmationMsg.style.display = 'none';
                }, 2000);
            } else {
                throw new Error(result.message || 'Unknown error');
            }
        } catch {
            // If response isn't JSON, show generic success
            confirmationMsg.style.display = 'block';
            setTimeout(() => {
                form.reset();
                previewContainer.style.display = 'none';
                confirmationMsg.style.display = 'none';
            }, 2000);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    } finally {
        spinner.style.display = 'none';
        btnText.style.display = 'inline-block';
    }
});
});
</script>

    <?php
    renderFooter();
}

renderFormPage();

?>
