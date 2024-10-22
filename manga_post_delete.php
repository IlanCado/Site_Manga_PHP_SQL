<?php

session_start();

require_once(__DIR__ . '/isConnect.php'); // Vérifie si l'utilisateur est connecté
require_once(__DIR__ . '/config/mysql.php'); // Configuration de la connexion MySQL
require_once(__DIR__ . '/databaseconnect.php'); // Connexion à la base de données
require_once(__DIR__ . '/functions.php'); // Contient des fonctions utilitaires

/**
 * On ne traite pas les super globales provenant de l'utilisateur directement,
 * ces données doivent être testées et vérifiées.
 */
$postData = $_POST;

// Vérification que l'identifiant est présent et est un nombre valide
if (!isset($postData['id']) || !is_numeric($postData['id'])) {
    echo 'Il faut un identifiant valide pour supprimer un manga.';
    return;
}

/**
 * @var int $mangaId L'identifiant du manga à supprimer, converti en entier.
 */
$mangaId = (int)$postData['id'];

// Supprimer toutes les évaluations associées à ce manga
/**
 * @var PDOStatement $deleteRatingsStatement La requête préparée pour supprimer toutes les évaluations associées au manga.
 */
$deleteRatingsStatement = $mysqlClient->prepare('DELETE FROM ratings WHERE manga_id = :manga_id');
$deleteRatingsStatement->execute(['manga_id' => $mangaId]);

// Supprimer tous les commentaires associés à ce manga
/**
 * @var PDOStatement $deleteCommentsStatement La requête préparée pour supprimer tous les commentaires associés au manga.
 */
$deleteCommentsStatement = $mysqlClient->prepare('DELETE FROM comments WHERE manga_id = :manga_id');
$deleteCommentsStatement->execute(['manga_id' => $mangaId]);

// Supprimer le manga après avoir supprimé toutes les dépendances
/**
 * @var PDOStatement $deleteMangaStatement La requête préparée pour supprimer le manga de la base de données.
 */
$deleteMangaStatement = $mysqlClient->prepare('DELETE FROM mangas WHERE manga_id = :manga_id');
$deleteMangaStatement->execute(['manga_id' => $mangaId]);

// Rediriger l'utilisateur vers la page d'accueil après la suppression
redirectToUrl('index.php');
?>
