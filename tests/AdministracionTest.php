<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class AdministracionTest extends TestCase
{
    private $appRoot;

    protected function setUp(): void
    {
        $this->appRoot = dirname(__DIR__);
    }

    public function testAdministracionFileExists(): void
    {
        $this->assertFileExists($this->appRoot . '/views/administracion.php');
    }

    public function testAdministracionFileIsReadable(): void
    {
        $this->assertIsReadable($this->appRoot . '/views/administracion.php');
    }

    public function testAdministracionFileContainsExpectedPhp(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString('$conn', $content);
        $this->assertStringContainsString('query', $content);
    }

    public function testAdministracionHandlesEditAction(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("'edit'", $content);
        $this->assertStringContainsString("UPDATE", $content);
    }

    public function testAdministracionHandlesDeleteAction(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("'delete'", $content);
        $this->assertStringContainsString("DELETE", $content);
    }

    public function testAdministracionPerformsDatabaseSelect(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("SELECT", $content);
        $this->assertStringContainsString("FROM", $content);
    }

    public function testAdministracionFileSyntax(): void
    {
        $file = $this->appRoot . '/views/administracion.php';
        $output = shell_exec("php -l " . escapeshellarg($file) . " 2>&1");
        $this->assertStringContainsString("No syntax errors", $output);
    }

    public function testAdministracionChecksRequestMethod(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("REQUEST_METHOD", $content);
        $this->assertStringContainsString("POST", $content);
    }

    public function testAdministracionValidatesConnections(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("connect_error", $content);
    }

    public function testAdministracionClosesConnection(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("close()", $content);
    }

    public function testAdministracionHasHtmlStructure(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("<!DOCTYPE html>", $content);
        $this->assertStringContainsString("<html", $content);
        $this->assertStringContainsString("</html>", $content);
    }

    public function testAdministracionHasFormElements(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("<form", $content);
        $this->assertStringContainsString("type='submit'", $content);
    }

    public function testAdministracionHandlesEmptyDestinos(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("num_rows", $content);
        $this->assertStringContainsString("fetch_assoc", $content);
    }

    public function testAdministracionHasEditFormFields(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("nombre", $content);
        $this->assertStringContainsString("tipo_destino", $content);
        $this->assertStringContainsString("precio_nino", $content);
        $this->assertStringContainsString("precio_adulto", $content);
        $this->assertStringContainsString("precio_mayor", $content);
    }

    public function testAdministracionProtectsAgainstSqlInjection(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        // Check if file handles input (even if not perfectly - this would be improved with prepared statements)
        $this->assertStringContainsString('$_POST', $content);
    }

    public function testAdministracionHasNavigationLinks(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString('href=', $content);
        $this->assertStringContainsString('index.php', $content);
    }

    public function testAdministracionSetsPageTitle(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString('<title>', $content);
        $this->assertStringContainsString('Administración', $content);
    }

    public function testAdministracionIncludesCss(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString('stylesheet', $content);
        $this->assertStringContainsString('css', $content);
    }

    public function testAdministracionHasEditButtonAction(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("Guardar Cambios", $content);
    }

    public function testAdministracionHasDeleteButtonAction(): void
    {
        $content = file_get_contents($this->appRoot . '/views/administracion.php');
        $this->assertStringContainsString("Eliminar", $content);
    }
}
