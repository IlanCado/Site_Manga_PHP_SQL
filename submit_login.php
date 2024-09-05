<?php

session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');
require_once(__DIR__ . '/variables.php');
require_once(__DIR__ . '/functions.php');

// Récupération des données du formulaire
$postData = $_POST;

// Validation du formulaire
if (isset($postData['email']) && isset($postData['password'])) {
    // Vérification si l'email est valide
    if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['LOGIN_ERROR_MESSAGE'] = 'Il faut un email valide pour soumettre le formulaire.';
        header('Location: login.php');
        exit();
    } else {
        // Requête pour récupérer l'utilisateur à partir de la base de données
        $checkUserStatement = $mysqlClient->prepare('SELECT * FROM users WHERE email = :email');
        $checkUserStatement->execute(['email' => $postData['email']]);
        $user = $checkUserStatement->fetch(PDO::FETCH_ASSOC);

        // Vérification si l'utilisateur existe et que le mot de passe est correct
        if ($user && password_verify($postData['password'], $user['password'])) {
            // Enregistrement des infos utilisateur dans la session avec le rôle
            $_SESSION['LOGGED_USER'] = [
                'email' => $user['email'],
                'user_id' => $user['user_id'],
                'full_name' => $user['full_name'],
                'role' => isset($user['role']) ? $user['role'] : 'user', // Stocker le rôle dans la session
            ];

            // Redirection vers la page d'accueil après connexion
            header('Location: index.php');
            exit();
        }

        // Si les informations ne permettent pas d'identifier l'utilisateur
        if (!isset($_SESSION['LOGGED_USER'])) {
            $_SESSION['LOGIN_ERROR_MESSAGE'] = 'Les informations envoyées ne permettent pas de vous identifier.';
            // Redirection vers la page de connexion avec message d'erreur
            header('Location: login.php');
            exit();
        }
    }
} else {
    // Si les champs ne sont pas correctement remplis, rediriger vers login
    $_SESSION['LOGIN_ERROR_MESSAGE'] = 'Veuillez remplir tous les champs.';
    header('Location: login.php');
    exit();
}
