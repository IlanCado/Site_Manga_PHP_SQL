<?php
session_start();

require_once(__DIR__ . '/isConnect.php');
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/functions.php'); // Inclusion de functions.php pour la validation

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */
$postData = $_POST;

if (
    !isset($postData['comment']) ||
    !isset($postData['manga_id']) ||
    !is_numeric($postData['manga_id']) ||
    !is_numeric($postData['review'])
) {
    echo('Le commentaire ou la note sont invalides.');
    return;
}

/**
 * @var string $comment Le commentaire fourni par l'utilisateur, nettoyé pour éviter les balises HTML.
 */
$comment = trim(strip_tags($postData['comment']));

/**
 * @var int $mangaId L'identifiant du manga fourni par l'utilisateur.
 */
$mangaId = (int)$postData['manga_id'];

/**
 * @var int $review La note donnée par l'utilisateur, convertie en entier.
 */
$review = (int)$postData['review'];

// Validation de la note
if ($review < 1 || $review > 5) {
    echo 'La note doit être comprise entre 1 et 5.';
    return;
}

// Validation du commentaire
$validationResult = validateComment($comment);
if ($validationResult !== true) {
    echo $validationResult;
    return;
}

/**
 * Insertion du commentaire en base de données.
 *
 * @var PDOStatement $insertComment La requête préparée pour insérer un commentaire dans la base de données.
 */
$insertComment = $mysqlClient->prepare('INSERT INTO comments(comment, manga_id, user_id, review) VALUES (:comment, :manga_id, :user_id, :review)');
$insertComment->execute([
    'comment' => $comment,
    'manga_id' => $mangaId,
    'user_id' => $_SESSION['LOGGED_USER']['user_id'],
    'review' => $review,
]);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de mangas - Création de commentaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <?php require_once(__DIR__ . '/header.php'); ?>
        <h1>Commentaire ajouté avec succès !</h1>

        <div class="card">
            <div class="card-body">
                <p class="card-text"><b>Note</b> : <?php echo($review); ?> / 5</p>
                <p class="card-text"><b>Votre commentaire</b> : <?php echo strip_tags($comment); ?></p>
            </div>
        </div>
    </div>
    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
