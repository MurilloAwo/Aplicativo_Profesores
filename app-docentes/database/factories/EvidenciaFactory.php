<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\Evidencia;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvidenciaFactory extends Factory
{
    protected $model = Evidencia::class;

    public function definition(): array
    {
        $tipo = fake()->randomElement(['foto', 'acta', 'lista_asistencia', 'certificado', 'otro']);
        $extension = match ($tipo) {
            'foto' => 'jpg',
            'acta', 'lista_asistencia', 'certificado' => 'pdf',
            default => 'docx',
        };
        $mime = match ($extension) {
            'jpg' => 'image/jpeg',
            'pdf' => 'application/pdf',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };

        return [
            'actividad_id' => Actividad::factory(),
            'tipo' => $tipo,
            'nombre_original' => "documento_{$tipo}.{$extension}",
            'ruta' => "evidencias/test/{$tipo}_".fake()->uuid().".{$extension}",
            'mime' => $mime,
            'tamano' => fake()->numberBetween(10240, 5242880), // 10 KB a 5 MB
        ];
    }
}
