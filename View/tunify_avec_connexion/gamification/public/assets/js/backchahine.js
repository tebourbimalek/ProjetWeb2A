// Configuration
const config = {
    baseURL: "/projetweb/View/backoffice/backoffice.php",
    imageBasePath: "/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/"
};

// State Management
const state = {
    currentGame: null,
    currentQuestion: null
};

// DOM Elements
const elements = {
    tabs: document.querySelectorAll(".sidebar-menu li"),
    contents: document.querySelectorAll(".tab-content"),
    addGameBtn: document.getElementById("add-new-game-btn"),
    addGameForm: document.getElementById("add-game-form"),
    editIdField: document.getElementById("edit-id-game"),
    addQuestionBtn: document.getElementById('add-new-question-btn'),
    backButton: document.getElementById('back-to-games-btn'),
    questionsBody: document.getElementById("questions-table-body"),
    headerTitle: document.getElementById("header-title"),
    currentGameName: document.getElementById('current-game-name'),
    currentGameType: document.getElementById('current-game-type'),
    questionFormContainer: document.getElementById('question-form-container'),
    gamesSection: document.getElementById('games-section'),
    questionsSection: document.getElementById('questions-section'),
    // Reward elements
    addRewardBtn: document.getElementById('add-new-reward-btn'),
    addRewardForm: document.getElementById('add-reward-form'),
    editRewardIdField: document.getElementById('edit-id-reward'),
    rewardsTable: document.getElementById('rewards-table')
};

// Initialization
document.addEventListener("DOMContentLoaded", () => {
    initTabNavigation();
    initGameHandlers();
    initQuestionHandlers();
    initRewardHandlers();
    
    
    // Initialize search functionality
    const searchGamesInput = document.getElementById('search-games');
    if (searchGamesInput) {
        searchGamesInput.addEventListener('input', handleGameSearch);
    }
});

// Tab Navigation
function initTabNavigation() {
    elements.tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            elements.tabs.forEach(t => t.classList.remove("active"));
            tab.classList.add("active");

            const selectedTab = tab.getAttribute("data-tab");
            elements.contents.forEach(content => {
                content.classList.remove("active");
                if (content.id === selectedTab) {
                    content.classList.add("active");
                }
            });

            // Toggle appropriate buttons based on selected tab
            elements.addGameBtn.style.display = selectedTab === "jeux" ? "inline-block" : "none";
            elements.addGameForm.style.display = "none";
            elements.addRewardBtn.style.display = selectedTab === "recompenses" ? "inline-block" : "none";
            elements.addRewardForm.style.display = "none";
        });
    });
}

// Game CRUD Operations
function initGameHandlers() {
    // Add Game Form Toggle
    elements.addGameBtn.addEventListener("click", () => {
        elements.addGameForm.reset();
        elements.editIdField.value = "";
        elements.addGameForm.action = `${config.baseURL}?action=add`;
        elements.addGameForm.querySelector("button[type='submit']").textContent = "Add Game";
        elements.addGameForm.style.display = elements.addGameForm.style.display === "none" ? "block" : "none";
    });

    // Delete Game
    document.querySelectorAll(".delete-game").forEach(btn => {
        btn.addEventListener("click", handleDeleteGame);
    });

    // Edit Game
    document.querySelectorAll(".edit-game").forEach(btn => {
        btn.addEventListener("click", handleEditGame);
    });

    // Show Questions
    document.querySelectorAll('.show-questions').forEach(button => {
        button.addEventListener('click', handleShowQuestions);
    });
}

