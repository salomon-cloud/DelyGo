<?php

namespace Database\Factories;

use App\Models\Orden;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Orden>
 */
class OrdenFactory extends Factory
{
    protected $model = Orden::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => \App\Models\User::factory(),
            'restaurante_id' => \App\Models\Restaurante::factory(),
            'repartidor_id' => null,
            'estado' => 'recibida',
            'total' => $this->faker->randomFloat(2, 50, 500),
            'direccion_entrega' => $this->faker->address(),
        ];
    }

    public function preparando(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'preparando',
        ]);
    }

    public function en_camino(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'en_camino',
            'repartidor_id' => \App\Models\User::factory(['rol' => 'repartidor']),
        ]);
    }

    public function entregada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'entregada',
            'repartidor_id' => \App\Models\User::factory(['rol' => 'repartidor']),
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => 'cancelada',
        ]);
    }
}
