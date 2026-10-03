<?php

namespace Database\Factories;

use App\Models\Asignatura;
use App\Models\ProgramaCurricular;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsignaturaFactory extends Factory
{
    protected $model = Asignatura::class;

    public function definition(): array
    {
        return [
            'codigo' => (string) fake()->unique()->numberBetween(2010000, 2099999),
            'nombre' => fake()->randomElement([
                'Programación Orientada a Objetos',
                'Estructuras de Datos',
                'Ingeniería de Software II',
                'Redes de Computadores',
                'Bases de Datos',
                'Inteligencia Artificial',
                'Arquitectura de Computadores',
            ]),
            'creditos' => fake()->numberBetween(2, 4),
            'tipologia' => fake()->randomElement(['Disciplinar Obligatoria', 'Disciplinar Optativa', 'Fundamental']),
            'programa_curricular_id' => ProgramaCurricular::factory(),
        ];
    }
}
