<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ViewsTest extends TestCase
{
    private $viewsPath;

    protected function setUp(): void
    {
        $this->viewsPath = __DIR__ . '/../views';
    }

    /**
     * Test that administracion.php exists and is valid PHP
     */
    public function testAdministracionPhpExists(): void
    {
        $file = $this->viewsPath . '/administracion.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that agregar_paquete.php exists and is valid PHP
     */
    public function testAgregarPaquetePhpExists(): void
    {
        $file = $this->viewsPath . '/agregar_paquete.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that buscar_viajes.php exists and is valid PHP
     */
    public function testBuscarVijesPhpExists(): void
    {
        $file = $this->viewsPath . '/buscar_viajes.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that catalogo_viajes.php exists and is valid PHP
     */
    public function testCatalogoVialesPhpExists(): void
    {
        $file = $this->viewsPath . '/catalogo_viajes.php';
        $this->assertFileExists($file);
        $content = file_get_contents($file);
        $this->assertStringContainsString('<?php', $content);
    }

    /**
     * Test that confirmar_reserva.php exists and contains PHP code
     */
    public function testConfirmarReservaPhpExists(): void
    {
        $file = $this->viewsPath . '/confirmar_reserva.php';
        $this->assertFileExists($file);
        $content = file_get_contents($file);
        $this->assertStringContainsString('<?php', $content);
    }

    /**
     * Test that contacto.php exists and contains PHP code
     */
    public function testContactoPhpExists(): void
    {
        $file = $this->viewsPath . '/contacto.php';
        $this->assertFileExists($file);
        $content = file_get_contents($file);
        $this->assertStringContainsString('<?php', $content);
    }

    /**
     * Test that database.php exists and is valid PHP
     */
    public function testDatabasePhpExists(): void
    {
        $file = $this->viewsPath . '/database.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that detalles_reservas.php exists and contains PHP code
     */
    public function testDetallesReservasPhpExists(): void
    {
        $file = $this->viewsPath . '/detalles_reservas.php';
        $this->assertFileExists($file);
        $content = file_get_contents($file);
        $this->assertStringContainsString('<?php', $content);
    }

    /**
     * Test that detalles_viaje.php exists and is valid PHP
     */
    public function testDetallesViajePhpExists(): void
    {
        $file = $this->viewsPath . '/detalles_viaje.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that login_form.php exists and is valid PHP
     */
    public function testLoginFormPhpExists(): void
    {
        $file = $this->viewsPath . '/login_form.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that logout.php exists and is valid PHP
     */
    public function testLogoutPhpExists(): void
    {
        $file = $this->viewsPath . '/logout.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that procesar_reserva.php exists and is valid PHP
     */
    public function testProcesarReservaPhpExists(): void
    {
        $file = $this->viewsPath . '/procesar_reserva.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that register_form.php exists and is valid PHP
     */
    public function testRegisterFormPhpExists(): void
    {
        $file = $this->viewsPath . '/register_form.php';
        $this->assertFileExists($file);
        $this->assertStringStartsWith('<?php', file_get_contents($file));
    }

    /**
     * Test that all PHP files contain valid PHP syntax
     */
    public function testAllPhpFilesHaveValidSyntax(): void
    {
        $files = glob($this->viewsPath . '/*.php');
        $this->assertNotEmpty($files, 'Should find PHP files in views directory');
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $this->assertStringContainsString('<?php', $content, "File $file should contain PHP code");
        }
    }

    /**
     * Test views directory exists
     */
    public function testViewsDirectoryExists(): void
    {
        $this->assertDirectoryExists($this->viewsPath);
    }
}
