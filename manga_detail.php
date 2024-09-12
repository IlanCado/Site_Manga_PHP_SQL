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

// Récupérer tous les commentaires pour ce manga, y compris les réponses
$commentsStatement = $mysqlClient->prepare('SELECT * FROM comments WHERE manga_id = :manga_id AND parent_id IS NULL ORDER BY created_at DESC');
$commentsStatement->execute(['manga_id' => $manga_id]);
$comments = $commentsStatement->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour récupérer les réponses aux commentaires
function fetchReplies($mysqlClient, $comment_id) {
    $repliesStatement = $mysqlClient->prepare('SELECT * FROM comments WHERE parent_id = :comment_id ORDER BY created_at ASC');
    $repliesStatement->execute(['comment_id' => $comment_id]);
    return $repliesStatement->fetchAll(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du manga - <?php echo htmlspecialchars($manga['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <?php require_once(__DIR__ . '/header.php'); ?>

        <h1><?php echo htmlspecialchars($manga['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($manga['synopsis'])); ?></p>
        <?php
// Récupérer l'utilisateur correspondant à l'auteur
$userStatement = $mysqlClient->prepare('SELECT full_name FROM users WHERE email = :email');
$userStatement->execute(['email' => $manga['author']]);
$user = $userStatement->fetch(PDO::FETCH_ASSOC);
?>

<p><strong>Auteur : </strong><?php echo htmlspecialchars($user['full_name']); ?></p>

        <!-- Système de notation par étoiles -->
        <div class="rating">
            <form action="submit_rating.php" method="POST" id="ratingForm">
                <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                <input type="radio" name="rating" id="star1" value="1"><label for="star1">1 &#9733;</label>
                <input type="radio" name="rating" id="star2" value="2"><label for="star2">2 &#9733;</label>
                <input type="radio" name="rating" id="star3" value="3"><label for="star3">3 &#9733;</label>
                <input type="radio" name="rating" id="star4" value="4"><label for="star4">4 &#9733;</label>
                <input type="radio" name="rating" id="star5" value="5"><label for="star5">5 &#9733;</label>
                <button type="submit" class="btn btn-primary mt-2">Envoyer</button> <!-- Bouton pour envoyer la note -->
            </form>
            <p>Note moyenne : <?php echo $averageRating; ?>/5 (<?php echo $voteCount; ?> votes)</p>
        </div>

        <!-- Section commentaires -->
        <h3>Commentaires</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="comment mb-3">
                <p><strong><?php echo htmlspecialchars($comment['user_id']); ?>:</strong> <?php echo htmlspecialchars($comment['content']); ?></p>
                <p><small>Posté le <?php echo $comment['created_at']; ?></small></p>

                <!-- Bouton pour répondre -->
                <button class="btn btn-sm btn-outline-primary" onclick="toggleReplyForm(<?php echo $comment['comment_id']; ?>)">Répondre</button>

                <!-- Formulaire de réponse masqué -->
                <div id="replyForm<?php echo $comment['comment_id']; ?>" class="collapse mt-2">
                    <form action="submit_comment.php" method="POST">
                        <input type="hidden" name="manga_id" value="<?php echo $manga_id; ?>">
                        <input type="hidden" name="parent_id" value="<?php echo $comment['comment_id']; ?>">
                        <textarea name="content" rows="2" class="form-control" placeholder="Votre réponse..."></textarea>
                        <button type="submit" class="btn btn-sm btn-primary mt-2">Envoyer</button>
                    </form>
                </div>

                <!-- Afficher les réponses -->
                <?php
                $replies = fetchReplies($mysqlClient, $comment['comment_id']);
                if (!empty($replies)) {
                    echo '<div class="replies pl-4 mt-2">';
                    foreach ($replies as $reply) {
                        echo '<div class="reply mb-2">';
                        echo '<p><strong>' . htmlspecialchars($reply['user_id']) . ':</strong> ' . htmlspecialchars($reply['content']) . '</p>';
                        echo '<p><small>Posté le ' . $reply['created_at'] . '</small></p>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
                ?>
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
    </script>

    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
