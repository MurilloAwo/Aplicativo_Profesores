<?php

namespace Database\Factories;

use App\Models\CriterioAcreditacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CriterioAcreditacionFactory extends Factory
{
    protected $model = CriterioAcreditacion::class;

    public function definition(): array
    {
        return [
            'codigo' => 'CRIT-'.fake()->unique()->numberBetween(1, 999),
            'nombre' => fake()->sentence(4),
            'descripcion' => fake()->paragraph(),
        ];
    }
}
