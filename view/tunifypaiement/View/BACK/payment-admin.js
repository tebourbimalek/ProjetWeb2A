document.addEventListener('DOMContentLoaded', function() {
    initializeApplication();
});

function initializeApplication() {
    // Vérifie que tous les éléments nécessaires existent
    if (!checkRequiredElements()) {
        showFatalError("Éléments HTML manquants - Vérifiez la structure de la page");
        return;
    }

    setupTabEvents();
    loadInitialData();
}

function checkRequiredElements() {
    const requiredElements = [
        '#recent-transactions-body',
        '#card-payments-body',
        '#mobile-payments-body',
        '.tab'
    ];
    
    return requiredElements.every(selector => {
        const exists = document.querySelector(selector);
        if (!exists) console.error("Element manquant:", selector);
        return exists;
    });
}

function setupTabEvents() {
    document.querySelectorAll('.tab').forEach(tab => {
        tab.addEventListener('click', async function() {
            const tabId = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            await handleTabChange(tabId);
        });
    });
}

async function loadInitialData() {
    showLoadingState();
    
    try {
        const response = await fetchData();
        if (response) {
            updateAllTables(response.data);
        }
    } catch (error) {
        handleDataError(error);
    } finally {
        hideLoadingState();
    }
}

async function handleTabChange(tabId) {
    updateTabUI(tabId);
    showLoadingState(tabId);
    
    try {
        const response = await fetchData();
        if (response) {
            updateSingleTab(tabId, response.data);
        }
    } catch (error) {
        handleTabError(tabId, error);
    } finally {
        hideLoadingState(tabId);
    }
}

function updateTabUI(tabId) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}

async function fetchData() {
    try {
        const response = await fetch('get_transactions.php', {
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data || data.status !== 'success') {
            throw new Error(data.message || 'Format de données invalide');
        }
        
        return data;
        
    } catch (error) {
        console.error("Erreur fetchData:", error);
        throw error;
    }
}

function updateAllTables(data) {
    if (!data) return;
    
    fillTableData({
        id: 'recent-transactions-body',
        data: data.recent,
        columns: ['ID', 'Date', 'payment_method', 'Abonnement', 'user_id'],
        formatter: formatDefaultRow
    });
    
    fillTableData({
        id: 'card-payments-body',
        data: data.cards,
        columns: ['ID', 'Date_Expiration', 'transaction_id', 'Type_Carte'],
        formatter: formatCardRow
    });
    
    fillTableData({
        id: 'mobile-payments-body',
        data: data.mobile,
        columns: ['ID', 'Date_Expiration', 'transaction_id', 'mobile_provider'],
        formatter: formatMobileRow
    });
}

function updateSingleTab(tabId, data) {
    if (!data) return;
    
    switch (tabId) {
        case 'recent':
            fillTableData({
                id: 'recent-transactions-body',
                data: data.recent,
                columns: ['ID', 'Date', 'payment_method', 'Abonnement', 'user_id'],
                formatter: formatDefaultRow
            });
            break;
            
        case 'cards':
            fillTableData({
                id: 'card-payments-body',
                data: data.cards,
                columns: ['ID', 'Date_Expiration', 'transaction_id', 'Type_Carte'],
                formatter: formatCardRow
            });
            break;
            
        case 'mobile':
            fillTableData({
                id: 'mobile-payments-body',
                data: data.mobile,
                columns: ['ID', 'Date_Expiration', 'transaction_id', 'mobile_provider'],
                formatter: formatMobileRow
            });
            break;
    }
}

function fillTableData({id, data, columns, formatter}) {
    const tbody = document.getElementById(id);
    if (!tbody) {
        console.error(`Table body #${id} introuvable`);
        return;
    }
    
    try {
        tbody.innerHTML = data && data.length > 0
            ? data.map(item => formatter(item, columns)).join('')
            : `<tr><td colspan="${columns.length + 1}">Aucune donnée disponible</td></tr>`;
    } catch (error) {
        console.error(`Erreur lors du remplissage de ${id}:`, error);
        tbody.innerHTML = `<tr><td colspan="${columns.length + 1}" class="error">Erreur d'affichage</td></tr>`;
    }
}

// Formatters
function formatDefaultRow(item, columns) {
    return `
        <tr>
            ${columns.map(col => `<td>${item[col] || 'N/A'}</td>`).join('')}
            <td><button class="btn btn-edit"><i class="fas fa-edit"></i></button></td>
        </tr>
    `;
}

function formatCardRow(item) {
    return `
        <tr>
            <td>${item.ID || 'N/A'}</td>
            <td>${item.Date_Expiration || 'N/A'}</td>
            <td>${item.transaction_id || 'N/A'}</td>
            <td>${item.Type_Carte || 'N/A'}</td>
            <td>•••• ${item.Numero_Carte ? item.Numero_Carte.slice(-4) : '****'}</td>
            <td><button class="btn btn-edit"><i class="fas fa-edit"></i></button></td>
        </tr>
    `;
}

function formatMobileRow(item) {
    return `
        <tr>
            <td>${item.ID || 'N/A'}</td>
            <td>${item.Date_Expiration || 'N/A'}</td>
            <td>${item.transaction_id || 'N/A'}</td>
            <td>${item.mobile_provider || 'N/A'}</td>
            <td>•••• ••${item.phone_number ? item.phone_number.slice(-2) : '**'}</td>
            <td><button class="btn btn-edit"><i class="fas fa-edit"></i></button></td>
        </tr>
    `;
}

// Error handling
function handleDataError(error) {
    console.error("Erreur de données:", error);
    
    const errorHtml = `
        <tr>
            <td colspan="6" class="error-message">
                Erreur de chargement: ${error.message}
                <button onclick="window.location.reload()">Actualiser</button>
            </td>
        </tr>
    `;
    
    ['recent-transactions-body', 'card-payments-body', 'mobile-payments-body'].forEach(id => {
        const tbody = document.getElementById(id);
        if (tbody) tbody.innerHTML = errorHtml;
    });
}

function handleTabError(tabId, error) {
    console.error(`Erreur onglet ${tabId}:`, error);
    
    const tbody = document.querySelector(`#${tabId} tbody`);
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="error-message">
                    Erreur: ${error.message}
                    <button onclick="handleTabChange('${tabId}')">Réessayer</button>
                </td>
            </tr>
        `;
    }
}

function showFatalError(message) {
    const container = document.createElement('div');
    container.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: #ff6b6b;
        color: white;
        padding: 20px;
        z-index: 1000;
        text-align: center;
    `;
    container.innerHTML = `
        <h3>Erreur Critique</h3>
        <p>${message}</p>
        <button onclick="window.location.reload()">Actualiser la page</button>
    `;
    document.body.prepend(container);
}

function showLoadingState(containerId) {
    // Implémentez un indicateur de chargement si nécessaire
}

function hideLoadingState(containerId) {
    // Cachez l'indicateur de chargement
}