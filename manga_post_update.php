<?php
session_start();

require_once(__DIR__ . '/isConnect.php');
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/functions.php'); // Inclure le fichier functions.php pour la validation

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */
$postData = $_POST;

if (
    !isset($postData['id'])
    || !is_numeric($postData['id'])
    || empty($postData['title'])
    || empty($postData['synopsis'])
    || trim(strip_tags($postData['title'])) === ''
    || trim(strip_tags($postData['synopsis'])) === ''
) {
    echo 'Il manque des informations pour permettre l\'édition du formulaire.';
    return;
}

$id = (int)$postData['id'];
$title = trim(strip_tags($postData['title']));
$synopsis = trim(strip_tags($postData['synopsis']));

// Validation du titre et du synopsis
$validationResult = validateManga($title, $synopsis);
if ($validationResult !== true) {
    echo $validationResult;
    return;
}

// Mise à jour du manga dans la base
$insertmangaStatement = $mysqlClient->prepare('UPDATE mangas SET title = :title, synopsis = :synopsis WHERE manga_id = :id');
$insertmangaStatement->execute([
    'title' => $title,
    'synopsis' => $synopsis,
    'id' => $id,
]);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de mangas - Modification de manga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        <?php require_once(__DIR__ . '/header.php'); ?>
        <h1>Manga modifié avec succès !</h1>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo($title); ?></h5>
                <p class="card-text"><b>Email</b> : <?php echo $_SESSION['LOGGED_USER']['email']; ?></p>
                <p class="card-text"><b>Synopsis</b> : <?php echo $synopsis; ?></p>
            </div>
        </div>
        <div class="mt-4">
            <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    </div>
    </div>
    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
