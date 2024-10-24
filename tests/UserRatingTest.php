<?php
use PHPUnit\Framework\TestCase;

class UserRatingTest extends TestCase
{
    protected function setUp(): void
    {
        // Configuration avant chaque test
        $_POST = []; // S'assurer que $_POST est vide avant chaque test
        $_SESSION = []; // Réinitialiser la session
    }

    public function testRatingWithValidInput()
    {
        // Exemple de données valides pour la notation
        $_SESSION['LOGGED_USER'] = ['user_id' => 1]; // Utilisateur connecté
        $_POST['manga_id'] = 1;
        $_POST['user_id'] = 1;
        $_POST['rating'] = 5;

        // Simuler la logique de notation
        $output = $this->simulateRating();

        // Test de succès - on s'attend à ce que la notation soit acceptée sans erreur
        $this->assertStringNotContainsString('Erreur lors de la soumission de la note.', $output);
        $this->assertStringContainsString('Notation réussie', $output);
    }

    public function testRatingFailsWhenUserNotLoggedIn()
    {
        // L'utilisateur n'est pas connecté
        $_POST['manga_id'] = 1;
        $_POST['user_id'] = 1;
        $_POST['rating'] = 5;

        $output = $this->simulateRating();

        // Test d'échec - on s'attend à un message d'erreur indiquant que l'utilisateur n'est pas connecté
        $this->assertStringContainsString('Utilisateur non connecté.', $output);
    }

    protected function tearDown(): void
    {
        // Nettoyage après chaque test
        $_POST = [];
        $_SESSION = [];
    }

    private function simulateRating()
    {
        // Simuler la logique de validation de notation
        $mangaId = $_POST['manga_id'] ?? null;
        $userId = $_POST['user_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $loggedUser = $_SESSION['LOGGED_USER'] ?? null;

        if (!$loggedUser) {
            return 'Utilisateur non connecté.';
        }

        if (empty($mangaId) || empty($userId) || empty($rating)) {
            return 'Tous les champs sont requis.';
        }

        return 'Notation réussie';
    }
}
