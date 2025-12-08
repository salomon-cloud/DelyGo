<?php

namespace Database\Factories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rating>
 */
class RatingFactory extends Factory
{
    protected $model = Rating::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'orden_id' => \App\Models\Orden::factory()->entregada(),
            'cliente_id' => \App\Models\User::factory(['rol' => 'cliente']),
            'repartidor_id' => \App\Models\User::factory(['rol' => 'repartidor']),
            'puntuacion' => $this->faker->numberBetween(1, 5),
            'comentario' => $this->faker->sentence(),
        ];
    }
}
