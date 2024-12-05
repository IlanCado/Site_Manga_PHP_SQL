<?php
session_start();
require_once('databaseconnect.php');

// Vérifie que l'utilisateur connecté est un modérateur
if (!isset($_SESSION['LOGGED_USER']) || $_SESSION['LOGGED_USER']['role'] !== 'moderator') {
    die('Accès interdit : seuls les modérateurs peuvent accéder à cette page.');
}

// Récupère les utilisateurs existants (excluant les modérateurs et administrateurs)
$query = $mysqlClient->prepare('SELECT user_id, email, role FROM users WHERE role = :role');
$query->execute(['role' => 'user']);
$users = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Liste des utilisateurs</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td><?= htmlspecialchars($user['role']); ?></td>
                    <td>
                        <form action="delete_user.php" method="POST" onsubmit="return confirm('Confirmer la suppression de cet utilisateur ?');">
                            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']); ?>">
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
