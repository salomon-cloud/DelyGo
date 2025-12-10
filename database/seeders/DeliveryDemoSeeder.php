<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurante;
use App\Models\Producto;
use App\Models\Orden;
use Illuminate\Support\Str;

class DeliveryDemoSeeder extends Seeder
{
    /**
     * Seed demo data: 
     * - 4 usuarios de prueba principales (admin, cliente, restaurante, repartidor)
     * - 3 repartidores adicionales
     * - 3 restaurantes (with owners)
     * - 10 productos
     * - 4+ clientes
     * - 4+ órdenes
     */
    public function run(): void
    {
        // Create 4 main test users
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'rol' => 'admin',
            'password' => 'password',
        ]);

        $cliente_test = User::factory()->create([
            'name' => 'Cliente Prueba',
            'email' => 'cliente@example.com',
            'rol' => 'cliente',
            'password' => 'password',
        ]);

        $restaurante_test = User::factory()->create([
            'name' => 'Restaurante Prueba',
            'email' => 'restaurante@example.com',
            'rol' => 'restaurante',
            'password' => 'password',
        ]);

        $repartidor_test = User::factory()->create([
            'name' => 'Repartidor Prueba',
            'email' => 'repartidor@example.com',
            'rol' => 'repartidor',
            'password' => 'password',
        ]);
        // Create restaurant for test restaurant user
        $rest_prueba = Restaurante::create([
            'user_id' => $restaurante_test->id,
            'nombre' => 'Restaurante Prueba',
            'descripcion' => 'Restaurante de prueba para tests',
            'direccion' => 'Calle Principal 123',
        ]);

        // Create 3 additional repartidores
        $repartidores = collect([$repartidor_test]);
        for ($i = 1; $i <= 3; $i++) {
            $repartidores->push(User::factory()->create([
                'name' => "Repartidor $i",
                'email' => "repartidor{$i}@example.com",
                'rol' => 'repartidor',
                'password' => 'secret123', // User model will hash via cast
            ]));
        }

        // Create 3 additional restaurant owners and restaurants
        $restaurantes = collect([$rest_prueba]);
        for ($i = 1; $i <= 3; $i++) {
            $owner = User::factory()->create([
                'name' => "RestOwner $i",
                'email' => "restowner{$i}@example.com",
                'rol' => 'restaurante',
                'password' => 'secret123',
            ]);

            $restaurantes->push(Restaurante::create([
                'user_id' => $owner->id,
                'nombre' => "Demo Restaurante $i",
                'descripcion' => "Descripción del Restaurante $i",
                'direccion' => "Calle Demo $i",
            ]));
        }

        // Create 15 products distributed among the 4 restaurants
        $productos = collect();
        for ($i = 1; $i <= 15; $i++) {
            $rest = $restaurantes->get(($i - 1) % $restaurantes->count());
            $productos->push(Producto::create([
                'restaurante_id' => $rest->id,
                'nombre' => "Producto Demo $i",
                'descripcion' => "Descripción del producto demo $i",
                'precio' => round(100 + rand(0, 900) / 10, 2),
                'disponible' => true,
            ]));
        }

        // Create 4+ clientes (including test client)
        $clientes = collect([$cliente_test]);
        for ($i = 1; $i <= 3; $i++) {
            $clientes->push(User::factory()->create([
                'name' => "Cliente $i",
                'email' => "cliente{$i}@example.com",
                'rol' => 'cliente',
                'password' => 'secret123',
            ]));
        }

        // Create 4+ ordenes with products attached
        for ($i = 0; $i < 4; $i++) {
            $cliente = $clientes->get($i % $clientes->count());
            $rest = $restaurantes->get($i % $restaurantes->count());

            $orden = Orden::create([
                'cliente_id' => $cliente->id,
                'restaurante_id' => $rest->id,
                'repartidor_id' => $repartidores->get($i % $repartidores->count())->id,
                'estado' => $i === 0 ? 'recibida' : ($i === 1 ? 'preparando' : ($i === 2 ? 'en_camino' : 'entregada')),
                'total' => 0, // will calculate after attaching productos
                'direccion_entrega' => "Calle entrega $i",
            ]);

            // Attach 2-3 products to each order
            $attachedTotal = 0;
            $cantidad_productos = rand(2, 3);
            $toAttach = $productos->slice(($i * 3) % $productos->count(), $cantidad_productos);
            foreach ($toAttach as $prod) {
                $cantidad = rand(1, 3);
                $orden->productos()->attach($prod->id, ['cantidad' => $cantidad, 'precio_unitario' => $prod->precio]);
                $attachedTotal += $prod->precio * $cantidad;
            }

            $orden->total = $attachedTotal;
            $orden->save();
        }

        $this->command->info('✅ Demo data seeded:');
        $this->command->info('   - 4 main test users (admin, cliente, restaurante, repartidor)');
        $this->command->info('   - 3 additional repartidores');
        $this->command->info('   - 4 restaurantes with owners');
        $this->command->info('   - 15 productos');
        $this->command->info('   - 4 clientes');
        $this->command->info('   - 4 órdenes (with different states: recibida, preparando, en_camino, entregada)');
    }
}
