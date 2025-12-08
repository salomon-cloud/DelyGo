<?php

namespace Database\Factories;

use App\Models\Restaurante;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Restaurante>
 */
class RestauranteFactory extends Factory
{
    protected $model = Restaurante::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(['rol' => 'restaurante']),
            'nombre' => $this->faker->name() . ' Restaurant',
            'descripcion' => $this->faker->paragraph(),
            'direccion' => $this->faker->address(),
        ];
    }
}
