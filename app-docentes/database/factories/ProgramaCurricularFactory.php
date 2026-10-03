<?php

namespace Database\Factories;

use App\Models\ProgramaCurricular;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgramaCurricularFactory extends Factory
{
    protected $model = ProgramaCurricular::class;

    public function definition(): array
    {
        return [
            'codigo' => (string) fake()->unique()->numberBetween(2500, 2999),
            'nombre' => fake()->randomElement([
                'Ingeniería de Sistemas y Computación',
                'Maestría en Ingeniería - Ingeniería de Sistemas',
                'Doctorado en Ingeniería - Sistemas y Computación',
                'Especialización en Seguridad Informática',
            ]),
            'nivel' => fake()->randomElement(['pregrado', 'posgrado']),
        ];
    }
}
