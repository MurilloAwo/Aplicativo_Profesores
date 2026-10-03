<?php

namespace Database\Seeders;

use App\Models\PeriodoAcademico;
use Illuminate\Database\Seeder;

class PeriodoAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        // 2 periodos: uno previo cerrado y el actual activo
        PeriodoAcademico::firstOrCreate(
            ['codigo' => '2026-1'],
            [
                'fecha_inicio' => '2026-02-02',
                'fecha_fin' => '2026-06-26',
                'activo' => false,
                'cerrado' => true,
            ]
        );

        PeriodoAcademico::firstOrCreate(
            ['codigo' => '2026-2'],
            [
                'fecha_inicio' => '2026-08-04',
                'fecha_fin' => '2026-12-18',
                'activo' => true,
                'cerrado' => false,
            ]
        );
    }
}
