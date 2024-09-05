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
