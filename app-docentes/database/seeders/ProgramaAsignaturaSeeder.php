<?php

namespace Database\Seeders;

use App\Models\Asignatura;
use App\Models\ProgramaCurricular;
use Illuminate\Database\Seeder;

class ProgramaAsignaturaSeeder extends Seeder
{
    public function run(): void
    {
        $pregrado = ProgramaCurricular::firstOrCreate(
            ['codigo' => '2541'],
            [
                'nombre' => 'Ingeniería de Sistemas y Computación',
                'nivel' => 'pregrado',
            ]
        );

        $posgrado = ProgramaCurricular::firstOrCreate(
            ['codigo' => '2542'],
            [
                'nombre' => 'Maestría en Ingeniería - Ingeniería de Sistemas y Computación',
                'nivel' => 'posgrado',
            ]
        );

        $asignaturas = [
            [
                'codigo' => '2016699',
                'nombre' => 'Ingeniería de Software II',
                'creditos' => 3,
                'tipologia' => 'Disciplinar Obligatoria',
                'programa_curricular_id' => $pregrado->id,
            ],
            [
                'codigo' => '2016701',
                'nombre' => 'Programación Orientada a Objetos',
                'creditos' => 3,
                'tipologia' => 'Disciplinar Obligatoria',
                'programa_curricular_id' => $pregrado->id,
            ],
            [
                'codigo' => '2016702',
                'nombre' => 'Estructuras de Datos',
                'creditos' => 3,
                'tipologia' => 'Disciplinar Obligatoria',
                'programa_curricular_id' => $pregrado->id,
            ],
            [
                'codigo' => '2016705',
                'nombre' => 'Redes de Computadores',
                'creditos' => 3,
                'tipologia' => 'Disciplinar Obligatoria',
                'programa_curricular_id' => $pregrado->id,
            ],
            [
                'codigo' => '2016710',
                'nombre' => 'Inteligencia Artificial',
                'creditos' => 3,
                'tipologia' => 'Disciplinar Optativa',
                'programa_curricular_id' => $pregrado->id,
            ],
            [
                'codigo' => '4010101',
                'nombre' => 'Arquitecturas de Software Avanzadas',
                'creditos' => 4,
                'tipologia' => 'Elegible Posgrado',
                'programa_curricular_id' => $posgrado->id,
            ],
        ];

        foreach ($asignaturas as $asig) {
            Asignatura::firstOrCreate(['codigo' => $asig['codigo']], $asig);
        }
    }
}
