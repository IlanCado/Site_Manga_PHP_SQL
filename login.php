<?php
session_start();
require_once(__DIR__ . '/header.php');  // Inclusion du menu de navigation
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion utilisateur</title>
    <!-- Lien vers le CSS (Bootstrap) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">

</head>
<body>
    <br>
    <div class="container">
        <h1 class="my-4">Connexion</h1>

        <!-- Si utilisateur/trice est non identifié(e), on affiche le formulaire -->
        <?php if (!isset($_SESSION['LOGGED_USER'])) : ?>
            <form action="submit_login.php" method="POST">
                <!-- si message d'erreur on l'affiche -->
                <?php if (isset($_SESSION['LOGIN_ERROR_MESSAGE'])) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $_SESSION['LOGIN_ERROR_MESSAGE'];
                        unset($_SESSION['LOGIN_ERROR_MESSAGE']); ?>
                    </div>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="email-help" placeholder="you@exemple.com">
                    <div id="email-help" class="form-text">L'email utilisé lors de la création de compte.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </form>
        <?php else : ?>
            <!-- Si utilisateur/trice bien connecté(e), on affiche un message de succès -->
            <div class="alert alert-success" role="alert">
                Bonjour <?php echo $_SESSION['LOGGED_USER']['email']; ?> et bienvenue sur le site !
            </div>
        <?php endif; ?>
    </div>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <?php require_once(__DIR__ . '/footer.php'); ?>  <!-- Inclusion du footer -->
</body>
</html>
