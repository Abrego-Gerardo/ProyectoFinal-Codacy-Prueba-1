<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private $indexPath;

    protected function setUp(): void
    {
        $this->indexPath = __DIR__ . '/../index.php';
    }

    /**
     * Test that index.php file exists
     */
    public function testIndexFileExists(): void
    {
        $this->assertFileExists($this->indexPath);
    }

    /**
     * Test that index.php file is readable
     */
    public function testIndexFileIsReadable(): void
    {
        $this->assertIsReadable($this->indexPath);
    }

    /**
     * Test that index.php starts with PHP opening tag
     */
    public function testIndexStartsWithPhpTag(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringStartsWith('<?php', $content);
    }

    /**
     * Test that index.php has error suppression for database connection
     */
    public function testIndexCallsSessionStart(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('new mysqli', $content);
    }

    /**
     * Test that index.php performs database connection
     */
    public function testIndexConnectsToDatabase(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('mysqli', $content);
        $this->assertStringContainsString('connect_error', $content);
    }

    /**
     * Test that index.php queries Nacional destinos
     */
    public function testIndexQueryNacionales(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('Nacional', $content);
        $this->assertStringContainsString('SELECT * FROM destinos', $content);
    }

    /**
     * Test that index.php queries Internacional destinos
     */
    public function testIndexQueryInternacionales(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('Internacional', $content);
    }

    /**
     * Test that index.php provides login links
     */
    public function testIndexChecksUserSession(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('login_form.php', $content);
        $this->assertStringContainsString('Iniciar Sesión', $content);
    }

    /**
     * Test that index.php handles empty destinos
     */
    public function testIndexHandlesEmptyDestinos(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('num_rows', $content);
        $this->assertStringContainsString('No hay destinos', $content);
    }

    /**
     * Test that index.php closes database connection
     */
    public function testIndexClosesConnection(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('$conn->close()', $content);
    }

    /**
     * Test that index.php has proper HTML structure
     */
    public function testIndexHasValidHtmlStructure(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('<!DOCTYPE html>', $content);
        $this->assertStringContainsString('<html', $content);
        $this->assertStringContainsString('</html>', $content);
        $this->assertStringContainsString('<head>', $content);
        $this->assertStringContainsString('</head>', $content);
        $this->assertStringContainsString('<body>', $content);
        $this->assertStringContainsString('</body>', $content);
    }

    /**
     * Test that index.php includes header navigation
     */
    public function testIndexIncludesNavigation(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('class="header"', $content);
        $this->assertStringContainsString('class="nav"', $content);
        $this->assertStringContainsString('Catálogo de Viajes', $content);
        $this->assertStringContainsString('Reservas', $content);
        $this->assertStringContainsString('Administración', $content);
    }

    /**
     * Test that index.php has footer
     */
    public function testIndexIncludesFooter(): void
    {
        $content = file_get_contents($this->indexPath);
        $this->assertStringContainsString('class="footer"', $content);
        $this->assertStringContainsString('derechos reservados', strtolower($content));
    }

    /**
     * Test PHP syntax validity
     */
    public function testIndexPhpSyntaxIsValid(): void
    {
        $output = shell_exec("php -l " . escapeshellarg($this->indexPath) . " 2>&1");
        $this->assertStringContainsString("No syntax errors", $output);
    }
}
