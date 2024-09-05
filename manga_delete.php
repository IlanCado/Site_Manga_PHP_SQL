<?php
session_start();

require_once(__DIR__ . '/isConnect.php');

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */
$getData = $_GET;

if (!isset($getData['id']) || !is_numeric($getData['id'])) {
    echo('Il faut un identifiant pour supprimer le manga.');
    return;
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
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
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