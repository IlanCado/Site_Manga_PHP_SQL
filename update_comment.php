<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['LOGGED_USER'])) {
    header('Location: login.php');
    exit();
}

$comment_id = (int)$_POST['comment_id'];
$manga_id = (int)$_POST['manga_id'];
$content = $_POST['content'];

// Récupérer le commentaire pour vérifier son auteur
$commentStatement = $mysqlClient->prepare('SELECT user_id FROM comments WHERE comment_id = :comment_id');
$commentStatement->execute(['comment_id' => $comment_id]);
$comment = $commentStatement->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    header('Location: manga_detail.php?id=' . $manga_id . '&error=comment_not_found');
    exit();
}

// Vérification si l'utilisateur est l'auteur ou un administrateur
if ($comment['user_id'] === $_SESSION['LOGGED_USER']['user_id'] || $_SESSION['LOGGED_USER']['role'] === 'admin') {
    // Mettre à jour le commentaire
    $updateStatement = $mysqlClient->prepare('UPDATE comments SET content = :content WHERE comment_id = :comment_id');
    $updateStatement->execute(['content' => $content, 'comment_id' => $comment_id]);

    header('Location: manga_detail.php?id=' . $manga_id . '&success=comment_updated');
} else {
    header('Location: manga_detail.php?id=' . $manga_id . '&error=unauthorized');
}
exit();
