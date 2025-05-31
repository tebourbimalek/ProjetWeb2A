<?php
session_start();


require_once 'C:\xampp\htdocs\projetweb\controlleur\CommentsController.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\ReactionController.php';
require_once 'C:\xampp\htdocs\projetweb\model\config.php';
require_once 'C:\xampp\htdocs\projetweb\model\Reaction.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\controlleruser.php';
require_once 'C:\xampp\htdocs\projetweb\controlleur\functionsnews.php';

$pdo = config::getConnexion();
$user = getUserInfo($pdo);
$user_id = $user->getArtisteId();


$id=$_POST['playlist_id'];


$commentController = new CommentsController();
$reactionController = new ReactionController();

// Récupérer les détails de la news
try {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomutilisateur = $user->getNomUtilisateur();
    


    // Récupérer les commentaires pour cette news
    $comments = $commentController->listCommentsByNews($id);
    $userHasReacted = hasUserReactednews($pdo, $id, $user_id);
    $countaimer = countReactionsByNews($pdo, $id);
    // Vérifier si l'utilisateur a déjà réagi (basé sur IP)
   

} catch (Exception $e) {
    header('Location: ../tunisfy_sans_conexion/page_sans_connexion.php');
    exit;
}

// Traitement du formulaire de commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'comment' && isset($_POST['auteur']) && isset($_POST['contenu'])) {
            $auteur = htmlspecialchars($_POST['auteur']);
            $contenu = htmlspecialchars($_POST['contenu']);
            
            if (!empty($auteur) && !empty($contenu)) {
                $newComment = new Comments(null, $id, $auteur, $contenu, date('Y-m-d H:i:s'));
                $commentController->addComment($newComment);
                
                // Redirection pour éviter les soumissions multiples
                header("Location: view_news.php?id=$id&commented=1");
                exit;
            }
        } elseif ($_POST['action'] === 'reaction' && !$userHasReacted) {
            $newReaction = new Reaction(null, $id, $userIp, date('Y-m-d H:i:s'));
            $reactionController->addReaction($newReaction);
            
            // Redirection pour éviter les soumissions multiples
            header("Location: view_news.php?id=$id&reacted=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news['titre']); ?> - Tunify News</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="app.js" defer></script>
    <style>
        :root {
            --background-base: #121212;
            --background-highlight: #1a1a1a;
            --background-press: #000;
            --background-elevated-base: #242424;
            --background-elevated-highlight: #2a2a2a;
            --text-base: #fff;
            --text-subdued: #a7a7a7;
            --text-bright-accent: #1ed760;
            --essential-base: #fff;
            --essential-subdued: #727272;
            --essential-bright-accent: #1ed760;
            --decorative-base: #fff;
            --decorative-subdued: #292929;
        }

        .top-bar {
            display: flex;
            align-items: center;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-buttons {
            display: flex;
            margin-right: 20px;
        }

        .nav-button {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-base);
            margin-right: 10px;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .article-header {
            display: flex;
            padding: 30px 0;
            align-items: flex-end;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.5)), linear-gradient(to right, #8f00ff, #6c4dff);
            color: var(--text-base);
            height: 100%;
            position: relative;
        }

        .article-info {
            padding: 20px;
            z-index: 1;
        }

        .article-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .article-date {
            font-size: 0.9rem;
            color: var(--text-subdued);
            margin-bottom: 20px;
        }

        .article-content {
            padding: 40px 20px;
            font-size: 1rem;
            color: var(--text-base);
            line-height: 1.8;
            background-color: var(--background-base);
        }

        .article-content p {
            margin-bottom: 20px;
            color: var(--text-subdued);
        }

        .reaction-section {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: var(--background-highlight);
            border-radius: 8px;
            margin: 20px 0;
        }

        .reaction-button {
            background-color: transparent;
            color: <?php echo $userHasReacted ? 'var(--essential-bright-accent)' : 'var(--text-base)'; ?>;
            border: none;
            padding: 10px;
            border-radius: 50%;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: <?php echo $userHasReacted ? 'default' : 'pointer'; ?>;
            transition: all 0.3s;
        }

        .reaction-button:not(:disabled):hover {
            color: var(--essential-bright-accent);
        }

        .reaction-count {
            margin-left: 15px;
            font-size: 0.9rem;
            color: var(--text-subdued);
        }

        .comments-section {
            background-color: var(--background-highlight);
            border-radius: 8px;
            padding: 30px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .comments-section h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-base);
            display: flex;
            align-items: center;
        }

        .comments-section h2 i {
            margin-right: 10px;
        }

        .comment-form {
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-subdued);
        }

        .form-group input, 
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            background-color: var(--background-elevated-base);
            border: 1px solid var(--decorative-subdued);
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            color: var(--text-base);
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-group input:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: var(--essential-bright-accent);
            box-shadow: 0 0 0 2px rgba(30, 215, 96, 0.2);
        }

        .comment-submit {
            background-color: var(--essential-bright-accent);
            color: #000;
            border: none;
            padding: 12px 25px;
            border-radius: 500px;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .comment-submit:hover {
            background-color: #1fdf64;
            transform: scale(1.04);
        }

        .comments-list {
            margin-top: 30px;
        }

        .comment-item {
            padding: 20px;
            margin-bottom: 15px;
            background-color: var(--background-elevated-base);
            border-radius: 4px;
        }

        .comment-author {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-base);
        }

        .comment-date {
            font-size: 0.8rem;
            color: var(--text-subdued);
            margin-bottom: 10px;
        }

        .comment-content {
            margin-top: 10px;
            line-height: 1.5;
            color: var(--text-base);
        }

        .comment-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .edit-comment-btn, .delete-comment-btn {
            background-color: transparent;
            border: none;
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: all 0.2s;
        }

        .edit-comment-btn {
            color: var(--essential-subdued);
        }

        .delete-comment-btn {
            color: #e74c3c;
        }

        .edit-comment-btn i, .delete-comment-btn i {
            margin-right: 5px;
        }

        .edit-comment-btn:hover {
            color: var(--essential-bright-accent);
        }

        .delete-comment-btn:hover {
            color: #c0392b;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--background-base);
            padding: 30px;
            border-radius: 8px;
            width: 500px;
            max-width: 90%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-base);
        }

        .modal-close {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-subdued);
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-btn {
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel {
            background-color: var(--background-highlight);
            color: var(--text-base);
            border: none;
        }

        .btn-confirm {
            background-color: var(--essential-bright-accent);
            color: white;
            border: none;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
            border: none;
        }

        .btn-confirm:hover {
            background-color: #1db954;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .no-comments {
            text-align: center;
            padding: 20px;
            color: var(--text-subdued);
            font-style: italic;
        }

        .comment-success,
        .reaction-success {
            background-color: rgba(30, 215, 96, 0.2);
            color: var(--essential-bright-accent);
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            color: var(--text-base);
            background-color: rgba(0, 0, 0, 0.7);
            padding: 8px 16px;
            border-radius: 500px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            margin: 20px 0;
            transition: all 0.3s;
        }

        .back-button:hover {
            background-color: var(--background-elevated-highlight);
        }

        .back-button i {
            margin-right: 8px;
        }

        @media (max-width: 768px) {
            .article-title {
                font-size: 2rem;
            }
            
            .article-header {
                height: 280px;
            }
        }

        @media (max-width: 480px) {
            .article-title {
                font-size: 1.8rem;
            }
            
            .article-header {
                height: 240px;
            }
        }
        .article-header {
            display: flex;
            justify-content: center; /* Centers the content horizontally */
            align-items: center;     /* Centers the content vertically */
            text-align: center;      /* Centers text within the container */
            padding: 20px;           /* Optional: add padding for spacing */
        }

        .article-info {
            display: flex;
            flex-direction: column;
            align-items: center;  /* Centers text inside the article-info container */
            text-align: center;    /* Ensures text inside the article-info is centered */
        }

        .article-title {
            font-size: 3rem;
            font-weight: bold;
        }

        .article-date {
            font-size: 1rem;
            color: #666;
        }

    </style>
</head>
<body>
    <div class="top-bar">
        <div class="nav-buttons">
            <a href="../tunify_avec_connexion/avec_connexion.php" class="nav-button">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($_GET['commented']) && $_GET['commented'] == 1): ?>
        <div class="comment-success">
            <i class="fas fa-check-circle"></i> Votre commentaire a été ajouté avec succès.
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['reacted']) && $_GET['reacted'] == 1): ?>
        <div class="reaction-success">
            <i class="fas fa-check-circle"></i> Merci d'avoir réagi à cette publication!
        </div>
        <?php endif; ?>
        
        <article>
            <div class="article-header" style="text-align: center;">
                <div class="article-info" style="text-align: center;">
                    <h1 class="article-title"><?php echo htmlspecialchars($news['titre']); ?></h1>
                    <div class="article-date">
                        <?php echo date('d/m/Y', strtotime($news['date_publication'])); ?>
                    </div>
                </div>
            </div>
            
            <div class="article-content">
                <h3>Description:</h3>
                <?php 
                    // Format paragraphs
                    $paragraphs = explode("\n", $news['contenu']);
                    foreach($paragraphs as $paragraph) {
                        if(trim($paragraph) !== '') {
                            echo '<p>' . htmlspecialchars($paragraph) . '</p>';
                        }
                    }
                ?>
                <hr>
            
                <div class="reaction-section">
                    <form method="post" action="news/ajoutrec.php" id="">
                        <input type="hidden" name="action" value="reaction">
                        <button type="submit" class="reaction-button" <?php if ($userHasReacted) echo 'disabled'; ?>>
                            <?php if ($userHasReacted): ?>
                                <i class="fas fa-heart" style="color: var(--essential-bright-accent);"></i>
                            <?php else: ?>
                                <i class="far fa-heart"></i>
                            <?php endif; ?>
                        </button>
                        <input type="hidden" name="id_news" value="<?php echo $id; ?>">
                    </form>
                    <div class="reaction-count">
                        <?php echo $countaimer ?> personnes aiment cette publication
                    </div>
                </div>
                 <hr>
            </div>
           
        </article>

        <section class="comments-section">
            <h2><i class="fas fa-comments"></i> Commentaires (<?php echo count($comments); ?>)</h2>
            
                <div class="comment-form">
                    <form method="post" action="news/ajoutercomm.php" id="commentForm">
                        <input type="hidden" name="action" value="comment">
                        <input type="hidden" name="id_news" value="<?php echo $id; ?>">

                        <div class="form-group">
                            <label for="auteur">Votre nom</label>
                            <input type="text" id="auteur" name="auteur" class="form-control" value="<?php echo htmlspecialchars($nomutilisateur, ENT_QUOTES, 'UTF-8'); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="contenu">Votre commentaire</label>
                            <textarea id="contenu" name="contenu" class="form-control"></textarea>
                        </div>

                        <button type="submit" class="comment-submit">Publier</button>
                    </form>
                </div>


            
            <div class="comments-list">
                <?php if (empty($comments)): ?>
                    <div class="no-comments">
                        <p>Aucun commentaire pour le moment. Soyez le premier à commenter!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-author"><?php echo htmlspecialchars($comment->getAuteur()); ?></div>
                            <div class="comment-date">
                                <i class="far fa-clock"></i> 
                                <?php echo date('d/m/Y à H:i', strtotime($comment->getDate_Commentaire())); ?>
                            </div>
                            <div class="comment-content"><?php echo htmlspecialchars($comment->getContenu()); ?></div>
                            <div class="comment-actions">
                                <button class="edit-comment-btn" data-id="<?php echo $comment->getId(); ?>" data-content="<?php echo htmlspecialchars($comment->getContenu()); ?>">
                                    <i class="fas fa-edit"></i> Modifier
                                </button>
                                <button class="delete-comment-btn" data-id="<?php echo $comment->getId(); ?>">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Edit Comment Modal -->
    <div id="editCommentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Modifier le commentaire</h3>
                <button class="modal-close" id="editModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editCommentForm">
                    <input type="hidden" id="edit_comment_id" name="id_commentaire">
                    <div class="form-group">
                        <label for="edit_contenu">Commentaire</label>
                        <textarea id="edit_contenu" name="contenu" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="modal-btn btn-cancel" id="editCancelBtn">Annuler</button>
                <button class="modal-btn btn-confirm" id="editSaveBtn">Enregistrer</button>
            </div>
        </div>
    </div>

    <!-- Delete Comment Modal -->
    <div id="deleteCommentModal" class="modal">
        <form action="news/supprimerComment.php" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Supprimer le commentaire</h3>
                    <button class="modal-close" id="deleteModalClose">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce commentaire ? Cette action est irréversible.</p>
                    <input type="hidden" id="delete_comment_id" name="id_commentaire">
                </div>
                <div class="modal-footer">
                    <button class="modal-btn btn-cancel" id="deleteCancelBtn">Annuler</button>
                    <button class="modal-btn btn-danger" id="deleteConfirmBtn">Supprimer</button>
                </div>
            </div>
        </form>
    </div>
 
    

<script src="news/app.js"></script>    
</body>
</html> 