function handleDeleteGame() {
    const id = this.getAttribute("data-id");
    if (confirm("Are you sure you want to delete this game?")) {
        fetch(`${config.baseURL}?action=delete`, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${encodeURIComponent(id)}`
        })
        .then(handleResponse)
        .then(() => this.closest("tr").remove())
        .catch(handleError("Failed to delete the game"));
    }
}

function handleEditGame() {
    const row = this.closest("tr");
    elements.addGameForm.style.display = "block";
    elements.editIdField.value = this.getAttribute("data-id");
    elements.addGameForm.nom_jeu.value = this.getAttribute("data-nom");
    elements.addGameForm.type_jeu.value = this.getAttribute("data-type");
    elements.addGameForm.points_attribues.value = this.getAttribute("data-points");
    elements.addGameForm.statut.value = this.getAttribute("data-statut");
    elements.addGameForm.action = `${config.baseURL}?action=update`;
    elements.addGameForm.querySelector("button[type='submit']").textContent = "Update Game";
}

// Question Management
function initQuestionHandlers() {
    // Add Question
    elements.addQuestionBtn.addEventListener('click', handleAddQuestion);

    // Back Button
    elements.backButton.addEventListener('click', handleBackToGames);

    // Handle question actions (edit/delete)
    elements.questionsBody.addEventListener('click', handleQuestionActions);
}

function handleShowQuestions() {
    state.currentGame = {
        id: this.dataset.id,
        name: this.dataset.nom,
        type: this.dataset.type
    };

    // Update UI
    elements.headerTitle.textContent = `Questions of ${state.currentGame.name}`;
    elements.currentGameName.textContent = state.currentGame.name;
    elements.currentGameType.textContent = state.currentGame.type;
    elements.gamesSection.style.display = 'none';
    elements.questionsSection.style.display = 'block';
    elements.addGameBtn.style.display = 'none';
    elements.addQuestionBtn.style.display = 'inline-block';
    elements.backButton.style.display = 'inline-block';
    elements.questionFormContainer.style.display = 'none';

    loadQuestionsForGame(state.currentGame.id);
}

function handleAddQuestion() {
    if (!state.currentGame) {
        alert("Please select a game first");
        return;
    }

    elements.questionFormContainer.innerHTML = `
        <form id="dynamic-question-form" enctype="multipart/form-data">
            <input type="hidden" name="id_game" value="${state.currentGame.id}">
            ${getQuestionFormHTML(state.currentGame.type)}
            <button type="submit" class="btn btn-success">Add Question</button>
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('question-form-container').style.display='none'">Cancel</button>
        </form>
    `;
    elements.questionFormContainer.style.display = 'block';
    
    document.getElementById('dynamic-question-form').addEventListener('submit', (e) => {
        e.preventDefault();
        handleQuestionSubmit('add_question');
    });
}

function handleBackToGames() {
    elements.gamesSection.style.display = 'block';
    elements.questionsSection.style.display = 'none';
    elements.addGameBtn.style.display = 'inline-block';
    elements.addQuestionBtn.style.display = 'none';
    elements.backButton.style.display = 'none';
    elements.questionFormContainer.style.display = 'none';
    elements.headerTitle.textContent = 'Gamification Dashboard';
}

function handleQuestionActions(e) {
    const editBtn = e.target.closest('.modify-question');
    const deleteBtn = e.target.closest('.delete-question');

    if (editBtn) {
        handleEditQuestion(editBtn);
    } else if (deleteBtn) {
        handleDeleteQuestion(deleteBtn);
    }
}

function handleEditQuestion(button) {
    const questionId = button.dataset.id;
    
    fetch(`${config.baseURL}?action=get_question&id=${questionId}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(question => {
        state.currentQuestion = question;
        
        elements.questionFormContainer.innerHTML = `
            <form id="dynamic-question-form" enctype="multipart/form-data">
                <input type="hidden" name="id_question" value="${questionId}">
                <input type="hidden" name="id_game" value="${state.currentGame.id}">
                ${getQuestionFormHTML(state.currentGame.type, question)}
                <button type="submit" class="btn btn-success">Update Question</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('question-form-container').style.display='none'">Cancel</button>
            </form>
        `;
        elements.questionFormContainer.style.display = 'block';
        
        document.getElementById('dynamic-question-form').addEventListener('submit', (e) => {
            e.preventDefault();
            handleQuestionSubmit('update_question');
        });
    })
    .catch(error => {
        console.error('Error loading question:', error);
        alert('Failed to load question details');
    });
}

function handleDeleteQuestion(button) {
    const questionId = button.dataset.id;
    
    if (confirm("Are you sure you want to delete this question?")) {
        fetch(`${config.baseURL}?action=delete_question`, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id_question=${encodeURIComponent(questionId)}`
        })
        .then(handleResponse)
        .then(() => {
            button.closest("tr").remove();
        })
        .catch(handleError("Failed to delete question"));
    }
}

function handleQuestionSubmit(action) {
    const form = document.getElementById('dynamic-question-form');
    const formData = new FormData(form);
    
    fetch(`${config.baseURL}?action=${action}`, {
        method: 'POST',
        body: formData
    })
    .then(handleResponse)
    .then(() => {
        alert(`Question ${action === 'add_question' ? 'added' : 'updated'} successfully!`);
        elements.questionFormContainer.style.display = 'none';
        loadQuestionsForGame(state.currentGame.id);
    })
    .catch(handleError(`Failed to ${action === 'add_question' ? 'add' : 'update'} question`));
}

// Question Form Handling
function getQuestionFormHTML(gameType, question = null) {
    const getValue = (field) => question ? question[field] || '' : '';
    const isChecked = (field) => question && question[field] ? 'checked' : '';
    
    let formHTML = `
        <div class="form-group">
            <label>Question Text:</label>
            <input type="text" name="question_text" class="form-control" value="${getValue('question_text')}" required>
        </div>
    `;

    switch(gameType) {
        case 'guess':
            formHTML += `
                <div class="form-group">
                    <label>Correct Answer:</label>
                    <input type="text" name="correct_answer" class="form-control" value="${getValue('correct_answer')}" required>
                </div>
            `;
            break;
            
        case 'quizz':
            formHTML += `
                <div class="form-group">
                    <label>Option 1:</label>
                    <input type="text" name="option_1" class="form-control" value="${getValue('option_1')}" required>
                </div>
                <div class="form-group">
                    <label>Option 2:</label>
                    <input type="text" name="option_2" class="form-control" value="${getValue('option_2')}" required>
                </div>
                <div class="form-group">
                    <label>Option 3:</label>
                    <input type="text" name="option_3" class="form-control" value="${getValue('option_3')}" required>
                </div>
                <div class="form-group">
                    <label>Option 4:</label>
                    <input type="text" name="option_4" class="form-control" value="${getValue('option_4')}" required>
                </div>
                <div class="form-group">
                    <label>Correct Option:</label>
                    <select name="correct_option" class="form-control" required>
                        <option value="1" ${getValue('correct_option') == 1 ? 'selected' : ''}>Option 1</option>
                        <option value="2" ${getValue('correct_option') == 2 ? 'selected' : ''}>Option 2</option>
                        <option value="3" ${getValue('correct_option') == 3 ? 'selected' : ''}>Option 3</option>
                        <option value="4" ${getValue('correct_option') == 4 ? 'selected' : ''}>Option 4</option>
                    </select>
                </div>
            `;
            break;
            
        case 'puzzle':
            formHTML += `
                <div class="form-group">
                    <label>Is Correct (for true/false puzzles):</label>
                    <select name="is_true" class="form-control">
                        <option value="1" ${isChecked('is_true')}>True</option>
                        <option value="0" ${!isChecked('is_true')}>False</option>
                    </select>
                </div>
            `;
            break;
    }

    // Common media fields for all question types
    formHTML += `
        <div class="form-group">
            <label>Image (optional):</label>
            <input type="file" name="image" accept="image/*" class="form-control">
            ${question && question.image_path ? `<img src="${config.imageBasePath}${question.image_path}" width="100" style="margin-top:10px;">` : ''}
        </div>
        <div class="form-group">
            <label>Audio File (MP3 - optional):</label>
            <input type="file" name="mp3" accept="audio/mp3" class="form-control">
            ${question && question.mp3_path ? `
                <audio controls src="/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/audio/${question.mp3_path}" style="width:100%; margin-top:10px;"></audio>
                <small class="text-muted">Current file: ${question.mp3_path}</small>
            ` : ''}
        </div>
    `;

    return formHTML;
}

// Question CRUD Operations
function loadQuestionsForGame(gameId) {
    fetch(`${config.baseURL}?action=load_questions&id_game=${gameId}`)
        .then(response => response.json())
        .then(questions => {
            elements.questionsBody.innerHTML = renderQuestionsTable(questions, state.currentGame.type);
        })
        .catch(handleError("Failed to load questions"));
}

function renderQuestionsTable(questions, gameType) {
    if (!questions || questions.length === 0) {
        return '<tr><td colspan="5" style="text-align:center;">No questions found.</td></tr>';
    }

    return questions.map(q => {
        const imagePath = q.image_path ? `${config.imageBasePath}${q.image_path}` : '';
        const audioPath = q.mp3_path ? `/projetweb/View/tunify_avec_connexion/gamification/sources/uploads/audio/${q.mp3_path}` : '';
        
        // Safely escape HTML and handle undefined values
        const safeEscape = (value) => {
            return value ? escapeHtml(value) : '';
        };

        switch(gameType) {
            case 'guess':
                return `
                    <tr>
                        <td>${q.id_question}</td>
                        <td>${safeEscape(q.question_text)}</td>
                        <td>${safeEscape(q.correct_answer)}</td>
                        <td>${imagePath ? `<img src="${imagePath}" width="50">` : 'No image'}</td>
                        <td>
                            <button class="btn btn-warning modify-question" data-id="${q.id_question}">Edit</button>
                            <button class="btn btn-danger delete-question" data-id="${q.id_question}">Delete</button>
                        </td>
                    </tr>
                `;
            case 'quizz':
                return `
                    <tr>
                        <td>${q.id_question}</td>
                        <td>${safeEscape(q.question_text)}</td>
                        <td>
                            <ol>
                                <li class="${q.correct_option == 1 ? 'text-success' : ''}">${safeEscape(q.option_1)}</li>
                                <li class="${q.correct_option == 2 ? 'text-success' : ''}">${safeEscape(q.option_2)}</li>
                                <li class="${q.correct_option == 3 ? 'text-success' : ''}">${safeEscape(q.option_3)}</li>
                                <li class="${q.correct_option == 4 ? 'text-success' : ''}">${safeEscape(q.option_4)}</li>
                            </ol>
                        </td>
                        <td>${imagePath ? `<img src="${imagePath}" width="50">` : 'No image'}</td>
                        <td>
                            <button class="btn btn-warning modify-question" data-id="${q.id_question}">Edit</button>
                            <button class="btn btn-danger delete-question" data-id="${q.id_question}">Delete</button>
                        </td>
                    </tr>
                `;
            case 'puzzle':
                return `
                    <tr>
                        <td>${q.id_question}</td>
                        <td>${safeEscape(q.question_text)}</td>
                        <td>${imagePath ? `<img src="${imagePath}" width="100">` : 'No image'}</td>
                        <td>${q.is_true ? 'True' : 'False'}</td>
                        <td>
                            <button class="btn btn-warning modify-question" data-id="${q.id_question}">Edit</button>
                            <button class="btn btn-danger delete-question" data-id="${q.id_question}">Delete</button>
                        </td>
                    </tr>
                `;
        }
    }).join('');
}

// Reward CRUD Operations
function initRewardHandlers() {
    // Add Reward Form Toggle
    if (elements.addRewardBtn) {
        elements.addRewardBtn.addEventListener("click", () => {
            elements.addRewardForm.reset();
            elements.editRewardIdField.value = "";
            elements.addRewardForm.action = `${config.baseURL}?action=add_reward`;
            elements.addRewardForm.querySelector("button[type='submit']").textContent = "Ajouter Récompense";
            document.getElementById('reward-image-preview').style.display = 'none';
            elements.addRewardForm.style.display = elements.addRewardForm.style.display === "none" ? "block" : "none";
        });
    }

    // Delete Reward
    document.querySelectorAll(".delete-reward").forEach(btn => {
        btn.addEventListener("click", handleDeleteReward);
    });

    // Edit Reward
    document.querySelectorAll(".edit-reward").forEach(btn => {
        btn.addEventListener("click", handleEditReward);
    });

    // Add First Reward button
    document.getElementById('add-first-reward')?.addEventListener('click', () => {
        elements.addRewardBtn.click();
    });
}

function handleDeleteReward() {
    const id = this.getAttribute("data-id");
    if (confirm("Êtes-vous sûr de vouloir supprimer cette récompense ?")) {
        fetch(`${config.baseURL}?action=delete_reward`, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id_reward=${encodeURIComponent(id)}`
        })
        .then(handleResponse)
        .then(() => this.closest("tr").remove())
        .catch(handleError("Échec de la suppression"));
    }
}

function handleEditReward() {
    const row = this.closest("tr");
    elements.addRewardForm.style.display = "block";
    elements.editRewardIdField.value = this.getAttribute("data-id");
    elements.addRewardForm.querySelector('[name="nom_reward"]').value = this.getAttribute("data-nom");
    elements.addRewardForm.querySelector('[name="points_requis"]').value = this.getAttribute("data-points");
    elements.addRewardForm.querySelector('[name="type_reward"]').value = this.getAttribute("data-type");
    elements.addRewardForm.querySelector('[name="disponibilite"]').value = this.getAttribute("data-dispo");
    
    const imagePath = this.getAttribute("data-image");
    const imagePreview = document.getElementById('reward-image-preview');
    if (imagePath) {
        imagePreview.src = `${config.imageBasePath}${imagePath}`;
        imagePreview.style.display = 'block';
        imagePreview.style.maxWidth = '200px';
        imagePreview.style.marginTop = '10px';
    } else {
        imagePreview.style.display = 'none';
    }
    
    elements.addRewardForm.action = `${config.baseURL}?action=update_reward`;
    elements.addRewardForm.querySelector("button[type='submit']").textContent = "Update Reward";
}

// Search Functionality
function handleGameSearch() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#games-table tbody tr');
    
    rows.forEach(row => {
        const gameName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const gameType = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const shouldShow = gameName.includes(searchTerm) || gameType.includes(searchTerm);
        row.style.display = shouldShow ? '' : 'none';
    });
}

// Helper Functions
function handleResponse(response) {
    if (!response.ok) throw new Error(response.statusText);
    return response.text().then(text => {
        if (text.trim() !== 'success') throw new Error(text);
        return text;
    });
}

function handleError(message) {
    return error => {
        console.error(message, error);
        alert(`${message}: ${error.message}`);
    };
}

function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
document.getElementById('add-reward-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });

        const result = await response.text();
        
        if (!response.ok) {
            throw new Error(result || 'Server responded with status ' + response.status);
        }

        if (result !== 'success') {
            throw new Error(result);
        }

        alert('Reward saved successfully!');
        window.location.reload();
    } catch (error) {
        console.error('Error details:', error);
        alert('Error saving reward: ' + error.message);
    }
});