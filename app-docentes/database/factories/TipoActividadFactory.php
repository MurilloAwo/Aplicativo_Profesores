<?php

namespace Database\Factories;

use App\Models\TipoActividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class TipoActividadFactory extends Factory
{
    protected $model = TipoActividad::class;

    public function definition(): array
    {
        return [
            'nombre' => fake()->unique()->sentence(3),
            'descripcion' => fake()->paragraph(),
            'categoria' => fake()->randomElement(array_keys(TipoActividad::CATEGORIAS)),
            'requiere_entidad_externa' => fake()->boolean(40),
            'activo' => true,
        ];
    }
}
