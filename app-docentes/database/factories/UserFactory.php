<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $nombres = fake()->firstName();
        $apellidos = fake()->lastName().' '.fake()->lastName();

        return [
            'name' => $nombres.' '.$apellidos,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'documento' => fake()->unique()->numerify('########'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'rol' => User::ROL_PROFESOR,
            'tipo_vinculacion' => fake()->randomElement(['planta', 'ocasional', 'catedra']),
            'dedicacion' => fake()->randomElement(['Dedicación Exclusiva', 'Tiempo Completo', 'Medio Tiempo', 'Cátedra']),
            'categoria' => fake()->randomElement(['Titular', 'Asociado', 'Asistente', 'Auxiliar']),
            'activo' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => User::ROL_ADMIN,
        ]);
    }

    public function profesor(): static
    {
        return $this->state(fn (array $attributes) => [
            'rol' => User::ROL_PROFESOR,
        ]);
    }

    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
