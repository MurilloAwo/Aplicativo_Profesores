<?php

namespace Database\Factories;

use App\Models\EntidadExterna;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntidadExternaFactory extends Factory
{
    protected $model = EntidadExterna::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->company(),
            'nit' => fake()->unique()->numerify('#########-#'),
            'sector' => fake()->randomElement(['Tecnología', 'Banca', 'Salud', 'Educación', 'Telecomunicaciones']),
            'ciudad' => fake()->city(),
            'contacto' => fake()->name().' - '.fake()->safeEmail(),
        ];
    }
}
