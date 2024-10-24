<?php
use PHPUnit\Framework\TestCase;

class UserCommentTest extends TestCase
{
    protected function setUp(): void
    {
        // Configuration avant chaque test
        $_POST = []; // S'assurer que $_POST est vide avant chaque test
        $_SESSION = []; // Réinitialiser la session
    }

    public function testCommentWithValidInput()
    {
        // Exemple de données valides pour un commentaire
        $_SESSION['LOGGED_USER'] = ['user_id' => 1]; // Utilisateur connecté
        $_POST['manga_id'] = 1;
        $_POST['user_id'] = 1;
        $_POST['comment'] = 'Ceci est un commentaire valide.';

        // Simuler la logique de commentaire
        $output = $this->simulateComment();

        // Test de succès - on s'attend à ce que le commentaire soit accepté sans erreur
        $this->assertStringNotContainsString('Erreur lors de la soumission du commentaire.', $output);
        $this->assertStringContainsString('Commentaire soumis avec succès', $output);
    }

    public function testCommentFailsWhenUserNotLoggedIn()
    {
        // L'utilisateur n'est pas connecté
        $_POST['manga_id'] = 1;
        $_POST['user_id'] = 1;
        $_POST['comment'] = 'Ceci est un commentaire.';

        $output = $this->simulateComment();

        // Test d'échec - on s'attend à un message d'erreur indiquant que l'utilisateur n'est pas connecté
        $this->assertStringContainsString('Utilisateur non connecté.', $output);
    }

    public function testCommentFailsWithEmptyComment()
    {
        // Utilisateur connecté mais commentaire vide
        $_SESSION['LOGGED_USER'] = ['user_id' => 1];
        $_POST['manga_id'] = 1;
        $_POST['user_id'] = 1;
        $_POST['comment'] = ''; // Commentaire vide

        $output = $this->simulateComment();

        // Test d'échec - on s'attend à un message d'erreur indiquant que le commentaire ne peut pas être vide
        $this->assertStringContainsString('Le commentaire ne peut pas être vide.', $output);
    }

    protected function tearDown(): void
    {
        // Nettoyage après chaque test
        $_POST = [];
        $_SESSION = [];
    }

    private function simulateComment()
    {
        // Simuler la logique de validation de commentaire
        $mangaId = $_POST['manga_id'] ?? null;
        $userId = $_SESSION['LOGGED_USER']['user_id'] ?? null; // Utiliser l'id de l'utilisateur connecté
        $comment = $_POST['comment'] ?? null;
        $loggedUser = $_SESSION['LOGGED_USER'] ?? null;

        if (!$loggedUser) {
            return 'Utilisateur non connecté.';
        }

        if (empty($mangaId) || empty($userId) || $comment === null) {
            return 'Tous les champs sont requis.';
        }

        if (trim($comment) === '') {
            return 'Le commentaire ne peut pas être vide.';
        }

        return 'Commentaire soumis avec succès';
    }
}
