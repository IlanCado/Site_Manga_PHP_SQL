<?php
use PHPUnit\Framework\TestCase;

// Inclure la fonction de validation à tester (à ajuster selon l'emplacement réel du fichier)
require_once __DIR__ . '/../functions.php';

class MangaValidationTest extends TestCase
{
    public function testMangaValidationSuccess()
    {
        // Exemple de manga valide
        $title = 'Naruto';
        $synopsis = 'L\'histoire d\'un jeune ninja qui rêve de devenir Hokage.';

        // Tester la validation avec des données valides
        $result = validateManga($title, $synopsis);
        $this->assertTrue($result, 'Le manga valide devrait être accepté.');
    }

    public function testMangaValidationFailsWithShortTitle()
    {
        // Exemple de manga avec un titre trop court
        $title = 'No';
        $synopsis = 'Un synopsis suffisamment long pour être valide.';

        // Tester la validation avec un titre trop court
        $result = validateManga($title, $synopsis);
        $this->assertNotTrue($result, 'Le titre trop court ne devrait pas être accepté.');
    }

    public function testMangaValidationFailsWithShortSynopsis()
    {
        // Exemple de manga avec un synopsis trop court
        $title = 'Naruto';
        $synopsis = 'Court';

        // Tester la validation avec un synopsis trop court
        $result = validateManga($title, $synopsis);
        $this->assertNotTrue($result, 'Le synopsis trop court ne devrait pas être accepté.');
    }
}
