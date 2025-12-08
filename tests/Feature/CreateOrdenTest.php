<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Orden;
use App\Models\Restaurante;
use App\Models\Producto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrdenTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_create_orden_with_valid_data()
    {
        // Crear usuario cliente
        $client = User::factory()->create([
            'rol' => 'cliente',
        ]);

        // Crear restaurante y productos
        $restaurant = Restaurante::factory()->create();
        $product1 = Producto::factory()->create([
            'restaurante_id' => $restaurant->id,
            'precio' => 100,
            'disponible' => 1,
        ]);
        $product2 = Producto::factory()->create([
            'restaurante_id' => $restaurant->id,
            'precio' => 50,
            'disponible' => 1,
        ]);

        // Hacer request como cliente autenticado
        $response = $this->actingAs($client)->post('/cliente/orden', [
            'restaurante_id' => $restaurant->id,
            'productos' => [
                ['id' => $product1->id, 'cantidad' => 2],
                ['id' => $product2->id, 'cantidad' => 1],
            ],
            'direccion_entrega' => 'Calle Principal 123, Apartamento 4B',
            'total' => 250,
        ]);

        // Verificar que la orden fue creada
        $this->assertDatabaseHas('ordenes', [
            'cliente_id' => $client->id,
            'restaurante_id' => $restaurant->id,
            'estado' => 'recibida',
        ]);

        // Verificar que responde exitosamente
        $this->assertTrue(in_array($response->getStatusCode(), [201, 302]));
    }

    public function test_orden_creation_fails_with_invalid_restaurante()
    {
        $client = User::factory()->create(['rol' => 'cliente']);

        $response = $this->actingAs($client)->post('/cliente/orden', [
            'restaurante_id' => 999, // No existe
            'productos' => [['id' => 1, 'cantidad' => 1]],
            'direccion_entrega' => 'Calle Principal 123',
            'total' => 100,
        ]);

        // Debe fallar validación
        $response->assertSessionHasErrors('restaurante_id');
    }

    public function test_orden_creation_requires_at_least_one_producto()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $restaurant = Restaurante::factory()->create();

        $response = $this->actingAs($client)->post('/cliente/orden', [
            'restaurante_id' => $restaurant->id,
            'productos' => [], // Vacío
            'direccion_entrega' => 'Calle Principal 123',
            'total' => 100,
        ]);

        $response->assertSessionHasErrors('productos');
    }

    public function test_non_client_cannot_create_orden()
    {
        // Usuario restaurante no puede crear orden
        $restaurante_user = User::factory()->create(['rol' => 'restaurante']);
        $restaurant = Restaurante::factory()->create();
        $product = Producto::factory()->create(['restaurante_id' => $restaurant->id]);

        $response = $this->actingAs($restaurante_user)->post('/cliente/orden', [
            'restaurante_id' => $restaurant->id,
            'productos' => [['id' => $product->id, 'cantidad' => 1]],
            'direccion_entrega' => 'Calle Principal 123',
            'total' => 100,
        ]);

        // Debe ser rechazado por no ser cliente
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_create_orden()
    {
        $restaurant = Restaurante::factory()->create();
        $product = Producto::factory()->create(['restaurante_id' => $restaurant->id]);

        $response = $this->post('/cliente/orden', [
            'restaurante_id' => $restaurant->id,
            'productos' => [['id' => $product->id, 'cantidad' => 1]],
            'direccion_entrega' => 'Calle Principal 123',
            'total' => 100,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_orden_total_validation()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $restaurant = Restaurante::factory()->create();
        $product = Producto::factory()->create(['restaurante_id' => $restaurant->id]);

        $response = $this->actingAs($client)->post('/cliente/orden', [
            'restaurante_id' => $restaurant->id,
            'productos' => [['id' => $product->id, 'cantidad' => 1]],
            'direccion_entrega' => 'Calle Principal 123',
            'total' => 0, // Total inválido
        ]);

        $response->assertSessionHasErrors('total');
    }

    public function test_direccion_entrega_required()
    {
        $client = User::factory()->create(['rol' => 'cliente']);
        $restaurant = Restaurante::factory()->create();
        $product = Producto::factory()->create(['restaurante_id' => $restaurant->id]);

        $response = $this->actingAs($client)->post('/cliente/orden', [
            'restaurante_id' => $restaurant->id,
            'productos' => [['id' => $product->id, 'cantidad' => 1]],
            'direccion_entrega' => '', // Vacío
            'total' => 100,
        ]);

        $response->assertSessionHasErrors('direccion_entrega');
    }
}
