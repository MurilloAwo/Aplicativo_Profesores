<?php

namespace Database\Factories;

use App\Models\Actividad;
use App\Models\EntidadExterna;
use App\Models\Grupo;
use App\Models\TipoActividad;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActividadFactory extends Factory
{
    protected $model = Actividad::class;

    public function definition(): array
    {
        return [
            'grupo_id' => Grupo::factory(),
            'tipo_actividad_id' => TipoActividad::factory(),
            'entidad_externa_id' => null,
            'titulo' => fake()->sentence(5),
            'descripcion' => fake()->paragraph(),
            'objetivo' => fake()->sentence(10),
            'fecha_inicio' => fake()->date(),
            'fecha_fin' => fake()->date(),
            'duracion_horas' => fake()->randomFloat(2, 1, 20),
            'lugar' => fake()->address(),
            'modalidad' => fake()->randomElement(['presencial', 'virtual', 'hibrida']),
            'numero_estudiantes_participantes' => fake()->numberBetween(5, 50),
            'nombre_invitado' => fake()->name(),
            'cargo_invitado' => fake()->jobTitle(),
            'resultados_obtenidos' => fake()->paragraph(),
            'observaciones' => fake()->sentence(),
            'estado' => Actividad::ESTADO_REGISTRADA,
        ];
    }

    public function borrador(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Actividad::ESTADO_BORRADOR,
        ]);
    }

    public function registrada(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado' => Actividad::ESTADO_REGISTRADA,
        ]);
    }

    public function conEntidad(): static
    {
        return $this->state(fn (array $attributes) => [
            'entidad_externa_id' => EntidadExterna::factory(),
        ]);
    }
}
