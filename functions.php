<?php

function displayAuthor(string $authorEmail, array $users): string
{
    foreach ($users as $user) {
        if ($authorEmail === $user['email']) {
            return '<strong>' . $user['full_name'] . '</strong>';
        }
    }

    return 'Auteur inconnu';
}

function isValidmanga(array $manga): bool
{
    if (array_key_exists('is_enabled', $manga)) {
        $isEnabled = $manga['is_enabled'];
    } else {
        $isEnabled = false;
    }

    return $isEnabled;
}

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

function redirectToUrl(string $url): never
{
    header("Location: {$url}");
    exit();
}

function isAdmin() {
    return isset($_SESSION['LOGGED_USER']) && isset($_SESSION['LOGGED_USER']['role']) && $_SESSION['LOGGED_USER']['role'] === 'admin';
}

// Validation du titre et du synopsis
define('MIN_TITLE_LENGTH', 3);
define('MAX_TITLE_LENGTH', 50);
define('MIN_DESC_LENGTH', 10);
define('MAX_DESC_LENGTH', 500);

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

function validateComment($comment) {
    $commentRegex = '/^.{'. MIN_COMMENT_LENGTH .','. MAX_COMMENT_LENGTH .'}$/';

    if (!preg_match($commentRegex, $comment)) {
        return "Le commentaire doit contenir entre " . MIN_COMMENT_LENGTH . " et " . MAX_COMMENT_LENGTH . " caractères.";
    }

    return true;
}