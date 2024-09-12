<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

if (!isset($_SESSION['LOGGED_USER'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter.']);
    exit();
}

$comment_id = (int)$_POST['comment_id'];
$user_id = $_SESSION['LOGGED_USER']['user_id'];

// Vérifier si l'utilisateur a déjà liké ce commentaire
$likeStatement = $mysqlClient->prepare('SELECT * FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
$likeStatement->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);
$like = $likeStatement->fetch();

if ($like) {
    // Si l'utilisateur a déjà liké, on supprime le like (dislike)
    $deleteLikeStatement = $mysqlClient->prepare('DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
    $deleteLikeStatement->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);

    // Mettre à jour le nombre de likes dans la table "comments"
    $updateLikesStatement = $mysqlClient->prepare('UPDATE comments SET likes_count = likes_count - 1 WHERE comment_id = :comment_id');
    $updateLikesStatement->execute(['comment_id' => $comment_id]);

    echo json_encode(['success' => true, 'action' => 'disliked']);
} else {
    // Si l'utilisateur n'a pas liké, on ajoute un like
    $insertLikeStatement = $mysqlClient->prepare('INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)');
    $insertLikeStatement->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);

    // Mettre à jour le nombre de likes
    $updateLikesStatement = $mysqlClient->prepare('UPDATE comments SET likes_count = likes_count + 1 WHERE comment_id = :comment_id');
    $updateLikesStatement->execute(['comment_id' => $comment_id]);

    echo json_encode(['success' => true, 'action' => 'liked']);
}
exit();
?>
