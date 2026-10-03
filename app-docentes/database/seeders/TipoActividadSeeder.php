<?php

namespace Database\Seeders;

use App\Models\TipoActividad;
use Illuminate\Database\Seeder;

class TipoActividadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Visita empresarial o técnica',
                'descripcion' => 'Visita académica con estudiantes a instalaciones de empresas u organizaciones del sector productivo.',
                'categoria' => 'relacion_sector_externo',
                'requiere_entidad_externa' => true,
                'activo' => true,
            ],
            [
                'nombre' => 'Charla o conferencia con invitado externo',
                'descripcion' => 'Participación de expertos de la industria o academia internacional como conferencistas invitados en el aula.',
                'categoria' => 'relacion_sector_externo',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Salida de campo académica',
                'descripcion' => 'Desplazamiento pedagógico fuera del campus para recolección de datos, observación o aplicación práctica.',
                'categoria' => 'innovacion_pedagogica',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Proyecto de aula aplicado',
                'descripcion' => 'Desarrollo de proyectos prácticos que resuelven problemas del contexto real o institucional.',
                'categoria' => 'innovacion_pedagogica',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Proyecto conjunto con empresa / sector externo',
                'descripcion' => 'Convenio o articulación específica de la asignatura con una entidad externa para desarrollo colaborativo.',
                'categoria' => 'relacion_sector_externo',
                'requiere_entidad_externa' => true,
                'activo' => true,
            ],
            [
                'nombre' => 'Incorporación de nuevas herramientas tecnológicas',
                'descripcion' => 'Adopción de nuevas plataformas de software, hardware especializado o metodologías innovadoras en la enseñanza.',
                'categoria' => 'innovacion_pedagogica',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Ajuste y actualización al programa de la asignatura',
                'descripcion' => 'Actualización de contenidos temáticos, bibliografía o competencias en el syllabus curricular.',
                'categoria' => 'gestion_curricular',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Participación en eventos académicos o congresos',
                'descripcion' => 'Presentación de ponencias, pósteres o asistencia organizada con estudiantes a congresos y simposios.',
                'categoria' => 'extension',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
            [
                'nombre' => 'Semillero de investigación o grupo de estudio',
                'descripcion' => 'Actividades extracurriculares de investigación formativa vinculadas al área de la asignatura.',
                'categoria' => 'investigacion_formativa',
                'requiere_entidad_externa' => false,
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            TipoActividad::firstOrCreate(['nombre' => $tipo['nombre']], $tipo);
        }
    }
}
