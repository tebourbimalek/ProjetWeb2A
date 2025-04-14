<?php
require_once "C:/xampp/htdocs/Tunify/Config.php";
require_once "C:/xampp/htdocs/Tunify/model/Reclamation.php";

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$reclamationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

function renderHeader($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tunify Admin - <?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            }

            .modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.8);
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }

            .modal-content {
                max-width: 90%;
                max-height: 90%;
            }

            .modal-close {
                position: absolute;
                top: 2rem;
                right: 2rem;
                color: white;
                font-size: 2rem;
                cursor: pointer;
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
            // Delete buttons handler
            document.querySelectorAll('.btn-supprimer').forEach(button => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        const id = button.dataset.id;

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

    // URL of the backend API (matches your existing PHP endpoint)
const actionsURL = 'http://localhost/Tunify/controller/actionsGestionReclamation.php';

async function handleAction(action, id, extraData = {}) {
    const formData = new FormData();
    formData.append('action', action);
    formData.append('id', id);

    // Append extra data (e.g., response text for 'respond' action)
    if (action === 'respond' && extraData.response) {
        formData.append('response', extraData.response);
    }

    try {
        const response = await fetch(actionsURL, {
            method: 'POST',
            body: formData, // FormData is automatically encoded
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
            // Image modal handling
            window.openModal = function(src) {
                const modal = document.getElementById('imageModal');
                const modalImg = document.getElementById('modalImage');
                modalImg.src = src;
                modal.style.display = 'flex';
            };

            document.getElementById('modalClose').addEventListener('click', () => {
                document.getElementById('imageModal').style.display = 'none';
            });

            document.getElementById('imageModal').addEventListener('click', (e) => {
                if (e.target === this) {
                    document.getElementById('imageModal').style.display = 'none';
                }
            });
        });
    </script>
    </body>
    </html>
    <?php
}

// Render the dashboard page
function renderDashboardPage() {
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $reclamations = $status ? Reclamation::getByStatus($status) : Reclamation::getAll();
    $stats = Reclamation::getStatistics();
    
    renderHeader('Reclamations Dashboard');
    ?>
    <nav class="nav-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="front.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
            <img src="../Tunify.png" alt="Tunify Logo" class="logo" width="50">
        </div>
        <div style="color: var(--text-muted); font-weight: 500;">
            Admin Panel
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-comment-alt"></i> Reclamations Dashboard</h1>
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
        </div>

        <div class="filters">
            <a href="?status=" class="filter-btn <?= !isset($_GET['status']) ? 'active' : '' ?>">All</a>
            <a href="?status=pending" class="filter-btn <?= ($_GET['status'] ?? '') === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="?status=resolved" class="filter-btn <?= ($_GET['status'] ?? '') === 'resolved' ? 'active' : '' ?>">Resolved</a>
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
                                <button class="action-btn delete-btn btn-supprimer" data-id="<?= $reclamation->id ?>">
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
            <img src="../Tunify.png" alt="Tunify Logo" class="logo" width="50">
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
                    <img src="../uploads/screenshots/<?= $reclamation->screenshot ?>" 
                         class="screenshot-thumbnail" alt="Issue screenshot" 
                         onclick="openModal('../uploads/screenshots/<?= $reclamation->screenshot ?>')">
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
                </div>
            <?php endif; ?>
        </div>

        <?php if ($reclamation->status === 'pending'): ?>
            <div class="form-actions">
                <button class="btn btn-primary btn-repondre" data-id="<?= $reclamation->id ?>">
                    <i class="fas fa-paper-plane"></i> Send Response
                </button>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal" id="imageModal">
        <span class="modal-close" id="modalClose">&times;</span>
        <img class="modal-content" id="modalImage">
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

