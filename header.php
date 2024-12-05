<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="logo.png" alt="Logo Mangaverse" width="30" height="30" class="d-inline-block align-text-top">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
                    <?php if ($_SESSION['LOGGED_USER']['role'] === 'moderator') : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="list_user.php">Gestion des utilisateurs</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="manga_create.php">Ajoutez un manga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                <?php else : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Créer un compte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Connexion</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php">Contact</a>
                </li>
            </ul>

            <!-- Section à droite pour afficher "Bonjour, (nom d'utilisateur)" si connecté -->
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['LOGGED_USER'])) : ?>
                    <li class="nav-item">
                        <span class="navbar-text">
                            Bonjour, <?php echo htmlspecialchars($_SESSION['LOGGED_USER']['full_name']); ?> <!-- Sécurité HTML -->
                        </span>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
