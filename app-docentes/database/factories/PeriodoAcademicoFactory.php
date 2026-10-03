<?php

namespace Database\Factories;

use App\Models\PeriodoAcademico;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodoAcademicoFactory extends Factory
{
    protected $model = PeriodoAcademico::class;

    public function definition(): array
    {
        $year = fake()->numberBetween(2024, 2028);
        $semestre = fake()->randomElement(['1', '2']);

        return [
            'codigo' => "{$year}-{$semestre}",
            'fecha_inicio' => "{$year}-02-01",
            'fecha_fin' => "{$year}-06-30",
            'activo' => false,
            'cerrado' => false,
        ];
    }

    public function activo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => true,
            'cerrado' => false,
        ]);
    }

    public function cerrado(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
            'cerrado' => true,
        ]);
    }
}
