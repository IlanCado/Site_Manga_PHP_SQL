<?php
session_start();

require_once(__DIR__ . '/isConnect.php');
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */
$postData = $_POST;

// Vérification du formulaire soumis
if (
    empty($postData['title'])
    || empty($postData['synopsis'])
    || trim(strip_tags($postData['title'])) === ''
    || trim(strip_tags($postData['synopsis'])) === ''
) {
    echo 'Il faut un titre et un synopsis pour soumettre le formulaire.';
    return;
}

$title = trim(strip_tags($postData['title']));
$synopsis = trim(strip_tags($postData['synopsis']));

// Faire l'insertion en base
$insertmanga = $mysqlClient->prepare('INSERT INTO mangas(title, synopsis, author, is_enabled) VALUES (:title, :synopsis, :author, :is_enabled)');
$insertmanga->execute([
    'title' => $title,
    'synopsis' => $synopsis,
    'is_enabled' => 1,
    'author' => $_SESSION['LOGGED_USER']['email'],
]);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de mangas - Création de manga</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">

        <?php require_once(__DIR__ . '/header.php'); ?>
        <!-- MESSAGE DE SUCCES -->
        <h1>manga ajouté avec succès !</h1>

        <div class="card">

            <div class="card-body">
                <h5 class="card-title"><?php echo $title ; ?></h5>
                <p class="card-text"><b>Email</b> : <?php echo $_SESSION['LOGGED_USER']['email']; ?></p>
                <p class="card-text"><b>manga</b> : <?php echo $synopsis; ?></p>
            </div>
        </div>
    </div>

    <a href="index.php">Retour à l'accueil</a>
    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
