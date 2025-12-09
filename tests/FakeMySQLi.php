<?php

class FakeMySQLi
{
    public $connect_error = null;

    public function __construct() {}

    public function query($sql)
    {
        // Fake SELECT result
        if (stripos($sql, 'SELECT') === 0) {
            return new class {
                public $num_rows = 1;
                public function fetch_assoc() {
                    return [
                        'id' => 1,
                        'city' => 'Ciudad Test',
                        'tipo_destino' => 'Nacional',
                        'precio_nino' => 10,
                        'precio_adulto' => 20,
                        'precio_mayor' => 30,
                        'detalles' => 'Detalles de prueba'
                    ];
                }
            };
        }

        // Fake UPDATE/DELETE result
        return true;
    }

    public function close() {}
}
