<?php

/**
 * Affiche le nom de l'auteur du commentaire.
 *
 * @param string $authorEmail L'adresse email de l'auteur.
 * @param array $users Un tableau d'utilisateurs avec leurs informations.
 * @return string Le nom de l'auteur mis en forme ou 'Auteur inconnu' si non trouvé.
 */
function displayAuthor(string $authorEmail, array $users): string
{
    foreach ($users as $user) {
        if ($authorEmail === $user['email']) {
            return '<strong>' . $user['full_name'] . '</strong>';
        }
    }

    return 'Auteur inconnu';
}

/**
 * Vérifie si un manga est valide en fonction de son statut 'is_enabled'.
 *
 * @param array $manga Un tableau représentant un manga avec ses attributs.
 * @return bool Retourne true si le manga est activé, sinon false.
 */
function isValidmanga(array $manga): bool
{
    if (array_key_exists('is_enabled', $manga)) {
        $isEnabled = $manga['is_enabled'];
    } else {
        $isEnabled = false;
    }

    return $isEnabled;
}

/**
 * Récupère tous les mangas valides parmi un tableau de mangas.
 *
 * @param array $mangas Un tableau de mangas.
 * @return array Un tableau de mangas valides.
 */
function getmangas(array $mangas): array
{
    $valid_mangas = [];

    foreach ($mangas as $manga) {
        if (isValidmanga($manga)) {
            $valid_mangas[] = $manga;
        }
    }

    return $valid_mangas;
}

/**
 * Redirige l'utilisateur vers une URL spécifique.
 *
 * @param string $url L'URL vers laquelle rediriger l'utilisateur.
 * @return never Cette fonction termine toujours par un exit(), ne retourne donc jamais de valeur.
 */
function redirectToUrl(string $url): never
{
    header("Location: {$url}");
    exit();
}

/**
 * Vérifie si l'utilisateur actuel est un administrateur.
 *
 * @return bool Retourne true si l'utilisateur est un administrateur, sinon false.
 */
function isAdmin(): bool
{
    return isset($_SESSION['LOGGED_USER']) && isset($_SESSION['LOGGED_USER']['role']) && $_SESSION['LOGGED_USER']['role'] === 'admin';
}

// Validation du titre et du synopsis
define('MIN_TITLE_LENGTH', 3);
define('MAX_TITLE_LENGTH', 50);
define('MIN_DESC_LENGTH', 10);
define('MAX_DESC_LENGTH', 500);

/**
 * Valide le titre et le synopsis d'un manga.
 *
 * @param string $title Le titre du manga.
 * @param string $synopsis Le synopsis du manga.
 * @return bool|string Retourne true si les validations passent, sinon retourne un message d'erreur.
 */
function validateManga($title, $synopsis) {
    // Regex pour valider le titre
    $titleRegex = '/^.{'. MIN_TITLE_LENGTH .','. MAX_TITLE_LENGTH .'}$/';
    // Regex pour valider la description
    $descRegex = '/^.{'. MIN_DESC_LENGTH .','. MAX_DESC_LENGTH .'}$/';

    // Valider le titre
    if (!preg_match($titleRegex, $title)) {
        return "Le titre doit contenir entre ". MIN_TITLE_LENGTH ." et ". MAX_TITLE_LENGTH ." caractères.";
    }

    // Valider la description
    if (!preg_match($descRegex, $synopsis)) {
        return "La description doit contenir entre ". MIN_DESC_LENGTH ." et ". MAX_DESC_LENGTH ." caractères.";
    }

    return true;
}

// Validation des commentaires
define('MIN_COMMENT_LENGTH', 1);
define('MAX_COMMENT_LENGTH', 300);

/**
 * Valide le contenu d'un commentaire.
 *
 * @param string $comment Le contenu du commentaire à valider.
 * @return bool|string Retourne true si le commentaire est valide, sinon retourne un message d'erreur.
 */
function validateComment($comment) {
    $commentRegex = '/^.{'. MIN_COMMENT_LENGTH .','. MAX_COMMENT_LENGTH .'}$/';

    if (!preg_match($commentRegex, $comment)) {
        return "Le commentaire doit contenir entre " . MIN_COMMENT_LENGTH . " et " . MAX_COMMENT_LENGTH . " caractères.";
    }

    return true;
}
