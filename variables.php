<?php

// Récupération des variables à l'aide du client MySQL
$usersStatement = $mysqlClient->prepare('SELECT * FROM users');
$usersStatement->execute();
$users = $usersStatement->fetchAll();

$mangasStatement = $mysqlClient->prepare('SELECT * FROM mangas WHERE is_enabled is TRUE');
$mangasStatement->execute();
$mangas = $mangasStatement->fetchAll();
