document.addEventListener('DOMContentLoaded', function() {
    console.log("Initialisation des notifications...");

    const notificationIcon = document.querySelector('.notification-icon');
    const notificationBadge = document.querySelector('.notification-badge');
    const notificationDropdown = document.getElementById('notif-list');
    const notificationContainer = document.getElementById('notification-container');

    // Charger les notifications
    function loadNotifications() {
        console.log("Chargement des notifications...");
        fetch('controller/notification_controller.php?action=get_notifications')
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau');
                return response.json();
            })
            .then(data => {
                console.log("Notifications reçues:", data);
                updateNotificationBadge(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(error => {
                console.error("Erreur:", error);
                notificationContainer.innerHTML = '<div class="notification-item">Erreur de chargement</div>';
            });
    }

    function updateNotificationBadge(count) {
        console.log("Mise à jour badge:", count);
        notificationBadge.textContent = count > 0 ? count : '';
        notificationBadge.style.display = count > 0 ? 'flex' : 'none';
    }

    function renderNotifications(notifications) {
        notificationContainer.innerHTML = '';
        
        // Afficher seulement les 5 dernières notifications
        const recentNotifications = notifications.slice(0, 5);
        
        if (recentNotifications.length === 0) {
            notificationContainer.innerHTML = '<div class="notification-item">Aucune notification</div>';
            return;
        }

        recentNotifications.forEach(notif => {
            const notifElement = document.createElement('div');
            notifElement.className = `notification-item ${notif.est_lue ? '' : 'unread'}`;
            notifElement.innerHTML = `
                <div>${notif.message}</div>
                <div class="time">${notif.formatted_date}</div>
            `;
            notificationContainer.appendChild(notifElement);
        });
    }

    // Charger au démarrage
    loadNotifications();
    
    // Recharger toutes les 5 minutes
    setInterval(loadNotifications, 300000);
});

// Fonction pour basculer le menu des notifications
function toggleNotifMenu() {
    const list = document.getElementById('notif-list');
    list.style.display = list.style.display === 'none' ? 'block' : 'none';
    
    // Recharger les notifications à chaque ouverture
    if (list.style.display === 'block') {
        document.querySelector('.notification-badge').textContent = '';
        document.querySelector('.notification-badge').style.display = 'none';
    }
}

// Marquer toutes les notifications comme lues
function markAllAsRead() {
    fetch('controller/notification_controller.php?action=mark_all_read')
        .then(response => response.json())
        .then(data => {
            console.log("Notifications marquées comme lues:", data);
            // Mettre à jour l'interface
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
            });
        })
        .catch(console.error);
}