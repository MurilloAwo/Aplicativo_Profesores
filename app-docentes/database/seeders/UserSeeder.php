<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password123');

        // 1 Admin
        $admin = User::firstOrNew(['email' => 'admin@unal.edu.co']);
        $admin->fill([
            'nombres' => 'Administrador',
            'apellidos' => 'Departamento',
            'documento' => '10000001',
            'password' => $password,
            'tipo_vinculacion' => 'planta',
            'dedicacion' => 'Dedicación Exclusiva',
            'categoria' => 'Titular',
        ]);
        $admin->rol = User::ROL_ADMIN;
        $admin->activo = true;
        $admin->save();

        // 3 Profesores de prueba
        $profesores = [
            [
                'email' => 'docente1@unal.edu.co',
                'nombres' => 'Bernardo',
                'apellidos' => 'Gómez Hernández',
                'documento' => '20000001',
                'tipo_vinculacion' => 'planta',
                'dedicacion' => 'Dedicación Exclusiva',
                'categoria' => 'Titular',
            ],
            [
                'email' => 'docente2@unal.edu.co',
                'nombres' => 'Claudia',
                'apellidos' => 'Martínez Ruiz',
                'documento' => '20000002',
                'tipo_vinculacion' => 'ocasional',
                'dedicacion' => 'Tiempo Completo',
                'categoria' => 'Asociada',
            ],
            [
                'email' => 'docente3@unal.edu.co',
                'nombres' => 'Diego',
                'apellidos' => 'Silva Pantoja',
                'documento' => '20000003',
                'tipo_vinculacion' => 'catedra',
                'dedicacion' => 'Cátedra 0.5',
                'categoria' => 'Asistente',
            ],
        ];

        foreach ($profesores as $data) {
            $prof = User::firstOrNew(['email' => $data['email']]);
            $prof->fill([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'documento' => $data['documento'],
                'password' => $password,
                'tipo_vinculacion' => $data['tipo_vinculacion'],
                'dedicacion' => $data['dedicacion'],
                'categoria' => $data['categoria'],
            ]);
            $prof->rol = User::ROL_PROFESOR;
            $prof->activo = true;
            $prof->save();
        }
    }
}
