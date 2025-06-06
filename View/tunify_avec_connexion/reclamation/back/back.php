<?php
session_start();
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reclamation.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\GestionReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\TypeReclamationController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionpaiments.php';





$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$reclamationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

function renderHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale: 1.0">
        <title>Tunify Admin - <?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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
        </script>
        <style>
            <style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 20px;
        color: #fff;
        padding: 10px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #222;
        min-width: 220px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.4);
        z-index: 999;
        overflow: hidden;
    }

    .dropdown-menu a {
        color: #fff;
        padding: 16px;
        text-decoration: none;
        display: block;
        font-weight: 500;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .dropdown-menu a:hover {
        background-color: rgba(255,255,255,0.05);
    }

    .external-link {
        float: right;
        opacity: 0.7;
    }

    .show {
        display: block;
    }
    .notification-icon {
        position: relative;
        display: inline-block;
    }

    .notification-count {
        position: absolute;
        top: -10px;
        right: -8px;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-count1 {
        position: absolute;
        top: 14px;
        right: 50%;
        background-color: red;
        color: white;
        font-size: 12px;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
    }
    .notification-icon {
    position: relative;
}
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
            }

            .nav-bar {
                background-color: rgba(0, 0, 0, 0.8);
                padding: 1rem 2rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 100;
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .logo {
                height: 40px;
                filter: brightness(0) invert(1);
            }

            .back-btn {
                color: var(--text-light);
                text-decoration: none;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-weight: 500;
                transition: var(--transition);
            }

            .back-btn:hover {
                color: var(--primary);
            }

            .container {
                max-width: 1200px;
                margin: 2rem auto;
                padding: 0 2rem;
            }

            .dashboard-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            h1 {
                color: var(--primary);
                font-weight: 700;
                font-size: 2rem;
                letter-spacing: -0.5px;
            }

            .stats-container {
                display: flex;
                gap: 1.5rem;
                margin-bottom: 2rem;
            }

            .stat-card {
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                padding: 1.5rem;
                flex: 1;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: var(--box-shadow);
            }

            .stat-title {
                font-size: 0.9rem;
                color: var(--text-muted);
                margin-bottom: 0.5rem;
            }

            .stat-value {
                font-size: 2rem;
                font-weight: 700;
            }

            .stat-total {
                color: var(--text-light);
            }

            .stat-pending {
                color: var(--primary);
            }

            .stat-resolved {
                color: #1ed760;
            }

            .filters {
                display: flex;
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .filter-btn {
                padding: 0.6rem 1.2rem;
                background-color: var(--dark-light);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 50px;
                color: var(--text-muted);
                font-weight: 500;
                cursor: pointer;
                transition: var(--transition);
                text-decoration: none;
            }

            .filter-btn:hover, .filter-btn.active {
                background-color: var(--primary-light);
                color: var(--primary);
                border-color: var(--primary);
            }

            .reclamations-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 2rem;
            }

            .reclamations-table th {
                text-align: left;
                padding: 1rem;
                background-color: var(--dark);
                color: var(--text-muted);
                font-weight: 600;
                font-size: 0.9rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .reclamations-table td {
                padding: 1rem;
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            }

            .reclamations-table tr:hover {
                background-color: rgba(255, 255, 255, 0.03);
            }

            .status-badge {
                padding: 0.4rem 0.8rem;
                border-radius: 50px;
                font-size: 0.75rem;
                font-weight: 600;
                display: inline-block;
            }

            .status-pending {
                background-color: rgba(155, 93, 229, 0.2);
                color: var(--primary);
            }

            .status-resolved {
                background-color: rgba(30, 215, 96, 0.2);
                color: #1ed760;
            }

            .action-btn {
                padding: 0.4rem 0.8rem;
                border-radius: var(--border-radius);
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: var(--transition);
                background-color: transparent;
                border: 1px solid;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
            }

            .view-btn {
                color: var(--primary);
                border-color: var(--primary);
            }

            .view-btn:hover {
                background-color: var(--primary-light);
            }

            .delete-btn {
                color: #ff4d4d;
                border-color: #ff4d4d;
            }

            .delete-btn:hover {
                background-color: rgba(255, 77, 77, 0.1);
            }

            .empty-state {
                text-align: center;
                padding: 3rem;
                background-color: var(--dark-light);
                border-radius: var(--border-radius);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .empty-state i {
                font-size: 3rem;
                color: var(--text-muted);
                margin-bottom: 1rem;
            }

            .empty-state h3 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            .empty-state p {
                color: var(--text-muted);
                margin-bottom: 1.5rem;
            }

            .truncate {
                max-width: 300px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Details page specific styles */
            .details-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 2rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .reclamation-card {
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                padding: 2rem;
                border: 1px solid rgba(255, 255, 255, 0.1);
                margin-bottom: 2rem;
                box-shadow: var(--box-shadow);
            }

            .reclamation-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .reclamation-title {
                font-weight: 600;
                font-size: 1.2rem;
                color: var(--primary);
            }

            .reclamation-meta {
                display: flex;
                gap: 1.5rem;
                flex-wrap: wrap;
            }

            .meta-item {
                display: flex;
                flex-direction: column;
            }

            .meta-label {
                font-size: 0.85rem;
                color: var(--text-muted);
                margin-bottom: 0.25rem;
            }

            .meta-value {
                font-weight: 500;
            }

            .reclamation-content {
                margin-top: 1.5rem;
            }

            .content-label {
                font-size: 0.9rem;
                color: var(--text-muted);
                margin-bottom: 0.5rem;
            }

            .content-value {
                line-height: 1.6;
                white-space: pre-wrap;
            }

            .screenshot-container {
                margin-top: 1.5rem;
            }

            .screenshot-thumbnail {
                max-width: 300px;
                max-height: 200px;
                border-radius: var(--border-radius);
                border: 1px solid rgba(255, 255, 255, 0.2);
                cursor: pointer;
                transition: var(--transition);
            }

            .screenshot-thumbnail:hover {
                transform: scale(1.02);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            }

            .response-form {
                background: linear-gradient(145deg, var(--dark-light), var(--dark));
                border-radius: var(--border-radius);
                padding: 2rem;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: var(--box-shadow);
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            label {
                display: block;
                margin-bottom: 0.75rem;
                font-weight: 600;
                color: var(--text-light);
                font-size: 0.95rem;
            }

            textarea {
                width: 100%;
                padding: 1rem;
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: var(--border-radius);
                font-size: 1rem;
                color: var(--text-light);
                min-height: 150px;
                resize: vertical;
                transition: var(--transition);
            }

            textarea:focus {
                outline: none;
                border-color: var(--primary);
                background-color: var(--primary-light);
                box-shadow: 0 0 0 2px rgba(155, 93, 229, 0.3);
            }

            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 1rem;
                margin-top: 2rem;
            }

            .btn {
                padding: 0.8rem 1.5rem;
                font-size: 1rem;
                font-weight: 600;
                border-radius: 50px;
                cursor: pointer;
                transition: var(--transition);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
            }

            .btn-primary {
                background-color: var(--primary);
                color: white;
                border: none;
            }

            .btn-primary:hover {
                background-color: var(--primary-dark);
                transform: scale(1.02);
                box-shadow: 0 5px 15px rgba(155, 93, 229, 0.4);
            }

            .dashboard-header .btn-primary {
                background-color: var(--primary);
                color: white;
                border: none;
                padding: 0.6rem 1.2rem;
                border-radius: 50px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .dashboard-header .btn-primary:hover {
                background-color: var(--primary-dark);
                transform: scale(1.02);
                box-shadow: 0 5px 15px rgba(155, 93, 229, 0.4);
            }

            .btn-secondary {
                background-color: transparent;
                color: var(--text-muted);
                border: 1px solid var(--text-muted);
            }

            .btn-secondary:hover {
                color: var(--text-light);
                border-color: var(--text-light);
            }

            .response-container {
                margin-top: 2rem;
                padding: 1.5rem;
                background-color: var(--primary-light);
                border-radius: var(--border-radius);
                border: 1px solid rgba(155, 93, 229, 0.3);
                position: relative;
            }

            .response-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1rem;
            }

            .response-title {
                font-weight: 600;
                color: var(--primary);
            }

            .response-date {
                font-size: 0.85rem;
                color: var(--text-muted);
            }

            .response-text {
                line-height: 1.6;
                white-space: pre-wrap;
                margin-bottom: 1rem;
            }
            
            .response-actions {
                display: flex;
                justify-content: flex-end;
                gap: 0.5rem;
                margin-top: 0.5rem;
            }
            
            .response-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.85rem;
                border-radius: var(--border-radius);
                cursor: pointer;
                transition: var(--transition);
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                background-color: transparent;
                border: 1px solid;
            }
            
            .edit-btn {
                color: var(--primary);
                border-color: var(--primary);
            }
            
            .edit-btn:hover {
                background-color: var(--primary-light);
            }
            
            .delete-response-btn {
                color: #ff4d4d;
                border-color: #ff4d4d;
            }
            
            .delete-response-btn:hover {
                background-color: rgba(255, 77, 77, 0.1);
            }

            .reject-btn {
                color: #ff9800;
                border-color: #ff9800;
            }

            .reject-btn:hover {
                background-color: rgba(255, 152, 0, 0.1);
            }

            .status-rejected {
                background-color: rgba(255, 152, 0, 0.2);
                color: #ff9800;
            }
            
            .view-screenshot-link {
                margin-top: 0.5rem;
            }
            
            .screenshot-link {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.6rem 1.2rem;
                background-color: var(--primary-light);
                color: var(--primary);
                border-radius: var(--border-radius);
                font-weight: 500;
                text-decoration: none;
                transition: var(--transition);
            }
            
            .screenshot-link:hover {
                background-color: var(--primary);
                color: white;
            }
            
            @media (max-width: 768px) {
                .container {
                    padding: 0 1.5rem;
                }
                
                .stats-container {
                    flex-direction: column;
                }
                
                .reclamations-table {
                    display: block;
                    overflow-x: auto;
                }
                
                .reclamation-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .reclamation-meta {
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .form-actions {
                    flex-direction: column;
                }
                
                .btn {
                    width: 100%;
                }
                
                .response-actions {
                    flex-direction: column;
                }
                
                .response-btn {
                    width: 100%;
                    justify-content: center;
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
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM fully loaded');
        
        // Delete buttons handler
        document.querySelectorAll('.btn-supprimer').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const id = button.dataset.id;
                console.log('Delete button clicked for ID:', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#9b5de5',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await handleAction('delete', id);
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message || 'Reclamation deleted successfully',
                                    icon: 'success'
                                }).then(() => window.location.reload());
                            } else {
                                Swal.fire({
                                    title: 'Operation Failed',
                                    text: response.message || 'Could not delete the reclamation. Please try again.',
                                    icon: 'error'
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                title: 'Network Error',
                                text: 'Failed to connect to the server. Please check your connection.',
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });
        
        // Response buttons handler
        document.querySelectorAll('.btn-repondre').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const id = button.dataset.id;
                console.log('Respond button clicked for ID:', id);

                Swal.fire({
                    title: 'Write your response',
                    input: 'textarea',
                    inputPlaceholder: 'Type your response here...',
                    showCancelButton: true,
                    confirmButtonColor: '#9b5de5',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Send Response'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await handleAction('respond', id, { response: result.value });
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });
        
        // Edit Response button handler
        document.querySelectorAll('.btn-edit-response').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const id = button.dataset.id;
                const currentResponse = button.dataset.response;
                console.log('Edit response button clicked for ID:', id);

                Swal.fire({
                    title: 'Edit your response',
                    input: 'textarea',
                    inputValue: currentResponse,
                    inputPlaceholder: 'Edit your response here...',
                    showCancelButton: true,
                    confirmButtonColor: '#9b5de5',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Update Response'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const response = await handleAction('edit_response', id, { response: result.value });
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });
        
        // Delete Response button handler
        document.querySelectorAll('.btn-delete-response').forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const id = button.dataset.id;
                console.log('Delete response button clicked for ID:', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete your response and set the reclamation back to pending status",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#9b5de5',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await handleAction('delete_response', id);
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message || 'Response deleted successfully',
                                    icon: 'success'
                                }).then(() => window.location.reload());
                            } else {
                                Swal.fire({
                                    title: 'Operation Failed',
                                    text: response.message || 'Could not delete the response. Please try again.',
                                    icon: 'error'
                                });
                            }
                        } catch (error) {
                            console.error('Error deleting response:', error);
                            Swal.fire({
                                title: 'Network Error',
                                text: 'Failed to connect to the server. Please check your connection.',
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });

        // Reject buttons handler - Fixed to work with all reject buttons
        document.querySelectorAll('.btn-rejeter').forEach(button => {
            console.log('Found reject button:', button);
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const id = this.dataset.id;
                console.log('Reject button clicked for ID:', id);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will mark the reclamation as rejected",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#9b5de5',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, reject it!'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await handleAction('reject', id);
                            console.log('Reject response:', response);
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message || 'Reclamation rejected successfully',
                                    icon: 'success'
                                }).then(() => window.location.reload());
                            } else {
                                Swal.fire({
                                    title: 'Operation Failed',
                                    text: response.message || 'Could not reject the reclamation. Please try again.',
                                    icon: 'error'
                                });
                            }
                        } catch (error) {
                            console.error('Error rejecting reclamation:', error);
                            Swal.fire({
                                title: 'Network Error',
                                text: 'Failed to connect to the server. Please check your connection.',
                                icon: 'error'
                            });
                        }
                    }
                });
            });
        });

        // Handle AJAX requests
        async function handleAction(action, id, extraData = {}) {
            console.log(`Handling action: ${action} for ID: ${id}`);
            const formData = new FormData();
            formData.append('action', action);
            formData.append('id', id);

            // Append extra data (e.g., response text for 'respond' action)
            if ((action === 'respond' || action === 'edit_response') && extraData.response) {
                formData.append('response', extraData.response);
            }

            try {
                console.log('Sending request to:', 'http://localhost/projetweb/controlleur/actionsGestionReclamation.php');
                const response = await fetch('http://localhost/projetweb/controlleur/actionsGestionReclamation.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Server response:', result);
                return result;
            } catch (error) {
                console.error('Error in handleAction:', error);
                throw error;
            }
        }

        // Add Type button handler
        document.getElementById('addTypeBtn')?.addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Reclamation Type',
                input: 'text',
                inputPlaceholder: 'Enter type name...',
                showCancelButton: true,
                confirmButtonColor: '#9b5de5',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Add Type',
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Type name cannot be empty';
                    }
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const formData = new FormData();
                        formData.append('type', result.value.trim());
                        
                        const response = await fetch('http://localhost/projetweb/controlleur/add_reclamation_type.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Reclamation type added successfully',
                                icon: 'success'
                            }).then(() => window.location.reload());
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: data.message || 'Failed to add reclamation type',
                                icon: 'error'
                            });
                        }
                    } catch (error) {
                        console.error('Error adding type:', error);
                        Swal.fire({
                            title: 'Network Error',
                            text: 'Failed to connect to the server. Please check your connection.',
                            icon: 'error'
                        });
                    }
                }
            });
        });
    });
    </script>
    </body>
    </html>
    <?php
}

