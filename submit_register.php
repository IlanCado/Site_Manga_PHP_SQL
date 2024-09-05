<?php
session_start();  // Démarrage de session si nécessaire

require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');  // Connexion à la base de données
require_once(__DIR__ . '/functions.php');  // Pour la redirection

// Récupérer les données soumises par le formulaire
$full_name = htmlspecialchars($_POST['username']);  
$email = htmlspecialchars($_POST['email']);
$password = $_POST['password'];

// Vérifier si tous les champs sont remplis
if (empty($full_name) || empty($email) || empty($password)) {
    echo "Veuillez remplir tous les champs.";
    exit;
}

// Vérifier si l'email est valide
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "L'adresse email n'est pas valide.";
    exit;
}

// Vérifier si l'email existe déjà dans la base de données
$checkEmailStatement = $mysqlClient->prepare('SELECT * FROM users WHERE email = :email');
$checkEmailStatement->execute(['email' => $email]);
$existingUser = $checkEmailStatement->fetch(PDO::FETCH_ASSOC);

if ($existingUser) {
    // Si un utilisateur avec cet email existe déjà, afficher un message d'erreur
    echo "Cet email est déjà utilisé. Veuillez en choisir un autre.";
    exit;
}

// Hashage du mot de passe
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Préparation de la requête SQL pour l'insertion
$sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $mysqlClient->prepare($sql);

try {
    // Exécution de la requête avec les données de l'utilisateur
    $stmt->execute([$full_name, $email, $hashedPassword, 'user']);  // Utilisation du rôle 'user' par défaut

    // Redirection vers confirmation.php après l'inscription
    redirectToUrl('confirmation.php');
    exit();  // Assure que le script s'arrête après la redirection
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
