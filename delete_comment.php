<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['LOGGED_USER'])) {
    header('Location: login.php');
    exit();
}

/**
 * @var int $comment_id L'identifiant du commentaire à supprimer, converti en entier.
 */
$comment_id = (int)$_POST['comment_id'];

/**
 * @var int $manga_id L'identifiant du manga auquel appartient le commentaire, converti en entier.
 */
$manga_id = (int)$_POST['manga_id'];

// Récupérer le commentaire pour vérifier son existence
$commentStatement = $mysqlClient->prepare('SELECT comment_id FROM comments WHERE comment_id = :comment_id');
$commentStatement->execute(['comment_id' => $comment_id]);
$comment = $commentStatement->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    header('Location: manga_detail.php?id=' . $manga_id . '&error=comment_not_found');
    exit();
}

// Vérification des permissions (seulement modérateurs)
if ($_SESSION['LOGGED_USER']['role'] !== 'moderator') {
    header('Location: manga_detail.php?id=' . $manga_id . '&error=unauthorized');
    exit();
}

// Supprimer le commentaire
$deleteStatement = $mysqlClient->prepare('DELETE FROM comments WHERE comment_id = :comment_id');
$deleteStatement->execute(['comment_id' => $comment_id]);

header('Location: manga_detail.php?id=' . $manga_id . '&success=comment_deleted');
exit();
