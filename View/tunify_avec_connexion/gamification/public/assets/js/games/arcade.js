document.addEventListener('DOMContentLoaded', function() {
    // Game card hover effects
    const gameCards = document.querySelectorAll('.game-card');
    
    gameCards.forEach(card => {
        // Add hover effect
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.03)';
            this.style.transition = 'transform 0.3s ease';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
        
        // Add click effect for active games
        if (!card.classList.contains('inactive-game')) {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        }
    });
    
    // Header button effects
    const headerButtons = document.querySelectorAll('.header-buttons button');
    
    headerButtons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
            this.style.transition = 'transform 0.2s ease';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        button.addEventListener('click', function() {
            // Add functionality for header buttons here
            console.log(this.textContent + ' button clicked');
            // You can add redirects or modal openings here
        });
    });
});