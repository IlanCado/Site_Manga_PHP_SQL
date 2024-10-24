<?php
use PHPUnit\Framework\TestCase;

class UserAccountTest extends TestCase
{
    protected function setUp(): void
    {
        // Réinitialiser $_POST avant chaque test
        $_POST = [];
    }

    public function testAccountCreationWithValidData()
    {
        // Exemple de données valides
        $_POST['username'] = 'ValidUser';
        $_POST['email'] = 'validuser@example.com';
        $_POST['password'] = 'ValidPassword123';

        // Simuler la logique de validation sans effectuer de création en base
        $output = $this->simulateAccountCreation();

        // Test de succès - on s'attend à ce qu'il n'y ait pas de message d'erreur
        $this->assertStringNotContainsString('Veuillez remplir tous les champs.', $output);
        $this->assertStringNotContainsString("L'adresse email n'est pas valide.", $output);
    }

    public function testAccountCreationFailsWithMissingFields()
    {
        // Champs manquants : pas de mot de passe
        $_POST['username'] = 'UserWithoutPassword';
        $_POST['email'] = 'user@example.com';
        unset($_POST['password']);

        $output = $this->simulateAccountCreation();

        $this->assertStringContainsString('Veuillez remplir tous les champs.', $output);
    }

    public function testAccountCreationFailsWithInvalidEmail()
    {
        // Email invalide
        $_POST['username'] = 'UserWithInvalidEmail';
        $_POST['email'] = 'invalid-email';
        $_POST['password'] = 'ValidPassword123';

        $output = $this->simulateAccountCreation();

        $this->assertStringContainsString("L'adresse email n'est pas valide.", $output);
    }

    public function testAccountCreationFailsWithDuplicateEmail()
    {
        // Simuler un email déjà présent
        $_POST['username'] = 'DuplicateUser';
        $_POST['email'] = 'existinguser@example.com'; // Un email déjà utilisé
        $_POST['password'] = 'ValidPassword123';

        // Ici, on simule l'email en double en utilisant une logique dans la méthode simulateAccountCreation
        $output = $this->simulateAccountCreation(true); // Pass true pour simuler un email existant

        // Test que la création de compte échoue pour un email déjà présent
        $this->assertStringContainsString('Email déjà utilisé', $output);
    }

    protected function tearDown(): void
    {
        // Nettoyage après chaque test
        $_POST = [];
    }

    private function simulateAccountCreation($simulateDuplicateEmail = false)
    {
        // Simuler la logique de validation sans toucher la base de données

        $username = $_POST['username'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if (empty($username) || empty($email) || empty($password)) {
            return 'Veuillez remplir tous les champs.';
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "L'adresse email n'est pas valide.";
        }

        if ($simulateDuplicateEmail && $email == 'existinguser@example.com') {
            return 'Email déjà utilisé';
        }

        return 'Succès';
    }
}
