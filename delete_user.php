<?php
session_start();
require_once('databaseconnect.php');

// Vérifie que l'utilisateur connecté est un modérateur
if ($_SESSION['LOGGED_USER']['role'] !== 'moderator') {
    die('Accès interdit');
}

// Vérifie que l'ID de l'utilisateur à supprimer est envoyé
if (!isset($_POST['user_id']) || empty($_POST['user_id'])) {
    die('ID utilisateur invalide');
}

// Supprime l'utilisateur
$userId = intval($_POST['user_id']);
$query = $mysqlClient->prepare('DELETE FROM users WHERE user_id = :user_id AND role = "user"'); // Empêche la suppression de modérateurs/admins
$success = $query->execute(['user_id' => $userId]);

if ($success) {
    header('Location: list_user.php?message=success');
} else {
    header('Location: list_user.php?message=error');
}
exit;
