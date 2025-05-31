// News Comment System - Works when loaded at any time
(function() {
    // Validation constants
    const VALIDATION_RULES = {
        auteur: {
            min: 2,
            max: 50,
            pattern: /^[a-zA-ZÀ-ÿ\s\-']+$/,
            patternMessage: "Le nom d'auteur ne doit contenir que des lettres"
        },
        commentaire: {
            min: 5,
            max: 500,
            pattern: /^[a-zA-ZÀ-ÿ0-9\s\-_,.!?'"()\n\r]+$/,
            patternMessage: "Le commentaire ne doit contenir que du texte et de la ponctuation"
        }
    };

    // Utility functions
    function showError(field, message) {
        clearError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.classList.add('error-message');
        errorDiv.textContent = message;
        field.classList.add('error');
        field.parentNode.insertBefore(errorDiv, field.nextSibling);
    }

    function clearError(field) {
        field.classList.remove('error');
        const errorMessage = field.parentNode.querySelector('.error-message');
        if (errorMessage) {
            errorMessage.remove();
        }
    }

    function clearAllErrors() {
        document.querySelectorAll('.error-message').forEach(el => el.remove());
        document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
    }

    // Comment form validation
    function validateCommentForm(form) {
        let isValid = true;
        clearAllErrors();

        const auteur = form.querySelector('#auteur');
        const contenu = form.querySelector('#contenu');

        // Validate author
        if (!auteur.value.trim()) {
            showError(auteur, "Le nom est obligatoire");
            isValid = false;
        } else if (auteur.value.length < VALIDATION_RULES.auteur.min) {
            showError(auteur, `Le nom doit contenir au moins ${VALIDATION_RULES.auteur.min} caractères`);
            isValid = false;
        } else if (auteur.value.length > VALIDATION_RULES.auteur.max) {
            showError(auteur, `Le nom ne doit pas dépasser ${VALIDATION_RULES.auteur.max} caractères`);
            isValid = false;
        } else if (!VALIDATION_RULES.auteur.pattern.test(auteur.value)) {
            showError(auteur, VALIDATION_RULES.auteur.patternMessage);
            isValid = false;
        }

        // Validate comment content
        if (!contenu.value.trim()) {
            showError(contenu, "Le commentaire est obligatoire");
            isValid = false;
        } else if (contenu.value.length < VALIDATION_RULES.commentaire.min) {
            showError(contenu, `Le commentaire doit contenir au moins ${VALIDATION_RULES.commentaire.min} caractères`);
            isValid = false;
        } else if (contenu.value.length > VALIDATION_RULES.commentaire.max) {
            showError(contenu, `Le commentaire ne doit pas dépasser ${VALIDATION_RULES.commentaire.max} caractères`);
            isValid = false;
        } else if (!VALIDATION_RULES.commentaire.pattern.test(contenu.value)) {
            showError(contenu, VALIDATION_RULES.commentaire.patternMessage);
            isValid = false;
        }

        return isValid;
    }

    // Modal management
    function setupModal(modalId, openButtons, closeButtons, onConfirm) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        // Open modal
        document.querySelectorAll(openButtons).forEach(button => {
            button.addEventListener('click', function() {
                modal.style.display = 'flex';
            });
        });

        // Close modal
        closeButtons.forEach(selector => {
            const closeBtn = modal.querySelector(selector);
            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                    clearAllErrors();
                });
            }
        });

        // Close when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                clearAllErrors();
            }
        });

        // Confirm action
        const confirmBtn = modal.querySelector('.btn-confirm');
        if (confirmBtn && onConfirm) {
            confirmBtn.addEventListener('click', onConfirm);
        }
    }

    // Initialize all functionality
    function initNewsCommentSystem() {
        console.log('Initializing news comment system');

        // Setup comment form
        const commentForm = document.getElementById('commentForm');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateCommentForm(this)) {
                    this.submit();
                }
            });
        }

        // Edit comment modal
        setupModal('editCommentModal', '.edit-comment-btn', ['.modal-close', '.btn-cancel'], function() {
            const form = document.getElementById('editCommentForm');
            const contenu = form.querySelector('#edit_contenu');
            
            clearAllErrors();
            
            // Validate edit form
            if (!contenu.value.trim()) {
                showError(contenu, "Le commentaire est obligatoire");
                return;
            } else if (contenu.value.length < VALIDATION_RULES.commentaire.min) {
                showError(contenu, `Le commentaire doit contenir au moins ${VALIDATION_RULES.commentaire.min} caractères`);
                return;
            } else if (contenu.value.length > VALIDATION_RULES.commentaire.max) {
                showError(contenu, `Le commentaire ne doit pas dépasser ${VALIDATION_RULES.commentaire.max} caractères`);
                return;
            } else if (!VALIDATION_RULES.commentaire.pattern.test(contenu.value)) {
                showError(contenu, VALIDATION_RULES.commentaire.patternMessage);
                return;
            }

            // Submit form
            const formData = new FormData(form);
            fetch('news/miseajourComment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showError(contenu, data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError(contenu, 'Une erreur est survenue lors de la mise à jour du commentaire');
            });
        });

        // Delete comment modal
        setupModal('deleteCommentModal', '.delete-comment-btn', ['.modal-close', '.btn-cancel'], function() {
        // Get the form and comment ID
        const form = document.querySelector('#deleteCommentModal form');
        const commentId = document.getElementById('delete_comment_id').value;
        
        if (!commentId) {
            alert('ID de commentaire invalide');
            return;
        }

        // Submit the form using Fetch API
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form)  // Automatically includes the form data (including the comment ID)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Commentaire supprimé avec succès');
                location.reload();  // Reload the page to reflect the changes
            } else {
                alert('Erreur: ' + (data.message || 'Une erreur est survenue'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue lors de la suppression du commentaire');
        });
    });



        // Initialize edit buttons
        document.querySelectorAll('.edit-comment-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const content = this.dataset.content;
                
                document.getElementById('edit_comment_id').value = id;
                document.getElementById('edit_contenu').value = content;
            });
        });

        // Initialize delete buttons
        document.querySelectorAll('.delete-comment-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete_comment_id').value = this.dataset.id;
            });
        });

        // Add CSS for error styling if not already added
        if (!document.getElementById('news-comment-system-styles')) {
            const style = document.createElement('style');
            style.id = 'news-comment-system-styles';
            style.textContent = `
                .error-message {
                    color: #dc3545;
                    font-size: 0.875rem;
                    margin-top: 0.25rem;
                }
                .error {
                    border-color: #dc3545 !important;
                }
                .modal {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0,0,0,0.5);
                    justify-content: center;
                    align-items: center;
                    z-index: 1000;
                }
                .modal-content {
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 5px;
                    max-width: 500px;
                    width: 100%;
                }
            `;
            document.head.appendChild(style);
        }
    }

    // Initialize immediately (will work whether loaded during DOMContentLoaded or later)
    initNewsCommentSystem();

    // Export the init function in case you need to call it manually
    window.initNewsCommentSystem = initNewsCommentSystem;
})();