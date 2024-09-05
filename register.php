<?php
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');  // Connexion à la base de données
require_once(__DIR__ . '/header.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($_POST['username']);  
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Préparation de la requête SQL avec $mysqlClient
    $sql = "INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)";
    $stmt = $mysqlClient->prepare($sql);

    try {
        // Exécution de la requête avec les données de l'utilisateur
        $stmt->execute([$full_name, $email, $password]);  // Utilisation de full_name

        // Redirection vers confirmation.php après l'inscription
        header('Location: confirmation.php');
        exit();  // Assure que le script s'arrête après la redirection
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">  <!-- Ajout du conteneur pour centrer le contenu -->
        <h1 class="my-4">Créer un compte</h1>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">S'inscrire</button>
        </form>
    </div>

    <?php require_once(__DIR__ . '/footer.php'); ?>  <!-- Inclusion du footer -->
</body>
</html>
