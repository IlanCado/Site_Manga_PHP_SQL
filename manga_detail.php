<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Récupération de l'ID du manga depuis l'URL
$getData = $_GET;

if (!isset($getData['id']) || !is_numeric($getData['id'])) {
    echo('Il faut un identifiant de manga pour afficher les détails.');
    return;
}

$manga_id = (int)$getData['id'];

// Récupérer les informations du manga
$retrievemangaStatement = $mysqlClient->prepare('SELECT * FROM mangas WHERE manga_id = :id');
$retrievemangaStatement->execute(['id' => $manga_id]);
$manga = $retrievemangaStatement->fetch(PDO::FETCH_ASSOC);

// Vérifier que le manga existe
if (!$manga) {
    echo "Manga introuvable.";
    exit();
}

// Récupérer la moyenne des notes et le nombre de votes pour ce manga
$ratingStatement = $mysqlClient->prepare('SELECT AVG(rating) as average_rating, COUNT(*) as vote_count FROM ratings WHERE manga_id = :manga_id');
$ratingStatement->execute(['manga_id' => $manga_id]);
$ratingData = $ratingStatement->fetch(PDO::FETCH_ASSOC);

$averageRating = round($ratingData['average_rating'], 1);
$voteCount = $ratingData['vote_count'];

// Récupérer tous les commentaires pour ce manga, y compris le full_name des utilisateurs
$commentsStatement = $mysqlClient->prepare(
    'SELECT comments.*, users.full_name FROM comments 
    JOIN users ON comments.user_id = users.user_id 
    WHERE comments.manga_id = :manga_id AND parent_id IS NULL 
    ORDER BY comments.created_at ASC'
);
$commentsStatement->execute(['manga_id' => $manga_id]);
$comments = $commentsStatement->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les réponses aux commentaires, y compris le full_name des utilisateurs
function fetchReplies($mysqlClient, $comment_id) {
    $repliesStatement = $mysqlClient->prepare(
        'SELECT comments.*, users.full_name FROM comments 
        JOIN users ON comments.user_id = users.user_id 
        WHERE comments.parent_id = :comment_id 
        ORDER BY comments.created_at ASC'
    );
    $repliesStatement->execute(['comment_id' => $comment_id]);
    return $repliesStatement->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer l'utilisateur correspondant à l'auteur
$userStatement = $mysqlClient->prepare('SELECT full_name FROM users WHERE email = :email');
$userStatement->execute(['email' => $manga['author']]);
$user = $userStatement->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du manga - <?php echo htmlspecialchars($manga['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <?php require_once(__DIR__ . '/header.php'); ?>

        <h1><?php echo htmlspecialchars($manga['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($manga['synopsis'])); ?></p>
        <p><strong>Auteur : </strong><?php echo htmlspecialchars($user['full_name']); ?></p>

        <!-- Système de notation par étoiles -->
        <h3>Note</h3>

        <div class="rating-container">
            <form action="submit_rating.php" method="POST" id="ratingForm">
                <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                <div class="rating">
                    <input type="radio" name="rating" id="star5" value="5"><label for="star5"> &#9733;</label>
                    <input type="radio" name="rating" id="star4" value="4"><label for="star4"> &#9733;</label>
                    <input type="radio" name="rating" id="star3" value="3"><label for="star3"> &#9733;</label>
                    <input type="radio" name="rating" id="star2" value="2"><label for="star2"> &#9733;</label>
                    <input type="radio" name="rating" id="star1" value="1"><label for="star1"> &#9733;</label>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Soumettre</button>
            </form>
            <p class="note-moyenne">Note moyenne : <?php echo $averageRating; ?>/5 (<?php echo $voteCount; ?> votes)</p>
        </div>

        <!-- Section commentaires -->
        <h3>Commentaires</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="comment mb-3">
                <p><strong><?php echo htmlspecialchars($comment['full_name']); ?>:</strong> <?php echo htmlspecialchars($comment['content']); ?></p>
                <p><small>Posté le <?php echo $comment['created_at']; ?></small></p>

                <!-- Boutons pour répondre, éditer, voir les réponses et supprimer -->
                <button class="btn btn-sm btn-outline-primary" onclick="toggleReplyForm(<?php echo $comment['comment_id']; ?>)">Répondre</button>
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleReplies(<?php echo $comment['comment_id']; ?>)">Voir les réponses</button>

                <?php if (isset($_SESSION['LOGGED_USER']) && ($comment['user_id'] === $_SESSION['LOGGED_USER']['user_id'] || $_SESSION['LOGGED_USER']['role'] === 'admin')): ?>
                    <!-- Bouton pour éditer -->
                    <button class="btn btn-sm btn-outline-warning" onclick="toggleEditForm(<?php echo $comment['comment_id']; ?>)">Éditer</button>
                    
                    <!-- Formulaire d'édition de commentaire masqué -->
                    <div id="editForm<?php echo $comment['comment_id']; ?>" class="collapse mt-2">
                        <form action="update_comment.php" method="POST">
                            <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                            <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                            <textarea name="content" rows="2" class="form-control"><?php echo htmlspecialchars($comment['content']); ?></textarea>
                            <button type="submit" class="btn btn-sm btn-success mt-2">Mettre à jour</button>
                        </form>
                    </div>

                    <!-- Bouton pour supprimer -->
                    <form action="delete_comment.php" method="POST" style="display:inline;">
                        <input type="hidden" name="comment_id" value="<?php echo $comment['comment_id']; ?>">
                        <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                    </form>
                <?php endif; ?>

                <!-- Formulaire de réponse masqué -->
                <div id="replyForm<?php echo $comment['comment_id']; ?>" class="collapse mt-2">
                    <form action="submit_comment.php" method="POST">
                        <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                        <input type="hidden" name="parent_id" value="<?php echo $comment['comment_id']; ?>">
                        <textarea name="content" rows="2" class="form-control" placeholder="Votre réponse..."></textarea>
                        <button type="submit" class="btn btn-sm btn-primary mt-2">Envoyer</button>
                    </form>
                </div>

                <!-- Afficher/Masquer les réponses -->
                <div id="replies<?php echo $comment['comment_id']; ?>" class="replies collapse mt-2">
                    <?php
                    $replies = fetchReplies($mysqlClient, $comment['comment_id']);
                    if (!empty($replies)) {
                        foreach ($replies as $reply) {
                            echo '<div class="reply mb-2">';
                            echo '<p><strong>Réponse de ' . htmlspecialchars($reply['full_name']) . ':</strong> ' . htmlspecialchars($reply['content']) . '</p>';
                            echo '<p><small>Posté le ' . $reply['created_at'] . '</small></p>';

                            if (isset($_SESSION['LOGGED_USER']) && ($reply['user_id'] === $_SESSION['LOGGED_USER']['user_id'] || $_SESSION['LOGGED_USER']['role'] === 'admin')) {
                                // Bouton pour éditer la réponse
                                echo '<button class="btn btn-sm btn-outline-warning" onclick="toggleEditForm(' . $reply['comment_id'] . ')">Éditer</button>';
                                // Formulaire d'édition de la réponse
                                echo '<div id="editForm' . $reply['comment_id'] . '" class="collapse mt-2">';
                                echo '<form action="update_comment.php" method="POST">';
                                echo '<input type="hidden" name="comment_id" value="' . $reply['comment_id'] . '">';
                                echo '<input type="hidden" name="manga_id" value="' . $manga_id . '">';
                                echo '<textarea name="content" rows="2" class="form-control">' . htmlspecialchars($reply['content']) . '</textarea>';
                                echo '<button type="submit" class="btn btn-sm btn-success mt-2">Mettre à jour</button>';
                                echo '</form></div>';

                                // Bouton pour supprimer la réponse
                                echo '<form action="delete_comment.php" method="POST" style="display:inline;">';
                                echo '<input type="hidden" name="comment_id" value="' . $reply['comment_id'] . '">';
                                echo '<input type="hidden" name="manga_id" value="' . $manga_id . '">';
                                echo '<button type="submit" class="btn btn-sm btn-danger mt-2">Supprimer</button>';
                                echo '</form>';
                            }

                            echo '</div>';
                        }
                    } else {
                        echo '<p>Aucune réponse pour ce commentaire.</p>';
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Formulaire pour ajouter un commentaire -->
        <?php if (isset($_SESSION['LOGGED_USER'])): ?>
            <form action="submit_comment.php" method="POST">
                <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                <div class="form-group">
                    <label for="content">Votre commentaire :</label>
                    <textarea name="content" id="content" rows="3" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Commenter</button>
            </form>
        <?php else: ?>
            <p><a href="login.php">Connectez-vous</a> pour laisser un commentaire.</p>
        <?php endif; ?>
    </div>

    <script>
        function toggleReplyForm(commentId) {
            var replyForm = document.getElementById('replyForm' + commentId);
            replyForm.classList.toggle('collapse');
        }

        function toggleReplies(commentId) {
            var repliesDiv = document.getElementById('replies' + commentId);
            repliesDiv.classList.toggle('collapse');
        }

        function toggleEditForm(commentId) {
            var editForm = document.getElementById('editForm' + commentId);
            editForm.classList.toggle('collapse');
        }
    </script>

    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