// Render the dashboard page
function renderDashboardPage() {
    // Get the GestionReclamationController instance
    $gestionController = new GestionReclamationController();
    
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $reclamations = $status ? Reclamation::getByStatus($status) : Reclamation::getAll();
    
    // Get statistics using the controller
    $stats = $gestionController->countReclamations();
    
    renderHeader('Reclamations Dashboard');
    ?>
    <nav class="nav-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="../../avec_connexion.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
        <div style="color: var(--text-muted); font-weight: 500;">
            <?php      
                $pdo = config::getConnexion();
                $user = getUserInfo($pdo);
                $user_id = $user->getArtisteId();
                $unreadCount = countUnreadNotifications($user_id);
                if (isSubscriptionExpired($pdo, $user_id)){
                    $type= 'expired';
                }else{
                    $type= 'valid';
                }
                $user_role = $user->getTypeUtilisateur();
            ?>
             <?php if (isset($_SESSION['user'])): ?>
                <div class="dropdown">
                    <button onclick="toggleDropdown()" class="dropdown-button">
                        <i class="fas fa-user"></i>
                        <?php if ($unreadCount > 0): ?>
                            <span class="notification-count"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <a href="../../user/overview.php" target="_blank" onclick="reloadPage(); return false;">Account 
                            <i class="fas fa-external-link-alt external-link"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-count1"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </a>

                        <script>
                            function reloadPage() {
                                // Open overview.php in a new tab
                                window.open('../../user/overview.php', '_blank');

                                // Reload the current page (avec_connexion.php)
                                location.reload();
                            }
                        </script>

                        <a href="../../avec_connexion.php" onclick="toggleBox4(<?= $userdata['artiste_id']; ?>, '<?= $userdata['nom_utilisateur']; ?>', '<?= $userdata['image_path']; ?>')" style="border:none;">Profile</a>
                        <?php
                            if ($type == 'expired') {
                                echo '<a href="../../../tunifypaiement/dashboard.php">Upgrade to Premium <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <?php
                            if ($user_role == 'admin' || $user_role == 'artiste') {
                                echo '<a href="../../../backoffice/backoffice.php">Dashboard <i class="fas fa-external-link-alt external-link"></i></a>';
                            }
                        ?>
                        <a href="#support">Support <i class="fas fa-external-link-alt external-link"></i></a>

                        <a href="#" onclick="showSettingsSection(); return false;">Settings</a>
                        <a href="../../logout.php">Log out</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/projetweb/View/tunisfy_sans_conexion/login.php" class="nav-link">Se connecter</a>
                <a href="/projetweb/View/tunisfy_sans_conexion/register.php" class="nav-link">S'inscrire</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-comment-alt"></i> Reclamations Dashboard</h1>
            <button type="button" id="addTypeBtn" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Reclamation Type
            </button>
        </div>

        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-title">Total Reclamations</div>
                <div class="stat-value stat-total"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pending</div>
                <div class="stat-value stat-pending"><?= $stats['pending'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Resolved</div>
                <div class="stat-value stat-resolved"><?= $stats['resolved'] ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Rejected</div>
                <div class="stat-value" style="color: #ff9800;"><?= $stats['rejected'] ?? 0 ?></div>
            </div>
        </div>

        <div class="filters">
            <a href="?status=" class="filter-btn <?= !isset($_GET['status']) ? 'active' : '' ?>">All</a>
            <a href="?status=pending" class="filter-btn <?= ($_GET['status'] ?? '') === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?status=resolved" class="filter-btn <?= ($_GET['status'] ?? '') === 'resolved' ? 'active' : '' ?>">Resolved</a>
            <a href="?status=rejected" class="filter-btn <?= ($_GET['status'] ?? '') === 'rejected' ? 'active' : '' ?>">Rejected</a>
        </div>

        <?php if (empty($reclamations)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No reclamations found</h3>
                <p>There are no reclamations matching your criteria.</p>
            </div>
        <?php else: ?>
            <table class="reclamations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Cause</th>
                        <th>Description</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reclamations as $reclamation): ?>
                        <tr>
                            <td>TUN-<?= str_pad($reclamation->id, 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($reclamation->full_name) ?></td>
                            <td><?= htmlspecialchars($reclamation->email) ?></td>
                            <td><?= ucfirst(htmlspecialchars($reclamation->cause)) ?></td>
                            <td class="truncate"><?= htmlspecialchars($reclamation->description) ?></td>
                            <td><?= date('M d, Y', strtotime($reclamation->created_at)) ?></td>
                            <td>
                                <span class="status-badge status-<?= $reclamation->status ?>">
                                    <?= ucfirst($reclamation->status) ?>
                                </span>
                            </td>
                            <td>
                                <a href="?page=details&id=<?= $reclamation->id ?>" class="action-btn view-btn">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <?php if ($reclamation->status === 'pending'): ?>
                                    <button type="button" class="action-btn reject-btn btn-rejeter" data-id="<?= $reclamation->id ?>">
                                        <i class="fas fa-ban"></i> Reject
                                    </button>
                                <?php endif; ?>
                                <button type="button" class="action-btn delete-btn btn-supprimer" data-id="<?= $reclamation->id ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
    renderFooter();
}

function renderDetailsPage($id) {
    $reclamation = Reclamation::getById($id);
    if (!$reclamation) {
        header('Location: back.php');
        exit;
    }
    
    renderHeader('Reclamation Details');
    ?>
    <nav class="nav-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="back.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
        <div style="color: var(--text-muted); font-weight: 500;">
            Admin Panel
        </div>
    </nav>

    <div class="container">
        <div class="details-header">
            <h1><i class="fas fa-comment-alt"></i> Reclamation Details</h1>
            <div class="status-badge status-<?= $reclamation->status ?>">
                <?= ucfirst($reclamation->status) ?>
            </div>
        </div>

        <div class="reclamation-card">
            <div class="reclamation-header">
                <div class="reclamation-title">
                    <?= ucfirst(htmlspecialchars($reclamation->cause)) ?>: 
                    <?= htmlspecialchars(substr($reclamation->description, 0, 50)) ?>...
                </div>
                <div class="reclamation-meta">
                    <div class="meta-item">
                        <span class="meta-label">Reference ID</span>
                        <span class="meta-value">TUN-<?= str_pad($reclamation->id, 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Submitted</span>
                        <span class="meta-value">
                            <?= date('F j, Y \a\t g:i A', strtotime($reclamation->created_at)) ?>
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">User</span>
                        <span class="meta-value"><?= htmlspecialchars($reclamation->email) ?></span>
                    </div>
                </div>
            </div>

            <div class="reclamation-content">
                <div class="content-label">Description</div>
                <div class="content-value"><?= htmlspecialchars($reclamation->description) ?></div>
            </div>

            <?php if ($reclamation->screenshot): ?>
                <div class="screenshot-container">
                    <div class="content-label">Screenshot</div>
                    <?php
                    // Debug the screenshot path
                    echo "<!-- Screenshot path: " . htmlspecialchars($reclamation->screenshot) . " -->";
                    
                    // Get the screenshot filename only
                    $screenshotFilename = basename($reclamation->screenshot);
                    
                    // Construct the direct URL to the screenshot
                    $screenshotUrl = "/projetweb/View/tunify_avec_connexion/reclamation/front/uploads/" . $screenshotFilename;
                    ?>
                    <div class="view-screenshot-link">
                        <a href="<?= $screenshotUrl ?>" target="_blank" class="screenshot-link">
                            <i class="fas fa-image"></i> View Screenshot
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($reclamation->response): ?>
                <div class="response-container">
                    <div class="response-header">
                        <div class="response-title">Admin Response</div>
                        <div class="response-date">
                            <?= date('F j, Y \a\t g:i A', strtotime($reclamation->updated_at)) ?>
                        </div>
                    </div>
                    <div class="response-text"><?= htmlspecialchars($reclamation->response) ?></div>
                    
                    <!-- Response Actions -->
                    <div class="response-actions">
                        <button type="button" class="response-btn edit-btn btn-edit-response" 
                                data-id="<?= $reclamation->id ?>" 
                                data-response="<?= htmlspecialchars($reclamation->response) ?>">
                            <i class="fas fa-edit"></i> Edit Response
                        </button>
                        <button type="button" class="response-btn delete-response-btn btn-delete-response" 
                                data-id="<?= $reclamation->id ?>">
                            <i class="fas fa-trash"></i> Delete Response
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($reclamation->status === 'pending'): ?>
            <div class="form-actions">
                <button type="button" class="btn btn-primary btn-repondre" data-id="<?= $reclamation->id ?>">
                    <i class="fas fa-paper-plane"></i> Send Response
                </button>
                <button type="button" class="btn btn-secondary btn-rejeter" data-id="<?= $reclamation->id ?>" style="background-color: rgba(255, 152, 0, 0.1); color: #ff9800; border-color: #ff9800;">
                    <i class="fas fa-ban"></i> Reject Reclamation
                </button>
            </div>
        <?php endif; ?>
    </div>
    <?php
    renderFooter();
}

switch ($page) {
    case 'details':
        renderDetailsPage($reclamationId);
        break;
    default:
        renderDashboardPage();
        break;
}
?>
