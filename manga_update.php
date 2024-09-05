<?php
session_start();

require_once(__DIR__ . '/isConnect.php');
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/functions.php'); // Assurez-vous que isAdmin() est dans ce fichier

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */
$getData = $_GET;

if (!isset($getData['id']) || !is_numeric($getData['id'])) {
    echo('Il faut un identifiant de manga pour le modifier.');
    return;
}

$retrieveMangaStatement = $mysqlClient->prepare('SELECT * FROM mangas WHERE manga_id = :id');
$retrieveMangaStatement->execute([
    'id' => (int)$getData['id'],
]);
$manga = $retrieveMangaStatement->fetch(PDO::FETCH_ASSOC);

// Vérifier que le manga existe
if (!$manga) {
    echo "Manga introuvable.";
    exit();
}

// Vérifier que l'utilisateur est autorisé à modifier (admin ou auteur du manga)
if (!isAdmin() && $manga['author'] !== $_SESSION['LOGGED_USER']['email']) {
    echo "Vous n'avez pas les droits pour modifier cet article.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de mangas - Édition de manga</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">

        <?php require_once(__DIR__ . '/header.php'); ?>
        <h1>Mettre à jour <?php echo htmlspecialchars($manga['title']); ?></h1>
        <form action="manga_post_update.php" method="POST">
            <div class="mb-3 visually-hidden">
                <label for="id" class="form-label">Identifiant du manga</label>
                <input type="hidden" class="form-control" id="id" name="id" value="<?php echo($getData['id']); ?>">
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Titre du manga</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="title-help" value="<?php echo htmlspecialchars($manga['title']); ?>">
                <div id="title-help" class="form-text">Choisissez un titre percutant !</div>
            </div>
            <div class="mb-3">
                <label for="synopsis" class="form-label">Description du manga</label>
                <textarea class="form-control" placeholder="Seulement du contenu vous appartenant ou libre de droits." id="synopsis" name="synopsis"><?php echo htmlspecialchars($manga['synopsis']); ?></textarea>
            </div>
            
            <!-- Bouton vert pour envoyer et bouton rouge pour revenir à l'accueil -->
            <button type="submit" class="btn btn-success">Envoyer</button>
            <a href="index.php" class="btn btn-danger">Revenir à l'accueil</a>
        </form>
        <br />
    </div>

    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
