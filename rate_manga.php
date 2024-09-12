<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

if (!isset($_SESSION['LOGGED_USER'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter.']);
    exit();
}

$postData = $_POST;
$manga_id = (int) $postData['manga_id'];
$rating = (int) $postData['rating'];
$user_id = $_SESSION['LOGGED_USER']['user_id'];

// Vérifier si la note est valide
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Note non valide.']);
    exit();
}

// Vérifier si l'utilisateur a déjà noté ce manga
$ratingStatement = $mysqlClient->prepare('SELECT * FROM ratings WHERE manga_id = :manga_id AND user_id = :user_id');
$ratingStatement->execute(['manga_id' => $manga_id, 'user_id' => $user_id]);
$existingRating = $ratingStatement->fetch();

if ($existingRating) {
    // Mettre à jour la note existante
    $updateRatingStatement = $mysqlClient->prepare('UPDATE ratings SET rating = :rating WHERE manga_id = :manga_id AND user_id = :user_id');
    $updateRatingStatement->execute(['rating' => $rating, 'manga_id' => $manga_id, 'user_id' => $user_id]);
} else {
    // Insérer une nouvelle note
    $insertRatingStatement = $mysqlClient->prepare('INSERT INTO ratings (manga_id, user_id, rating) VALUES (:manga_id, :user_id, :rating)');
    $insertRatingStatement->execute(['manga_id' => $manga_id, 'user_id' => $user_id, 'rating' => $rating]);
}

echo json_encode(['success' => true]);

