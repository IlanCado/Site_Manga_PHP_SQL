<?php
session_start();
require_once(__DIR__ . '/config/mysql.php');
require_once(__DIR__ . '/databaseconnect.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['LOGGED_USER'])) {
    // Si non connecté, rediriger vers la page de connexion
    header('Location: login.php');
    exit();
}

// Récupérer les données du formulaire
$postData = $_POST;

if (!isset($postData['manga_id']) || !is_numeric($postData['manga_id'])) {
    echo('Il faut un identifiant de manga pour poster un commentaire.');
    return;
}

$manga_id = (int)$postData['manga_id'];
$content = trim($postData['content']);
$parent_id = isset($postData['parent_id']) && is_numeric($postData['parent_id']) ? (int)$postData['parent_id'] : null;

// Vérifier que le commentaire n'est pas vide
if (empty($content)) {
    header('Location: manga_detail.php?id=' . $manga_id . '&error=empty_content');
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['LOGGED_USER']['user_id'];

// Insérer le commentaire ou la réponse dans la base de données
$insertCommentStatement = $mysqlClient->prepare('
    INSERT INTO comments (manga_id, user_id, content, parent_id, created_at) 
    VALUES (:manga_id, :user_id, :content, :parent_id, NOW())
');

try {
    $insertCommentStatement->execute([
        'manga_id' => $manga_id,
        'user_id' => $user_id,
        'content' => $content,
        'parent_id' => $parent_id
    ]);
    
    // Rediriger vers la page des détails du manga avec un message de succès
    header('Location: manga_detail.php?id=' . $manga_id . '&success=1');
    exit();
} catch (Exception $e) {
    echo 'Erreur : ' . $e->getMessage();
    exit();
}
?>
