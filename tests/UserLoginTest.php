<?php
use PHPUnit\Framework\TestCase;

class UserLoginTest extends TestCase
{
    protected function setUp(): void
    {
        // Configuration avant chaque test
        $_POST = []; // S'assurer que $_POST est vide avant chaque test
    }

    public function testLoginWithValidCredentials()
    {
        // Exemple de données valides pour la connexion
        $_POST['email'] = 'validuser@example.com';
        $_POST['password'] = 'ValidPassword123';

        // Simuler la logique de connexion
        $output = $this->simulateLogin();

        // Test de succès - on s'attend à ce qu'il n'y ait pas de message d'erreur et que la connexion soit réussie
        $this->assertStringNotContainsString('Email ou mot de passe incorrect.', $output);
        $this->assertStringContainsString('Connexion réussie', $output);
    }

    public function testLoginFailsWithInvalidEmail()
    {
        // Exemple de données invalides (email incorrect)
        $_POST['email'] = 'invaliduser@example.com';
        $_POST['password'] = 'ValidPassword123';

        $output = $this->simulateLogin();

        // Test d'échec - on s'attend à un message d'erreur
        $this->assertStringContainsString('Email ou mot de passe incorrect.', $output);
    }

    public function testLoginFailsWithWrongPassword()
    {
        // Exemple de données invalides (mot de passe incorrect)
        $_POST['email'] = 'validuser@example.com';
        $_POST['password'] = 'WrongPassword';

        $output = $this->simulateLogin();

        // Test d'échec - on s'attend à un message d'erreur
        $this->assertStringContainsString('Email ou mot de passe incorrect.', $output);
    }

    protected function tearDown(): void
    {
        // Nettoyage après chaque test
        $_POST = [];
    }

    private function simulateLogin()
    {
        // Simuler la logique de validation de connexion
        $validUsers = [
            'validuser@example.com' => 'ValidPassword123'
        ];

        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (empty($email) || empty($password)) {
            return 'Email ou mot de passe incorrect.';
        }

        if (!isset($validUsers[$email]) || $validUsers[$email] !== $password) {
            return 'Email ou mot de passe incorrect.';
        }

        return 'Connexion réussie';
    }
}
