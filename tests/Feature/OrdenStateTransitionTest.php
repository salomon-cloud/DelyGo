<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Orden;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrdenStateTransitionTest extends TestCase
{
    use RefreshDatabase;

    public function test_orden_starts_in_recibida_state()
    {
        $orden = Orden::factory()->create();

        $this->assertEquals('recibida', $orden->estado);
    }

    public function test_orden_can_transition_from_recibida_to_preparando()
    {
        $orden = Orden::factory()->create(['estado' => 'recibida']);

        $orden->transicionarA('preparando');

        $this->assertEquals('preparando', $orden->refresh()->estado);
    }

    public function test_orden_can_transition_from_preparando_to_en_camino()
    {
        $orden = Orden::factory()->create(['estado' => 'preparando']);

        $orden->transicionarA('en_camino');

        $this->assertEquals('en_camino', $orden->refresh()->estado);
    }

    public function test_orden_can_transition_from_en_camino_to_entregada()
    {
        $orden = Orden::factory()->create(['estado' => 'en_camino']);

        $orden->transicionarA('entregada');

        $this->assertEquals('entregada', $orden->refresh()->estado);
    }

    public function test_orden_can_transition_to_cancelada_from_recibida()
    {
        $orden = Orden::factory()->create(['estado' => 'recibida']);

        $orden->transicionarA('cancelada');

        $this->assertEquals('cancelada', $orden->refresh()->estado);
    }

    public function test_invalid_transition_throws_exception()
    {
        $this->expectException(\Exception::class);
        
        $orden = Orden::factory()->create(['estado' => 'entregada']);

        // Intentar transición inválida
        $orden->transicionarA('recibida');
    }

    public function test_cannot_transition_from_cancelada()
    {
        $this->expectException(\Exception::class);
        
        $orden = Orden::factory()->create(['estado' => 'cancelada']);

        $orden->transicionarA('preparando');
    }

    public function test_estado_cambio_event_is_fired()
    {
        \Event::fake();

        $orden = Orden::factory()->create(['estado' => 'recibida']);
        $orden->transicionarA('preparando');

        \Event::assertDispatched(\App\Events\EstadoOrdenCambio::class);
    }
}
