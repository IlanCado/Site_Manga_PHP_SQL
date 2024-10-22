<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['LOGGED_USER'])) {
    echo json_encode(['success' => false, 'message' => 'Veuillez vous connecter.']);
    exit();
}

/**
 * @var int $comment_id L'identifiant du commentaire à liker, converti en entier.
 */
$comment_id = (int)$_POST['comment_id'];

/**
 * @var int $user_id L'identifiant de l'utilisateur connecté.
 */
$user_id = $_SESSION['LOGGED_USER']['user_id'];

// Vérifier si l'utilisateur a déjà liké ce commentaire
/**
 * @var PDOStatement $likeStatement La requête préparée pour vérifier si l'utilisateur a déjà liké le commentaire.
 */
$likeStatement = $mysqlClient->prepare('SELECT * FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
$likeStatement->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);

/**
 * @var array|false $like Le tableau associatif contenant les informations du like, ou false si non trouvé.
 */
$like = $likeStatement->fetch();

if ($like) {
    // Si l'utilisateur a déjà liké, on supprime le like (dislike)
    /**
     * @var PDOStatement $deleteLikeStatement La requête préparée pour supprimer le like de la base de données.
     */
    $deleteLikeStatement = $mysqlClient->prepare('DELETE FROM comment_likes WHERE comment_id = :comment_id AND user_id = :user_id');
    $deleteLikeStatement->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);

    // Mettre à jour le nombre de likes dans la table "comments"
    /**
     * @var PDOStatement $updateLikesStatement La requête préparée pour décrémenter le nombre de likes du commentaire.
     */
    $updateLikesStatement = $mysqlClient->prepare('UPDATE comments SET likes_count = likes_count - 1 WHERE comment_id = :comment_id');
    $updateLikesStatement->execute(['comment_id' => $comment_id]);

    echo json_encode(['success' => true, 'action' => 'disliked']);
} else {
    // Si l'utilisateur n'a pas liké, on ajoute un like
    /**
     * @var PDOStatement $insertLikeStatement La requête préparée pour insérer un nouveau like dans la base de données.
     */
    $insertLikeStatement = $mysqlClient->prepare('INSERT INTO comment_likes (comment_id, user_id) VALUES (:comment_id, :user_id)');
    $insertLikeStatement->execute(['comment_id' => $comment_id, 'user_id' => $user_id]);

    // Mettre à jour le nombre de likes
    /**
     * @var PDOStatement $updateLikesStatement La requête préparée pour incrémenter le nombre de likes du commentaire.
     */
    $updateLikesStatement = $mysqlClient->prepare('UPDATE comments SET likes_count = likes_count + 1 WHERE comment_id = :comment_id');
    $updateLikesStatement->execute(['comment_id' => $comment_id]);

    echo json_encode(['success' => true, 'action' => 'liked']);
}
exit();
?>
