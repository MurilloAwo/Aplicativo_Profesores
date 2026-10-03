<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega los datos institucionales del docente a la tabla users de Breeze.
     * Se conserva `name` (usado por Breeze) como nombre completo.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombres')->default('')->after('id');
            $table->string('apellidos')->default('')->after('nombres');
            $table->string('documento', 30)->nullable()->unique()->after('apellidos');
            $table->enum('rol', ['admin', 'profesor'])->default('profesor')->after('email');
            $table->enum('tipo_vinculacion', ['planta', 'ocasional', 'catedra'])->nullable()->after('rol');
            // Valores institucionales no definidos aún: texto libre configurable.
            $table->string('dedicacion', 50)->nullable()->after('tipo_vinculacion');
            $table->string('categoria', 50)->nullable()->after('dedicacion');
            $table->boolean('activo')->default(true)->after('categoria');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['documento']);
            $table->dropColumn([
                'nombres', 'apellidos', 'documento', 'rol',
                'tipo_vinculacion', 'dedicacion', 'categoria', 'activo',
            ]);
        });
    }
};
