// /tunifiy(gamification)/backoffice/gamification/public/assets/js/guess.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize game based on current state
    initGameState();
    
    // Auto-focus on answer input when playing
    const answerInput = document.querySelector('.answer-input');
    if (answerInput) {
        answerInput.focus();
    }
});

function initGameState() {
    // Countdown timer before game starts
    if (document.getElementById('countdown')) {
        startCountdownTimer();
    }
    
    // Answer timer during gameplay
    if (document.getElementById('answer-timer')) {
        startAnswerTimer();
    }
    
    // Next round timer during feedback
    if (document.getElementById('nextTimer')) {
        startNextRoundTimer();
    }
}

function startCountdownTimer() {
    const countdownElement = document.getElementById('countdown');
    const gameData = document.getElementById('gameData');
    let counter = parseInt(countdownElement.textContent);
    
    const countdown = setInterval(() => {
        counter--;
        countdownElement.textContent = counter;
        
        if (counter <= 0) {
            clearInterval(countdown);
            redirectToAction(
                gameData.dataset.gameId, 
                gameData.dataset.action,
                gameData.dataset.redirectUrl
            );
        }
    }, 1000);
}

function startAnswerTimer() {
    const timerElement = document.getElementById('answer-timer');
    let timeLeft = parseInt(timerElement.textContent);
    
    const timer = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            document.querySelector('form').submit();
        }
    }, 1000);
}

function startNextRoundTimer() {
    const timerElement = document.getElementById('nextTimer');
    const gameData = document.getElementById('gameData');
    let timeLeft = parseInt(timerElement.textContent);
    
    const timer = setInterval(() => {
        timeLeft--;
        timerElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(timer);
            redirectToAction(
                gameData.dataset.gameId, 
                gameData.dataset.action,
                gameData.dataset.redirectUrl
            );
        }
    }, 1000);
}

function redirectToAction(gameId, action, baseUrl) {
    const url = new URL(baseUrl || window.location.href);
    url.searchParams.set('action', action);
    window.location.href = url.toString();
}

// Handle keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Focus answer input when any key is pressed during gameplay
    if (e.key.length === 1 && document.querySelector('.answer-input')) {
        document.querySelector('.answer-input').focus();
    }
    
    // Submit form on Enter key
    if (e.key === 'Enter' && document.querySelector('.answer-input')) {
        document.querySelector('form').submit();
    }
});