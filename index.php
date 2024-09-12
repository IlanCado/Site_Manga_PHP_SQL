<!-- inclusion des variables et fonctions -->
<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/variables.php');
require_once(__DIR__ . '/functions.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MangaVerse - Page d'accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <!-- inclusion de l'entête du site -->
        <?php require_once(__DIR__ . '/header.php'); ?><br>
        <div class="text-center mb-4">
            <img src="logo.png" alt="MangaVerse" class="logo-img">
        </div>

        <?php foreach (getmangas($mangas) as $manga) : ?>
    <article>
        <h3><a href="manga_detail.php?id=<?php echo($manga['manga_id']); ?>"><?php echo($manga['title']); ?></a></h3>
        <div><?php echo $manga['synopsis']; ?></div>
        <i><?php echo displayAuthor($manga['author'], $users); ?></i>
        <?php if (isset($_SESSION['LOGGED_USER']) && ($manga['author'] === $_SESSION['LOGGED_USER']['email'] || isAdmin())) : ?>
            <div class="mt-2">
                <a href="manga_update.php?id=<?php echo($manga['manga_id']); ?>" class="btn btn-warning btn-sm">Éditer l'article</a>
                <a href="manga_delete.php?id=<?php echo($manga['manga_id']); ?>" class="btn btn-danger btn-sm">Supprimer l'article</a>
            </div>
        <?php endif; ?>
    </article>
<?php endforeach ?>

    </div>


    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
