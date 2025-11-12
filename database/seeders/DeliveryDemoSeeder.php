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
     * Seed demo data: 3 repartidores, 3 restaurantes (with owners), 10 productos, 2 clientes and 2 ordenes.
     */
    public function run(): void
    {
        // Create 3 repartidores
        $repartidores = collect();
        for ($i = 1; $i <= 3; $i++) {
            $repartidores->push(User::factory()->create([
                'name' => "Repartidor $i",
                'email' => "repartidor{$i}@example.com",
                'rol' => 'repartidor',
                'password' => 'secret123', // User model will hash via cast
            ]));
        }

        // Create 3 restaurant owners and restaurants
        $restaurantes = collect();
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

        // Create 10 products distributed among the 3 restaurants
        $productos = collect();
        for ($i = 1; $i <= 10; $i++) {
            $rest = $restaurantes->get(($i - 1) % $restaurantes->count());
            $productos->push(Producto::create([
                'restaurante_id' => $rest->id,
                'nombre' => "Producto Demo $i",
                'descripcion' => "Descripción del producto demo $i",
                'precio' => round(100 + rand(0, 900) / 10, 2),
                'disponible' => true,
            ]));
        }

        // Create 2 clientes
        $clientes = collect();
        for ($i = 1; $i <= 2; $i++) {
            $clientes->push(User::factory()->create([
                'name' => "Cliente $i",
                'email' => "cliente{$i}@example.com",
                'rol' => 'cliente',
                'password' => 'secret123',
            ]));
        }

        // Create 2 ordenes (one per cliente) with products attached
        for ($i = 1; $i <= 2; $i++) {
            $cliente = $clientes->get($i - 1);
            $rest = $restaurantes->get(($i - 1) % $restaurantes->count());

            $orden = Orden::create([
                'cliente_id' => $cliente->id,
                'restaurante_id' => $rest->id,
                'repartidor_id' => $repartidores->first()->id,
                'estado' => 'preparando',
                'total' => 0, // will calculate after attaching productos
                'direccion_entrega' => "Calle entrega $i",
            ]);

            // Attach 2 products to each order
            $attachedTotal = 0;
            $toAttach = $productos->slice(($i - 1) * 2, 2);
            foreach ($toAttach as $prod) {
                $cantidad = rand(1, 3);
                $orden->productos()->attach($prod->id, ['cantidad' => $cantidad, 'precio_unitario' => $prod->precio]);
                $attachedTotal += $prod->precio * $cantidad;
            }

            $orden->total = $attachedTotal;
            $orden->save();
        }

        $this->command->info('Demo data seeded: 3 repartidores, 3 restaurantes, 10 productos, 2 clientes and 2 ordenes.');
    }
}
