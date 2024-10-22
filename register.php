<?php
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');  // Connexion à la base de données
require_once(__DIR__ . '/header.php'); 

$errorMessage = '';  // Variable pour stocker les messages d'erreur

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Vérifier si tous les champs sont remplis
    if (empty($full_name) || empty($email) || empty($password)) {
        $errorMessage = "Veuillez remplir tous les champs.";
    }

    // Vérifier si l'email est valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "L'adresse email n'est pas valide.";
    }

    // Vérifier si l'email existe déjà dans la base de données
    $checkEmailStatement = $mysqlClient->prepare('SELECT * FROM users WHERE email = :email');
    $checkEmailStatement->execute(['email' => $email]);
    $existingEmail = $checkEmailStatement->fetch(PDO::FETCH_ASSOC);

    if ($existingEmail) {
        $errorMessage = "Les informations fournies sont incorrectes.";
    }

    // Vérifier si le nom d'utilisateur existe déjà
    $checkUsernameStatement = $mysqlClient->prepare('SELECT * FROM users WHERE full_name = :full_name');
    $checkUsernameStatement->execute(['full_name' => $full_name]);
    $existingUsername = $checkUsernameStatement->fetch(PDO::FETCH_ASSOC);

    if ($existingUsername) {
        $errorMessage = "Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.";
    }

    // Si aucune erreur n'a été trouvée, procéder à l'inscription
    if (empty($errorMessage)) {
        // Hashage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Préparation de la requête SQL pour l'insertion
        $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $mysqlClient->prepare($sql);

        try {
            // Exécution de la requête avec les données de l'utilisateur
            $stmt->execute([$full_name, $email, $hashedPassword, 'user']);  // Utilisation du rôle 'user' par défaut

            // Redirection vers confirmation.php après l'inscription
            header('Location: confirmation.php');
            exit();  // Assure que le script s'arrête après la redirection
        } catch (Exception $e) {
            $errorMessage = "Erreur : " . $e->getMessage();
        }
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
    <link href="style.css" rel="stylesheet">

</head>
<body>
    <div class="container">  <!-- Ajout du conteneur pour centrer le contenu -->
        <h1 class="my-4">Créer un compte</h1>

        <!-- Affichage des messages d'erreur -->
        <?php if (!empty($errorMessage)) : ?>
            <div class="alert alert-danger">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <input type="text" class="form-control" id="username" name="username" required value="<?php echo isset($full_name) ? $full_name : ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($email) ? $email : ''; ?>">
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
