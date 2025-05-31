// JavaScript for dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.profile-menu');
    const dropdownContent = document.querySelector('.dropdown-content');
    
    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
            dropdownContent.style.display = 'none';
        }
    });
    
    // Toggle dropdown on profile button click
    dropdown.querySelector('.profile-button').addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdownContent.style.display === 'block') {
            dropdownContent.style.display = 'none';
        } else {
            dropdownContent.style.display = 'block';
        }
    });
});