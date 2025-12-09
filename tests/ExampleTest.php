<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testBasicExample(): void
    {
        $this->assertTrue(true);
    }

    public function testCanInstantiate(): void
    {
        $this->assertIsString('Hello World');
    }
}
