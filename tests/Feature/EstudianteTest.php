<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UsuarioRol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EstudianteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testDatosAcademicos()
    {
        $ur = UsuarioRol::find(20026);
        // factory()
        $response = $this->withSession(['ur' => $ur])
            ->withHeader('Accept', 'application/json')
            ->get('/egresado/datos-academicos');

        $response->dump();
    }
}
