<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation d'inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <div class="container">
        
        <?php require_once(__DIR__ . '/header.php'); ?>

        <h1>Inscription réussie !</h1>
        <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.</p>

        <a href="login.php" class="btn btn-success">Se connecter</a>

        <br />
    </div>

    <?php require_once(__DIR__ . '/footer.php'); ?>
</body>
</html>
