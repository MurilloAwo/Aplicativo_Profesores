<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Criterios de acreditación se mantienen vacíos por defecto (catálogo editable por admin).
     */
    public function run(): void
    {
        $this->call([
            TipoActividadSeeder::class,
            PeriodoAcademicoSeeder::class,
            ProgramaAsignaturaSeeder::class,
            UserSeeder::class,
            GrupoSeeder::class,
        ]);
    }
}
