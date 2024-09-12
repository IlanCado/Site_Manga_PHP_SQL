<?php
session_start();

require_once(__DIR__ . '/isConnect.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site de mangas - Ajout de manga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link href="style.css" rel="stylesheet">

    
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">

        <?php require_once(__DIR__ . '/header.php'); ?>

        <h1>Ajouter un manga</h1>
        <form action="manga_post_create.php" method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Titre du manga</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="title-help">
                
            </div>
            <div class="mb-3">
                <label for="synopsis" class="form-label">Description du manga</label>
                <textarea class="form-control" placeholder="Laisse la créativité t'envahir :)" id="synopsis" name="synopsis"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>

    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
