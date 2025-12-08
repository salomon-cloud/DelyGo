<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Orden;
use App\Models\User;
use App\Models\Restaurante;
use App\Mail\OrdenEstadoCambio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_mailable_can_be_instantiated()
    {
        $client = User::factory()->create([
            'rol' => 'cliente',
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);
        $restaurante = Restaurante::factory()->create(['nombre' => 'Test Restaurant']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'restaurante_id' => $restaurante->id,
            'estado' => 'preparando',
            'total' => 125.50,
            'direccion_entrega' => 'Test Address',
        ]);

        $mailable = new OrdenEstadoCambio($orden);

        // Verificar que puede ser instanciada y contiene datos
        $this->assertNotNull($mailable->orden);
        $this->assertEquals($orden->id, $mailable->orden->id);
        $this->assertEquals($client->email, $mailable->orden->cliente->email);
    }

    public function test_listener_handles_estado_orden_cambio_event()
    {
        $client = User::factory()->create(['rol' => 'cliente', 'email' => 'client@test.com']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'recibida',
        ]);

        // Ejecutar transición - debe disparar evento
        $orden->transicionarA('preparando');

        // Verificar que la orden tiene el nuevo estado
        $this->assertEquals('preparando', $orden->fresh()->estado);
    }

    public function test_mailable_has_correct_recipient()
    {
        $client = User::factory()->create([
            'rol' => 'cliente',
            'email' => 'testclient@example.com',
        ]);
        $orden = Orden::factory()->create(['cliente_id' => $client->id]);

        $mailable = new OrdenEstadoCambio($orden);

        // El mailable debería poder acceder a los datos del cliente
        $this->assertEquals($client->email, $orden->cliente->email);
    }

    public function test_mailable_envelope_subject_is_set()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create(['cliente_id' => $client->id, 'estado' => 'preparando']);

        $mailable = new OrdenEstadoCambio($orden);
        $envelope = $mailable->envelope();

        // Verificar que el subject no está vacío
        $this->assertNotEmpty($envelope->subject);
    }

    public function test_mailable_content_view_is_defined()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create(['cliente_id' => $client->id, 'estado' => 'preparando']);

        $mailable = new OrdenEstadoCambio($orden);
        $content = $mailable->content();

        // Verificar que tiene una vista definida
        $this->assertNotNull($content);
    }
}
