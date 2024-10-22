<?php
session_start();

require_once(__DIR__ . '/isConnect.php');
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/functions.php');

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */

/**
 * @var array $getData Tableau contenant les paramètres GET de l'URL.
 */
$getData = $_GET;

if (!isset($getData['id']) || !is_numeric($getData['id'])) {
    echo('Il faut un identifiant pour supprimer le manga.');
    return;
}

/**
 * @var PDOStatement $retrieveMangaStatement La requête préparée pour récupérer les informations du manga.
 */
$retrieveMangaStatement = $mysqlClient->prepare('SELECT * FROM mangas WHERE manga_id = :id');
$retrieveMangaStatement->execute([
    'id' => (int)$getData['id'],
]);

/**
 * @var array|false $manga Le tableau associatif contenant les informations du manga, ou false si non trouvé.
 */
$manga = $retrieveMangaStatement->fetch(PDO::FETCH_ASSOC);

// Vérifier que le manga existe
if (!$manga) {
    echo "Manga introuvable.";
    exit();
}

// Vérifier que l'utilisateur est autorisé à supprimer (admin ou auteur du manga)
if (!isAdmin() && $manga['author'] !== $_SESSION['LOGGED_USER']['email']) {
    echo "Vous n'avez pas les droits pour supprimer cet article.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de mangas - Supprimer le manga ?</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">

        <?php require_once(__DIR__ . '/header.php'); ?>
        <h1>La suppression est définitive</h1> <br>
        <form action="manga_post_delete.php" method="POST">
            <div class="mb-3 visually-hidden">
                <label for="id" class="form-label">Identifiant du manga</label>
                <input type="hidden" class="form-control" id="id" name="id" value="<?php echo $getData['id']; ?>">
            </div>

            <!-- Bouton pour supprimer le manga -->
            <button type="submit" class="btn btn-danger">Supprimer le manga</button>

            <!-- Bouton pour revenir à la page d'accueil -->
            <a href="index.php" class="btn btn-success">Revenir à l'accueil</a>
        </form>
        <br />
    </div>

    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
