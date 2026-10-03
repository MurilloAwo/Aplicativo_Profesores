<?php

namespace Database\Seeders;

use App\Models\Asignatura;
use App\Models\Grupo;
use App\Models\PeriodoAcademico;
use App\Models\User;
use Illuminate\Database\Seeder;

class GrupoSeeder extends Seeder
{
    public function run(): void
    {
        $periodoActivo = PeriodoAcademico::where('codigo', '2026-2')->firstOrFail();
        $periodoAnterior = PeriodoAcademico::where('codigo', '2026-1')->firstOrFail();

        $prof1 = User::where('email', 'docente1@unal.edu.co')->firstOrFail();
        $prof2 = User::where('email', 'docente2@unal.edu.co')->firstOrFail();
        $prof3 = User::where('email', 'docente3@unal.edu.co')->firstOrFail();

        $asigSoftware = Asignatura::where('codigo', '2016699')->firstOrFail();
        $asigPOO = Asignatura::where('codigo', '2016701')->firstOrFail();
        $asigDatos = Asignatura::where('codigo', '2016702')->firstOrFail();
        $asigRedes = Asignatura::where('codigo', '2016705')->firstOrFail();
        $asigIA = Asignatura::where('codigo', '2016710')->firstOrFail();
        $asigArqPos = Asignatura::where('codigo', '4010101')->firstOrFail();

        // Grupos en el periodo activo 2026-2
        $gruposActivos = [
            [
                'asignatura_id' => $asigSoftware->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof1->id,
                'numero_grupo' => '1',
                'modalidad' => 'presencial',
                'horario' => 'Mar-Jue 07:00-09:00',
                'numero_estudiantes' => 32,
            ],
            [
                'asignatura_id' => $asigDatos->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof1->id,
                'numero_grupo' => '1',
                'modalidad' => 'presencial',
                'horario' => 'Mié-Vie 09:00-11:00',
                'numero_estudiantes' => 38,
            ],
            [
                'asignatura_id' => $asigPOO->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof2->id,
                'numero_grupo' => '1',
                'modalidad' => 'presencial',
                'horario' => 'Lun-Mié 07:00-09:00',
                'numero_estudiantes' => 35,
            ],
            [
                'asignatura_id' => $asigPOO->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof2->id,
                'numero_grupo' => '2',
                'modalidad' => 'hibrida',
                'horario' => 'Lun-Mié 09:00-11:00',
                'numero_estudiantes' => 34,
            ],
            [
                'asignatura_id' => $asigRedes->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof3->id,
                'numero_grupo' => '1',
                'modalidad' => 'presencial',
                'horario' => 'Mar-Jue 14:00-16:00',
                'numero_estudiantes' => 28,
            ],
            [
                'asignatura_id' => $asigIA->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof3->id,
                'numero_grupo' => '1',
                'modalidad' => 'virtual',
                'horario' => 'Vie 14:00-18:00',
                'numero_estudiantes' => 25,
            ],
            [
                'asignatura_id' => $asigArqPos->id,
                'periodo_academico_id' => $periodoActivo->id,
                'profesor_id' => $prof1->id,
                'numero_grupo' => '1',
                'modalidad' => 'presencial',
                'horario' => 'Sáb 08:00-12:00',
                'numero_estudiantes' => 15,
            ],
        ];

        foreach ($gruposActivos as $g) {
            Grupo::firstOrCreate(
                [
                    'asignatura_id' => $g['asignatura_id'],
                    'periodo_academico_id' => $g['periodo_academico_id'],
                    'numero_grupo' => $g['numero_grupo'],
                ],
                $g
            );
        }

        // Grupo histórico en periodo cerrado 2026-1
        Grupo::firstOrCreate(
            [
                'asignatura_id' => $asigSoftware->id,
                'periodo_academico_id' => $periodoAnterior->id,
                'numero_grupo' => '1',
            ],
            [
                'asignatura_id' => $asigSoftware->id,
                'periodo_academico_id' => $periodoAnterior->id,
                'profesor_id' => $prof1->id,
                'numero_grupo' => '1',
                'modalidad' => 'presencial',
                'horario' => 'Mar-Jue 07:00-09:00',
                'numero_estudiantes' => 30,
            ]
        );
    }
}
