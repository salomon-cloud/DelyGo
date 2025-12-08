<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Orden;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_rate_delivered_order()
    {
        // Crear orden entregada
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'entregada',
        ]);

        // Cliente califica
        $response = $this->actingAs($client)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 5,
            'comentario' => 'Excelente comida y rápida entrega',
        ]);

        // Verificar que la rating fue creada
        $this->assertDatabaseHas('ratings', [
            'orden_id' => $orden->id,
            'cliente_id' => $client->id,
            'puntuacion' => 5,
            'comentario' => 'Excelente comida y rápida entrega',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('status');
    }

    public function test_client_cannot_rate_non_delivered_order()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'preparando', // No entregada
        ]);

        $response = $this->actingAs($client)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 5,
            'comentario' => 'Excelente',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('ratings', ['orden_id' => $orden->id]);
    }

    public function test_duplicate_rating_prevented()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'entregada',
        ]);

        // Crear primera rating
        Rating::factory()->create([
            'orden_id' => $orden->id,
            'cliente_id' => $client->id,
        ]);

        // Intentar crear segunda rating
        $response = $this->actingAs($client)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 4,
            'comentario' => 'Otra calificación',
        ]);

        $response->assertSessionHas('status');
        
        // Verificar que solo existe una rating
        $this->assertEquals(1, Rating::where('orden_id', $orden->id)->count());
    }

    public function test_invalid_puntuacion_rejected()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'entregada',
        ]);

        // Puntuación inválida (6, mayor a 5)
        $response = $this->actingAs($client)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 6,
            'comentario' => 'Excelente',
        ]);

        $response->assertSessionHasErrors('puntuacion');
        $this->assertDatabaseMissing('ratings', ['orden_id' => $orden->id]);
    }

    public function test_puntuacion_minimum_is_one()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'entregada',
        ]);

        $response = $this->actingAs($client)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 0, // Mínimo es 1
            'comentario' => 'Mala experiencia',
        ]);

        $response->assertSessionHasErrors('puntuacion');
    }

    public function test_other_client_cannot_rate_orden()
    {
        $client1 = User::factory()->create(['rol' => 'cliente']);
        $client2 = User::factory()->create(['rol' => 'cliente']);
        
        $orden = Orden::factory()->create([
            'cliente_id' => $client1->id,
            'estado' => 'entregada',
        ]);

        // client2 intenta calificar orden de client1
        $response = $this->actingAs($client2)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 5,
            'comentario' => 'Intento de fraude',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('ratings', ['orden_id' => $orden->id]);
    }

    public function test_unauthenticated_user_cannot_rate()
    {
        $orden = Orden::factory()->create(['estado' => 'entregada']);

        $response = $this->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 5,
            'comentario' => 'Excelente',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_comentario_max_length()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $orden = Orden::factory()->create([
            'cliente_id' => $client->id,
            'estado' => 'entregada',
        ]);

        // Comentario muy largo (más de 500 caracteres)
        $long_comment = str_repeat('a', 501);

        $response = $this->actingAs($client)->post('/ratings', [
            'orden_id' => $orden->id,
            'puntuacion' => 5,
            'comentario' => $long_comment,
        ]);

        $response->assertSessionHasErrors('comentario');
    }
}
