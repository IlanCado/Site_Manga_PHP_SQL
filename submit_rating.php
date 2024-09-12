<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['LOGGED_USER'])) {
    header('Location: login.php');
    exit();
}

$manga_id = (int)$_POST['manga_id'];
$rating = (int)$_POST['rating'];
$user_id = $_SESSION['LOGGED_USER']['user_id'];

// Vérifier que la note est entre 1 et 5
if ($rating < 1 || $rating > 5) {
    header('Location: manga_detail.php?id=' . $manga_id . '&error=invalid_rating');
    exit();
}

// Vérifiez si l'utilisateur a déjà noté ce manga
$checkRatingStatement = $mysqlClient->prepare('SELECT * FROM ratings WHERE manga_id = :manga_id AND user_id = :user_id');
$checkRatingStatement->execute(['manga_id' => $manga_id, 'user_id' => $user_id]);
$existingRating = $checkRatingStatement->fetch();

if ($existingRating) {
    // Si une note existe déjà, on la met à jour
    $updateRatingStatement = $mysqlClient->prepare('UPDATE ratings SET rating = :rating WHERE manga_id = :manga_id AND user_id = :user_id');
    $updateRatingStatement->execute(['rating' => $rating, 'manga_id' => $manga_id, 'user_id' => $user_id]);
} else {
    // Sinon, on insère une nouvelle note
    $insertRatingStatement = $mysqlClient->prepare('INSERT INTO ratings (manga_id, user_id, rating) VALUES (:manga_id, :user_id, :rating)');
    $insertRatingStatement->execute(['manga_id' => $manga_id, 'user_id' => $user_id, 'rating' => $rating]);
}

// Redirection après la soumission de la note
header('Location: manga_detail.php?id=' . $manga_id . '&success=rating_submitted');
exit();
