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
            console.log('Sending request to:', 'http://localhost/projetweb/controller/actionsGestionReclamation.php');
            const response = await fetch('http://localhost/projetweb/controller/actionsGestionReclamation.php', {
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
                    
                    const response = await fetch('http://localhost/projetweb/controller/add_reclamation_type.php', {
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
