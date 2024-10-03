# Site Manga

**Description :**
Le projet *Site Manga* est une application web en PHP permettant la gestion et l'interaction avec du contenu manga. Les principales fonctionnalités incluent l'authentification utilisateur, la gestion des mangas (CRUD), un système de commentaires et de likes, des notations, ainsi qu'un système de contact. La base de données MySQL est utilisée pour stocker les données.

## Structure des dossiers et fichiers

### Fichiers principaux
- `back.png` : Image utilisée pour le fond du site.
- `logo.png` : Logo de l'application.
- `favicon.ico` : Icône affichée dans l'onglet du navigateur.
- `style.css` : Feuille de style principale gérant l'apparence du site.

### Fichiers PHP

#### Gestion des utilisateurs
- `login.php` : Formulaire de connexion.
- `submit_login.php` : Traitement des identifiants pour la connexion.
- `register.php` : Formulaire d'inscription pour les nouveaux utilisateurs.
- `submit_register.php` : Inscription des utilisateurs dans la base de données.
- `logout.php` : Déconnexion de l'utilisateur et destruction de la session.
- `isConnect.php` : Vérification de l'état de connexion de l'utilisateur.

#### Gestion des mangas
- `manga_create.php` : Formulaire de création de manga.
- `manga_post_create.php` : Insertion d'un manga dans la base de données.
- `manga_update.php` : Formulaire de mise à jour d'un manga.
- `manga_post_update.php` : Traitement de la mise à jour d'un manga.
- `manga_delete.php` : Page de confirmation pour la suppression d'un manga.
- `manga_post_delete.php` : Suppression effective d'un manga dans la base de données.
- `manga_read.php` : Liste des mangas disponibles.
- `manga_detail.php` : Détails d'un manga spécifique.

#### Gestion des commentaires
- `comments_create.php` : Formulaire d'ajout de commentaire.
- `comments_post_create.php` : Enregistrement du commentaire dans la base de données.
- `update_comment.php` : Modification d'un commentaire existant.
- `delete_comment.php` : Suppression d'un commentaire.
- `submit_comment.php` : Gestion de l'enregistrement et de la modification des commentaires.
- `like_comment.php` : Ajout d'un "like" à un commentaire.

#### Gestion des notations
- `rate_manga.php` : Formulaire de notation d'un manga.
- `submit_rating.php` : Enregistrement des notes et mise à jour des moyennes.

#### Système de contact
- `contact.php` : Formulaire de contact pour les utilisateurs.
- `submit_contact.php` : Traitement et enregistrement des messages de contact.

### Fichiers utilitaires
- `header.php` : En-tête avec la barre de navigation.
- `footer.php` : Pied de page.
- `functions.php` : Fonctions réutilisables dans le projet (gestion des sessions, validation, etc.).
- `variables.php` : Variables globales utilisées dans plusieurs parties du site.
- `databaseconnect.php` : Connexion à la base de données MySQL.
- `confirmation.php` : Page de confirmation après certaines actions utilisateur.
- `mysql.php` : Fonctions utilitaires pour l'exécution des requêtes SQL sécurisées.

### Base de données
- `partage_de_mangas.sql` : Contient la structure des tables pour l'initialisation de la base de données MySQL.
