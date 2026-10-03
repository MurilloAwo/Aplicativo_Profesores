<?php

namespace Database\Factories;

use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GrupoFactory extends Factory
{
    protected $model = Grupo::class;

    public function definition(): array
    {
        return [
            'asignatura_id' => Asignatura::factory(),
            'periodo_academico_id' => PeriodoAcademico::factory(),
            'profesor_id' => User::factory(),
            'numero_grupo' => (string) fake()->numberBetween(1, 10),
            'modalidad' => fake()->randomElement(['presencial', 'virtual', 'hibrida']),
            'horario' => fake()->randomElement(['Lun-Mié 07:00-09:00', 'Mar-Jue 09:00-11:00', 'Vie 14:00-18:00']),
            'numero_estudiantes' => fake()->numberBetween(15, 45),
        ];
    }
}
